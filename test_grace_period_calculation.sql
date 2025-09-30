-- Test Grace Period Implementation for Overtime Calculations
USE `u902429527_ttphrm`;

-- Clear existing test data
DELETE FROM overtime_calculations WHERE employee_id = 65;

-- Test Case 1: 10 minutes late (within 15-minute grace period)
-- Expected: lateMinutes = 0, full overtime credited
INSERT INTO `overtime_calculations` (
    `employee_id`, `attendance_date`, `clock_in`, `clock_out`,
    `shift_start_time`, `shift_end_time`, `working_minutes`, `shift_minutes`,
    `late_minutes`, `overtime_minutes`, `net_overtime_minutes`,
    `hourly_rate`, `overtime_rate`, `overtime_amount`,
    `overtime_eligible`, `required_hours_per_day`, `basic_salary`,
    `shift_name`, `status`, `calculation_notes`, `calculated_at`, `created_at`, `updated_at`
) VALUES (
    65, '2025-05-02', '08:10:00', '17:30:00',
    '08:00:00', '17:00:00', 560, 540,
    0, 30, 30, -- 0 late minutes due to grace period, full 30 minutes overtime
    ROUND((42750 / 26) / 9, 2), ROUND(((42750 / 26) / 9) * 2, 2), ROUND((30 / 60.0) * (((42750 / 26) / 9) * 2), 2),
    1, 9, 42750,
    'General', 'calculated', 'Test: 10 min late (within grace period)',
    NOW(), NOW(), NOW()
);

-- Test Case 2: 15 minutes late (exactly at grace period limit)
-- Expected: lateMinutes = 0, full overtime credited
INSERT INTO `overtime_calculations` (
    `employee_id`, `attendance_date`, `clock_in`, `clock_out`,
    `shift_start_time`, `shift_end_time`, `working_minutes`, `shift_minutes`,
    `late_minutes`, `overtime_minutes`, `net_overtime_minutes`,
    `hourly_rate`, `overtime_rate`, `overtime_amount`,
    `overtime_eligible`, `required_hours_per_day`, `basic_salary`,
    `shift_name`, `status`, `calculation_notes`, `calculated_at`, `created_at`, `updated_at`
) VALUES (
    65, '2025-05-06', '08:15:00', '17:45:00',
    '08:00:00', '17:00:00', 570, 540,
    0, 45, 45, -- 0 late minutes due to grace period, full 45 minutes overtime
    ROUND((42750 / 26) / 9, 2), ROUND(((42750 / 26) / 9) * 2, 2), ROUND((45 / 60.0) * (((42750 / 26) / 9) * 2), 2),
    1, 9, 42750,
    'General', 'calculated', 'Test: 15 min late (at grace period limit)',
    NOW(), NOW(), NOW()
);

-- Test Case 3: 30 minutes late (beyond 15-minute grace period)
-- Expected: lateMinutes = 30, overtime reduced by lateness
INSERT INTO `overtime_calculations` (
    `employee_id`, `attendance_date`, `clock_in`, `clock_out`,
    `shift_start_time`, `shift_end_time`, `working_minutes`, `shift_minutes`,
    `late_minutes`, `overtime_minutes`, `net_overtime_minutes`,
    `hourly_rate`, `overtime_rate`, `overtime_amount`,
    `overtime_eligible`, `required_hours_per_day`, `basic_salary`,
    `shift_name`, `status`, `calculation_notes`, `calculated_at`, `created_at`, `updated_at`
) VALUES (
    65, '2025-05-09', '08:30:00', '17:00:00',
    '08:00:00', '17:00:00', 510, 540,
    30, 0, 0, -- 30 minutes late (beyond grace), no overtime since no work beyond shift
    ROUND((42750 / 26) / 9, 2), ROUND(((42750 / 26) / 9) * 2, 2), 0.00,
    1, 9, 42750,
    'General', 'calculated', 'Test: 30 min late (beyond grace period)',
    NOW(), NOW(), NOW()
);

-- Verification query
SELECT
    attendance_date,
    TIME(clock_in) as clock_in,
    TIME(clock_out) as clock_out,
    late_minutes,
    overtime_minutes,
    net_overtime_minutes,
    overtime_amount,
    calculation_notes
FROM overtime_calculations
WHERE employee_id = 65
ORDER BY attendance_date;