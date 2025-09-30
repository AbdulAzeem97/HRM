-- ===============================================
-- CREATE OVERTIME CALCULATIONS TABLE ONLY
-- This creates the table structure without sample data
-- ===============================================

USE `u902429527_ttphrm`;

-- Create overtime_calculations table
CREATE TABLE IF NOT EXISTS `overtime_calculations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `clock_in` time NOT NULL,
  `clock_out` time NOT NULL,
  `shift_start_time` time NOT NULL,
  `shift_end_time` time NOT NULL,
  `working_minutes` int(11) NOT NULL COMMENT 'Total working time in minutes',
  `shift_minutes` int(11) NOT NULL COMMENT 'Expected shift duration in minutes',
  `late_minutes` int(11) NOT NULL DEFAULT 0 COMMENT 'Late arrival in minutes',
  `overtime_minutes` int(11) NOT NULL DEFAULT 0 COMMENT 'Gross overtime before adjustments',
  `net_overtime_minutes` int(11) NOT NULL DEFAULT 0 COMMENT 'Net overtime after late deduction',
  `hourly_rate` decimal(10,2) NOT NULL COMMENT 'Basic hourly rate',
  `overtime_rate` decimal(10,2) NOT NULL COMMENT 'Overtime rate (usually 2x)',
  `overtime_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Final overtime pay',
  `overtime_eligible` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Employee OT eligibility',
  `required_hours_per_day` int(11) NOT NULL DEFAULT 9 COMMENT 'Employee required hours',
  `basic_salary` decimal(10,2) NOT NULL COMMENT 'Employee basic salary for calculation',
  `calculation_notes` varchar(255) DEFAULT NULL COMMENT 'Any special notes',
  `shift_name` varchar(100) DEFAULT NULL COMMENT 'Shift worked',
  `status` enum('calculated','verified','paid') NOT NULL DEFAULT 'calculated',
  `calculated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_employee_date_ot` (`employee_id`,`attendance_date`),
  KEY `overtime_calculations_employee_id_attendance_date_index` (`employee_id`,`attendance_date`),
  KEY `overtime_calculations_attendance_date_index` (`attendance_date`),
  KEY `overtime_calculations_status_index` (`status`),
  CONSTRAINT `overtime_calculations_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_overtime_calc_amount` ON `overtime_calculations` (`overtime_amount`);
CREATE INDEX IF NOT EXISTS `idx_overtime_calc_status_date` ON `overtime_calculations` (`status`, `attendance_date`);

-- Verification query
SELECT 'Overtime calculations table created successfully' as message;