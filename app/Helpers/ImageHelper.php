<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageHelper
{
    /**
     * يتحقق من وجود الرابط الرمزي ويُنشئه إذا لزم الأمر
     * 
     * @return bool
     */
    private static function ensureStorageLink(): bool
    {
        try {
            $linkPath = public_path('storage');
            $targetPath = storage_path('app/public');
            
            // التأكد من وجود المجلد الهدف
            if (!file_exists($targetPath)) {
                @mkdir($targetPath, 0755, true);
            }
            
            // إذا كان الرابط موجوداً بالفعل ويعمل، نرجع true
            if (file_exists($linkPath) || is_link($linkPath)) {
                // التحقق من أن الرابط يعمل بشكل صحيح
                $testFile = $targetPath . '/.test';
                @file_put_contents($testFile, 'test');
                if (file_exists($linkPath . '/.test')) {
                    @unlink($testFile);
                    @unlink($linkPath . '/.test');
                    return true;
                }
                @unlink($testFile);
            }
            
            // محاولة إنشاء الرابط الرمزي
            // في Windows، قد نحتاج لاستخدام junction بدلاً من symlink
            if (PHP_OS_FAMILY === 'Windows') {
                // في Windows، نستخدم exec لإنشاء junction (يحتاج صلاحيات إدارية)
                // إذا فشل، نترك الأمر للمستخدم لإنشائه يدوياً
                if (!file_exists($linkPath)) {
                    $command = sprintf('mklink /J "%s" "%s"', $linkPath, $targetPath);
                    @exec($command, $output, $returnCode);
                    if ($returnCode === 0 || is_dir($linkPath)) {
                        Log::info('تم إنشاء الرابط الرمزي بنجاح', ['linkPath' => $linkPath]);
                        return true;
                    } else {
                        Log::warning('فشل إنشاء الرابط الرمزي في Windows - قد تحتاج صلاحيات إدارية', [
                            'linkPath' => $linkPath,
                            'targetPath' => $targetPath
                        ]);
                    }
                }
            } else {
                // في Linux/Unix، نستخدم symlink
                if (!file_exists($linkPath)) {
                    if (@symlink($targetPath, $linkPath)) {
                        Log::info('تم إنشاء الرابط الرمزي بنجاح', ['linkPath' => $linkPath]);
                        return true;
                    } else {
                        Log::warning('فشل إنشاء الرابط الرمزي', [
                            'linkPath' => $linkPath,
                            'targetPath' => $targetPath
                        ]);
                    }
                }
            }
            
            // إذا وصلنا هنا، الرابط موجود أو فشلنا في إنشائه
            // نرجع true لأن الحفظ قد يعمل حتى بدون الرابط (اعتماداً على الإعدادات)
            return true;
        } catch (\Exception $e) {
            Log::warning('فشل التحقق من الرابط الرمزي', [
                'error' => $e->getMessage()
            ]);
            // لا نرجع false هنا لأن الرابط قد يكون موجوداً بالفعل
            return true;
        }
    }

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

            // التأكد من وجود الرابط الرمزي
            if ($disk === 'public') {
                self::ensureStorageLink();
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

            // محاولة التحقق من صلاحيات الكتابة (مع معالجة الأخطاء)
            try {
                $fullPath = $storage->path($directory);
                if (file_exists($fullPath) && !is_writable($fullPath)) {
                    // محاولة تغيير الصلاحيات
                    @chmod($fullPath, 0755);
                    if (!is_writable($fullPath)) {
                        Log::warning('المجلد قد لا يكون قابل للكتابة، سيتم المحاولة على أي حال', [
                            'directory' => $directory,
                            'fullPath' => $fullPath,
                            'permissions' => file_exists($fullPath) ? substr(sprintf('%o', fileperms($fullPath)), -4) : 'N/A'
                        ]);
                        // لا نرجع null هنا، نترك المحاولة الفعلية للحفظ
                    }
                }
            } catch (\Exception $e) {
                // إذا فشل التحقق من المسار، نتابع المحاولة على أي حال
                Log::warning('فشل التحقق من صلاحيات المجلد، سيتم المحاولة على أي حال', [
                    'directory' => $directory,
                    'error' => $e->getMessage()
                ]);
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

            // حفظ الملف باستخدام طريقة أكثر موثوقية
            $storedPath = null;
            try {
                // الحصول على المحتوى الفعلي للملف
                $fileContents = file_get_contents($file->getRealPath());
                
                if ($fileContents === false) {
                    Log::error('فشل قراءة محتوى الملف', [
                        'directory' => $directory,
                        'fileName' => $fileName,
                        'disk' => $disk,
                        'realPath' => $file->getRealPath()
                    ]);
                    return null;
                }
                
                // حفظ الملف باستخدام Storage::put مباشرة
                $storedPath = $filePath;
                $saved = $storage->put($storedPath, $fileContents);
                
                if (!$saved) {
                    Log::error('فشل حفظ الملف - Storage::put أرجعت false', [
                        'directory' => $directory,
                        'fileName' => $fileName,
                        'filePath' => $storedPath,
                        'disk' => $disk
                    ]);
                    return null;
                }
                
                // التحقق الفعلي من وجود الملف وحجمه
                if (!$storage->exists($storedPath)) {
                    $fullStoredPath = $storage->path($storedPath);
                    Log::error('فشل حفظ الملف - الملف غير موجود بعد Storage::put', [
                        'directory' => $directory,
                        'fileName' => $fileName,
                        'storedPath' => $storedPath,
                        'fullStoredPath' => $fullStoredPath,
                        'disk' => $disk,
                        'directoryExists' => $storage->exists($directory),
                        'directoryPath' => $storage->path($directory),
                        'directoryWritable' => is_writable($storage->path($directory))
                    ]);
                    return null;
                }
                
                // التحقق من أن حجم الملف المحفوظ يتطابق مع الملف الأصلي
                $savedFileSize = $storage->size($storedPath);
                $originalFileSize = $file->getSize();
                
                if ($savedFileSize !== $originalFileSize) {
                    Log::warning('حجم الملف المحفوظ لا يتطابق مع الملف الأصلي', [
                        'directory' => $directory,
                        'fileName' => $fileName,
                        'storedPath' => $storedPath,
                        'originalSize' => $originalFileSize,
                        'savedSize' => $savedFileSize,
                        'disk' => $disk
                    ]);
                    // لا نرجع null هنا لأن الملف موجود لكن حجمه مختلف
                }
                
                Log::info('تم حفظ الصورة بنجاح - التحقق النهائي', [
                    'directory' => $directory,
                    'fileName' => $fileName,
                    'storedPath' => $storedPath,
                    'fileSize' => $savedFileSize,
                    'fullPath' => $storage->path($storedPath)
                ]);
                
            } catch (\Exception $e) {
                Log::error('استثناء أثناء حفظ الملف', [
                    'directory' => $directory,
                    'fileName' => $fileName,
                    'disk' => $disk,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return null;
            }

            if ($storedPath === null) {
                Log::error('فشل حفظ الملف - storedPath is null', [
                    'directory' => $directory,
                    'fileName' => $fileName,
                    'disk' => $disk
                ]);
                return null;
            }

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
