# إعداد تسجيل الدخول الاجتماعي (Google & Facebook)

## الخطوات المطلوبة:

### 1. إضافة المفاتيح في ملف `.env`

افتح ملف `.env` في جذر المشروع وأضف المفاتيح التالية:

```env
# Google OAuth
GOOGLE_CLIENT_ID=your-google-client-id-here
GOOGLE_CLIENT_SECRET=your-google-client-secret-here
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback

# Facebook OAuth
FACEBOOK_CLIENT_ID=your-facebook-app-id-here
FACEBOOK_CLIENT_SECRET=your-facebook-app-secret-here
FACEBOOK_REDIRECT_URI=http://127.0.0.1:8000/auth/facebook/callback
```

### 2. الحصول على مفاتيح Google OAuth

1. اذهب إلى [Google Cloud Console](https://console.cloud.google.com/)
2. أنشئ مشروع جديد أو اختر مشروع موجود
3. اذهب إلى **APIs & Services** > **Credentials**
4. انقر على **Create Credentials** > **OAuth client ID**
5. اختر **Web application**
6. أضف **Authorized redirect URIs**: `http://127.0.0.1:8000/auth/google/callback`
7. انسخ **Client ID** و **Client Secret** وضعها في ملف `.env`

### 3. الحصول على مفاتيح Facebook OAuth

1. اذهب إلى [Facebook Developers](https://developers.facebook.com/)
2. أنشئ تطبيق جديد أو اختر تطبيق موجود
3. اذهب إلى **Settings** > **Basic**
4. انسخ **App ID** و **App Secret**
5. اذهب إلى **Products** > **Facebook Login** > **Settings**
6. أضف **Valid OAuth Redirect URIs**: `http://127.0.0.1:8000/auth/facebook/callback`
7. ضع **App ID** و **App Secret** في ملف `.env`

### 4. مسح الكاش بعد التعديل

بعد إضافة المفاتيح في `.env`، قم بمسح الكاش:

```bash
php artisan config:clear
php artisan cache:clear
```

### 5. التحقق من الإعدادات

تأكد من أن المفاتيح موجودة في ملف `.env` وأن القيم غير فارغة.

---

**ملاحظة:** في الإنتاج، استبدل `http://127.0.0.1:8000` بعنوان موقعك الفعلي.
