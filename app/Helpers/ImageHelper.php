<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

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
        if (!$file || !$file->isValid()) {
            return null;
        }

        $storage = Storage::disk($disk);
        
        // التأكد من وجود المجلد
        if (!$storage->exists($directory)) {
            $storage->makeDirectory($directory, 0755, true);
        }

        // الحصول على امتداد الملف
        $extension = $file->getClientOriginalExtension() ?: $file->guessExtension();
        
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
        
        return $storedPath;
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
