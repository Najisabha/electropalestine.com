<?php
/**
 * ملف تشخيص لـ Hostinger
 * 
 * ارفع هذا الملف إلى المجلد الرئيسي (بجانب artisan)
 * ثم افتحه في المتصفح: https://electropalestine.com/diagnose-hostinger.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;

echo "<h2>🔍 تشخيص مشكلة حفظ الصور على Hostinger</h2>";
echo "<style>
    body { font-family: Arial; padding: 20px; background: #f5f5f5; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    pre { background: #fff; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
    .section { margin: 20px 0; padding: 15px; background: white; border-radius: 5px; }
</style>";

echo "<div class='section'>";
echo "<h3>1️⃣ المسارات الأساسية</h3>";
$basePath = base_path();
$storagePath = storage_path('app/public');
$publicPath = public_path();
$publicStoragePath = public_path('storage');

echo "<pre>";
echo "Base Path: $basePath\n";
echo "Storage Path (app/public): $storagePath\n";
echo "Public Path: $publicPath\n";
echo "Public Storage Path: $publicStoragePath\n";
echo "</pre>";
echo "</div>";

echo "<div class='section'>";
echo "<h3>2️⃣ فحص وجود المجلدات</h3>";
echo "<pre>";

$dirs = [
    'storage' => storage_path(),
    'storage/app' => storage_path('app'),
    'storage/app/public' => storage_path('app/public'),
    'public' => public_path(),
    'public/storage' => public_path('storage'),
];

foreach ($dirs as $name => $path) {
    $exists = file_exists($path);
    $isDir = is_dir($path);
    $isLink = is_link($path);
    $writable = $exists ? is_writable($path) : false;
    
    $status = $exists ? ($isDir ? '✅ موجود (مجلد)' : '⚠️ موجود لكن ليس مجلد') : '❌ غير موجود';
    if ($isLink) {
        $status .= ' (Symlink → ' . readlink($path) . ')';
    }
    
    echo "$name: $status\n";
    echo "  المسار: $path\n";
    if ($exists) {
        $perms = fileperms($path);
        echo "  الصلاحيات: " . substr(sprintf('%o', $perms), -4) . "\n";
        echo "  قابل للكتابة: " . ($writable ? '✅ نعم' : '❌ لا') . "\n";
    }
    echo "\n";
}
echo "</pre>";
echo "</div>";

echo "<div class='section'>";
echo "<h3>3️⃣ فحص المجلدات المطلوبة للصور</h3>";
echo "<pre>";

$storage = Storage::disk('public');
$requiredDirs = ['categories', 'types', 'companies', 'products', 'campaigns', 'ids'];

foreach ($requiredDirs as $dir) {
    $fullPath = $storage->path($dir);
    $exists = file_exists($fullPath);
    $writable = $exists ? is_writable($fullPath) : false;
    
    $status = $exists ? '✅ موجود' : '❌ غير موجود';
    echo "$dir: $status\n";
    echo "  المسار: $fullPath\n";
    if ($exists) {
        $perms = fileperms($fullPath);
        echo "  الصلاحيات: " . substr(sprintf('%o', $perms), -4) . "\n";
        echo "  قابل للكتابة: " . ($writable ? '✅ نعم' : '❌ لا') . "\n";
    }
    echo "\n";
}
echo "</pre>";
echo "</div>";

echo "<div class='section'>";
echo "<h3>4️⃣ اختبار الكتابة</h3>";
echo "<pre>";

$testDir = storage_path('app/public/categories');
$testFile = $testDir . '/test_write_' . time() . '.txt';
$testContent = 'Test content ' . date('Y-m-d H:i:s');

// التأكد من وجود المجلد
if (!file_exists($testDir)) {
    $created = @mkdir($testDir, 0755, true);
    echo "محاولة إنشاء مجلد categories: " . ($created ? '✅ نجح' : '❌ فشل') . "\n\n";
}

if (file_exists($testDir)) {
    // محاولة الكتابة
    $written = @file_put_contents($testFile, $testContent);
    if ($written !== false && file_exists($testFile)) {
        echo "✅ نجح الكتابة!\n";
        echo "   الملف: $testFile\n";
        echo "   الحجم: $written bytes\n";
        
        // محاولة القراءة
        $read = @file_get_contents($testFile);
        if ($read === $testContent) {
            echo "✅ نجحت القراءة!\n";
        } else {
            echo "⚠️ فشلت القراءة (المحتوى مختلف)\n";
        }
        
        // حذف الملف
        $deleted = @unlink($testFile);
        echo "   حذف الملف: " . ($deleted ? '✅ نجح' : '❌ فشل') . "\n";
    } else {
        echo "❌ فشل الكتابة!\n";
        echo "   المسار: $testFile\n";
        echo "   المجلد موجود: " . (file_exists($testDir) ? 'نعم' : 'لا') . "\n";
        echo "   المجلد قابل للكتابة: " . (is_writable($testDir) ? 'نعم' : 'لا') . "\n";
        if (file_exists($testDir)) {
            $perms = fileperms($testDir);
            echo "   الصلاحيات: " . substr(sprintf('%o', $perms), -4) . "\n";
        }
    }
} else {
    echo "❌ مجلد categories غير موجود ولا يمكن إنشاؤه!\n";
}
echo "</pre>";
echo "</div>";

echo "<div class='section'>";
echo "<h3>5️⃣ إعدادات PHP</h3>";
echo "<pre>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "file_uploads: " . (ini_get('file_uploads') ? '✅ مفعّل' : '❌ معطّل') . "\n";
echo "temp_dir: " . sys_get_temp_dir() . "\n";
echo "temp_dir قابل للكتابة: " . (is_writable(sys_get_temp_dir()) ? '✅ نعم' : '❌ لا') . "\n";
echo "</pre>";
echo "</div>";

echo "<div class='section'>";
echo "<h3>6️⃣ فحص Disk Configuration</h3>";
echo "<pre>";

try {
    $disk = Storage::disk('public');
    $root = $disk->getDriver()->getAdapter()->getPathPrefix();
    echo "Disk 'public' root: $root\n";
    echo "Root موجود: " . (file_exists($root) ? '✅ نعم' : '❌ لا') . "\n";
    if (file_exists($root)) {
        echo "Root قابل للكتابة: " . (is_writable($root) ? '✅ نعم' : '❌ لا') . "\n";
        $perms = fileperms($root);
        echo "Root صلاحيات: " . substr(sprintf('%o', $perms), -4) . "\n";
    }
} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
}
echo "</pre>";
echo "</div>";

echo "<div class='section'>";
echo "<h3>7️⃣ الملفات الموجودة حالياً</h3>";
echo "<pre>";

$categoriesPath = storage_path('app/public/categories');
if (file_exists($categoriesPath)) {
    $files = @scandir($categoriesPath);
    if ($files) {
        $files = array_filter($files, function($f) {
            return $f !== '.' && $f !== '..' && !str_starts_with($f, 'test_');
        });
        if (empty($files)) {
            echo "لا توجد ملفات في categories/\n";
        } else {
            echo "الملفات الموجودة في categories/:\n";
            foreach ($files as $file) {
                $filePath = $categoriesPath . '/' . $file;
                $size = filesize($filePath);
                $modified = date('Y-m-d H:i:s', filemtime($filePath));
                echo "  - $file ($size bytes, آخر تعديل: $modified)\n";
            }
        }
    } else {
        echo "❌ فشل قراءة محتويات المجلد\n";
    }
} else {
    echo "❌ مجلد categories غير موجود\n";
}
echo "</pre>";
echo "</div>";

echo "<div class='section'>";
echo "<h3>✅ الخلاصة والتوصيات</h3>";
echo "<pre>";

$issues = [];

if (!file_exists(storage_path('app/public'))) {
    $issues[] = "مجلد storage/app/public غير موجود - يجب إنشاؤه";
}

if (!is_writable(storage_path('app/public'))) {
    $issues[] = "مجلد storage/app/public غير قابل للكتابة - يجب تعديل الصلاحيات إلى 755 أو 775";
}

$categoriesPath = storage_path('app/public/categories');
if (!file_exists($categoriesPath)) {
    $issues[] = "مجلد categories غير موجود - يجب إنشاؤه";
} elseif (!is_writable($categoriesPath)) {
    $issues[] = "مجلد categories غير قابل للكتابة - يجب تعديل الصلاحيات";
}

if (empty($issues)) {
    echo "✅ كل شيء يبدو جيداً! المشكلة قد تكون في الكود نفسه.\n";
    echo "\n";
    echo "توصيات:\n";
    echo "1. تأكد من رفع app/Helpers/ImageHelper.php المحدث\n";
    echo "2. تحقق من storage/logs/laravel.log بعد محاولة رفع صورة\n";
    echo "3. تأكد من أن FILESYSTEM_DISK=public في ملف .env\n";
} else {
    echo "❌ المشاكل الموجودة:\n\n";
    foreach ($issues as $i => $issue) {
        echo ($i + 1) . ". $issue\n";
    }
    echo "\n";
    echo "🔧 الحلول:\n";
    echo "1. في File Manager، اذهب إلى storage/app/public\n";
    echo "2. اضبط الصلاحيات على 755 (أو 775) بشكل Recursive\n";
    echo "3. أنشئ المجلدات المطلوبة: categories, types, companies, products, campaigns, ids\n";
    echo "4. اضبط صلاحيات كل مجلد على 755 أو 775\n";
}

echo "</pre>";
echo "</div>";

echo "<p><strong>⚠️ مهم:</strong> احذف هذا الملف بعد الانتهاء!</p>";