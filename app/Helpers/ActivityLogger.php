<?php

namespace App\Helpers;

use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * تسجيل نشاط للمستخدم
     */
    public static function log(string $type, string $action, ?string $description = null, ?array $metadata = null): void
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        $request = request();

        UserActivity::create([
            'user_id' => $user->id,
            'type' => $type,
            'action' => $action,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * تسجيل تسجيل دخول
     */
    public static function logLogin(): void
    {
        self::log('login', 'تسجيل دخول', 'تم تسجيل الدخول بنجاح');
    }

    /**
     * تسجيل تسجيل خروج
     */
    public static function logLogout(): void
    {
        self::log('logout', 'تسجيل خروج', 'تم تسجيل الخروج');
    }

    /**
     * تسجيل عرض منتج
     */
    public static function logProductView($product): void
    {
        self::log(
            'product_view',
            'عرض منتج',
            "عرض المنتج: {$product->translated_name}",
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $product->price,
            ]
        );
    }

    /**
     * تسجيل إيداع مبلغ
     */
    public static function logDeposit(float $amount, ?string $description = null): void
    {
        self::log(
            'deposit',
            'إيداع مبلغ',
            $description ?? "إيداع مبلغ: $" . number_format($amount, 2),
            ['amount' => $amount]
        );
    }

    /**
     * تسجيل سحب مبلغ
     */
    public static function logWithdrawal(float $amount, ?string $description = null): void
    {
        self::log(
            'withdrawal',
            'سحب مبلغ',
            $description ?? "سحب مبلغ: $" . number_format($amount, 2),
            ['amount' => $amount]
        );
    }

    /**
     * تسجيل تحويل نقاط
     */
    public static function logPointsConversion(int $points, float $amount, string $direction = 'to_balance'): void
    {
        $action = $direction === 'to_balance' ? 'تحويل نقاط إلى رصيد' : 'تحويل رصيد إلى نقاط';
        self::log(
            'points_conversion',
            $action,
            "تحويل {$points} نقطة إلى $" . number_format($amount, 2),
            [
                'points' => $points,
                'amount' => $amount,
                'direction' => $direction,
            ]
        );
    }

    /**
     * تسجيل كسب نقاط
     */
    public static function logPointsEarned(int $points, ?string $reason = null): void
    {
        self::log(
            'points_earned',
            'كسب نقاط',
            $reason ?? "كسب {$points} نقطة",
            ['points' => $points, 'reason' => $reason]
        );
    }

    /**
     * تسجيل إتمام طلب
     */
    public static function logOrderPlaced($order): void
    {
        self::log(
            'order_placed',
            'إتمام طلب',
            "تم إتمام الطلب رقم #{$order->id}",
            [
                'order_id' => $order->id,
                'total' => $order->total,
                'payment_method' => $order->payment_method,
            ]
        );
    }

    /**
     * تسجيل تغيير العملة
     */
    public static function logCurrencyChange(string $oldCurrency, string $newCurrency): void
    {
        self::log(
            'currency_changed',
            'تغيير العملة',
            "تم تغيير العملة من {$oldCurrency} إلى {$newCurrency}",
            [
                'old_currency' => $oldCurrency,
                'new_currency' => $newCurrency,
            ]
        );
    }

    /**
     * تسجيل إضافة منتج إلى السلة
     */
    public static function logAddToCart($product, int $quantity): void
    {
        self::log(
            'cart_add',
            'إضافة إلى السلة',
            "تم إضافة {$quantity} من المنتج: {$product->translated_name} إلى السلة",
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'quantity' => $quantity,
            ]
        );
    }

    /**
     * تسجيل حذف منتج من السلة
     */
    public static function logRemoveFromCart($product): void
    {
        self::log(
            'cart_remove',
            'حذف من السلة',
            "تم حذف المنتج: {$product->translated_name} من السلة",
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
            ]
        );
    }

    /**
     * تسجيل تحديث كمية منتج في السلة
     */
    public static function logUpdateCart($product, int $quantity): void
    {
        self::log(
            'cart_update',
            'تحديث السلة',
            "تم تحديث كمية المنتج: {$product->translated_name} إلى {$quantity}",
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
            ]
        );
    }

    /**
     * تسجيل تفريغ السلة
     */
    public static function logClearCart(): void
    {
        self::log(
            'cart_clear',
            'تفريغ السلة',
            'تم تفريغ السلة بالكامل'
        );
    }
}
