# إعداد البريد الإلكتروني (Email Configuration)

## المشكلة الحالية
الموقع حالياً يستخدم `MAIL_MAILER=log` مما يعني أن البريد يُحفظ فقط في ملفات الـ log ولا يُرسل فعلياً.

## الحل: تكوين Gmail SMTP

### الخطوة 1: تفعيل "App Password" في Gmail

1. اذهب إلى [Google Account Security](https://myaccount.google.com/security)
2. فعّل "2-Step Verification" إذا لم يكن مفعّل
3. اذهب إلى [App Passwords](https://myaccount.google.com/apppasswords)
4. اختر "Mail" و "Other (Custom name)"
5. اكتب اسم للتطبيق مثل "Laravel App"
6. انسخ كلمة المرور التي ستظهر (16 حرف بدون مسافات)

### الخطوة 2: تحديث ملف .env

افتح ملف `.env` وقم بتحديث هذه الأسطر:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=nageammar628@gmail.com
MAIL_PASSWORD=your-app-password-here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="nageammar628@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"

CONTACT_EMAIL=nageammar628@gmail.com
```

**مهم:** ضع App Password في `MAIL_PASSWORD` وليس كلمة مرور Gmail العادية.

### الخطوة 3: مسح الـ Cache

بعد التحديث، شغّل هذه الأوامر:

```bash
php artisan config:clear
php artisan cache:clear
```

## بدائل أخرى

### استخدام Mailtrap (للاختبار فقط)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
```

### استخدام Mailgun

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.mailgun.org
MAILGUN_SECRET=your-mailgun-secret
```

## التحقق من الإرسال

بعد التكوين:
1. جرّب إرسال رسالة من نموذج "تواصل معنا"
2. تحقق من صندوق الوارد (و Spam أيضاً)
3. إذا لم تصل، تحقق من `storage/logs/laravel.log` للأخطاء
