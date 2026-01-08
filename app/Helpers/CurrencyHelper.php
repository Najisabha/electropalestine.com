<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyHelper
{
    /**
     * أسعار الصرف الافتراضية (في حالة فشل API)
     */
    private static array $defaultRates = [
        'USD_to_ILS' => 3.65,
        'USD_to_JOD' => 0.71,
        'ILS_to_JOD' => 0.1945, // 0.71 / 3.65
    ];

    /**
     * الحصول على أسعار الصرف
     */
    public static function getExchangeRates(): array
    {
        // محاولة الحصول من الـ cache أولاً (لمدة ساعة)
        return Cache::remember('exchange_rates', 3600, function () {
            try {
                $response = @file_get_contents('https://api.exchangerate-api.com/v4/latest/USD');
                if ($response) {
                    $data = json_decode($response, true);
                    if (isset($data['rates'])) {
                        $usdToIls = $data['rates']['ILS'] ?? self::$defaultRates['USD_to_ILS'];
                        $usdToJod = $data['rates']['JOD'] ?? self::$defaultRates['USD_to_JOD'];
                        $ilsToJod = $usdToJod / $usdToIls;

                        return [
                            'USD_to_ILS' => round($usdToIls, 4),
                            'USD_to_JOD' => round($usdToJod, 4),
                            'ILS_to_JOD' => round($ilsToJod, 4),
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch exchange rates from API: ' . $e->getMessage());
            }

            // استخدام القيم الافتراضية
            return self::$defaultRates;
        });
    }

    /**
     * تحويل السعر من USD إلى العملة المطلوبة
     */
    public static function convertPrice(float $priceInUSD, string $targetCurrency): float
    {
        if ($targetCurrency === 'USD') {
            return $priceInUSD;
        }

        $rates = self::getExchangeRates();

        if ($targetCurrency === 'ILS') {
            return $priceInUSD * $rates['USD_to_ILS'];
        } elseif ($targetCurrency === 'JOD') {
            return $priceInUSD * $rates['USD_to_JOD'];
        }

        return $priceInUSD;
    }

    /**
     * الحصول على رمز العملة
     */
    public static function getCurrencySymbol(string $currency): string
    {
        return match ($currency) {
            'USD' => '$',
            'ILS' => '₪',
            'JOD' => 'د.أ',
            default => '$',
        };
    }

    /**
     * تنسيق السعر مع رمز العملة
     */
    public static function formatPrice(float $price, string $currency): string
    {
        $symbol = self::getCurrencySymbol($currency);
        return $symbol . number_format($price, 2);
    }

    /**
     * تحويل وتنسيق السعر
     */
    public static function convertAndFormat(float $priceInUSD, string $targetCurrency): string
    {
        $convertedPrice = self::convertPrice($priceInUSD, $targetCurrency);
        return self::formatPrice($convertedPrice, $targetCurrency);
    }

    /**
     * الحصول على العملة المفضلة للمستخدم
     */
    public static function getUserPreferredCurrency(): string
    {
        if (auth()->check()) {
            return auth()->user()->preferred_currency ?? 'USD';
        }

        // للزوار، يمكن استخدام session أو cookie
        return session('preferred_currency', 'USD');
    }
}
