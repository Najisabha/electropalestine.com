<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // جعل حقل البريد الإلكتروني قابلاً للإفراغ (NULL)
        // نستخدم SQL مباشر لتفادي الحاجة إلى doctrine/dbal
        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إعادة الحقل ليكون NOT NULL (قد يفشل إذا وُجدت سجلات بلا بريد)
        // لذلك ننظف السجلات أولاً بإعطائها قيمة افتراضية مؤقتة
        DB::statement("UPDATE users SET email = CONCAT('user_', id, '@example.com') WHERE email IS NULL");
        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NOT NULL');
    }
};
