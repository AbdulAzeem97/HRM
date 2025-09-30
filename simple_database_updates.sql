-- ===============================================
-- SIMPLE DATABASE UPDATES FOR EXISTING TTPHRM DATABASE
-- Execute these one by one in phpMyAdmin
-- ===============================================

USE `u902429527_ttphrm`;

-- 1. ADD MISSING COLUMNS TO EMPLOYEES TABLE
-- -----------------------------------------

-- Add is_labor_employee column (if not exists)
ALTER TABLE employees
ADD COLUMN is_labor_employee TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Labor/Contract employee flag';

-- Add overtime_allowed column (if not exists)
ALTER TABLE employees
ADD COLUMN overtime_allowed TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Overtime eligibility';

-- Add required_hours_per_day column (if not exists)
ALTER TABLE employees
ADD COLUMN required_hours_per_day INT(11) NOT NULL DEFAULT 9 COMMENT 'Required working hours per day';


-- 2. MODIFY DATE COLUMNS TO VARCHAR (if needed)
-- --------------------------------------------

-- Change date columns to VARCHAR for dd-mm-yyyy format
ALTER TABLE employees MODIFY COLUMN date_of_birth VARCHAR(10) NULL COMMENT 'Date format: dd-mm-yyyy';
ALTER TABLE employees MODIFY COLUMN joining_date VARCHAR(10) NULL COMMENT 'Date format: dd-mm-yyyy';
ALTER TABLE employees MODIFY COLUMN exit_date VARCHAR(10) NULL COMMENT 'Date format: dd-mm-yyyy';

-- 3. CREATE MISSING SUPPORT TABLES
-- --------------------------------

-- Company Types
CREATE TABLE IF NOT EXISTS company_types (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(191) NOT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
);

-- Loan Types
CREATE TABLE IF NOT EXISTS loan_types (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(191) NOT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
);

-- Relation Types
CREATE TABLE IF NOT EXISTS relation_types (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(191) NOT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
);

-- Deduction Types
CREATE TABLE IF NOT EXISTS deduction_types (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(191) NOT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
);

-- Job Experiences
CREATE TABLE IF NOT EXISTS job_experiences (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(191) NOT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
);

-- 4. INSERT DEFAULT DATA
-- ----------------------

-- Company Types
INSERT IGNORE INTO company_types (id, name, created_at, updated_at) VALUES
(1, 'Private Limited', NOW(), NOW()),
(2, 'Public Limited', NOW(), NOW()),
(3, 'Partnership', NOW(), NOW()),
(4, 'Sole Proprietorship', NOW(), NOW());

-- Loan Types
INSERT IGNORE INTO loan_types (id, name, created_at, updated_at) VALUES
(1, 'Personal Loan', NOW(), NOW()),
(2, 'Advance Salary', NOW(), NOW()),
(3, 'Emergency Loan', NOW(), NOW());

-- Relation Types
INSERT IGNORE INTO relation_types (id, name, created_at, updated_at) VALUES
(1, 'Father', NOW(), NOW()),
(2, 'Mother', NOW(), NOW()),
(3, 'Spouse', NOW(), NOW()),
(4, 'Brother', NOW(), NOW()),
(5, 'Sister', NOW(), NOW()),
(6, 'Emergency Contact', NOW(), NOW());

-- Deduction Types
INSERT IGNORE INTO deduction_types (id, name, created_at, updated_at) VALUES
(1, 'Tax Deduction', NOW(), NOW()),
(2, 'Insurance Premium', NOW(), NOW()),
(3, 'Loan Deduction', NOW(), NOW()),
(4, 'Late Coming', NOW(), NOW()),
(5, 'Absence', NOW(), NOW());

-- Job Experiences
INSERT IGNORE INTO job_experiences (id, name, created_at, updated_at) VALUES
(1, 'Fresher', NOW(), NOW()),
(2, '1-2 Years', NOW(), NOW()),
(3, '3-5 Years', NOW(), NOW()),
(4, '5-10 Years', NOW(), NOW()),
(5, '10+ Years', NOW(), NOW());

-- Enhanced Status Options
INSERT IGNORE INTO statuses (id, name, class, created_at, updated_at) VALUES
(1, 'Active', 'bg-success', NOW(), NOW()),
(2, 'Inactive', 'bg-danger', NOW(), NOW()),
(3, 'Suspended', 'bg-warning', NOW(), NOW()),
(4, 'Terminated', 'bg-dark', NOW(), NOW()),
(5, 'On Leave', 'bg-info', NOW(), NOW()),
(6, 'Probation', 'bg-secondary', NOW(), NOW());

-- 5. ADD FOREIGN KEY COLUMNS (if tables exist)
-- --------------------------------------------

-- Add company_type_id to companies table
ALTER TABLE companies ADD COLUMN company_type_id BIGINT(20) UNSIGNED NULL;

-- 6. VERIFICATION QUERIES
-- -----------------------

-- Check employees table structure
DESCRIBE employees;

-- Count records
SELECT
    (SELECT COUNT(*) FROM employees) as total_employees,
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM attendances) as total_attendance_records;