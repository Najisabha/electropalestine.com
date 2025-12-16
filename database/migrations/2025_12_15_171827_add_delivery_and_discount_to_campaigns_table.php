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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('shipping_type')->default('none')->after('budget'); // none, free, conditional
            $table->decimal('shipping_min_amount', 10, 2)->nullable()->after('shipping_type');
            $table->string('discount_type')->default('none')->after('shipping_min_amount'); // none, percent, amount
            $table->decimal('discount_value', 10, 2)->default(0)->after('discount_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['shipping_type', 'shipping_min_amount', 'discount_type', 'discount_value']);
        });
    }
};
