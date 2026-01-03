# ✅ قائمة التحقق السريعة للنشر (Quick Deployment Checklist)

## قبل النشر

- [ ] تحديث ملف `.env` بالإعدادات الصحيحة
- [ ] التأكد من `APP_ENV=production`
- [ ] التأكد من `APP_DEBUG=false`
- [ ] التأكد من `APP_URL=https://electropalestine.com`
- [ ] التأكد من إعدادات قاعدة البيانات صحيحة
- [ ] التأكد من إعدادات البريد الإلكتروني صحيحة

## بعد رفع الملفات

- [ ] تثبيت Composer dependencies: `composer install --no-dev --optimize-autoloader`
- [ ] تثبيت NPM dependencies: `npm install`
- [ ] بناء Assets: `npm run build`
- [ ] إنشاء/تحديث APP_KEY: `php artisan key:generate`
- [ ] تشغيل Migrations: `php artisan migrate --force`
- [ ] إعداد الصلاحيات: `chmod -R 775 storage bootstrap/cache`
- [ ] إنشاء المجلدات المطلوبة في `storage/app/public`
- [ ] إنشاء Storage Link: `php artisan storage:link`
- [ ] مسح الـ Cache: `php artisan optimize:clear`
- [ ] تحسين الأداء: `php artisan config:cache && php artisan route:cache && php artisan view:cache`

## التحقق من النشر

- [ ] الموقع يعمل: `https://electropalestine.com`
- [ ] يمكن تسجيل الدخول
- [ ] يمكن رفع صورة (فئة، منتج، إلخ)
- [ ] الصورة تظهر بعد الرفع
- [ ] لا توجد أخطاء في `storage/logs/laravel.log`

## ملاحظات

- استخدم `setup-production.sh` لتسهيل العملية
- راجع `PRODUCTION_DEPLOYMENT.md` للتفاصيل الكاملة
