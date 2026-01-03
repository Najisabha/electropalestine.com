<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageHelper
{
    /**
     * يحفظ الصورة مع اسم متسلسل بناءً على اسم المجلد
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param string $disk
     * @return string|null
     */
    public static function storeWithSequentialName($file, string $directory = 'images', string $disk = 'public'): ?string
    {
        try {
            if (!$file || !$file->isValid()) {
                Log::warning('محاولة حفظ ملف غير صالح', [
                    'directory' => $directory,
                    'disk' => $disk,
                    'error' => $file ? $file->getError() : 'File is null'
                ]);
                return null;
            }

            $storage = Storage::disk($disk);
            
            // التأكد من وجود المجلد وإنشاؤه إذا لم يكن موجوداً
            if (!$storage->exists($directory)) {
                try {
                    $storage->makeDirectory($directory, 0755, true);
                    Log::info('تم إنشاء مجلد جديد', ['directory' => $directory, 'disk' => $disk]);
                } catch (\Exception $e) {
                    Log::error('فشل إنشاء المجلد', [
                        'directory' => $directory,
                        'disk' => $disk,
                        'error' => $e->getMessage()
                    ]);
                    return null;
                }
            }

            // التحقق من صلاحيات الكتابة على المجلد
            $fullPath = $storage->path($directory);
            if (!is_writable($fullPath)) {
                Log::error('المجلد غير قابل للكتابة', [
                    'directory' => $directory,
                    'fullPath' => $fullPath,
                    'permissions' => substr(sprintf('%o', fileperms($fullPath)), -4)
                ]);
                return null;
            }

            // الحصول على امتداد الملف
            $extension = $file->getClientOriginalExtension() ?: $file->guessExtension();
            if (empty($extension)) {
                Log::warning('لم يتم تحديد امتداد الملف', [
                    'originalName' => $file->getClientOriginalName(),
                    'mimeType' => $file->getMimeType()
                ]);
                $extension = 'jpg'; // افتراضي
            }
            
            // الحصول على البادئة بناءً على اسم المجلد
            $prefix = self::getPrefixForDirectory($directory);
            
            // الحصول على الرقم التالي
            $nextNumber = self::getNextImageNumber($directory, $prefix, $disk);
            
            // إنشاء اسم الملف
            $fileName = "{$prefix}{$nextNumber}.{$extension}";
            $filePath = $directory . '/' . $fileName;

            // التأكد من عدم وجود ملف بنفس الاسم (في حالة التضارب)
            $counter = 0;
            while ($storage->exists($filePath)) {
                $counter++;
                $fileName = "{$prefix}{$nextNumber}_{$counter}.{$extension}";
                $filePath = $directory . '/' . $fileName;
            }

            // حفظ الملف
            $storedPath = $file->storeAs($directory, $fileName, $disk);
            
            // التحقق من نجاح الحفظ
            if (!$storedPath || !$storage->exists($storedPath)) {
                Log::error('فشل حفظ الملف', [
                    'directory' => $directory,
                    'fileName' => $fileName,
                    'storedPath' => $storedPath,
                    'disk' => $disk
                ]);
                return null;
            }

            Log::info('تم حفظ الصورة بنجاح', [
                'directory' => $directory,
                'fileName' => $fileName,
                'storedPath' => $storedPath
            ]);
            
            return $storedPath;
            
        } catch (\Exception $e) {
            Log::error('خطأ في حفظ الصورة', [
                'directory' => $directory,
                'disk' => $disk,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * يحصل على البادئة المناسبة للمجلد
     * 
     * @param string $directory
     * @return string
     */
    private static function getPrefixForDirectory(string $directory): string
    {
        // إزالة المسارات الفرعية والحصول على اسم المجلد الأساسي
        $dirName = basename($directory);
        
        // تحديد البادئة بناءً على اسم المجلد
        $prefixes = [
            'ids' => 'id',
            'categories' => 'category',
            'types' => 'type',
            'companies' => 'company',
            'products' => 'product',
            'campaigns' => 'campaign',
        ];

        // إرجاع البادئة المطابقة أو استخدام اسم المجلد كبادئة
        return $prefixes[$dirName] ?? $dirName;
    }

    /**
     * يحصل على الرقم التالي للصورة بناءً على عدد الملفات في المجلد
     * 
     * @param string $directory
     * @param string $prefix
     * @param string $disk
     * @return int
     */
    private static function getNextImageNumber(string $directory, string $prefix, string $disk = 'public'): int
    {
        $storage = Storage::disk($disk);
        
        if (!$storage->exists($directory)) {
            return 1;
        }

        // الحصول على جميع الملفات في المجلد
        $files = $storage->files($directory);
        
        if (empty($files)) {
            return 1;
        }

        // استخراج الأرقام من أسماء الملفات التي تبدأ بالبادئة
        $numbers = [];
        foreach ($files as $file) {
            $fileName = basename($file);
            // البحث عن نمط {prefix}{number}.{extension} أو {prefix}{number}_{counter}.{extension}
            $pattern = '/^' . preg_quote($prefix, '/') . '(\d+)(?:_\d+)?\./';
            if (preg_match($pattern, $fileName, $matches)) {
                $numbers[] = (int) $matches[1];
            }
        }

        // إذا لم نجد أي أرقام، نبدأ من 1
        if (empty($numbers)) {
            return 1;
        }

        // إرجاع أكبر رقم + 1
        return max($numbers) + 1;
    }
}
