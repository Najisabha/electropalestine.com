<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompressResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // التحقق من أن الاستجابة يمكن ضغطها
        if (!$this->shouldCompress($request, $response)) {
            return $response;
        }

        // التحقق من دعم compression من المتصفح
        $acceptEncoding = $request->header('Accept-Encoding');
        
        if (empty($acceptEncoding)) {
            return $response;
        }

        $content = $response->getContent();
        
        // Gzip compression
        if (str_contains($acceptEncoding, 'gzip') && function_exists('gzencode')) {
            $compressed = gzencode($content, 6); // مستوى الضغط 6 (متوازن)
            
            if ($compressed !== false) {
                $response->setContent($compressed);
                $response->headers->set('Content-Encoding', 'gzip');
                $response->headers->set('Vary', 'Accept-Encoding');
            }
        }
        // Deflate compression (fallback)
        elseif (str_contains($acceptEncoding, 'deflate') && function_exists('gzdeflate')) {
            $compressed = gzdeflate($content, 6);
            
            if ($compressed !== false) {
                $response->setContent($compressed);
                $response->headers->set('Content-Encoding', 'deflate');
                $response->headers->set('Vary', 'Accept-Encoding');
            }
        }

        return $response;
    }

    /**
     * تحديد ما إذا كان يجب ضغط الاستجابة
     */
    protected function shouldCompress(Request $request, Response $response): bool
    {
        // لا نضغط إذا كانت الاستجابة صغيرة جداً
        if (strlen($response->getContent()) < 1024) {
            return false;
        }

        // لا نضغط إذا كانت الاستجابة مضغوطة بالفعل
        if ($response->headers->has('Content-Encoding')) {
            return false;
        }

        // تحديد أنواع المحتوى التي يجب ضغطها
        $contentType = $response->headers->get('Content-Type', '');
        $compressibleTypes = [
            'text/html',
            'text/plain',
            'text/css',
            'text/javascript',
            'application/javascript',
            'application/json',
            'application/xml',
            'text/xml',
        ];

        foreach ($compressibleTypes as $type) {
            if (str_contains($contentType, $type)) {
                return true;
            }
        }

        return false;
    }
}
