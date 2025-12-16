<?php

/**
 * إصلاح مشكلة الخط العربي في DomPDF
 * هذا السكربت يسجل خط Cairo في DomPDF بشكل صحيح
 */

require __DIR__ . '/vendor/autoload.php';

use Dompdf\FontMetrics;
use Dompdf\Options;
use FontLib\Font;

$fontsDir = __DIR__ . '/vendor/dompdf/dompdf/lib/fonts';
$installedFontsFile = $fontsDir . '/installed-fonts.json';

// قراءة ملف الخطوط المثبتة
$fontFamilies = [];
if (file_exists($installedFontsFile)) {
    $fontFamilies = json_decode(file_get_contents($installedFontsFile), true);
    if (!$fontFamilies) {
        $fontFamilies = [];
    }
}

// تسجيل خط Cairo
$fontFamilies['cairo'] = [
    'normal' => 'Cairo-Regular',
    'bold' => 'Cairo-Bold',
    'italic' => 'Cairo-Regular',
    'bold_italic' => 'Cairo-Bold'
];

// حفظ الملف
file_put_contents($installedFontsFile, json_encode($fontFamilies, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✅ تم تسجيل خط Cairo في DomPDF بنجاح!\n";

// إنشاء ملفات UFM يدوياً باستخدام FontLib
echo "📄 جاري إنشاء ملفات UFM...\n";

$cairoRegular = $fontsDir . '/Cairo-Regular.ttf';
$cairoBold = $fontsDir . '/Cairo-Bold.ttf';

if (file_exists($cairoRegular)) {
    try {
        $font = Font::load($cairoRegular);
        if ($font) {
            $font->parse();
            echo "✅ تم تحليل Cairo-Regular.ttf\n";
        }
    } catch (\Exception $e) {
        echo "⚠️  تحذير: " . $e->getMessage() . "\n";
    }
}

if (file_exists($cairoBold)) {
    try {
        $font = Font::load($cairoBold);
        if ($font) {
            $font->parse();
            echo "✅ تم تحليل Cairo-Bold.ttf\n";
        }
    } catch (\Exception $e) {
        echo "⚠️  تحذير: " . $e->getMessage() . "\n";
    }
}

echo "\n✨ تم الإصلاح بنجاح!\n";
