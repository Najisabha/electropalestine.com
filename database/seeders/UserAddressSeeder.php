<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;

class UserAddressSeeder extends Seeder
{
    public function run(): void
    {
        // الحصول على المستخدمين
        $admin = User::where('email', 'admin@electropalestine.com')->first();
        $user = User::where('email', 'user@electropalestine.com')->first();

        if ($admin) {
            // إضافة عنوانين للمدير
            UserAddress::updateOrCreate(
                [
                    'user_id' => $admin->id,
                    'city' => 'رام الله',
                ],
                [
                    'first_name' => $admin->first_name ?? 'electropalestine',
                    'last_name' => $admin->last_name ?? 'Admin',
                    'city' => 'رام الله',
                    'governorate' => 'رام الله والبيرة',
                    'zip_code' => '00970',
                    'country_code' => '+970',
                    'phone' => '0599123456',
                    'street' => 'شارع ياسر عرفات، مبنى رقم 15',
                    'is_default' => true,
                ]
            );

            UserAddress::updateOrCreate(
                [
                    'user_id' => $admin->id,
                    'city' => 'القدس',
                ],
                [
                    'first_name' => $admin->first_name ?? 'electropalestine',
                    'last_name' => $admin->last_name ?? 'Admin',
                    'city' => 'القدس',
                    'governorate' => 'القدس',
                    'zip_code' => '00970',
                    'country_code' => '+970',
                    'phone' => '0599876543',
                    'street' => 'شارع صلاح الدين، حي البطريركية',
                    'is_default' => false,
                ]
            );
        }

        if ($user) {
            // إضافة عنوانين للمستخدم العادي
            UserAddress::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'city' => 'نابلس',
                ],
                [
                    'first_name' => $user->first_name ?? 'electropalestine',
                    'last_name' => $user->last_name ?? 'User',
                    'city' => 'نابلس',
                    'governorate' => 'نابلس',
                    'zip_code' => '00970',
                    'country_code' => '+970',
                    'phone' => '0599111111',
                    'street' => 'شارع الجامعة، حي رفيديا',
                    'is_default' => true,
                ]
            );

            UserAddress::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'city' => 'بيت لحم',
                ],
                [
                    'first_name' => $user->first_name ?? 'electropalestine',
                    'last_name' => $user->last_name ?? 'User',
                    'city' => 'بيت لحم',
                    'governorate' => 'بيت لحم',
                    'zip_code' => '00970',
                    'country_code' => '+970',
                    'phone' => '0599222222',
                    'street' => 'شارع المهد، وسط المدينة',
                    'is_default' => false,
                ]
            );

            // إضافة عنوان ثالث للمستخدم العادي لإظهار إمكانية إضافة أكثر من عنوان
            UserAddress::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'city' => 'غزة',
                ],
                [
                    'first_name' => $user->first_name ?? 'electropalestine',
                    'last_name' => $user->last_name ?? 'User',
                    'city' => 'غزة',
                    'governorate' => 'غزة',
                    'zip_code' => '00970',
                    'country_code' => '+970',
                    'phone' => '0599333333',
                    'street' => 'شارع عمر المختار، حي الرمال',
                    'is_default' => false,
                ]
            );
        }

        // إضافة عناوين لجميع المستخدمين الآخرين الذين ليس لديهم عناوين
        $usersWithoutAddresses = User::whereDoesntHave('addresses')->get();
        
        foreach ($usersWithoutAddresses as $usr) {
            UserAddress::create([
                'user_id' => $usr->id,
                'first_name' => $usr->first_name ?? $usr->name ?? 'مستخدم',
                'last_name' => $usr->last_name ?? 'غير محدد',
                'city' => $usr->city ?? 'رام الله',
                'governorate' => $usr->governorate ?? 'رام الله والبيرة',
                'zip_code' => $usr->zip_code ?? '00970',
                'country_code' => $usr->country_code ?? '+970',
                'phone' => $usr->phone ?? '0599000000',
                'street' => $usr->address ?? 'العنوان غير محدد',
                'is_default' => true,
            ]);
        }
    }
}