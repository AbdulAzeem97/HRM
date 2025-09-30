-- ===============================================
-- TEST OVERTIME CALCULATION WITH SHIFT CHANGES
-- ===============================================

USE `u902429527_ttphrm`;

-- Clear existing overtime calculations
DELETE FROM overtime_calculations WHERE employee_id = 65;

-- Manually calculate overtime for both shifts to demonstrate different calculations

-- 1. GENERAL shift overtime (May 1-14, 8:00-17:15, 15-min grace period)
INSERT INTO overtime_calculations (
    employee_id, attendance_date, clock_in, clock_out,
    shift_start_time, shift_end_time, working_minutes, shift_minutes,
    late_minutes, overtime_minutes, net_overtime_minutes,
    hourly_rate, overtime_rate, overtime_amount,
    overtime_eligible, required_hours_per_day, basic_salary,
    shift_name, status, calculation_notes, calculated_at, created_at, updated_at
) VALUES
-- May 1: 15 min overtime (17:30 - 17:15), no lateness penalty
(65, '2025-05-01', '08:00:00', '17:30:00', '08:00:00', '17:15:00', 570, 555,
 0, 15, 15, 182.69, 365.38, 91.35, 1, 9, 42750, 'GENERAL', 'calculated',
 'GENERAL shift: No lateness, 15 min overtime', NOW(), NOW(), NOW()),

-- May 2: 30 min overtime (17:45 - 17:15), 10 min late (within grace period)
(65, '2025-05-02', '08:10:00', '17:45:00', '08:00:00', '17:15:00', 575, 555,
 0, 30, 30, 182.69, 365.38, 182.69, 1, 9, 42750, 'GENERAL', 'calculated',
 'GENERAL shift: 10 min late (within grace), full 30 min overtime', NOW(), NOW(), NOW()),

-- May 6: 45 min overtime (18:00 - 17:15), no lateness penalty
(65, '2025-05-06', '08:00:00', '18:00:00', '08:00:00', '17:15:00', 600, 555,
 0, 45, 45, 182.69, 365.38, 274.04, 1, 9, 42750, 'GENERAL', 'calculated',
 'GENERAL shift: No lateness, 45 min overtime', NOW(), NOW(), NOW()),

-- May 7: No overtime, 20 min late (beyond grace period)
(65, '2025-05-07', '08:20:00', '17:15:00', '08:00:00', '17:15:00', 535, 555,
 20, 0, 0, 182.69, 365.38, 0.00, 1, 9, 42750, 'GENERAL', 'calculated',
 'GENERAL shift: 20 min late (beyond grace), no overtime', NOW(), NOW(), NOW()),

-- May 9: No overtime, 30 min late (beyond grace period)
(65, '2025-05-09', '08:30:00', '17:15:00', '08:00:00', '17:15:00', 525, 555,
 30, 0, 0, 182.69, 365.38, 0.00, 1, 9, 42750, 'GENERAL', 'calculated',
 'GENERAL shift: 30 min late (beyond grace), no overtime', NOW(), NOW(), NOW());

-- 2. SHIFT-A overtime (May 15-28, 7:00-15:45, 15-min grace period)
INSERT INTO overtime_calculations (
    employee_id, attendance_date, clock_in, clock_out,
    shift_start_time, shift_end_time, working_minutes, shift_minutes,
    late_minutes, overtime_minutes, net_overtime_minutes,
    hourly_rate, overtime_rate, overtime_amount,
    overtime_eligible, required_hours_per_day, basic_salary,
    shift_name, status, calculation_notes, calculated_at, created_at, updated_at
) VALUES
-- May 15: 15 min overtime (16:00 - 15:45), no lateness penalty
(65, '2025-05-15', '07:00:00', '16:00:00', '07:00:00', '15:45:00', 540, 525,
 0, 15, 15, 182.69, 365.38, 91.35, 1, 9, 42750, 'SHIFT-A', 'calculated',
 'SHIFT-A: No lateness, 15 min overtime', NOW(), NOW(), NOW()),

-- May 16: 30 min overtime (16:15 - 15:45), 10 min late (within grace period)
(65, '2025-05-16', '07:10:00', '16:15:00', '07:00:00', '15:45:00', 545, 525,
 0, 30, 30, 182.69, 365.38, 182.69, 1, 9, 42750, 'SHIFT-A', 'calculated',
 'SHIFT-A: 10 min late (within grace), full 30 min overtime', NOW(), NOW(), NOW()),

-- May 20: 45 min overtime (16:30 - 15:45), no lateness (5 min within grace)
(65, '2025-05-20', '07:05:00', '16:30:00', '07:00:00', '15:45:00', 565, 525,
 0, 45, 45, 182.69, 365.38, 274.04, 1, 9, 42750, 'SHIFT-A', 'calculated',
 'SHIFT-A: 5 min late (within grace), full 45 min overtime', NOW(), NOW(), NOW()),

-- May 21: No overtime, 20 min late (beyond grace period)
(65, '2025-05-21', '07:20:00', '15:45:00', '07:00:00', '15:45:00', 505, 525,
 20, 0, 0, 182.69, 365.38, 0.00, 1, 9, 42750, 'SHIFT-A', 'calculated',
 'SHIFT-A: 20 min late (beyond grace), no overtime', NOW(), NOW(), NOW()),

-- May 23: No overtime, 30 min late (beyond grace period)
(65, '2025-05-23', '07:30:00', '15:45:00', '07:00:00', '15:45:00', 495, 525,
 30, 0, 0, 182.69, 365.38, 0.00, 1, 9, 42750, 'SHIFT-A', 'calculated',
 'SHIFT-A: 30 min late (beyond grace), no overtime', NOW(), NOW(), NOW()),

-- May 28: 45 min overtime (16:30 - 15:45), no lateness penalty
(65, '2025-05-28', '07:00:00', '16:30:00', '07:00:00', '15:45:00', 570, 525,
 0, 45, 45, 182.69, 365.38, 274.04, 1, 9, 42750, 'SHIFT-A', 'calculated',
 'SHIFT-A: No lateness, 45 min overtime', NOW(), NOW(), NOW());

-- Verification: Show overtime calculations by shift
SELECT
    'OVERTIME CALCULATIONS WITH SHIFT CHANGES - MAY 2025' as title,
    '' as data
UNION ALL
SELECT '', ''
UNION ALL
SELECT
    'GENERAL SHIFT (May 1-14): 8:00 AM - 5:15 PM',
    ''
UNION ALL
SELECT
    CONCAT('  ', attendance_date, ': '),
    CONCAT(
        'Late: ', late_minutes, ' min | ',
        'OT: ', net_overtime_minutes, ' min | ',
        'Pay: ₹', overtime_amount, ' | ',
        calculation_notes
    )
FROM overtime_calculations
WHERE employee_id = 65 AND shift_name = 'GENERAL'
ORDER BY attendance_date

UNION ALL
SELECT '', ''
UNION ALL
SELECT
    'SHIFT-A (May 15-28): 7:00 AM - 3:45 PM',
    ''
UNION ALL
SELECT
    CONCAT('  ', attendance_date, ': '),
    CONCAT(
        'Late: ', late_minutes, ' min | ',
        'OT: ', net_overtime_minutes, ' min | ',
        'Pay: ₹', overtime_amount, ' | ',
        calculation_notes
    )
FROM overtime_calculations
WHERE employee_id = 65 AND shift_name = 'SHIFT-A'
ORDER BY attendance_date

UNION ALL
SELECT '', ''
UNION ALL
SELECT
    'MONTHLY SUMMARY:',
    CONCAT(
        'Total OT Minutes: ', SUM(net_overtime_minutes),
        ' | Total OT Pay: ₹', SUM(overtime_amount),
        ' | OT Days: ', COUNT(CASE WHEN net_overtime_minutes > 0 THEN 1 END)
    )
FROM overtime_calculations
WHERE employee_id = 65 AND attendance_date BETWEEN '2025-05-01' AND '2025-05-31';