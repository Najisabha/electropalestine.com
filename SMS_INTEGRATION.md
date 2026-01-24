# دمج خدمة SMS - دليل التكامل

## الوضع الحالي
حالياً، النظام يعمل في **وضع التطوير**:
- الكود يظهر في ملف `storage/logs/laravel.log`
- لا يتم إرسال SMS فعلي
- يمكن اختبار النظام بالكامل

---

## خيارات دمج SMS

### 1️⃣ **Twilio** (موصى به - عالمي)

**المميزات:**
- ✅ دعم فلسطين والدول العربية
- ✅ سهل الاستخدام
- ✅ API موثوق وسريع
- ✅ تجربة مجانية

**التكلفة:** ~$0.0075 لكل رسالة

**التثبيت:**
```bash
composer require twilio/sdk
```

**الإعدادات في `.env`:**
```env
TWILIO_SID=your_account_sid_here
TWILIO_TOKEN=your_auth_token_here
TWILIO_FROM=+1234567890
```

**التفعيل:**
افتح `app/Helpers/SMSHelper.php` وغيّر السطر 23:
```php
return self::sendViaTwilio($phone, $message);
```

**التسجيل:**
1. اذهب إلى: https://www.twilio.com/try-twilio
2. سجّل حساب مجاني
3. احصل على رقم هاتف
4. انسخ SID و Token

---

### 2️⃣ **Vonage (Nexmo سابقاً)**

**التثبيت:**
```bash
composer require vonage/client
```

**الإعدادات:**
```env
VONAGE_API_KEY=your_api_key
VONAGE_API_SECRET=your_api_secret
VONAGE_FROM=Electropalestine
```

---

### 3️⃣ **AWS SNS**

**التثبيت:**
```bash
composer require aws/aws-sdk-php
```

**الإعدادات:**
```env
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
```

---

### 4️⃣ **خدمة محلية فلسطينية**

إذا كنت تستخدم مزود خدمة محلي:

1. افتح `app/Helpers/SMSHelper.php`
2. عدّل دالة `sendViaLocalProvider()`
3. أضف API الخاص بالمزود
4. غيّر السطر 23:
```php
return self::sendViaLocalProvider($phone, $message);
```

---

## الاختبار

### في بيئة التطوير:
```bash
# شاهد الكود في الـ logs
tail -f storage/logs/laravel.log
```

### في بيئة الإنتاج:
```bash
# غيّر في .env
APP_ENV=production
```

---

## ملاحظات مهمة

1. **الأمان:**
   - احذف `'code' => $verification->code` من الـ logs في الإنتاج
   - احفظ مفاتيح API في `.env` فقط
   - لا تشارك مفاتيح API أبداً

2. **التكلفة:**
   - راقب استهلاك الرسائل
   - ضع حد أقصى يومي
   - استخدم Cache لمنع الإرسال المتكرر

3. **التحسينات المستقبلية:**
   - إضافة Queue للإرسال
   - إضافة Rate Limiting
   - إضافة إشعار WhatsApp كبديل

---

## الدعم

إذا واجهت مشاكل:
1. تحقق من الـ logs: `storage/logs/laravel.log`
2. تأكد من صحة مفاتيح API
3. تحقق من رصيد الحساب في خدمة SMS
