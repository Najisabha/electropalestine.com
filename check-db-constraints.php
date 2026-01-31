<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 فحص قيود المفاتيح الأجنبية لجدول products...\n\n";

try {
    // الحصول على قيود المفاتيح الأجنبية
    $constraints = DB::select("
        SELECT 
            TABLE_NAME,
            CONSTRAINT_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE REFERENCED_TABLE_NAME = 'products'
        AND TABLE_SCHEMA = DATABASE()
    ");
    
    if (empty($constraints)) {
        echo "✅ لا توجد جداول تشير إلى جدول products\n";
    } else {
        echo "📋 الجداول التي تشير إلى products:\n\n";
        foreach ($constraints as $constraint) {
            echo "• {$constraint->TABLE_NAME}.{$constraint->COLUMN_NAME}\n";
            echo "  القيد: {$constraint->CONSTRAINT_NAME}\n";
            
            // فحص نوع القيد
            $deleteRule = DB::selectOne("
                SELECT DELETE_RULE 
                FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS 
                WHERE CONSTRAINT_NAME = ? 
                AND CONSTRAINT_SCHEMA = DATABASE()
            ", [$constraint->CONSTRAINT_NAME]);
            
            echo "  عند الحذف: {$deleteRule->DELETE_RULE}\n\n";
        }
    }
    
    // عد السجلات في كل جدول مرتبط
    echo "\n📊 عدد السجلات المرتبطة بالمنتجات:\n\n";
    
    $tables = ['order_items', 'user_favorites', 'campaign_product', 'rewards'];
    foreach ($tables as $table) {
        try {
            $count = DB::table($table)->whereNotNull('product_id')->count();
            echo "• {$table}: {$count} سجل\n";
        } catch (\Exception $e) {
            echo "• {$table}: خطأ - {$e->getMessage()}\n";
        }
    }
    
    // اختبار حذف منتج تجريبي
    echo "\n\n🧪 اختبار إنشاء وحذف منتج تجريبي...\n";
    
    // الحصول على category, type, company موجودين
    $category = \App\Models\Category::first();
    $type = \App\Models\Type::first();
    $company = \App\Models\Company::first();
    
    if (!$category || !$type || !$company) {
        echo "❌ لا توجد بيانات كافية (category/type/company) لإجراء الاختبار\n";
        exit(0);
    }
    
    // إنشاء منتج تجريبي
    $testProduct = \App\Models\Product::create([
        'category_id' => $category->id,
        'type_id' => $type->id,
        'company_id' => $company->id,
        'name' => 'منتج تجريبي للحذف TEST_DELETE_' . time(),
        'slug' => 'test-delete-' . time(),
        'price' => 1.00,
        'stock' => 0,
        'is_active' => false,
    ]);
    
    echo "✅ تم إنشاء منتج تجريبي: ID={$testProduct->id}\n";
    
    // محاولة حذفه
    try {
        $testProduct->delete();
        echo "✅ تم حذف المنتج التجريبي بنجاح!\n";
        echo "\n✅ نظام الحذف يعمل بشكل صحيح في قاعدة البيانات.\n";
    } catch (\Exception $e) {
        echo "❌ فشل حذف المنتج التجريبي!\n";
        echo "الخطأ: {$e->getMessage()}\n";
    }
    
} catch (\Exception $e) {
    echo "❌ خطأ: {$e->getMessage()}\n";
}
