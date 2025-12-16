<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // اسم الدور الظاهر
            $table->string('key')->unique();  // مفتاح الدور (admin, user, editor...)
            $table->string('description')->nullable();
            $table->json('permissions')->nullable(); // قائمة بالصلاحيات كنص JSON
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};


