-- Test Overtime Calculation for Employee 65
USE `u902429527_ttphrm`;

-- Calculate test overtime for the first attendance record
INSERT INTO `overtime_calculations` (
    `employee_id`, `attendance_date`, `clock_in`, `clock_out`,
    `shift_start_time`, `shift_end_time`, `working_minutes`, `shift_minutes`,
    `late_minutes`, `overtime_minutes`, `net_overtime_minutes`,
    `hourly_rate`, `overtime_rate`, `overtime_amount`,
    `overtime_eligible`, `required_hours_per_day`, `basic_salary`,
    `shift_name`, `status`, `calculation_notes`, `calculated_at`, `created_at`, `updated_at`
) VALUES (
    65, '2025-05-01', '08:00:00', '17:15:00',
    '08:00:00', '17:00:00', 555, 540,
    0, 15, 15,
    ROUND((42750 / 26) / 9, 2), ROUND(((42750 / 26) / 9) * 2, 2), ROUND((15 / 60.0) * (((42750 / 26) / 9) * 2), 2),
    1, 9, 42750,
    'General', 'calculated', 'Test calculation for overtime integration',
    NOW(), NOW(), NOW()
);

-- Verify the calculation
SELECT
    employee_id,
    attendance_date,
    clock_in,
    clock_out,
    working_minutes,
    overtime_minutes,
    net_overtime_minutes,
    hourly_rate,
    overtime_rate,
    overtime_amount,
    status
FROM overtime_calculations
WHERE employee_id = 65 AND attendance_date = '2025-05-01';