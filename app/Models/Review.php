<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'rating',
        'comment',
    ];

    protected static function boot(): void
    {
        parent::boot();

        // مسح cache التقييمات عند إضافة أو تحديث تقييم
        static::saved(function (Review $review) {
            if ($review->order) {
                $order = $review->order;
                // مسح cache للمنتجات المرتبطة بهذا الطلب
                if (is_array($order->items) && !empty($order->items)) {
                    foreach ($order->items as $item) {
                        if (isset($item['product_id'])) {
                            Cache::forget('product.reviews.' . $item['product_id']);
                            // تحديث تقييم المنتج
                            static::updateProductRating($item['product_id']);
                        }
                    }
                }
                // في حالة الطلبات القديمة التي تستخدم product_name
                if ($order->product_name) {
                    $product = \App\Models\Product::where('name', $order->product_name)->first();
                    if ($product) {
                        Cache::forget('product.reviews.' . $product->id);
                        static::updateProductRating($product->id);
                    }
                }
            }
        });

        static::deleted(function (Review $review) {
            if ($review->order) {
                $order = $review->order;
                if (is_array($order->items) && !empty($order->items)) {
                    foreach ($order->items as $item) {
                        if (isset($item['product_id'])) {
                            Cache::forget('product.reviews.' . $item['product_id']);
                            static::updateProductRating($item['product_id']);
                        }
                    }
                }
                if ($order->product_name) {
                    $product = \App\Models\Product::where('name', $order->product_name)->first();
                    if ($product) {
                        Cache::forget('product.reviews.' . $product->id);
                        static::updateProductRating($product->id);
                    }
                }
            }
        });
    }

    /**
     * تحديث تقييم المنتج بناءً على جميع التقييمات
     */
    protected static function updateProductRating(int $productId): void
    {
        $product = \App\Models\Product::find($productId);
        if (!$product) {
            return;
        }

        // مسح cache قبل الحساب
        Cache::forget('product.reviews.' . $productId);

        $reviews = static::whereHas('order', function ($q) use ($product) {
            $q->where('product_name', $product->name)
                ->orWhere('items', 'like', '%"product_id":' . $product->id . '%');
        })->get();

        $ratingCount = $reviews->count();
        $ratingAverage = $ratingCount > 0 ? (float) $reviews->avg('rating') : 0.0;

        $product->rating_count = $ratingCount;
        $product->rating_average = $ratingAverage;
        $product->saveQuietly(); // استخدام saveQuietly لتجنب trigger events مرة أخرى
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

