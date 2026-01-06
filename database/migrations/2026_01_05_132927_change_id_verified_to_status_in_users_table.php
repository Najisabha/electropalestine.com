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
        Schema::table('users', function (Blueprint $table) {
            // إزالة الحقل القديم
            $table->dropColumn('id_verified');
        });
        
        Schema::table('users', function (Blueprint $table) {
            // إضافة الحقل الجديد مع 3 حالات
            $table->enum('id_verified_status', ['verified', 'pending', 'unverified'])
                  ->default('unverified')
                  ->after('id_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('id_verified_status');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('id_verified')->default(false)->after('id_image');
        });
    }
};
