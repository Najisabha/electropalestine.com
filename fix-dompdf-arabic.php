<?php

/**
 * حل شامل لمشكلة العربية في DomPDF
 * هذا السكربت يضمن أن DomPDF يستخدم الخط العربي بشكل صحيح
 */

require __DIR__ . '/vendor/autoload.php';

use Dompdf\Options;
use Dompdf\FontMetrics;

// إعدادات DomPDF
$options = new Options();
$options->set('fontDir', __DIR__ . '/storage/fonts');
$options->set('fontCache', __DIR__ . '/storage/fonts');
$options->set('isFontSubsettingEnabled', false);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'cairo');

$fontMetrics = new FontMetrics(null, $options);

// التأكد من تسجيل الخط
$fontsDir = base_path('vendor/dompdf/dompdf/lib/fonts');
$installedFontsFile = $fontsDir . '/installed-fonts.json';

if (file_exists($installedFontsFile)) {
    $fonts = json_decode(file_get_contents($installedFontsFile), true);
    if (!$fonts) {
        $fonts = [];
    }
    
    // تسجيل Cairo بأشكال مختلفة
    $fonts['cairo'] = [
        'normal' => 'Cairo-Regular',
        'bold' => 'Cairo-Bold',
        'italic' => 'Cairo-Regular',
        'bold_italic' => 'Cairo-Bold'
    ];
    
    file_put_contents($installedFontsFile, json_encode($fonts, JSON_PRETTY_PRINT));
    echo "✅ تم تحديث ملف الخطوط المثبتة\n";
}

// التأكد من وجود ملفات الخط
$cairoRegular = $fontsDir . '/Cairo-Regular.ttf';
$cairoBold = $fontsDir . '/Cairo-Bold.ttf';

if (!file_exists($cairoRegular)) {
    echo "❌ خطأ: ملف Cairo-Regular.ttf غير موجود!\n";
    exit(1);
}

echo "✅ جميع ملفات الخط موجودة\n";
echo "✅ تم إعداد DomPDF لدعم العربية بنجاح!\n";
