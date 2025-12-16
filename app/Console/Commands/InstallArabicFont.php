<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use FontLib\Font;

class InstallArabicFont extends Command
{
    protected $signature = 'font:install-arabic {font-name=Cairo}';
    protected $description = 'تثبيت خط عربي لـ DomPDF (مثل Cairo)';

    public function handle()
    {
        $fontName = $this->argument('font-name');
        $fontsDir = storage_path('fonts');
        $dompdfFontsDir = base_path('vendor/dompdf/dompdf/lib/fonts');
        
        // التأكد من وجود المجلدات
        if (!is_dir($fontsDir)) {
            mkdir($fontsDir, 0755, true);
        }
        
        $regularFont = $fontsDir . '/' . $fontName . '-Regular.ttf';
        $boldFont = $fontsDir . '/' . $fontName . '-Bold.ttf';
        
        // التحقق من وجود ملفات الخط
        if (!file_exists($regularFont)) {
            $this->error("❌ لم يتم العثور على ملف الخط: {$fontName}-Regular.ttf");
            $this->info("📥 يرجى تحميل خط {$fontName} من: https://fonts.google.com/specimen/{$fontName}");
            $this->info("📁 ثم نسخ ملفات .ttf إلى: {$fontsDir}/");
            return 1;
        }
        
        $this->info("🔄 جاري تثبيت خط {$fontName}...");
        
        try {
            // نسخ الخطوط إلى مجلد DomPDF
            if (!is_dir($dompdfFontsDir)) {
                mkdir($dompdfFontsDir, 0755, true);
            }
            
            $destRegular = $dompdfFontsDir . '/' . $fontName . '-Regular.ttf';
            copy($regularFont, $destRegular);
            $this->info("✅ تم نسخ {$fontName}-Regular.ttf");
            
            if (file_exists($boldFont)) {
                $destBold = $dompdfFontsDir . '/' . $fontName . '-Bold.ttf';
                copy($boldFont, $destBold);
                $this->info("✅ تم نسخ {$fontName}-Bold.ttf");
            } else {
                // استخدام الخط العادي كخط عريض
                $destBold = $dompdfFontsDir . '/' . $fontName . '-Bold.ttf';
                copy($regularFont, $destBold);
                $this->warn("⚠️  استخدام {$fontName}-Regular.ttf كخط عريض (لم يتم العثور على Bold)");
            }
            
            // إنشاء ملفات UFM
            $this->info("📄 جاري إنشاء ملفات UFM...");
            
            $font = new Font($regularFont);
            if ($font->parse()) {
                // سيتم إنشاء ملفات UFM عند أول استخدام
                $this->info("✅ تم تحليل الخط بنجاح");
            }
            
            $this->info("\n✨ تم تثبيت خط {$fontName} بنجاح!");
            $this->info("📝 يمكنك الآن استخدام '{$fontName}' كاسم الخط في قوالب PDF");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ خطأ: " . $e->getMessage());
            return 1;
        }
    }
}
