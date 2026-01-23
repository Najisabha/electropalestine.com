<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ForceDeleteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:force-delete {user_id : ID المستخدم المراد حذفه}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'حذف مستخدم قسرياً مع جميع بياناته (لحل مشاكل InnoDB)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        $this->info("جارٍ حذف المستخدم #{$userId} قسرياً...");

        try {
            // التحقق من وجود المستخدم
            $user = User::find($userId);
            if (!$user) {
                $this->error("المستخدم #{$userId} غير موجود!");
                return Command::FAILURE;
            }

            $this->info("المستخدم: {$user->first_name} {$user->last_name} ({$user->email})");

            // 1. حذف جميع السجلات المرتبطة
            $this->info('1. حذف السجلات المرتبطة...');
            
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::statement('SET UNIQUE_CHECKS=0');
            
            // حذف الطلبات وعناصرها
            $orderIds = DB::table('orders')->where('user_id', $userId)->pluck('id');
            if ($orderIds->isNotEmpty()) {
                $this->info("   - حذف {$orderIds->count()} طلب وعناصره...");
                DB::table('order_items')->whereIn('order_id', $orderIds)->delete();
                DB::table('reviews')->whereIn('order_id', $orderIds)->delete();
                DB::table('orders')->where('user_id', $userId)->delete();
            }
            
            // حذف باقي السجلات
            $tables = [
                'reviews' => 'user_id',
                'user_addresses' => 'user_id',
                'user_activities' => 'user_id',
                'user_favorites' => 'user_id',
                'user_rewards' => 'user_id',
            ];
            
            foreach ($tables as $table => $column) {
                $count = DB::table($table)->where($column, $userId)->count();
                if ($count > 0) {
                    $this->info("   - حذف {$count} سجل من {$table}...");
                    DB::table($table)->where($column, $userId)->delete();
                }
            }
            
            // 2. محاولة إصلاح الجدول
            $this->info('2. محاولة إصلاح جدول users...');
            try {
                DB::statement('ALTER TABLE users ENGINE=InnoDB ROW_FORMAT=DYNAMIC');
                $this->info('   ✓ تم إعادة إنشاء الجدول');
            } catch (\Exception $e) {
                $this->warn('   ✗ فشل إعادة إنشاء الجدول: ' . $e->getMessage());
            }
            
            // 3. حذف المستخدم مباشرة
            $this->info('3. حذف المستخدم...');
            
            $deleted = false;
            $methods = [
                'DB::table()->delete()' => function() use ($userId) {
                    return DB::table('users')->where('id', $userId)->delete();
                },
                'SQL مباشر' => function() use ($userId) {
                    DB::statement("DELETE FROM `users` WHERE `id` = {$userId}");
                    return true;
                },
                'PDO exec' => function() use ($userId) {
                    $connection = DB::connection();
                    $connection->getPdo()->exec("DELETE FROM `users` WHERE `id` = {$userId}");
                    return true;
                },
            ];
            
            foreach ($methods as $methodName => $method) {
                try {
                    $this->info("   - جاري المحاولة باستخدام: {$methodName}...");
                    $method();
                    $deleted = true;
                    $this->info("   ✓ نجح الحذف باستخدام: {$methodName}");
                    break;
                } catch (\Exception $e) {
                    $this->warn("   ✗ فشل: {$e->getMessage()}");
                    continue;
                }
            }
            
            DB::statement('SET UNIQUE_CHECKS=1');
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            if ($deleted) {
                $this->info('');
                $this->info('✓ تم حذف المستخدم بنجاح!');
                return Command::SUCCESS;
            } else {
                $this->error('');
                $this->error('✗ فشل حذف المستخدم بعد محاولة جميع الطرق.');
                $this->error('');
                $this->error('الحلول المقترحة:');
                $this->error('1. أعد تشغيل MySQL');
                $this->error('2. شغّل: php artisan db:fix-users-innodb');
                $this->error('3. أو استخدم: ALTER TABLE users DISCARD TABLESPACE ثم IMPORT TABLESPACE');
                return Command::FAILURE;
            }
            
        } catch (\Exception $e) {
            $this->error('حدث خطأ: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
