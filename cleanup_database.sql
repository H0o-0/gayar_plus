-- SQL commands to clear data from specified tables

-- Disable foreign key checks to avoid errors during truncation
SET FOREIGN_KEY_CHECKS = 0;

-- Truncate the 'series' table to delete all its data
TRUNCATE TABLE `series`;

-- Truncate the 'models' table to delete all its data
TRUNCATE TABLE `models`;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Note: The 'brands' table is intentionally left untouched as per requirements.
