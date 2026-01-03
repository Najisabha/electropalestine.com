<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageController extends Controller
{
    /**
     * عرض الملفات من storage/app/public
     * هذا الحل البديل لـ symlink في الاستضافة المشتركة
     * 
     * @param string $path المسار النسبي للملف داخل storage/app/public
     * @return BinaryFileResponse|Response
     */
    public function show(string $path): BinaryFileResponse|\Illuminate\Http\Response
    {
        try {
            // تنظيف المسار من أي محاولات للوصول إلى ملفات خارج storage/app/public
            $path = ltrim($path, '/');
            $path = str_replace('..', '', $path); // منع directory traversal attacks
            
            // التأكد من أن الملف موجود
            if (!Storage::disk('public')->exists($path)) {
                Log::warning('محاولة الوصول إلى ملف غير موجود', ['path' => $path]);
                abort(404, 'File not found');
            }

            // الحصول على مسار الملف الكامل
            $filePath = Storage::disk('public')->path($path);
            
            // التأكد من أن الملف موجود فعلياً
            if (!file_exists($filePath)) {
                Log::error('الملف غير موجود في المسار الفعلي', [
                    'path' => $path,
                    'filePath' => $filePath
                ]);
                abort(404, 'File not found');
            }

            // التأكد من أن الملف قابل للقراءة
            if (!is_readable($filePath)) {
                Log::error('الملف غير قابل للقراءة', [
                    'path' => $path,
                    'filePath' => $filePath
                ]);
                abort(403, 'File not accessible');
            }

            // تحديد نوع المحتوى
            $mimeType = Storage::disk('public')->mimeType($path);
            if (!$mimeType) {
                // إذا لم يتم تحديد نوع المحتوى، حاول تحديده من الامتداد
                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                $mimeTypes = [
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    'svg' => 'image/svg+xml',
                    'ico' => 'image/x-icon',
                ];
                $mimeType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
            }
            
            // إرجاع الملف كاستجابة
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=31536000', // كاش لمدة سنة
                'Content-Disposition' => 'inline', // عرض الملف بدلاً من تحميله
            ]);
            
        } catch (\Exception $e) {
            Log::error('خطأ في عرض الملف من storage', [
                'path' => $path,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Error serving file');
        }
    }
}
