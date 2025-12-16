<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@electropalestine.com')->first();
        $user = User::where('email', 'user@electropalestine.com')->first();

        Order::updateOrCreate(
            ['id' => 1],
            [
                'user_id' => $user?->id,
                'product_name' => 'سماعات بلوتوث',
                'quantity' => 2,
                'unit_price' => 50,
                'total' => 100,
                'status' => 'pending',
            ]
        );

        Order::updateOrCreate(
            ['id' => 2],
            [
                'user_id' => $admin?->id,
                'product_name' => 'هاتف ذكي',
                'quantity' => 1,
                'unit_price' => 250,
                'total' => 250,
                'status' => 'confirmed',
            ]
        );
    }
}

