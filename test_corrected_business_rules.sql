-- ===============================================
-- TEST CORRECTED BUSINESS RULES
-- 15-minute grace period + proper overtime calculation
-- ===============================================

USE `u902429527_ttphrm`;

-- Clear existing test data
DELETE FROM attendances WHERE employee_id = 65 AND attendance_date BETWEEN '2025-06-01' AND '2025-06-05';
DELETE FROM overtime_calculations WHERE employee_id = 65 AND attendance_date BETWEEN '2025-06-01' AND '2025-06-05';

-- Test Case 1: 10 minutes late (within grace), 30 minutes overtime
-- Expected: time_late = 00:00, overtime = 00:30
INSERT INTO attendances (
    employee_id, office_shift_id, attendance_date, clock_in, clock_out,
    clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status
) VALUES (
    65, 1, '2025-06-01', '08:10:00', '17:45:00',
    '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:30', '09:35', '00:00', 'Present'
);

-- Test Case 2: 20 minutes late (beyond grace), 60 minutes overtime
-- Expected: time_late = 00:20, overtime = 00:40 (60 - 20 = 40)
INSERT INTO attendances (
    employee_id, office_shift_id, attendance_date, clock_in, clock_out,
    clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status
) VALUES (
    65, 1, '2025-06-02', '08:20:00', '18:15:00',
    '127.0.0.1', '127.0.0.1', 0, '00:20', '00:00', '00:40', '09:55', '00:00', 'Present'
);

-- Test Case 3: 30 minutes late (beyond grace), 30 minutes overtime
-- Expected: time_late = 00:30, overtime = 00:00 (30 - 30 = 0)
INSERT INTO attendances (
    employee_id, office_shift_id, attendance_date, clock_in, clock_out,
    clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status
) VALUES (
    65, 1, '2025-06-03', '08:30:00', '17:45:00',
    '127.0.0.1', '127.0.0.1', 0, '00:30', '00:00', '00:00', '09:15', '00:00', 'Present'
);

-- Test Case 4: No lateness, 45 minutes overtime
-- Expected: time_late = 00:00, overtime = 00:45
INSERT INTO attendances (
    employee_id, office_shift_id, attendance_date, clock_in, clock_out,
    clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status
) VALUES (
    65, 1, '2025-06-04', '08:00:00', '18:00:00',
    '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:45', '10:00', '00:00', 'Present'
);

-- Test Case 5: 15 minutes late (at grace limit), 30 minutes overtime
-- Expected: time_late = 00:00, overtime = 00:30
INSERT INTO attendances (
    employee_id, office_shift_id, attendance_date, clock_in, clock_out,
    clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status
) VALUES (
    65, 1, '2025-06-05', '08:15:00', '17:45:00',
    '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:30', '09:30', '00:00', 'Present'
);

-- Show current attendance records with GENERAL shift (8:00 AM - 5:15 PM)
SELECT
    'NEW BUSINESS RULES TEST RESULTS' as title,
    '' as details
UNION ALL
SELECT
    'GENERAL Shift: 8:00 AM - 5:15 PM | 15-Min Grace Period', ''
UNION ALL
SELECT '', ''
UNION ALL
SELECT
    CONCAT('June ', DAY(attendance_date), ': '),
    CONCAT(
        'In: ', TIME(clock_in),
        ' | Out: ', TIME(clock_out),
        ' | Late: ', time_late,
        ' | OT: ', overtime,
        ' | Total: ', total_work
    )
FROM attendances
WHERE employee_id = 65 AND attendance_date BETWEEN '2025-06-01' AND '2025-06-05'
ORDER BY attendance_date;

-- Business Rules Explanation
SELECT
    '' as separator,
    'BUSINESS RULES APPLIED:' as rules
UNION ALL
SELECT
    '• Late 1-15 min', 'Grace period - No penalty'
UNION ALL
SELECT
    '• Late 16+ min', 'Late time deducted from overtime'
UNION ALL
SELECT
    '• Overtime', 'Starts after shift end time'
UNION ALL
SELECT
    '• Net Overtime', 'Gross OT minus late deduction';