<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_count')->default(0)->after('stock');
            $table->decimal('rating_average', 3, 2)->default(0)->after('sales_count');
            $table->unsignedBigInteger('rating_count')->default(0)->after('rating_average');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sales_count', 'rating_average', 'rating_count']);
        });
    }
};


