<?php

namespace App\Console\Commands;

use App\Helpers\ImageHelper;
use Illuminate\Console\Command;

class SyncStorageToPublic extends Command
{
    protected $signature = 'storage:sync-public {directory? : المجلد المحدد (مثل categories) - فارغ لنسخ الكل}';
    protected $description = 'نسخ جميع الملفات من storage/app/public إلى public/storage';

    public function handle()
    {
        $directory = $this->argument('directory');
        
        $this->info('🔄 بدء مزامنة الملفات من storage/app/public إلى public/storage...');
        
        if ($directory) {
            $this->info("📁 المزامنة للمجلد: {$directory}");
        } else {
            $this->info('📁 المزامنة لجميع المجلدات');
        }
        
        $results = ImageHelper::syncToPublicStorage($directory);
        
        $this->newLine();
        $this->info("✅ تم بنجاح: {$results['success']} ملف");
        $this->comment("⏭️  تم تخطي: {$results['skipped']} ملف (موجود بالفعل)");
        
        if ($results['failed'] > 0) {
            $this->error("❌ فشل: {$results['failed']} ملف");
            foreach ($results['errors'] as $error) {
                $this->error("   - {$error}");
            }
        }
        
        $this->newLine();
        $this->info('✨ تمت المزامنة بنجاح!');
        
        return 0;
    }
}
