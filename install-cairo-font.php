<?php

/**
 * سكربت تثبيت خط Cairo لـ DomPDF
 * 
 * الاستخدام:
 * php install-cairo-font.php
 */

require __DIR__ . '/vendor/autoload.php';

// التحقق من وجود ملفات الخط
$fontsDir = __DIR__ . '/storage/fonts';
$cairoRegular = $fontsDir . '/Cairo-Regular.ttf';
$cairoBold = $fontsDir . '/Cairo-Bold.ttf';

if (!file_exists($cairoRegular)) {
    echo "❌ لم يتم العثور على ملف الخط: Cairo-Regular.ttf\n";
    echo "📥 يرجى تحميل خط Cairo من: https://fonts.google.com/specimen/Cairo\n";
    echo "📁 ثم نسخ ملفات .ttf إلى مجلد: storage/fonts/\n\n";
    echo "الملفات المطلوبة:\n";
    echo "  - Cairo-Regular.ttf\n";
    echo "  - Cairo-Bold.ttf\n";
    exit(1);
}

if (!file_exists($cairoBold)) {
    echo "⚠️  تحذير: لم يتم العثور على Cairo-Bold.ttf، سيتم استخدام Cairo-Regular.ttf للخط العريض\n";
    $cairoBold = $cairoRegular;
}

// مجلد خطوط DomPDF
$dompdfFontsDir = __DIR__ . '/vendor/dompdf/dompdf/lib/fonts';

if (!is_dir($dompdfFontsDir)) {
    mkdir($dompdfFontsDir, 0755, true);
}

echo "🔄 جاري تثبيت خط Cairo...\n\n";

try {
    // نسخ ملفات الخط
    echo "📝 نسخ Cairo-Regular.ttf...\n";
    $destRegular = $dompdfFontsDir . '/Cairo-Regular.ttf';
    if (copy($cairoRegular, $destRegular)) {
        echo "✅ تم نسخ Cairo-Regular.ttf إلى مجلد DomPDF\n";
    } else {
        throw new \Exception("فشل نسخ Cairo-Regular.ttf");
    }
    
    // نسخ الخط العريض
    $destBold = $dompdfFontsDir . '/Cairo-Bold.ttf';
    if ($cairoBold !== $cairoRegular) {
        echo "📝 نسخ Cairo-Bold.ttf...\n";
        if (copy($cairoBold, $destBold)) {
            echo "✅ تم نسخ Cairo-Bold.ttf إلى مجلد DomPDF\n";
        } else {
            throw new \Exception("فشل نسخ Cairo-Bold.ttf");
        }
    } else {
        echo "📝 نسخ Cairo-Regular.ttf كـ Cairo-Bold.ttf...\n";
        copy($cairoRegular, $destBold);
        echo "✅ تم نسخ Cairo-Regular.ttf كـ Cairo-Bold.ttf\n";
    }
    
    // ملاحظة: ملفات UFM سيتم إنشاؤها تلقائياً عند أول استخدام للخط
    echo "\n✅ تم نسخ جميع ملفات الخط بنجاح\n";
    echo "💡 ملفات UFM سيتم إنشاؤها تلقائياً عند أول استخدام للخط في PDF\n";
    
    echo "\n✨ تم تثبيت خط Cairo بنجاح!\n";
    echo "📝 يمكنك الآن استخدام 'Cairo' كاسم الخط في قوالب PDF\n";
    echo "💡 تلميح: قد تحتاج إلى مسح الكاش بعد التثبيت: php artisan cache:clear\n";
    
} catch (\Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✅ تم الانتهاء!\n";
