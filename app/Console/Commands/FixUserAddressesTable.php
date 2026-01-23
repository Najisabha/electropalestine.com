<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixUserAddressesTable extends Command
{
    protected $signature = 'db:fix-user-addresses {--force : Force fix}';
    protected $description = 'إصلاح جدول user_addresses المتضرر';

    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('هل أنت متأكد من إعادة إنشاء جدول user_addresses؟', true)) {
                return Command::FAILURE;
            }
        }

        $this->info('جارٍ إصلاح جدول user_addresses...');

        try {
            // محاولة حذف الجدول القديم
            $this->info('1. حذف الجدول القديم...');
            try {
                DB::statement('DROP TABLE IF EXISTS `user_addresses`');
                $this->info('  ✓ تم حذف الجدول القديم');
            } catch (\Exception $e) {
                $this->warn('  ✗ فشل حذف الجدول: ' . $e->getMessage());
            }

            // إعادة إنشاء الجدول
            $this->info('2. إعادة إنشاء الجدول...');
            DB::statement("
                CREATE TABLE `user_addresses` (
                    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                    `user_id` bigint unsigned NOT NULL,
                    `first_name` varchar(255) NOT NULL,
                    `last_name` varchar(255) NOT NULL,
                    `city` varchar(255) DEFAULT NULL,
                    `governorate` varchar(255) DEFAULT NULL,
                    `zip_code` varchar(255) DEFAULT NULL,
                    `country_code` varchar(10) DEFAULT NULL,
                    `phone` varchar(255) NOT NULL,
                    `street` varchar(255) DEFAULT NULL,
                    `is_default` tinyint(1) NOT NULL DEFAULT '0',
                    `created_at` timestamp NULL DEFAULT NULL,
                    `updated_at` timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `user_addresses_user_id_foreign` (`user_id`),
                    CONSTRAINT `user_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC
            ");
            
            $this->info('  ✓ تم إعادة إنشاء الجدول');
            $this->info('');
            $this->info('✓ تم إصلاح جدول user_addresses بنجاح!');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('حدث خطأ: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
