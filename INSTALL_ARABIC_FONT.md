# دليل تثبيت خط عربي لـ DomPDF

## المشكلة:
خط `DejaVu Sans` المدمج في DomPDF لا يدعم النصوص العربية بشكل كامل، مما يؤدي إلى ظهور الأحرف منفصلة وغير متصلة.

## الحل:
تثبيت خط يدعم العربية بشكل كامل مثل Cairo أو XB Riyaz.

## الخطوات:

### 1. تحميل خط Cairo:
- انتقل إلى: https://fonts.google.com/specimen/Cairo
- اضغط "Download family"
- استخرج الملفات وحفظها في مجلد مؤقت

### 2. نسخ ملفات الخط إلى المشروع:
```bash
# قم بنسخ ملفات .ttf إلى مجلد storage/fonts/
# ستحتاج على الأقل:
# - Cairo-Regular.ttf
# - Cairo-Bold.ttf
```

### 3. تثبيت الخط في DomPDF:
```bash
php vendor/dompdf/dompdf/src/Fonts/load_font.php "Cairo" storage/fonts/Cairo-Regular.ttf storage/fonts/Cairo-Bold.ttf
```

### 4. تحديث ملف الفاتورة:
بعد التثبيت، سيتم استخدام خط Cairo تلقائياً لأننا أضفناه كخيار أول في CSS.

## بديل سريع (للتجربة):
يمكنك تجربة استخدام خط XB Riyaz إذا كان متوفراً:
```bash
php vendor/dompdf/dompdf/src/Fonts/load_font.php "XB Riyaz" /path/to/XBRiyaz.ttf /path/to/XBRiyazBold.ttf
```

## ملاحظة مهمة:
- تأكد من أن مجلد `storage/fonts` قابل للكتابة
- بعد تثبيت الخط، قد تحتاج إلى مسح الكاش: `php artisan cache:clear`
