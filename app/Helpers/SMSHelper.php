<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SMSHelper
{
    /**
     * إرسال رسالة SMS
     * 
     * @param string $phone رقم الهاتف مع مقدمة الدولة (+970599123456)
     * @param string $message نص الرسالة
     * @return bool
     */
    public static function send(string $phone, string $message): bool
    {
        try {
            // في بيئة التطوير، فقط نسجل الرسالة
            if (config('app.env') !== 'production') {
                Log::info('SMS Message (Development Mode)', [
                    'phone' => $phone,
                    'message' => $message
                ]);
                return true;
            }

            // في بيئة الإنتاج، استخدم خدمة SMS حقيقية
            return self::sendViaTwilio($phone, $message);
            
        } catch (\Exception $e) {
            Log::error('فشل إرسال SMS', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * إرسال كود التحقق
     * 
     * @param string $phone رقم الهاتف
     * @param string $code كود التحقق
     * @return bool
     */
    public static function sendVerificationCode(string $phone, string $code): bool
    {
        $message = "كود التحقق الخاص بك في Electropalestine هو: {$code}\nالكود صالح لمدة 10 دقائق.";
        return self::send($phone, $message);
    }

    /**
     * إرسال عبر Twilio (مثال)
     * قم بتثبيت: composer require twilio/sdk
     */
    private static function sendViaTwilio(string $phone, string $message): bool
    {
        // تحتاج إضافة في .env:
        // TWILIO_SID=your_account_sid
        // TWILIO_TOKEN=your_auth_token
        // TWILIO_FROM=+1234567890

        try {
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_TOKEN');
            $from = env('TWILIO_FROM');

            if (!$sid || !$token || !$from) {
                Log::warning('Twilio credentials not configured');
                return false;
            }

            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => $from,
                    'To' => $phone,
                    'Body' => $message,
                ]);

            return $response->successful();
            
        } catch (\Exception $e) {
            Log::error('Twilio SMS error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * إرسال عبر خدمة محلية (مثال - قم بتعديلها حسب الخدمة التي تستخدمها)
     */
    private static function sendViaLocalProvider(string $phone, string $message): bool
    {
        try {
            // مثال: استبدل بـ API الخاص بمزود الخدمة المحلي
            $response = Http::post('https://sms-provider.ps/api/send', [
                'api_key' => env('SMS_API_KEY'),
                'phone' => $phone,
                'message' => $message,
            ]);

            return $response->successful();
            
        } catch (\Exception $e) {
            Log::error('Local SMS provider error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
