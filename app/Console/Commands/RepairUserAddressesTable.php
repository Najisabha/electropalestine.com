<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RepairUserAddressesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:repair-user-addresses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Repair the corrupted user_addresses table by recreating it';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting repair of user_addresses table...');

        try {
            // Check if table exists
            if (!Schema::hasTable('user_addresses')) {
                $this->warn('Table user_addresses does not exist. Running migration...');
                $this->call('migrate', ['--path' => 'database/migrations/2026_01_07_090000_create_user_addresses_table.php']);
                $this->info('Table created successfully!');
                return 0;
            }

            // Try to backup data first
            $this->info('Attempting to backup existing data...');
            $backupCount = 0;
            try {
                $addresses = DB::table('user_addresses')->get();
                $backupCount = $addresses->count();
                
                if ($backupCount > 0) {
                    // Create backup table
                    DB::statement('CREATE TABLE IF NOT EXISTS user_addresses_backup AS SELECT * FROM user_addresses');
                    $this->info("Backed up {$backupCount} addresses to user_addresses_backup table.");
                } else {
                    $this->info('No existing data to backup.');
                }
            } catch (\Exception $e) {
                $this->warn('Could not backup data (table may be corrupted): ' . $e->getMessage());
                $this->warn('Continuing with table recreation...');
            }

            // Drop the corrupted table
            $this->info('Dropping corrupted table...');
            Schema::dropIfExists('user_addresses');

            // Recreate the table using migration
            $this->info('Recreating table...');
            $this->call('migrate', ['--path' => 'database/migrations/2026_01_07_090000_create_user_addresses_table.php']);

            // Restore data if backup exists
            if ($backupCount > 0) {
                $this->info('Restoring data from backup...');
                try {
                    $backupData = DB::table('user_addresses_backup')->get();
                    foreach ($backupData as $address) {
                        DB::table('user_addresses')->insert((array) $address);
                    }
                    $this->info("Restored {$backupCount} addresses successfully.");
                    
                    // Ask if user wants to drop backup table
                    if ($this->confirm('Do you want to drop the backup table?', true)) {
                        Schema::dropIfExists('user_addresses_backup');
                        $this->info('Backup table dropped.');
                    }
                } catch (\Exception $e) {
                    $this->error('Error restoring data: ' . $e->getMessage());
                    $this->warn('Backup table user_addresses_backup still exists. You can manually restore data if needed.');
                }
            }

            $this->info('✅ Table repair completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('Error repairing table: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
