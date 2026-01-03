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
                // الحصول على المسار الكامل للمجلد
                $directoryPath = $storage->path($directory);
                
                // التأكد من وجود المجلد
                if (!file_exists($directoryPath)) {
                    @mkdir($directoryPath, 0755, true);
                }
                
                // التأكد من أن المجلد قابل للكتابة
                if (!is_writable($directoryPath)) {
                    @chmod($directoryPath, 0755);
                    if (!is_writable($directoryPath)) {
                        Log::error('المجلد غير قابل للكتابة', [
                            'directory' => $directory,
                            'directoryPath' => $directoryPath,
                            'permissions' => file_exists($directoryPath) ? substr(sprintf('%o', fileperms($directoryPath)), -4) : 'N/A'
                        ]);
                        return null;
                    }
                }
                
                // المسار الكامل للملف الهدف
                $fullFilePath = $directoryPath . '/' . $fileName;
                $storedPath = $filePath;
                
                // طريقة 1: استخدام Storage::putFileAs (الأكثر موثوقية مع Laravel)
                try {
                    $storedPath = $storage->putFileAs($directory, $file, $fileName);
                    
                    if (!$storedPath) {
                        throw new \Exception('Storage::putFileAs أرجعت null');
                    }
                    
                    // التحقق من وجود الملف
                    if (!$storage->exists($storedPath)) {
                        throw new \Exception('الملف غير موجود بعد Storage::putFileAs');
                    }
                    
                    Log::info('تم حفظ الصورة باستخدام Storage::putFileAs', [
                        'directory' => $directory,
                        'fileName' => $fileName,
                        'storedPath' => $storedPath,
                        'fullPath' => $storage->path($storedPath)
                    ]);
                    
                } catch (\Exception $storageException) {
                    // إذا فشل Storage::putFileAs، نجرب copy مباشرة
                    Log::warning('فشل Storage::putFileAs، نجرب copy مباشرة', [
                        'error' => $storageException->getMessage()
                    ]);
                    
                    $tempPath = $file->getRealPath();
                    if ($tempPath && file_exists($tempPath)) {
                        $copied = @copy($tempPath, $fullFilePath);
                        if ($copied && file_exists($fullFilePath)) {
                            // نجح الحفظ باستخدام copy
                            Log::info('تم حفظ الصورة باستخدام copy', [
                                'directory' => $directory,
                                'fileName' => $fileName,
                                'storedPath' => $storedPath,
                                'fullPath' => $fullFilePath
                            ]);
                        } else {
                            Log::error('فشل حفظ الملف - جميع الطرق فشلت', [
                                'directory' => $directory,
                                'fileName' => $fileName,
                                'disk' => $disk,
                                'directoryPath' => $directoryPath,
                                'fullFilePath' => $fullFilePath,
                                'tempPath' => $tempPath,
                                'copy_result' => $copied ?? false,
                                'storage_exception' => $storageException->getMessage()
                            ]);
                            return null;
                        }
                    } else {
                        Log::error('فشل حفظ الملف - الملف المؤقت غير موجود', [
                            'directory' => $directory,
                            'fileName' => $fileName,
                            'disk' => $disk,
                            'tempPath' => $tempPath,
                            'storage_exception' => $storageException->getMessage()
                        ]);
                        return null;
                    }
                }
                
                // التحقق النهائي من وجود الملف وحجمه
                if (!$storage->exists($storedPath)) {
                    $fullStoredPath = $storage->path($storedPath);
                    if (!file_exists($fullStoredPath)) {
                        Log::error('فشل حفظ الملف - الملف غير موجود بعد الحفظ', [
                            'directory' => $directory,
                            'fileName' => $fileName,
                            'storedPath' => $storedPath,
                            'fullStoredPath' => $fullStoredPath,
                            'disk' => $disk
                        ]);
                        return null;
                    }
                }
                
                // التحقق من أن حجم الملف المحفوظ يتطابق مع الملف الأصلي
                try {
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
                    }
                    
                    Log::info('تم حفظ الصورة بنجاح - التحقق النهائي', [
                        'directory' => $directory,
                        'fileName' => $fileName,
                        'storedPath' => $storedPath,
                        'fileSize' => $savedFileSize,
                        'fullPath' => $storage->path($storedPath)
                    ]);
                } catch (\Exception $sizeException) {
                    // إذا فشل التحقق من الحجم، نكمل على أي حال إذا كان الملف موجود
                    Log::warning('فشل التحقق من حجم الملف', [
                        'error' => $sizeException->getMessage(),
                        'storedPath' => $storedPath
                    ]);
                }
                
                // نسخ الملف إلى public/storage/ لضمان الوصول المباشر (بدون symbolic link)
                if ($disk === 'public' && $storedPath) {
                    try {
                        $publicStoragePath = public_path('storage');
                        $targetPublicPath = $publicStoragePath . '/' . $storedPath;
                        $sourcePath = $storage->path($storedPath);
                        
                        // التأكد من وجود المجلد في public/storage/
                        $targetDir = dirname($targetPublicPath);
                        if (!file_exists($targetDir)) {
                            @mkdir($targetDir, 0755, true);
                            Log::info('تم إنشاء مجلد جديد في public/storage', ['directory' => dirname($storedPath)]);
                        }
                        
                        // نسخ الملف إذا لم يكن موجوداً أو إذا كان قد تم تحديثه
                        if (!file_exists($targetPublicPath) || filemtime($sourcePath) > filemtime($targetPublicPath)) {
                            $copied = @copy($sourcePath, $targetPublicPath);
                            if ($copied) {
                                // نسخ صلاحيات الملف أيضاً
                                @chmod($targetPublicPath, 0644);
                                Log::info('تم نسخ الملف إلى public/storage', [
                                    'source' => $sourcePath,
                                    'target' => $targetPublicPath,
                                    'path' => $storedPath
                                ]);
                            } else {
                                Log::warning('فشل نسخ الملف إلى public/storage', [
                                    'source' => $sourcePath,
                                    'target' => $targetPublicPath
                                ]);
                            }
                        }
                    } catch (\Exception $copyException) {
                        // لا نفشل العملية إذا فشل النسخ، فقط نسجل التحذير
                        Log::warning('خطأ أثناء نسخ الملف إلى public/storage', [
                            'error' => $copyException->getMessage(),
                            'path' => $storedPath
                        ]);
                    }
                }
                
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

    /**
     * ينسخ جميع الملفات من storage/app/public إلى public/storage
     * مفيد بعد رفع المشروع إلى الاستضافة
     * 
     * @param string $directory المجلد الفرعي (مثل 'categories', 'products') - null لنسخ الكل
     * @return array
     */
    public static function syncToPublicStorage(?string $directory = null): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'errors' => []
        ];
        
        try {
            $storage = Storage::disk('public');
            $publicStoragePath = public_path('storage');
            
            // التأكد من وجود المجلد الهدف
            if (!file_exists($publicStoragePath)) {
                @mkdir($publicStoragePath, 0755, true);
            }
            
            // تحديد المجلدات المطلوبة
            $directoriesToSync = $directory ? [$directory] : $storage->directories();
            
            foreach ($directoriesToSync as $dir) {
                // الحصول على جميع الملفات في المجلد
                $files = $storage->files($dir);
                
                foreach ($files as $file) {
                    try {
                        $sourcePath = $storage->path($file);
                        $targetPath = $publicStoragePath . '/' . $file;
                        $targetDir = dirname($targetPath);
                        
                        // التأكد من وجود المجلد الهدف
                        if (!file_exists($targetDir)) {
                            @mkdir($targetDir, 0755, true);
                        }
                        
                        // نسخ الملف إذا لم يكن موجوداً أو إذا كان قد تم تحديثه
                        if (!file_exists($targetPath) || filemtime($sourcePath) > filemtime($targetPath)) {
                            $copied = @copy($sourcePath, $targetPath);
                            if ($copied) {
                                @chmod($targetPath, 0644);
                                $results['success']++;
                            } else {
                                $results['failed']++;
                                $results['errors'][] = "فشل نسخ: {$file}";
                            }
                        } else {
                            $results['skipped']++;
                        }
                    } catch (\Exception $e) {
                        $results['failed']++;
                        $results['errors'][] = "خطأ في {$file}: " . $e->getMessage();
                        Log::error('خطأ في نسخ ملف إلى public/storage', [
                            'file' => $file,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
            
            Log::info('تمت مزامنة الملفات إلى public/storage', $results);
            
        } catch (\Exception $e) {
            Log::error('خطأ في مزامنة الملفات إلى public/storage', [
                'error' => $e->getMessage(),
                'directory' => $directory
            ]);
            $results['errors'][] = 'خطأ عام: ' . $e->getMessage();
        }
        
        return $results;
    }

    /**
     * يحذف الملف من storage/app/public و public/storage
     * 
     * @param string|null $path المسار النسبي للملف
     * @param string $disk
     * @return bool
     */
    public static function delete(?string $path, string $disk = 'public'): bool
    {
        if (empty($path)) {
            return false;
        }
        
        $deleted = false;
        
        // حذف من storage/app/public
        try {
            $storage = Storage::disk($disk);
            if ($storage->exists($path)) {
                $deleted = $storage->delete($path);
            }
        } catch (\Exception $e) {
            Log::warning('فشل حذف الملف من storage', [
                'path' => $path,
                'disk' => $disk,
                'error' => $e->getMessage()
            ]);
        }
        
        // حذف من public/storage أيضاً
        if ($disk === 'public') {
            try {
                $publicPath = public_path('storage/' . $path);
                if (file_exists($publicPath)) {
                    @unlink($publicPath);
                    Log::info('تم حذف الملف من public/storage', ['path' => $path]);
                }
            } catch (\Exception $e) {
                Log::warning('فشل حذف الملف من public/storage', [
                    'path' => $path,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $deleted;
    }

    /**
     * يحصل على URL للصورة مع دعم symbolic link وroute كبديل
     * 
     * @param string|null $path المسار النسبي للصورة داخل storage/app/public
     * @return string|null
     */
    public static function url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // تنظيف المسار من الشرطة المائلة في البداية
        $path = ltrim($path, '/');

        // التحقق من وجود symbolic link
        $publicStoragePath = public_path('storage');
        $targetPath = storage_path('app/public');
        $testFilePath = $path; // ملف للاختبار (مثل categories/category1.png)
        
        // التحقق من وجود symbolic link ويعمل بشكل صحيح
        $symbolicLinkExists = false;
        if (is_link($publicStoragePath) || file_exists($publicStoragePath)) {
            try {
                if (is_link($publicStoragePath)) {
                    // في Unix/Linux
                    $linkTarget = readlink($publicStoragePath);
                    if ($linkTarget === $targetPath || realpath($linkTarget) === realpath($targetPath)) {
                        $symbolicLinkExists = true;
                    }
                } elseif (PHP_OS_FAMILY === 'Windows' && is_dir($publicStoragePath)) {
                    // في Windows، junction points
                    // التحقق من وجود ملف اختبار
                    $testFile = $publicStoragePath . '/' . $testFilePath;
                    if (file_exists($testFile)) {
                        $symbolicLinkExists = true;
                    }
                }
            } catch (\Exception $e) {
                Log::debug('خطأ في التحقق من symbolic link', ['error' => $e->getMessage()]);
            }
        }
        
        // إذا كان symbolic link موجود ويعمل، استخدم asset
        if ($symbolicLinkExists) {
            return asset('storage/' . $path);
        }
        
        // إذا لم يكن symbolic link موجود، استخدم route
        // route يعمل دائماً لأن StorageController يتحقق من وجود الملف
        return route('storage.show', ['path' => $path]);
    }
}
