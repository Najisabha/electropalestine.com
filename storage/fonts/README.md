# تعليمات تثبيت خط عربي لـ DomPDF

لإصلاح مشكلة عرض النصوص العربية في PDF، يجب تثبيت خط عربي يدعم RTL.

## الحل الموصى به:

### 1. تحميل خط Cairo (الخط المستخدم في الموقع):
- تحميل من: https://fonts.google.com/specimen/Cairo
- أو تنزيل من: https://github.com/google/fonts/tree/main/ofl/cairo

### 2. تثبيت الخط في DomPDF:
قم بتنفيذ الأمر التالي من مجلد المشروع:

```bash
php vendor/dompdf/dompdf/src/Fonts/load_font.php "Cairo" public/fonts/Cairo-Regular.ttf public/fonts/Cairo-Bold.ttf
```

### 3. بديل: استخدام XB Riyaz (خط عربي شائع):
```bash
php vendor/dompdf/dompdf/src/Fonts/load_font.php "XB Riyaz" storage/fonts/xb-riyaz.ttf storage/fonts/xb-riyaz-bold.ttf
```

### 4. بعد التثبيت:
قم بتحديث ملف `resources/views/store/invoice.blade.php` لتغيير الخط من 'DejaVu Sans' إلى اسم الخط المثبت.

## ملاحظة:
- تأكد من أن ملفات الخط (.ttf) موجودة في المسار المحدد
- الخط Cairo متوفر مجاناً ويدعم العربية بشكل ممتاز
