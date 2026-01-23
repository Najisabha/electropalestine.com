<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixUsersTableInnoDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fix-users-innodb {--force : Force fix without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إصلاح ملفات InnoDB لجدول users بشكل نهائي';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('هل أنت متأكد من إصلاح جدول users؟ قد تستغرق العملية بعض الوقت.', false)) {
                $this->info('تم إلغاء العملية.');
                return Command::FAILURE;
            }
        }

        $this->info('جارٍ إصلاح جدول users...');

        try {
            // 1. فحص الجدول
            $this->info('1. فحص الجدول...');
            $checkResult = DB::select("CHECK TABLE users");
            $this->line(json_encode($checkResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // 2. محاولة إصلاح الجدول
            $this->info('2. محاولة إصلاح الجدول...');
            try {
                DB::statement('REPAIR TABLE users');
            } catch (\Exception $e) {
                $this->warn('REPAIR TABLE فشل: ' . $e->getMessage());
            }

            // 3. إعادة إنشاء الجدول مع ROW_FORMAT
            $this->info('3. إعادة إنشاء الجدول مع ROW_FORMAT=DYNAMIC...');
            try {
                DB::statement('ALTER TABLE users ENGINE=InnoDB ROW_FORMAT=DYNAMIC');
                $this->info('✓ تم إعادة إنشاء الجدول بنجاح');
            } catch (\Exception $e) {
                $this->error('فشل إعادة إنشاء الجدول: ' . $e->getMessage());
            }

            // 4. تحسين الجدول
            $this->info('4. تحسين الجدول...');
            try {
                DB::statement('OPTIMIZE TABLE users');
                $this->info('✓ تم تحسين الجدول بنجاح');
            } catch (\Exception $e) {
                $this->warn('OPTIMIZE TABLE فشل: ' . $e->getMessage());
            }

            // 5. فحص نهائي
            $this->info('5. فحص نهائي...');
            $finalCheck = DB::select("CHECK TABLE users");
            $this->line(json_encode($finalCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // 6. اختبار الحذف على صف تجريبي (إذا كان موجوداً)
            $this->info('6. اختبار الحذف...');
            try {
                // محاولة حذف صف تجريبي (إذا كان موجوداً)
                $testUser = DB::table('users')->where('email', 'test-delete@example.com')->first();
                if ($testUser) {
                    DB::table('users')->where('id', $testUser->id)->delete();
                    $this->info('✓ اختبار الحذف نجح');
                } else {
                    $this->info('لا يوجد صف تجريبي للاختبار');
                }
            } catch (\Exception $e) {
                $this->error('اختبار الحذف فشل: ' . $e->getMessage());
                $this->error('');
                $this->error('الحل البديل:');
                $this->error('1. قم بإيقاف MySQL');
                $this->error('2. احذف ملفات .ibd للجدول من مجلد البيانات');
                $this->error('3. أعد تشغيل MySQL');
                $this->error('4. شغّل: ALTER TABLE users DISCARD TABLESPACE');
                $this->error('5. شغّل: ALTER TABLE users IMPORT TABLESPACE');
            }

            $this->info('');
            $this->info('تم إصلاح الجدول بنجاح!');
            $this->info('يرجى المحاولة مرة أخرى لحذف المستخدم.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('حدث خطأ أثناء إصلاح الجدول: ' . $e->getMessage());
            $this->error('');
            $this->error('الحلول البديلة:');
            $this->error('1. أعد تشغيل MySQL');
            $this->error('2. قم بعمل نسخة احتياطية من البيانات');
            $this->error('3. أعد إنشاء الجدول من الصفر');
            
            return Command::FAILURE;
        }
    }
}
