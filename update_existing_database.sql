-- ===============================================
-- TTPHRM Database Update Script
-- Updates existing database structure to match current project requirements
-- ===============================================

USE `u902429527_ttphrm`;

-- ===============================================
-- 1. ADD MISSING COLUMNS TO EMPLOYEES TABLE
-- ===============================================

-- Check and add is_labor_employee column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'employees'
AND COLUMN_NAME = 'is_labor_employee';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE employees ADD COLUMN is_labor_employee TINYINT(1) NOT NULL DEFAULT 0 COMMENT "Labor/Contract employee flag" AFTER is_active;',
    'SELECT "Column is_labor_employee already exists" as message;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add overtime_allowed column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'employees'
AND COLUMN_NAME = 'overtime_allowed';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE employees ADD COLUMN overtime_allowed TINYINT(1) NOT NULL DEFAULT 1 COMMENT "Overtime eligibility" AFTER is_labor_employee;',
    'SELECT "Column overtime_allowed already exists" as message;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add required_hours_per_day column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'employees'
AND COLUMN_NAME = 'required_hours_per_day';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE employees ADD COLUMN required_hours_per_day INT(11) NOT NULL DEFAULT 9 COMMENT "Required working hours per day" AFTER overtime_allowed;',
    'SELECT "Column required_hours_per_day already exists" as message;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add nic column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'employees'
AND COLUMN_NAME = 'nic';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE employees ADD COLUMN nic VARCHAR(50) NULL COMMENT "National Identity Card" AFTER country;',
    'SELECT "Column nic already exists" as message;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add nic_expiry column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'employees'
AND COLUMN_NAME = 'nic_expiry';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE employees ADD COLUMN nic_expiry VARCHAR(10) NULL COMMENT "NIC Expiry Date (dd-mm-yyyy format)" AFTER nic;',
    'SELECT "Column nic_expiry already exists" as message;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ===============================================
-- 2. MODIFY DATE COLUMNS TO VARCHAR (if they are DATE type)
-- ===============================================

-- Check if date_of_birth is DATE type and convert to VARCHAR
SET @col_type = '';
SELECT DATA_TYPE INTO @col_type
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'employees'
AND COLUMN_NAME = 'date_of_birth';

SET @sql = IF(@col_type = 'date',
    'ALTER TABLE employees MODIFY COLUMN date_of_birth VARCHAR(10) NULL COMMENT "Date format: dd-mm-yyyy";',
    'SELECT "Column date_of_birth already VARCHAR or does not exist" as message;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if joining_date is DATE type and convert to VARCHAR
SET @col_type = '';
SELECT DATA_TYPE INTO @col_type
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'employees'
AND COLUMN_NAME = 'joining_date';

SET @sql = IF(@col_type = 'date',
    'ALTER TABLE employees MODIFY COLUMN joining_date VARCHAR(10) NULL COMMENT "Date format: dd-mm-yyyy";',
    'SELECT "Column joining_date already VARCHAR or does not exist" as message;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if exit_date is DATE type and convert to VARCHAR
SET @col_type = '';
SELECT DATA_TYPE INTO @col_type
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'employees'
AND COLUMN_NAME = 'exit_date';

SET @sql = IF(@col_type = 'date',
    'ALTER TABLE employees MODIFY COLUMN exit_date VARCHAR(10) NULL COMMENT "Date format: dd-mm-yyyy";',
    'SELECT "Column exit_date already VARCHAR or does not exist" as message;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ===============================================
-- 3. CREATE MISSING TABLES (if they don't exist)
-- ===============================================

-- Create company_types table if not exists
CREATE TABLE IF NOT EXISTS `company_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create loan_types table if not exists
CREATE TABLE IF NOT EXISTS `loan_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create relation_types table if not exists
CREATE TABLE IF NOT EXISTS `relation_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create deduction_types table if not exists
CREATE TABLE IF NOT EXISTS `deduction_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create deposit_categories table if not exists
CREATE TABLE IF NOT EXISTS `deposit_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create job_experiences table if not exists
CREATE TABLE IF NOT EXISTS `job_experiences` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 4. ADD FOREIGN KEY COLUMNS TO EXISTING TABLES (if missing)
-- ===============================================

-- Add company_type_id to companies table if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'companies'
AND COLUMN_NAME = 'company_type_id';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE companies ADD COLUMN company_type_id BIGINT(20) UNSIGNED NULL AFTER logo;',
    'SELECT "Column company_type_id already exists" as message;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add relation_type_id to employee_contacts table if exists
SET @table_exists = 0;
SELECT COUNT(*) INTO @table_exists
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'employee_contacts';

SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'employee_contacts'
AND COLUMN_NAME = 'relation_type_id';

SET @sql = IF(@table_exists > 0 AND @col_exists = 0,
    'ALTER TABLE employee_contacts ADD COLUMN relation_type_id BIGINT(20) UNSIGNED NULL;',
    'SELECT "Table employee_contacts does not exist or column relation_type_id already exists" as message;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add loan_type_id to salary_loans table if exists
SET @table_exists = 0;
SELECT COUNT(*) INTO @table_exists
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'salary_loans';

SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'salary_loans'
AND COLUMN_NAME = 'loan_type_id';

SET @sql = IF(@table_exists > 0 AND @col_exists = 0,
    'ALTER TABLE salary_loans ADD COLUMN loan_type_id BIGINT(20) UNSIGNED NULL;',
    'SELECT "Table salary_loans does not exist or column loan_type_id already exists" as message;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add deduction_type_id to salary_loans table if exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'salary_loans'
AND COLUMN_NAME = 'deduction_type_id';

SET @sql = IF(@table_exists > 0 AND @col_exists = 0,
    'ALTER TABLE salary_loans ADD COLUMN deduction_type_id BIGINT(20) UNSIGNED NULL;',
    'SELECT "Table salary_loans does not exist or column deduction_type_id already exists" as message;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ===============================================
-- 5. INSERT DEFAULT DATA (if tables are empty)
-- ===============================================

-- Insert default company types if table is empty
INSERT IGNORE INTO company_types (id, name, created_at, updated_at) VALUES
(1, 'Private Limited', NOW(), NOW()),
(2, 'Public Limited', NOW(), NOW()),
(3, 'Partnership', NOW(), NOW()),
(4, 'Sole Proprietorship', NOW(), NOW());

-- Insert default loan types if table is empty
INSERT IGNORE INTO loan_types (id, name, created_at, updated_at) VALUES
(1, 'Personal Loan', NOW(), NOW()),
(2, 'Advance Salary', NOW(), NOW()),
(3, 'Emergency Loan', NOW(), NOW());

-- Insert default relation types if table is empty
INSERT IGNORE INTO relation_types (id, name, created_at, updated_at) VALUES
(1, 'Father', NOW(), NOW()),
(2, 'Mother', NOW(), NOW()),
(3, 'Spouse', NOW(), NOW()),
(4, 'Brother', NOW(), NOW()),
(5, 'Sister', NOW(), NOW()),
(6, 'Emergency Contact', NOW(), NOW());

-- Insert default deduction types if table is empty
INSERT IGNORE INTO deduction_types (id, name, created_at, updated_at) VALUES
(1, 'Tax Deduction', NOW(), NOW()),
(2, 'Insurance Premium', NOW(), NOW()),
(3, 'Loan Deduction', NOW(), NOW()),
(4, 'Late Coming', NOW(), NOW()),
(5, 'Absence', NOW(), NOW());

-- Insert default deposit categories if table is empty
INSERT IGNORE INTO deposit_categories (id, name, created_at, updated_at) VALUES
(1, 'Office Rent', NOW(), NOW()),
(2, 'Security Deposit', NOW(), NOW()),
(3, 'Utility Deposit', NOW(), NOW());

-- Insert default job experiences if table is empty
INSERT IGNORE INTO job_experiences (id, name, created_at, updated_at) VALUES
(1, 'Fresher', NOW(), NOW()),
(2, '1-2 Years', NOW(), NOW()),
(3, '3-5 Years', NOW(), NOW()),
(4, '5-10 Years', NOW(), NOW()),
(5, '10+ Years', NOW(), NOW());

-- Insert default status options if statuses table exists and is empty
INSERT IGNORE INTO statuses (id, name, class, created_at, updated_at) VALUES
(1, 'Active', 'bg-success', NOW(), NOW()),
(2, 'Inactive', 'bg-danger', NOW(), NOW()),
(3, 'Suspended', 'bg-warning', NOW(), NOW()),
(4, 'Terminated', 'bg-dark', NOW(), NOW()),
(5, 'On Leave', 'bg-info', NOW(), NOW()),
(6, 'Probation', 'bg-secondary', NOW(), NOW());

-- ===============================================
-- 6. UPDATE SALARY_COMMISSIONS DATE COLUMN (if exists)
-- ===============================================

SET @table_exists = 0;
SELECT COUNT(*) INTO @table_exists
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'salary_commissions';

SET @col_type = '';
SELECT DATA_TYPE INTO @col_type
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'salary_commissions'
AND COLUMN_NAME = 'date';

SET @sql = IF(@table_exists > 0 AND @col_type = 'date',
    'ALTER TABLE salary_commissions MODIFY COLUMN date VARCHAR(10) NULL COMMENT "Date format: dd-mm-yyyy";',
    'SELECT "Table salary_commissions does not exist or date column already VARCHAR" as message;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ===============================================
-- VERIFICATION QUERY
-- ===============================================

SELECT
    'Database update completed!' as message,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'u902429527_ttphrm') as total_tables,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'u902429527_ttphrm' AND TABLE_NAME = 'employees') as employee_columns;

-- Show updated employees table structure
SELECT
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u902429527_ttphrm'
AND TABLE_NAME = 'employees'
ORDER BY ORDINAL_POSITION;