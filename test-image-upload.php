<?php
/**
 * ملف اختبار لفحص عملية حفظ الصور
 * 
 * كيفية الاستخدام:
 * 1. ارفع هذا الملف إلى المجلد الرئيسي للموقع
 * 2. افتحه في المتصفح: https://electropalestine.com/test-image-upload.php
 * 3. اقرأ النتائج
 * 4. احذف الملف بعد الانتهاء (للمزيد من الأمان)
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

echo "<h2>اختبار إعدادات حفظ الصور</h2>";
echo "<pre>";

// 1. فحص مسار التخزين
$storagePath = storage_path('app/public');
$publicStoragePath = public_path('storage');

echo "1. مسارات التخزين:\n";
echo "   storage/app/public: $storagePath\n";
echo "   public/storage: $publicStoragePath\n";
echo "   storage/app/public موجود: " . (file_exists($storagePath) ? 'نعم' : 'لا') . "\n";
echo "   public/storage موجود: " . (file_exists($publicStoragePath) ? 'نعم' : 'لا') . "\n";
echo "   public/storage هو symlink: " . (is_link($publicStoragePath) ? 'نعم → ' . readlink($publicStoragePath) : 'لا') . "\n";
echo "\n";

// 2. فحص الصلاحيات
echo "2. الصلاحيات:\n";
if (file_exists($storagePath)) {
    $perms = fileperms($storagePath);
    echo "   storage/app/public: " . substr(sprintf('%o', $perms), -4) . "\n";
    echo "   storage/app/public قابل للكتابة: " . (is_writable($storagePath) ? 'نعم' : 'لا') . "\n";
}

if (file_exists($publicStoragePath)) {
    $perms = file_exists($publicStoragePath) ? fileperms($publicStoragePath) : null;
    if ($perms) {
        echo "   public/storage: " . substr(sprintf('%o', $perms), -4) . "\n";
    }
}
echo "\n";

// 3. فحص إعدادات Disk
echo "3. إعدادات Disk:\n";
$disk = Storage::disk('public');
$root = $disk->getDriver()->getAdapter()->getPathPrefix();
echo "   Disk 'public' root: $root\n";
echo "   Disk root موجود: " . (file_exists($root) ? 'نعم' : 'لا') . "\n";
echo "\n";

// 4. فحص المجلدات المطلوبة
echo "4. المجلدات المطلوبة:\n";
$directories = ['categories', 'types', 'companies', 'products', 'campaigns', 'ids'];
foreach ($directories as $dir) {
    $fullPath = $root . $dir;
    $exists = file_exists($fullPath);
    $writable = $exists ? is_writable($fullPath) : false;
    echo "   $dir: " . ($exists ? 'موجود' : 'غير موجود') . " - " . ($writable ? 'قابل للكتابة' : 'غير قابل للكتابة') . "\n";
    if ($exists) {
        $perms = fileperms($fullPath);
        echo "      الصلاحيات: " . substr(sprintf('%o', $perms), -4) . "\n";
    }
}
echo "\n";

// 5. محاولة إنشاء ملف اختبار
echo "5. اختبار الكتابة:\n";
$testFile = $root . 'categories/.test_file_' . time() . '.txt';
$testContent = 'test content';
try {
    $written = file_put_contents($testFile, $testContent);
    if ($written !== false) {
        echo "   ✅ تم إنشاء ملف اختبار بنجاح: $testFile\n";
        echo "   حجم الملف: $written bytes\n";
        // حذف الملف الاختبار
        if (file_exists($testFile)) {
            unlink($testFile);
            echo "   ✅ تم حذف ملف الاختبار\n";
        }
    } else {
        echo "   ❌ فشل إنشاء ملف اختبار\n";
    }
} catch (Exception $e) {
    echo "   ❌ خطأ: " . $e->getMessage() . "\n";
}
echo "\n";

// 6. فحص الملفات الموجودة
echo "6. الملفات الموجودة في categories:\n";
$categoriesPath = $root . 'categories';
if (file_exists($categoriesPath)) {
    $files = scandir($categoriesPath);
    $files = array_filter($files, function($file) {
        return $file !== '.' && $file !== '..';
    });
    if (empty($files)) {
        echo "   لا توجد ملفات\n";
    } else {
        foreach ($files as $file) {
            $filePath = $categoriesPath . '/' . $file;
            $size = filesize($filePath);
            $modified = date('Y-m-d H:i:s', filemtime($filePath));
            echo "   - $file ($size bytes, آخر تعديل: $modified)\n";
        }
    }
} else {
    echo "   المجلد غير موجود\n";
}
echo "\n";

// 7. فحص إعدادات PHP
echo "7. إعدادات PHP للرفع:\n";
echo "   upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "   post_max_size: " . ini_get('post_max_size') . "\n";
echo "   memory_limit: " . ini_get('memory_limit') . "\n";
echo "   max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "\n";

echo "</pre>";
echo "<p><strong>ملاحظة:</strong> احذف هذا الملف بعد الانتهاء من الاختبار!</p>";