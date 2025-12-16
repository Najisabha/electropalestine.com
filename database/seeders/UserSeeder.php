<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@electropalestine.com'],
            [
                'name' => 'electropalestine Admin',
                'first_name' => 'electropalestine',
                'last_name' => 'Admin',
                'phone' => '+970000000',
                'whatsapp_prefix' => '+970',
                'birth_year' => 1990,
                'birth_month' => 1,
                'birth_day' => 1,
                'role' => 'admin',
                'password' => Hash::make('Admin@123456'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@electropalestine.com'],
            [
                'name' => 'electropalestine User',
                'first_name' => 'electropalestine',
                'last_name' => 'User',
                'phone' => '+970000001',
                'whatsapp_prefix' => '+970',
                'birth_year' => 1995,
                'birth_month' => 1,
                'birth_day' => 1,
                'role' => 'user',
                'password' => Hash::make('User@123456'),
            ]
        );
    }
}

