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
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('id_image');
            }
            if (!Schema::hasColumn('users', 'district')) {
                $table->string('district')->nullable()->after('city'); // البلدة / الحي
            }
            if (!Schema::hasColumn('users', 'governorate')) {
                $table->string('governorate')->nullable()->after('district'); // المحافظة
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('governorate'); // العنوان الكامل
            }
            if (!Schema::hasColumn('users', 'zip_code')) {
                $table->string('zip_code')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'country_code')) {
                $table->string('country_code', 10)->nullable()->after('zip_code'); // مقدمة البلد
            }
            if (!Schema::hasColumn('users', 'secondary_address')) {
                $table->string('secondary_address')->nullable()->after('country_code'); // عنوان احتياطي
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'city',
                'district',
                'governorate',
                'address',
                'zip_code',
                'country_code',
                'secondary_address',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
