<?php

namespace App\Console\Commands;

use App\Http\Controllers\SitemapController;
use Illuminate\Console\Command;

class ClearSitemapCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the sitemap cache';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Clearing sitemap cache...');
        
        SitemapController::clearCache();
        
        $this->info('Sitemap cache cleared successfully!');
        
        return Command::SUCCESS;
    }
}
