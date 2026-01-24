<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PhoneVerification extends Model
{
    protected $fillable = [
        'phone',
        'code',
        'expires_at',
        'verified',
        'attempts',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified' => 'boolean',
    ];

    /**
     * توليد كود عشوائي من 6 أرقام
     */
    public static function generateCode(): string
    {
        return str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * إنشاء كود تحقق جديد
     */
    public static function createVerificationCode(string $phone): self
    {
        // حذف أي أكواد قديمة غير مستخدمة لنفس الرقم
        self::where('phone', $phone)
            ->where('verified', false)
            ->delete();

        return self::create([
            'phone' => $phone,
            'code' => self::generateCode(),
            'expires_at' => now()->addMinutes(10), // ينتهي بعد 10 دقائق
            'verified' => false,
            'attempts' => 0,
        ]);
    }

    /**
     * التحقق من صحة الكود
     */
    public function verify(string $inputCode): bool
    {
        // زيادة عدد المحاولات
        $this->increment('attempts');

        // التحقق من انتهاء صلاحية الكود
        if ($this->expires_at->isPast()) {
            return false;
        }

        // التحقق من تطابق الكود
        if ($this->code === $inputCode) {
            $this->update(['verified' => true]);
            return true;
        }

        return false;
    }

    /**
     * التحقق من وجود كود صالح
     */
    public static function hasValidCode(string $phone): bool
    {
        return self::where('phone', $phone)
            ->where('verified', false)
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * جلب آخر كود صالح
     */
    public static function getLatestCode(string $phone): ?self
    {
        return self::where('phone', $phone)
            ->where('verified', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }
}
