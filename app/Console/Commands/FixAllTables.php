<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixAllTables extends Command
{
    protected $signature = 'db:fix-all-tables {--force : Force fix without confirmation}';
    protected $description = 'إصلاح جميع الجداول في قاعدة البيانات';

    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('هل أنت متأكد من إصلاح جميع الجداول؟', true)) {
                $this->info('تم إلغاء العملية.');
                return Command::FAILURE;
            }
        }

        $this->info('جارٍ إصلاح جميع الجداول...');

        try {
            $database = env('DB_DATABASE', 'electropalestine');
            
            // الحصول على قائمة جميع الجداول
            $tables = DB::select("SHOW TABLES");
            $tableKey = "Tables_in_{$database}";
            
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                $this->info("إصلاح جدول: {$tableName}");
                
                try {
                    // إعادة إنشاء الجدول
                    DB::statement("ALTER TABLE `{$tableName}` ENGINE=InnoDB ROW_FORMAT=DYNAMIC");
                    $this->info("  ✓ تم إصلاح {$tableName}");
                } catch (\Exception $e) {
                    $this->warn("  ✗ فشل إصلاح {$tableName}: " . $e->getMessage());
                }
            }
            
            $this->info('');
            $this->info('تحسين جميع الجداول...');
            
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                try {
                    DB::statement("OPTIMIZE TABLE `{$tableName}`");
                } catch (\Exception $e) {}
            }
            
            $this->info('');
            $this->info('✓ تم إصلاح جميع الجداول بنجاح!');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('حدث خطأ: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
