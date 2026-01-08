<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reward_id')->constrained()->onDelete('cascade');
            $table->string('coupon_code')->nullable(); // كود الكوبون المستخدم
            $table->decimal('discount_value', 10, 2)->nullable(); // قيمة الخصم
            $table->string('discount_type')->nullable(); // نوع الخصم: percent أو amount
            $table->boolean('is_used')->default(false); // هل تم استخدام الكوبون
            $table->timestamp('used_at')->nullable(); // تاريخ الاستخدام
            $table->timestamp('expires_at')->nullable(); // تاريخ انتهاء الصلاحية
            $table->timestamps();
            
            // فهرس لتحسين الأداء
            $table->index(['user_id', 'is_used']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_rewards');
    }
};
