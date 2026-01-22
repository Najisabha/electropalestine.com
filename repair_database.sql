-- Database Repair Script for user_addresses table
-- Run this script in MySQL to repair the corrupted tablespace

-- Step 1: Check if the table exists and its status
SHOW TABLE STATUS LIKE 'user_addresses';

-- Step 2: Try to repair the table (for MyISAM - won't work for InnoDB)
-- REPAIR TABLE user_addresses;

-- Step 3: For InnoDB tables, we need to recreate the table
-- First, create a backup of the data (if any exists)
CREATE TABLE IF NOT EXISTS user_addresses_backup AS SELECT * FROM user_addresses;

-- Step 4: Drop the corrupted table
DROP TABLE IF EXISTS user_addresses;

-- Step 5: Recreate the table using the migration structure
CREATE TABLE user_addresses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    city VARCHAR(255) NULL,
    governorate VARCHAR(255) NULL,
    zip_code VARCHAR(255) NULL,
    country_code VARCHAR(10) NULL,
    phone VARCHAR(255) NOT NULL,
    street VARCHAR(255) NULL,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 6: Restore data from backup (if backup was created)
-- INSERT INTO user_addresses SELECT * FROM user_addresses_backup;

-- Step 7: Drop the backup table (after verifying data is restored)
-- DROP TABLE IF EXISTS user_addresses_backup;
