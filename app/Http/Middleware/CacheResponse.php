<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $minutes = 60): Response
    {
        // لا نكاش POST, PUT, DELETE, PATCH requests
        if (!in_array($request->method(), ['GET', 'HEAD'])) {
            return $next($request);
        }

        // لا نكاش للـ authenticated users في صفحات معينة
        if (auth()->check() && $this->shouldSkipCacheForAuthUser($request)) {
            return $next($request);
        }

        // إنشاء cache key بناءً على URL واللغة
        $cacheKey = 'response_cache:' . md5($request->fullUrl() . app()->getLocale());

        // محاولة جلب من cache
        $cachedResponse = Cache::get($cacheKey);

        if ($cachedResponse !== null) {
            return response($cachedResponse['content'])
                ->header('Content-Type', $cachedResponse['content_type'])
                ->header('X-Cache-Status', 'HIT');
        }

        // تنفيذ الطلب
        $response = $next($request);

        // حفظ النتيجة في cache فقط إذا كانت 200 OK
        if ($response->getStatusCode() === 200) {
            $content = $response->getContent();
            $contentType = $response->headers->get('Content-Type');

            Cache::put($cacheKey, [
                'content' => $content,
                'content_type' => $contentType,
            ], now()->addMinutes($minutes));

            $response->header('X-Cache-Status', 'MISS');
        }

        return $response;
    }

    /**
     * تحديد ما إذا كان يجب تخطي cache للمستخدمين المسجلين
     */
    protected function shouldSkipCacheForAuthUser(Request $request): bool
    {
        $skipPaths = [
            'account-settings',
            'my-orders',
            'points',
            'favorites',
            'cart',
            'checkout',
            'admin',
        ];

        foreach ($skipPaths as $path) {
            if ($request->is($path) || $request->is($path . '/*')) {
                return true;
            }
        }

        return false;
    }
}
