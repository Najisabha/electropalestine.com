# دليل إعدادات الإنتاج (Production Deployment Guide)

## 📋 قائمة التحقق قبل النشر

### 1. إعدادات ملف `.env`

عند النشر، تأكد من تحديث ملف `.env` في الخادم بالإعدادات التالية:

```env
# ============================================
# إعدادات التطبيق الأساسية
# ============================================
APP_NAME="ElectroPalestine"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_TIMEZONE=Asia/Gaza
APP_URL=https://electropalestine.com

# ============================================
# إعدادات قاعدة البيانات
# ============================================
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# ============================================
# إعدادات التخزين (Storage)
# ============================================
FILESYSTEM_DISK=public

# ============================================
# إعدادات البريد الإلكتروني
# ============================================
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"

CONTACT_EMAIL=your-email@gmail.com

# ============================================
# إعدادات الجلسات (Sessions)
# ============================================
SESSION_DRIVER=file
SESSION_LIFETIME=120

# ============================================
# إعدادات الـ Cache
# ============================================
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# ============================================
# إعدادات السجلات (Logging)
# ============================================
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error
```

**⚠️ مهم جداً:**
- `APP_DEBUG=false` في الإنتاج (للمزيد من الأمان)
- `APP_ENV=production` في الإنتاج
- `APP_URL` يجب أن يكون الرابط الكامل للموقع (https://electropalestine.com)
- `APP_KEY` يجب أن يكون موجوداً (شغّل `php artisan key:generate` إذا لم يكن موجوداً)

---

## 🔐 خطوات الإعداد بعد رفع الملفات

### الخطوة 1: تثبيت Dependencies

```bash
# في Terminal في hPanel أو SSH
cd /path/to/your/project

# تثبيت Composer dependencies
composer install --no-dev --optimize-autoloader

# تثبيت NPM dependencies وبناء Assets
npm install
npm run build
```

### الخطوة 2: إعداد ملف `.env`

1. انسخ `.env.example` إلى `.env`:
   ```bash
   cp .env.example .env
   ```

2. عدّل ملف `.env` بالإعدادات الصحيحة (انظر أعلاه)

3. أنشئ `APP_KEY`:
   ```bash
   php artisan key:generate
   ```

### الخطوة 3: إعداد قاعدة البيانات

```bash
# تشغيل Migrations
php artisan migrate --force

# (اختياري) ملء البيانات الأولية
php artisan db:seed --force
```

### الخطوة 4: إعداد الصلاحيات (Permissions)

**في Terminal (SSH):**
```bash
# إعطاء صلاحيات الكتابة لمجلدات التخزين
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# (اختياري) تغيير المالك إذا كان متاحاً
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

**في File Manager (hPanel):**
1. اذهب إلى مجلد `storage`
2. انقر بزر الماوس الأيمن → **Change Permissions**
3. اضبط على `775` أو `755`
4. فعّل **Recursive** ليطبق على جميع المجلدات الفرعية
5. كرر نفس الخطوات لمجلد `bootstrap/cache`

### الخطوة 5: إنشاء Storage Link

**الطريقة 1: عبر Terminal (الأفضل)**
```bash
php artisan storage:link
```

**الطريقة 2: يدوياً في File Manager**
1. اذهب إلى مجلد `public` (أو `public_html`)
2. إذا كان مجلد `storage` موجوداً كمجرد مجلد (وليس رابط رمزي)، احذفه
3. أنشئ رابط رمزي:
   - انقر بزر الماوس الأيمن → **Create Symbolic Link**
   - **Target/Source**: `../storage/app/public`
   - **Link Name**: `storage`
   - انقر **Create**

**الطريقة 3: استخدام Route بديل (موجود في الكود)**
- الكود يحتوي على route `/storage/{path}` كحل بديل
- إذا لم يعمل symlink، سيتم استخدام هذا الـ route تلقائياً
- **لا حاجة لعمل أي شيء إضافي**

### الخطوة 6: إنشاء المجلدات المطلوبة

تأكد من وجود المجلدات التالية في `storage/app/public`:
- `categories`
- `types`
- `companies`
- `products`
- `campaigns`
- `ids`

**في Terminal:**
```bash
mkdir -p storage/app/public/{categories,types,companies,products,campaigns,ids}
chmod -R 775 storage/app/public
```

**في File Manager:**
أنشئ المجلدات يدوياً في `storage/app/public`

### الخطوة 7: إنشاء ملفات الحماية

**في `storage/app/public` أنشئ ملف `.htaccess`:**
```apache
Options -Indexes
<IfModule mod_headers.c>
    # Allow access to image files
    <FilesMatch "\.(jpg|jpeg|png|gif|webp|svg|ico)$">
        Header set Access-Control-Allow-Origin "*"
    </FilesMatch>
</IfModule>
```

**في `storage/app/public` أنشئ ملف `index.php`:**
```php
<?php
// Silence is golden
```

### الخطوة 8: مسح الـ Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### الخطوة 9: تحسين الأداء (اختياري)

```bash
# تحسين الـ Autoloader
composer install --optimize-autoloader --no-dev

# تحسين الإعدادات
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔍 التحقق من نجاح الإعداد

### 1. التحقق من Storage Link

افتح في المتصفح:
```
https://electropalestine.com/storage/test.jpg
```

إذا ظهرت الصورة أو خطأ 404 (وليس 403)، فالحل يعمل.

### 2. اختبار رفع صورة

1. سجّل دخول كـ Admin
2. أضف فئة جديدة مع صورة
3. تحقق من:
   - حفظ الصورة في `storage/app/public/categories`
   - ظهور الصورة في الواجهة

### 3. التحقق من السجلات

```bash
tail -f storage/logs/laravel.log
```

إذا ظهرت أي أخطاء، راجعها وحلّها.

---

## ⚙️ إعدادات PHP المطلوبة

تأكد من أن إعدادات PHP في الخادم كالتالي:

```ini
upload_max_filesize = 2M
post_max_size = 8M
file_uploads = On
memory_limit = 256M
max_execution_time = 60
```

**في Hostinger:**
- اذهب إلى **hPanel** → **PHP Configuration**
- اضبط الإعدادات المذكورة أعلاه

---

## 🚨 حل المشاكل الشائعة

### المشكلة 1: الصور لا تُحفظ

**الحل:**
1. تحقق من صلاحيات `storage/app/public` (يجب أن تكون 775 أو 755)
2. تحقق من وجود المجلدات المطلوبة
3. راجع `storage/logs/laravel.log` للأخطاء

### المشكلة 2: خطأ 403 Forbidden عند الوصول للصور

**الحل:**
1. تأكد من وجود ملف `.htaccess` في `storage/app/public`
2. تأكد من وجود `index.php` في `storage/app/public`
3. تحقق من صلاحيات المجلدات

### المشكلة 3: خطأ 500 Internal Server Error

**الحل:**
1. راجع `storage/logs/laravel.log`
2. تحقق من إعدادات `.env`
3. تأكد من تشغيل `php artisan config:clear`

### المشكلة 4: الصور تظهر محلياً ولكن لا تظهر في الإنتاج

**الحل:**
1. تأكد من إنشاء Storage Link (`php artisan storage:link`)
2. تحقق من أن `APP_URL` في `.env` صحيح
3. تأكد من أن route `/storage/{path}` يعمل (موجود في الكود)

---

## 📝 ملاحظات مهمة

1. **لا ترفع ملف `.env` إلى Git** - موجود في `.gitignore`
2. **لا ترفع مجلد `storage/app/public`** - يجب أن يكون فارغاً في Git
3. **لا ترفع مجلد `vendor`** - شغّل `composer install` في الخادم
4. **لا ترفع مجلد `node_modules`** - شغّل `npm install` في الخادم
5. **لا ترفع `public/storage`** - سيتم إنشاؤه كـ symlink

---

## 🔄 عند تحديث الموقع

بعد كل تحديث للكود:

```bash
# سحب التحديثات
git pull origin main

# تحديث Dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# تشغيل Migrations الجديدة
php artisan migrate --force

# مسح الـ Cache
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📞 الدعم

إذا استمرت المشاكل:
1. راجع `storage/logs/laravel.log`
2. تحقق من صلاحيات المجلدات
3. تأكد من إعدادات `.env`
4. تحقق من إعدادات PHP
