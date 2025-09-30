-- Test Late Employee with Overtime (beyond grace period)
USE `u902429527_ttphrm`;

-- Test Case: 20 minutes late but worked 1 hour overtime
-- Expected: lateMinutes = 20, overtime = 60, net_overtime = 40
INSERT INTO `overtime_calculations` (
    `employee_id`, `attendance_date`, `clock_in`, `clock_out`,
    `shift_start_time`, `shift_end_time`, `working_minutes`, `shift_minutes`,
    `late_minutes`, `overtime_minutes`, `net_overtime_minutes`,
    `hourly_rate`, `overtime_rate`, `overtime_amount`,
    `overtime_eligible`, `required_hours_per_day`, `basic_salary`,
    `shift_name`, `status`, `calculation_notes`, `calculated_at`, `created_at`, `updated_at`
) VALUES (
    65, '2025-05-08', '08:20:00', '18:00:00',
    '08:00:00', '17:00:00', 580, 540,
    20, 60, 40, -- 20 min late (beyond grace), 60 min overtime, net = 40 min
    ROUND((42750 / 26) / 9, 2), ROUND(((42750 / 26) / 9) * 2, 2), ROUND((40 / 60.0) * (((42750 / 26) / 9) * 2), 2),
    1, 9, 42750,
    'General', 'calculated', 'Test: 20 min late with overtime',
    NOW(), NOW(), NOW()
);

-- Verification
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
WHERE employee_id = 65 AND attendance_date = '2025-05-08';