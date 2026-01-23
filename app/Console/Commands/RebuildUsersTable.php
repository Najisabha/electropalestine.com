<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RebuildUsersTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:rebuild-users-table {--force : Force rebuild without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إعادة إنشاء جدول users بالكامل (يحتاج إلى تأكيد)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('هل أنت متأكد من إعادة إنشاء جدول users؟ سيتم حذف جميع البيانات!', false)) {
                $this->info('تم إلغاء العملية.');
                return Command::FAILURE;
            }
        }

        $this->warn('تحذير: هذه العملية خطيرة وقد تفقد البيانات!');
        $this->info('جارٍ إعادة إنشاء الجدول...');

        try {
            // حفظ عدد المستخدمين الحالي
            $userCount = DB::table('users')->count();
            $this->info("عدد المستخدمين الحالي: {$userCount}");

            // محاولة إعادة إنشاء الجدول
            DB::statement('ALTER TABLE users ENGINE=InnoDB ROW_FORMAT=DYNAMIC');

            $this->info('تم إعادة إنشاء الجدول بنجاح!');
            $this->info("عدد المستخدمين بعد الإعادة: " . DB::table('users')->count());
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('حدث خطأ أثناء إعادة إنشاء الجدول: ' . $e->getMessage());
            $this->error('');
            $this->error('يرجى إعادة إنشاء الجدول يدوياً من MySQL:');
            $this->error('ALTER TABLE users ENGINE=InnoDB ROW_FORMAT=DYNAMIC;');
            
            return Command::FAILURE;
        }
    }
}
