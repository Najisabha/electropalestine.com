<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // إضافة indexes للمنتجات
        Schema::table('products', function (Blueprint $table) {
            // إضافة indexes للأعمدة المستخدمة بشكل متكرر في queries
            try {
                $table->index('is_active', 'products_is_active_index');
            } catch (\Exception $e) {
                // Index موجود بالفعل
            }
            try {
                $table->index('is_best_seller', 'products_is_best_seller_index');
            } catch (\Exception $e) {
                // Index موجود بالفعل
            }
            try {
                $table->index('sales_count', 'products_sales_count_index');
            } catch (\Exception $e) {
                // Index موجود بالفعل
            }
            try {
                $table->index('rating_average', 'products_rating_average_index');
            } catch (\Exception $e) {
                // Index موجود بالفعل
            }
            // Composite index للبحث مع active
            try {
                $table->index(['is_active', 'is_best_seller'], 'products_active_best_seller_index');
            } catch (\Exception $e) {
                // Index موجود بالفعل
            }
            // Composite index للترتيب
            try {
                $table->index(['is_active', 'created_at'], 'products_active_created_index');
            } catch (\Exception $e) {
                // Index موجود بالفعل
            }
        });

        // إضافة indexes للطلبيات
        Schema::table('orders', function (Blueprint $table) {
            try {
                $table->index('status', 'orders_status_index');
            } catch (\Exception $e) {
                // Index موجود بالفعل
            }
            try {
                $table->index('user_id', 'orders_user_id_index');
            } catch (\Exception $e) {
                // Index موجود بالفعل
            }
            try {
                $table->index('created_at', 'orders_created_at_index');
            } catch (\Exception $e) {
                // Index موجود بالفعل
            }
        });

        // إضافة indexes للتقييمات
        Schema::table('reviews', function (Blueprint $table) {
            try {
                $table->index('order_id', 'reviews_order_id_index');
            } catch (\Exception $e) {
                // Index موجود بالفعل
            }
            try {
                $table->index('user_id', 'reviews_user_id_index');
            } catch (\Exception $e) {
                // Index موجود بالفعل
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_is_active_index');
            $table->dropIndex('products_is_best_seller_index');
            $table->dropIndex('products_sales_count_index');
            $table->dropIndex('products_rating_average_index');
            $table->dropIndex('products_active_best_seller_index');
            $table->dropIndex('products_active_created_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_index');
            $table->dropIndex('orders_user_id_index');
            $table->dropIndex('orders_created_at_index');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_order_id_index');
            $table->dropIndex('reviews_user_id_index');
        });
    }

};
