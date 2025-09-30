-- ===============================================
-- ADD OVERTIME CALCULATIONS TABLE TO EXISTING DATABASE
-- This creates a dedicated table to store overtime calculations
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

-- Sample data insertion (if you want to populate with existing attendance data)
-- This will calculate overtime for the last 30 days of attendance records
INSERT INTO `overtime_calculations` (
    `employee_id`, `attendance_date`, `clock_in`, `clock_out`,
    `shift_start_time`, `shift_end_time`, `working_minutes`, `shift_minutes`,
    `late_minutes`, `overtime_minutes`, `net_overtime_minutes`,
    `hourly_rate`, `overtime_rate`, `overtime_amount`,
    `overtime_eligible`, `required_hours_per_day`, `basic_salary`,
    `shift_name`, `status`, `calculated_at`, `created_at`, `updated_at`
)
SELECT
    a.employee_id,
    a.attendance_date,
    a.clock_in,
    a.clock_out,
    COALESCE(os.start_time, '08:00:00') as shift_start_time,
    COALESCE(os.end_time, '17:00:00') as shift_end_time,
    TIMESTAMPDIFF(MINUTE,
        CONCAT(a.attendance_date, ' ', a.clock_in),
        CONCAT(a.attendance_date, ' ', a.clock_out)
    ) as working_minutes,
    TIMESTAMPDIFF(MINUTE,
        COALESCE(os.start_time, '08:00:00'),
        COALESCE(os.end_time, '17:00:00')
    ) as shift_minutes,
    GREATEST(0, TIMESTAMPDIFF(MINUTE,
        COALESCE(os.start_time, '08:00:00'),
        a.clock_in
    )) as late_minutes,
    GREATEST(0, TIMESTAMPDIFF(MINUTE,
        COALESCE(os.end_time, '17:00:00'),
        a.clock_out
    )) as overtime_minutes,
    GREATEST(0,
        TIMESTAMPDIFF(MINUTE, COALESCE(os.end_time, '17:00:00'), a.clock_out) -
        GREATEST(0, TIMESTAMPDIFF(MINUTE, COALESCE(os.start_time, '08:00:00'), a.clock_in))
    ) as net_overtime_minutes,
    ROUND((e.basic_salary / 26) / COALESCE(e.required_hours_per_day, 9), 2) as hourly_rate,
    ROUND(((e.basic_salary / 26) / COALESCE(e.required_hours_per_day, 9)) * 2, 2) as overtime_rate,
    ROUND(
        (GREATEST(0,
            TIMESTAMPDIFF(MINUTE, COALESCE(os.end_time, '17:00:00'), a.clock_out) -
            GREATEST(0, TIMESTAMPDIFF(MINUTE, COALESCE(os.start_time, '08:00:00'), a.clock_in))
        ) / 60.0) *
        (((e.basic_salary / 26) / COALESCE(e.required_hours_per_day, 9)) * 2), 2
    ) as overtime_amount,
    COALESCE(e.overtime_allowed, 1) as overtime_eligible,
    COALESCE(e.required_hours_per_day, 9) as required_hours_per_day,
    e.basic_salary,
    COALESCE(os.shift_name, 'General') as shift_name,
    'calculated' as status,
    NOW() as calculated_at,
    NOW() as created_at,
    NOW() as updated_at
FROM attendances a
JOIN employees e ON a.employee_id = e.id
LEFT JOIN office_shifts os ON e.office_shift_id = os.id
WHERE a.attendance_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
AND a.clock_out IS NOT NULL
AND a.clock_out != ''
AND e.overtime_allowed = 1
AND NOT EXISTS (
    SELECT 1 FROM overtime_calculations oc
    WHERE oc.employee_id = a.employee_id
    AND oc.attendance_date = a.attendance_date
)
ON DUPLICATE KEY UPDATE
    `clock_in` = VALUES(`clock_in`),
    `clock_out` = VALUES(`clock_out`),
    `updated_at` = NOW();

-- Verification query
SELECT
    'Overtime calculations table created and populated' as message,
    COUNT(*) as total_calculations,
    SUM(CASE WHEN overtime_amount > 0 THEN 1 ELSE 0 END) as records_with_overtime,
    SUM(overtime_amount) as total_overtime_amount,
    MIN(attendance_date) as earliest_date,
    MAX(attendance_date) as latest_date
FROM overtime_calculations;