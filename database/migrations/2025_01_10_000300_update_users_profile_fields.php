<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('whatsapp_prefix')->nullable()->after('phone');
            $table->unsignedSmallInteger('birth_year')->nullable()->after('whatsapp_prefix');
            $table->unsignedTinyInteger('birth_month')->nullable()->after('birth_year');
            $table->unsignedTinyInteger('birth_day')->nullable()->after('birth_month');
            $table->string('role')->default('user')->after('birth_day');
            $table->string('id_image')->nullable()->after('role');

            // keep original name column for compatibility; will store full name
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
                'whatsapp_prefix',
                'birth_year',
                'birth_month',
                'birth_day',
                'role',
                'id_image',
            ]);
        });
    }
};

