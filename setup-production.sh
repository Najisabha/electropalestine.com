#!/bin/bash

# سكريبت إعداد الإنتاج
# استخدم: bash setup-production.sh

echo "🚀 بدء إعداد الإنتاج..."

# التحقق من وجود ملف .env
if [ ! -f .env ]; then
    echo "⚠️  ملف .env غير موجود. جاري نسخه من .env.example..."
    cp .env.example .env
    echo "✅ تم نسخ .env.example إلى .env"
    echo "⚠️  يرجى تحديث ملف .env بالإعدادات الصحيحة قبل المتابعة!"
    exit 1
fi

# تثبيت Dependencies
echo "📦 تثبيت Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "📦 تثبيت NPM dependencies..."
npm install

echo "🔨 بناء Assets..."
npm run build

# إنشاء APP_KEY إذا لم يكن موجوداً
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 إنشاء APP_KEY..."
    php artisan key:generate
fi

# تشغيل Migrations
echo "🗄️  تشغيل Migrations..."
php artisan migrate --force

# إعداد الصلاحيات
echo "🔐 إعداد الصلاحيات..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# إنشاء المجلدات المطلوبة
echo "📁 إنشاء المجلدات المطلوبة..."
mkdir -p storage/app/public/{categories,types,companies,products,campaigns,ids}
chmod -R 775 storage/app/public

# إنشاء Storage Link
echo "🔗 إنشاء Storage Link..."
php artisan storage:link

# مسح الـ Cache
echo "🧹 مسح الـ Cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# تحسين الأداء
echo "⚡ تحسين الأداء..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ تم إعداد الإنتاج بنجاح!"
echo ""
echo "📝 الخطوات التالية:"
echo "1. تأكد من تحديث ملف .env بالإعدادات الصحيحة"
echo "2. تحقق من صلاحيات المجلدات (storage و bootstrap/cache)"
echo "3. اختبر رفع صورة للتأكد من أن كل شيء يعمل"
echo "4. راجع storage/logs/laravel.log إذا ظهرت أي مشاكل"
