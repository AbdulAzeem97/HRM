-- ===============================================
-- TTPHRM Complete Database Structure Script
-- Laravel Project Database Reconstruction
-- ===============================================

-- Drop existing database and recreate
DROP DATABASE IF EXISTS `u902429527_ttphrm`;
CREATE DATABASE `u902429527_ttphrm` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `u902429527_ttphrm`;

-- ===============================================
-- 1. CORE SYSTEM TABLES
-- ===============================================

-- Countries Table
CREATE TABLE `countries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles Table (Spatie Permissions)
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permissions Table (Spatie Permissions)
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Role Has Permissions
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Model Has Permissions
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Model Has Roles
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users Table
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_bg` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_users_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `contact_no` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_login_ip` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_date` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_role_users_id_foreign` (`role_users_id`),
  CONSTRAINT `users_role_users_id_foreign` FOREIGN KEY (`role_users_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 2. ORGANIZATIONAL STRUCTURE TABLES
-- ===============================================

-- Company Types
CREATE TABLE `company_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Companies
CREATE TABLE `companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_type_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `companies_company_type_id_foreign` (`company_type_id`),
  CONSTRAINT `companies_company_type_id_foreign` FOREIGN KEY (`company_type_id`) REFERENCES `company_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Locations
CREATE TABLE `locations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_start_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_end_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `locations_company_id_foreign` (`company_id`),
  CONSTRAINT `locations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Departments
CREATE TABLE `departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_company_id_foreign` (`company_id`),
  CONSTRAINT `departments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Designations
CREATE TABLE `designations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `designation_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `designations_company_id_foreign` (`company_id`),
  CONSTRAINT `designations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Office Shifts
CREATE TABLE `office_shifts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shift_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `office_shifts_company_id_foreign` (`company_id`),
  CONSTRAINT `office_shifts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Statuses
CREATE TABLE `statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 3. EMPLOYEE MANAGEMENT TABLES
-- ===============================================

-- Employees (Main employee table with all discovered fields)
CREATE TABLE `employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `staff_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_no` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Format: dd-mm-yyyy',
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_shift_id` bigint(20) unsigned DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `designation_id` bigint(20) unsigned DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `role_users_id` bigint(20) unsigned DEFAULT NULL,
  `status_id` bigint(20) unsigned DEFAULT NULL,
  `joining_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Format: dd-mm-yyyy',
  `exit_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Format: dd-mm-yyyy',
  `marital_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nic` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nic_expiry` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Format: dd-mm-yyyy',
  `cv` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skype_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fb_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedIn_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `basic_salary` double NOT NULL DEFAULT 0,
  `payslip_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attendance_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pension_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pension_amount` double(8,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT NULL,
  `is_labor_employee` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Labor/Contract employee flag',
  `overtime_allowed` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Overtime eligibility',
  `required_hours_per_day` int(11) NOT NULL DEFAULT 9 COMMENT 'Required working hours per day',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employees_company_id_foreign` (`company_id`),
  KEY `employees_department_id_foreign` (`department_id`),
  KEY `employees_designation_id_foreign` (`designation_id`),
  KEY `employees_location_id_foreign` (`location_id`),
  KEY `employees_office_shift_id_foreign` (`office_shift_id`),
  KEY `employees_role_users_id_foreign` (`role_users_id`),
  KEY `employees_status_id_foreign` (`status_id`),
  CONSTRAINT `employees_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_id_foreign` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_office_shift_id_foreign` FOREIGN KEY (`office_shift_id`) REFERENCES `office_shifts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_role_users_id_foreign` FOREIGN KEY (`role_users_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 4. ATTENDANCE MANAGEMENT TABLES
-- ===============================================

-- Attendances (Employee attendance tracking)
CREATE TABLE `attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `clock_in` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clock_in_ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clock_out` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clock_out_ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clock_in_out` tinyint(4) NOT NULL,
  `time_late` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '00:00',
  `early_leaving` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '00:00',
  `overtime` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '00:00',
  `total_work` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '00:00',
  `total_rest` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '00:00',
  `attendance_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  KEY `attendances_employee_id_foreign` (`employee_id`),
  CONSTRAINT `attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 5. ADDITIONAL SUPPORT TABLES
-- ===============================================

-- Password Resets
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Failed Jobs
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- General Settings
CREATE TABLE `general_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrations Table (Laravel)
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 6. INSERT INITIAL DATA
-- ===============================================

-- Insert Default Roles
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super-admin', 'web', NOW(), NOW()),
(2, 'admin', 'web', NOW(), NOW()),
(3, 'hr', 'web', NOW(), NOW()),
(4, 'employee', 'web', NOW(), NOW());

-- Insert Default Permissions
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'manage-employees', 'web', NOW(), NOW()),
(2, 'manage-attendance', 'web', NOW(), NOW()),
(3, 'manage-payroll', 'web', NOW(), NOW()),
(4, 'view-reports', 'web', NOW(), NOW()),
(5, 'manage-settings', 'web', NOW(), NOW());

-- Insert Default Status Options
INSERT INTO `statuses` (`id`, `name`, `class`, `created_at`, `updated_at`) VALUES
(1, 'Active', 'bg-success', NOW(), NOW()),
(2, 'Inactive', 'bg-danger', NOW(), NOW()),
(3, 'Suspended', 'bg-warning', NOW(), NOW()),
(4, 'Terminated', 'bg-dark', NOW(), NOW());

-- Insert Default Company Type
INSERT INTO `company_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Private Limited', NOW(), NOW()),
(2, 'Public Limited', NOW(), NOW()),
(3, 'Partnership', NOW(), NOW()),
(4, 'Sole Proprietorship', NOW(), NOW());

-- Insert Default Company
INSERT INTO `companies` (`id`, `company_name`, `email`, `contact_no`, `company_type_id`, `created_at`, `updated_at`) VALUES
(1, 'TTPHRM Company', 'admin@ttphrm.com', '+1234567890', 1, NOW(), NOW());

-- Insert Default Department
INSERT INTO `departments` (`id`, `department_name`, `company_id`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Human Resources', 1, 'HR Department', NOW(), NOW()),
(2, 'Information Technology', 1, 'IT Department', NOW(), NOW()),
(3, 'Finance', 1, 'Finance Department', NOW(), NOW());

-- Insert Default Designation
INSERT INTO `designations` (`id`, `designation_name`, `company_id`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Manager', 1, 'Department Manager', NOW(), NOW()),
(2, 'Senior Developer', 1, 'Senior Software Developer', NOW(), NOW()),
(3, 'HR Officer', 1, 'Human Resources Officer', NOW(), NOW());

-- Insert Default Location
INSERT INTO `locations` (`id`, `name`, `company_id`, `address`, `city`, `country`, `created_at`, `updated_at`) VALUES
(1, 'Head Office', 1, '123 Main Street', 'Karachi', 'Pakistan', NOW(), NOW());

-- Insert Default Office Shift
INSERT INTO `office_shifts` (`id`, `shift_name`, `start_time`, `end_time`, `company_id`, `description`, `created_at`, `updated_at`) VALUES
(1, 'General', '08:00:00', '17:15:00', 1, 'General Working Hours', NOW(), NOW()),
(2, 'Shift-A', '07:00:00', '15:45:00', 1, 'Morning Shift', NOW(), NOW()),
(3, 'Shift-B', '15:00:00', '23:45:00', 1, 'Evening Shift', NOW(), NOW()),
(4, 'Shift-C', '23:00:00', '07:15:00', 1, 'Night Shift', NOW(), NOW());

-- Insert Default Super Admin User
INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `password`, `role_users_id`, `is_active`, `contact_no`, `created_at`, `updated_at`) VALUES
(1, 'Super', 'Admin', 'superadmin', 'admin@ttphrm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, '+1234567890', NOW(), NOW());

-- Insert Default Super Admin Employee
INSERT INTO `employees` (`id`, `first_name`, `last_name`, `staff_id`, `email`, `contact_no`, `company_id`, `department_id`, `designation_id`, `location_id`, `office_shift_id`, `role_users_id`, `status_id`, `is_active`, `basic_salary`, `created_at`, `updated_at`) VALUES
(1, 'Super', 'Admin', 'EMP001', 'admin@ttphrm.com', '+1234567890', 1, 1, 1, 1, 1, 1, 1, 1, 50000, NOW(), NOW());

-- Insert Basic Countries
INSERT INTO `countries` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(1, 'Pakistan', 'PK', NOW(), NOW()),
(2, 'United States', 'US', NOW(), NOW()),
(3, 'United Kingdom', 'GB', NOW(), NOW()),
(4, 'India', 'IN', NOW(), NOW()),
(5, 'Canada', 'CA', NOW(), NOW());

-- ===============================================
-- COMPLETE SCHEMA CREATED SUCCESSFULLY
-- ===============================================