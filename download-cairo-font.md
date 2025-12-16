# دليل تحميل وتثبيت خط Cairo

## الخطوة 1: تحميل خط Cairo

### الطريقة الأولى (Google Fonts):
1. انتقل إلى: https://fonts.google.com/specimen/Cairo
2. اضغط على "Download family"
3. استخرج الملف المضغوط

### الطريقة الثانية (مباشرة):
قم بتحميل الملفات التالية:
- Cairo-Regular.ttf
- Cairo-Bold.ttf

## الخطوة 2: نسخ الملفات

انسخ ملفات الخط (.ttf) إلى مجلد المشروع:
```
storage/fonts/Cairo-Regular.ttf
storage/fonts/Cairo-Bold.ttf
```

## الخطوة 3: تثبيت الخط

بعد نسخ الملفات، قم بتنفيذ الأمر التالي:

```bash
php artisan font:install-arabic Cairo
```

أو باستخدام السكربت المباشر:
```bash
php install-cairo-font.php
```

## الخطوة 4: التحقق

بعد التثبيت، سيتم استخدام خط Cairo تلقائياً في الفواتير لأننا أضفناه كخيار أول في CSS.

## ملاحظات:
- تأكد من أن مجلد `storage/fonts` قابل للكتابة
- إذا لم تتوفر نسخة Bold، سيتم استخدام Regular كخط عريض
- بعد التثبيت، قد تحتاج إلى مسح الكاش: `php artisan cache:clear`
