-- ===============================================
-- FINAL DATABASE UPDATES FOR EXISTING TTPHRM DATABASE
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

-- Add nic column (if not exists)
ALTER TABLE employees
ADD COLUMN nic VARCHAR(50) NULL COMMENT 'National Identity Card';

-- Add nic_expiry column (if not exists)
ALTER TABLE employees
ADD COLUMN nic_expiry VARCHAR(10) NULL COMMENT 'NIC Expiry Date (dd-mm-yyyy format)';

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

-- 6. SAMPLE ATTENDANCE DATA FOR EMPLOYEE ID 65 (MAY 2025 - EXCLUDING SUNDAYS)
-- ---------------------------------------------------------------------------
-- Note: This will only insert if employee ID 65 exists and is active

INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status)
SELECT * FROM (
    SELECT 65, '2025-05-01', '08:00:00', '17:15:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:15', '09:15', '00:45', 'Present'
    UNION ALL
    SELECT 65, '2025-05-02', '08:10:00', '17:30:00', '127.0.0.1', '127.0.0.1', 0, '00:10', '00:00', '00:30', '09:20', '00:40', 'Present'
    UNION ALL
    SELECT 65, '2025-05-03', '08:00:00', '13:00:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '04:15', '00:00', '05:00', '00:00', 'Half Day'
    UNION ALL
    SELECT 65, '2025-05-05', '08:00:00', '17:00:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:15', '00:00', '09:00', '01:00', 'Present'
    UNION ALL
    SELECT 65, '2025-05-06', '08:15:00', '17:45:00', '127.0.0.1', '127.0.0.1', 0, '00:15', '00:00', '00:45', '09:30', '00:30', 'Present'
    UNION ALL
    SELECT 65, '2025-05-07', '08:00:00', '17:00:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:15', '00:00', '09:00', '01:00', 'Present'
    UNION ALL
    SELECT 65, '2025-05-08', '07:45:00', '18:00:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '01:15', '10:15', '00:00', 'Present'
    UNION ALL
    SELECT 65, '2025-05-09', '08:30:00', '17:00:00', '127.0.0.1', '127.0.0.1', 0, '00:30', '00:15', '00:00', '08:30', '01:30', 'Present'
    UNION ALL
    SELECT 65, '2025-05-10', '08:00:00', '14:00:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '03:15', '00:00', '06:00', '00:00', 'Half Day'
    UNION ALL
    SELECT 65, '2025-05-12', '08:00:00', '16:30:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:45', '00:00', '08:30', '01:30', 'Early Leave'
    UNION ALL
    SELECT 65, '2025-05-13', '08:05:00', '17:20:00', '127.0.0.1', '127.0.0.1', 0, '00:05', '00:00', '00:20', '09:15', '00:45', 'Present'
    UNION ALL
    SELECT 65, '2025-05-14', '08:00:00', '17:15:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:15', '09:15', '00:45', 'Present'
    UNION ALL
    SELECT 65, '2025-05-15', '08:20:00', '18:30:00', '127.0.0.1', '127.0.0.1', 0, '00:20', '00:00', '01:30', '10:10', '00:00', 'Present'
    UNION ALL
    SELECT 65, '2025-05-16', '08:00:00', '13:00:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '04:15', '00:00', '05:00', '00:00', 'Half Day'
    UNION ALL
    SELECT 65, '2025-05-17', '08:00:00', '17:00:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:15', '00:00', '09:00', '01:00', 'Present'
    UNION ALL
    SELECT 65, '2025-05-19', '08:00:00', '19:00:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '02:00', '11:00', '00:00', 'Present'
    UNION ALL
    SELECT 65, '2025-05-20', '08:45:00', '17:00:00', '127.0.0.1', '127.0.0.1', 0, '00:45', '00:15', '00:00', '08:15', '01:45', 'Present'
    UNION ALL
    SELECT 65, '2025-05-21', '08:00:00', '17:30:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:30', '09:30', '00:30', 'Present'
    UNION ALL
    SELECT 65, '2025-05-22', '08:10:00', '17:10:00', '127.0.0.1', '127.0.0.1', 0, '00:10', '00:05', '00:00', '09:00', '01:00', 'Present'
    UNION ALL
    SELECT 65, '2025-05-23', '08:00:00', '12:30:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '04:45', '00:00', '04:30', '00:00', 'Half Day'
    UNION ALL
    SELECT 65, '2025-05-24', '08:00:00', '17:00:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:15', '00:00', '09:00', '01:00', 'Present'
    UNION ALL
    SELECT 65, '2025-05-26', '07:50:00', '18:00:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '01:10', '10:10', '00:00', 'Present'
    UNION ALL
    SELECT 65, '2025-05-27', '08:15:00', '17:00:00', '127.0.0.1', '127.0.0.1', 0, '00:15', '00:15', '00:00', '08:45', '01:15', 'Present'
    UNION ALL
    SELECT 65, '2025-05-28', '08:00:00', '17:15:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:15', '09:15', '00:45', 'Present'
    UNION ALL
    SELECT 65, '2025-05-29', '08:00:00', '18:30:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '01:30', '10:30', '00:00', 'Present'
    UNION ALL
    SELECT 65, '2025-05-30', '08:05:00', '17:00:00', '127.0.0.1', '127.0.0.1', 0, '00:05', '00:15', '00:00', '08:55', '01:05', 'Present'
    UNION ALL
    SELECT 65, '2025-05-31', '08:00:00', '17:00:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:15', '00:00', '09:00', '01:00', 'Present'
) AS sample_data
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1);

-- 7. VERIFICATION QUERIES
-- -----------------------

-- Check employees table structure
DESCRIBE employees;

-- Count records
SELECT
    (SELECT COUNT(*) FROM employees) as total_employees,
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM attendances WHERE employee_id = 65) as employee_65_attendance_records;

-- Show attendance summary for employee 65
SELECT
    COUNT(*) as total_days,
    SUM(CASE WHEN attendance_status = 'Present' THEN 1 ELSE 0 END) as present_days,
    SUM(CASE WHEN attendance_status = 'Half Day' THEN 1 ELSE 0 END) as half_days,
    SUM(CASE WHEN attendance_status = 'Early Leave' THEN 1 ELSE 0 END) as early_leave_days
FROM attendances
WHERE employee_id = 65
AND attendance_date BETWEEN '2025-05-01' AND '2025-05-31';