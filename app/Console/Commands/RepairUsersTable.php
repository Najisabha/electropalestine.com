<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairUsersTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:repair-users-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إصلاح جدول users في حالة وجود مشاكل في tablespace';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('جارٍ فحص جدول users...');

        try {
            // فحص الجدول أولاً
            $checkResult = DB::select("CHECK TABLE users");
            $this->info('نتيجة فحص الجدول:');
            $this->line(json_encode($checkResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // محاولة إصلاح الجدول
            $this->info('جارٍ إصلاح الجدول...');
            try {
                $repairResult = DB::select("REPAIR TABLE users");
                $this->info('نتيجة الإصلاح:');
                $this->line(json_encode($repairResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } catch (\Exception $e) {
                $this->warn('فشل REPAIR TABLE: ' . $e->getMessage());
            }

            // محاولة تحسين الجدول (إعادة بناء)
            $this->info('جارٍ تحسين الجدول (إعادة بناء)...');
            try {
                $optimizeResult = DB::select("OPTIMIZE TABLE users");
                $this->info('نتيجة التحسين:');
                $this->line(json_encode($optimizeResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } catch (\Exception $e) {
                $this->warn('فشل OPTIMIZE TABLE: ' . $e->getMessage());
            }

            // محاولة إعادة إنشاء الجدول إذا كان ضرورياً
            $this->info('جارٍ محاولة إعادة إنشاء الجدول...');
            try {
                // الحصول على بنية الجدول
                $tableStructure = DB::select("SHOW CREATE TABLE users");
                if (!empty($tableStructure)) {
                    $createTable = $tableStructure[0]->{'Create Table'};
                    
                    // محاولة إعادة إنشاء الجدول
                    DB::statement("ALTER TABLE users ENGINE=InnoDB");
                    $this->info('تم إعادة إنشاء الجدول بنجاح!');
                }
            } catch (\Exception $e) {
                $this->warn('فشل إعادة إنشاء الجدول: ' . $e->getMessage());
            }

            // فحص نهائي
            $this->info('جارٍ فحص نهائي للجدول...');
            $finalCheck = DB::select("CHECK TABLE users");
            $this->info('نتيجة الفحص النهائي:');
            $this->line(json_encode($finalCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $this->info('تم إصلاح الجدول بنجاح!');
            $this->info('يرجى المحاولة مرة أخرى لحذف المستخدم.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('حدث خطأ أثناء إصلاح الجدول: ' . $e->getMessage());
            $this->error('');
            $this->error('الحلول البديلة:');
            $this->error('1. قم بتشغيل الأوامر التالية في MySQL مباشرة:');
            $this->error('   REPAIR TABLE users;');
            $this->error('   OPTIMIZE TABLE users;');
            $this->error('   ALTER TABLE users ENGINE=InnoDB;');
            $this->error('');
            $this->error('2. أو قم بعمل نسخة احتياطية من البيانات وأعد إنشاء الجدول.');
            return Command::FAILURE;
        }
    }
}
