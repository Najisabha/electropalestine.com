# دليل حل مشاكل الإنتاج (Production Troubleshooting)

## مشكلة: فشل إضافة المنتجات على الخادم

إذا كانت إضافة المنتجات تعمل محلياً ولكنها تفشل على الخادم (مثل Hostinger)، تحقق من النقاط التالية:

### 1. صلاحيات المجلدات (File Permissions)

تأكد من أن مجلدات التخزين لديها صلاحيات الكتابة:

```bash
# على الخادم، قم بتنفيذ:
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

أو إذا كنت تستخدم Hostinger cPanel:
- انتقل إلى File Manager
- انقر بزر الماوس الأيمن على مجلد `storage`
- اختر "Change Permissions"
- اضبط الصلاحيات على `775` أو `755`

### 2. إنشاء رابط التخزين (Storage Link)

تأكد من وجود رابط رمزي من `public/storage` إلى `storage/app/public`:

```bash
php artisan storage:link
```

إذا لم يعمل الأمر، يمكنك إنشاء الرابط يدوياً في cPanel:
- تأكد من وجود مجلد `storage/app/public`
- أنشئ رابط رمزي من `public/storage` يشير إلى `storage/app/public`

### 3. التحقق من قاعدة البيانات

تأكد من:
- إعدادات قاعدة البيانات في ملف `.env` صحيحة
- المستخدم لديه صلاحيات كاملة على قاعدة البيانات
- الجداول موجودة (قم بتشغيل migrations إذا لزم الأمر):

```bash
php artisan migrate
```

### 4. التحقق من سجلات الأخطاء

بعد إضافة معالجة الأخطاء، تحقق من سجلات Laravel:

```bash
# عرض آخر الأخطاء
tail -f storage/logs/laravel.log
```

أو في Hostinger:
- انتقل إلى `storage/logs/laravel.log`
- اقرأ آخر الأخطاء المسجلة

### 5. التحقق من إعدادات PHP

تأكد من:
- `upload_max_filesize` كافٍ (على الأقل 2M)
- `post_max_size` كافٍ
- `memory_limit` كافٍ (على الأقل 128M)

يمكنك التحقق من ذلك بإنشاء ملف `phpinfo.php`:

```php
<?php phpinfo(); ?>
```

### 6. التحقق من ملف .env

تأكد من:
- `APP_ENV=production` (أو `local` للاختبار)
- `APP_DEBUG=false` في الإنتاج (للمزيد من الأمان)
- `FILESYSTEM_DISK=public` أو `local`

### 7. مسح الكاش

بعد إجراء التغييرات، امسح الكاش:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 8. التحقق من حجم الملفات

تأكد من أن حجم الصورة لا يتجاوز الحد المسموح (2MB حالياً).

### 9. التحقق من الـ Middleware

تأكد من أن المستخدم لديه صلاحيات Admin وأن الـ middleware يعمل بشكل صحيح.

## بعد إضافة معالجة الأخطاء

الآن، عند فشل إضافة منتج، ستحصل على رسالة خطأ واضحة في الواجهة، وستجد تفاصيل الخطأ في:
- `storage/logs/laravel.log`

## نصائح إضافية لـ Hostinger

1. **استخدم File Manager في cPanel** للتحقق من وجود الملفات والمجلدات
2. **تحقق من Error Logs في cPanel** لعرض أخطاء PHP العامة
3. **استخدم PHP Selector** في cPanel لاختيار إصدار PHP مناسب (Laravel يحتاج PHP 8.1+)
4. **تحقق من MySQL Database** في cPanel للتأكد من اتصال قاعدة البيانات

## اختبار سريع

لاختبار ما إذا كانت المشكلة في رفع الملفات:

1. حاول إضافة منتج بدون صورة
2. إذا نجح، المشكلة في صلاحيات مجلد التخزين
3. إذا فشل، المشكلة في قاعدة البيانات أو التحقق من البيانات

## الحصول على المساعدة

إذا استمرت المشكلة، تحقق من:
- `storage/logs/laravel.log` للحصول على تفاصيل الخطأ
- رسائل الخطأ في الواجهة (بعد التحديث الجديد)
- سجلات الأخطاء في cPanel
