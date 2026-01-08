<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * أنواع النشاطات المتاحة
     */
    public static function getActivityTypes(): array
    {
        return [
            'login' => 'تسجيل دخول',
            'logout' => 'تسجيل خروج',
            'product_view' => 'عرض منتج',
            'cart_add' => 'إضافة إلى السلة',
            'cart_remove' => 'حذف من السلة',
            'cart_update' => 'تحديث السلة',
            'cart_clear' => 'تفريغ السلة',
            'deposit' => 'إيداع مبلغ',
            'withdrawal' => 'سحب مبلغ',
            'points_conversion' => 'تحويل نقاط',
            'points_earned' => 'كسب نقاط',
            'order_placed' => 'إتمام طلب',
            'order_cancelled' => 'إلغاء طلب',
            'profile_updated' => 'تحديث الملف الشخصي',
            'address_added' => 'إضافة عنوان',
            'address_updated' => 'تحديث عنوان',
            'address_deleted' => 'حذف عنوان',
            'currency_changed' => 'تغيير العملة',
            'review_submitted' => 'إرسال تقييم',
        ];
    }
}
