<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_name',
        'quantity',
        'unit_price',
        'total',
        'status',
        'payment_method',
        'shipping_address',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function review(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * نطاق للطلبات المؤكدة فقط.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * إجمالي المبيعات (total) في فترة زمنية معينة.
     */
    public static function salesBetween(Carbon $from, Carbon $to): float
    {
        return (float) static::confirmed()
            ->whereBetween('created_at', [$from, $to])
            ->sum('total');
    }

    public static function salesToday(): float
    {
        return static::salesBetween(Carbon::today(), Carbon::tomorrow());
    }

    public static function salesThisWeek(): float
    {
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek()->endOfDay();
        return static::salesBetween($start, $end);
    }

    public static function salesThisMonth(): float
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth()->endOfDay();
        return static::salesBetween($start, $end);
    }

    /**
     * عدد الطلبات حسب الحالة.
     */
    public static function countByStatus(string $status): int
    {
        return static::where('status', $status)->count();
    }

    /**
     * حساب ربح تقريبي لفترة محددة بافتراض أن items عبارة عن مصفوفة
     * تحتوي على product_id, quantity, unit_price.
     *
     * الربح التقريبي يعتمد على cost_price الحالي للمنتج وليس التكلفة التاريخية.
     */
    public static function approximateProfitBetween(Carbon $from, Carbon $to): float
    {
        /** @var Collection<int, array{items: array}> $orders */
        $orders = static::confirmed()
            ->whereBetween('created_at', [$from, $to])
            ->get(['items']);

        if ($orders->isEmpty()) {
            return 0.0;
        }

        // جمع الكميات المباعة لكل منتج
        $quantities = [];
        foreach ($orders as $order) {
            $items = $order->items ?? [];
            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                $qty = (float) ($item['quantity'] ?? 0);
                if ($productId && $qty > 0) {
                    if (! isset($quantities[$productId])) {
                        $quantities[$productId] = 0.0;
                    }
                    $quantities[$productId] += $qty;
                }
            }
        }

        if (empty($quantities)) {
            return 0.0;
        }

        // جلب أسعار التكلفة والبيع الحالية
        $products = DB::table('products')
            ->whereIn('id', array_keys($quantities))
            ->select('id', 'cost_price', 'price')
            ->get()
            ->keyBy('id');

        $profit = 0.0;
        foreach ($quantities as $productId => $qty) {
            $product = $products->get($productId);
            if (! $product) {
                continue;
            }

            $cost = $product->cost_price ?? 0.0;
            $price = $product->price ?? 0.0;

            // الربح للوحدة بناءً على الأسعار الحالية
            $unitProfit = $price - $cost;
            $profit += $unitProfit * $qty;
        }

        return (float) $profit;
    }

    /**
     * حساب مجموع النقاط المستحقة على طلبية معيّنة بالاعتماد على
     * حقل points_reward لكل منتج وعدد الوحدات المباعة في items.
     */
    public static function calculatePointsForOrder(self $order): int
    {
        $items = $order->items ?? [];

        if (empty($items) || ! is_array($items)) {
            return 0;
        }

        $quantities = [];
        foreach ($items as $item) {
            $productId = $item['product_id'] ?? null;
            $qty = (int) ($item['quantity'] ?? 0);

            if (! $productId || $qty <= 0) {
                continue;
            }

            if (! isset($quantities[$productId])) {
                $quantities[$productId] = 0;
            }

            $quantities[$productId] += $qty;
        }

        if (empty($quantities)) {
            return 0;
        }

        $products = DB::table('products')
            ->whereIn('id', array_keys($quantities))
            ->select('id', 'points_reward')
            ->get()
            ->keyBy('id');

        $totalPoints = 0;
        foreach ($quantities as $productId => $qty) {
            $product = $products->get($productId);
            if (! $product) {
                continue;
            }

            $perUnit = (int) ($product->points_reward ?? 0);
            if ($perUnit <= 0) {
                continue;
            }

            $totalPoints += $perUnit * $qty;
        }

        return $totalPoints;
    }

    /**
     * إضافة النقاط المستحقة للمستخدم بناءً على الطلبية (مرة واحدة فقط
     * عند تأكيد الطلب).
     */
    public static function awardPointsForOrder(self $order, ?User $user = null): void
    {
        $user = $user ?: $order->user;

        if (! $user) {
            return;
        }

        $points = static::calculatePointsForOrder($order);

        if ($points <= 0) {
            return;
        }

        $user->increment('points', $points);
    }

    /**
     * تحديث المخزون وعدّاد المبيعات بناءً على عناصر الطلبية المؤكَّدة.
     *
     * - يقلِّل حقل stock لكل منتج حسب الكمية المباعة (بدون السماح بأن يصبح بالسالب).
     * - يزيد حقل sales_count بعدد الوحدات المباعة.
     * - يسجِّل تحذيراً في السجلات إذا أصبح المخزون أقل أو يساوي عتبة المخزون المنخفض.
     */
    public static function applyInventoryForOrder(self $order): void
    {
        $items = $order->items ?? [];

        if (empty($items) || ! is_array($items)) {
            return;
        }

        $threshold = (int) config('catalog.low_stock_threshold', 5);

        foreach ($items as $item) {
            $productId = $item['product_id'] ?? null;
            $qty = (int) ($item['quantity'] ?? 0);

            if (! $productId || $qty <= 0) {
                continue;
            }

            $product = DB::table('products')
                ->where('id', $productId)
                ->first();

            if (! $product) {
                continue;
            }

            $currentStock = (int) ($product->stock ?? 0);
            $currentSalesCount = (int) ($product->sales_count ?? 0);

            // لا نسمح بأن يصبح المخزون بالسالب
            $deduct = $qty;
            if ($currentStock < $deduct) {
                $deduct = max($currentStock, 0);
            }

            $newStock = $currentStock - $deduct;
            $newSalesCount = $currentSalesCount + $qty;

            DB::table('products')
                ->where('id', $productId)
                ->update([
                    'stock' => $newStock,
                    'sales_count' => $newSalesCount,
                ]);

            if ($threshold > 0 && $newStock <= $threshold) {
                \Log::warning(
                    sprintf(
                        'Low stock for product #%d after order #%d. Remaining stock: %d',
                        $productId,
                        $order->id,
                        $newStock
                    )
                );
            }
        }
    }

    public static function approximateProfitToday(): float
    {
        return static::approximateProfitBetween(Carbon::today(), Carbon::tomorrow());
    }

    public static function approximateProfitThisWeek(): float
    {
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek()->endOfDay();
        return static::approximateProfitBetween($start, $end);
    }

    public static function approximateProfitThisMonth(): float
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth()->endOfDay();
        return static::approximateProfitBetween($start, $end);
    }
}

