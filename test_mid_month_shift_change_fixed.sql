-- ===============================================
-- TEST MID-MONTH SHIFT CHANGE SCENARIO
-- Employee 65 changes from GENERAL shift to SHIFT-A on May 15, 2025
-- ===============================================

USE `u902429527_ttphrm`;

-- Step 1: Record the shift change (Employee 65 changes to SHIFT-A on May 15, 2025)
INSERT INTO employee_shift_changes (
    employee_id, old_shift_id, new_shift_id, effective_date, reason, created_at, updated_at
) VALUES (
    65, 1, 2, '2025-05-15', 'Promoted to early morning shift for better productivity', NOW(), NOW()
);

-- Step 2: Create attendance records showing different shifts in the same month
-- Clear existing test data first
DELETE FROM attendances WHERE employee_id = 65 AND attendance_date BETWEEN '2025-05-01' AND '2025-05-31';

-- First half of May: GENERAL shift (8:00 AM to 5:15 PM)
INSERT INTO attendances (employee_id, office_shift_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status) VALUES
    (65, 1, '2025-05-01', '08:00:00', '17:30:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:15', '09:30', '00:30', 'Present'),
    (65, 1, '2025-05-02', '08:10:00', '17:45:00', '127.0.0.1', '127.0.0.1', 0, '00:10', '00:00', '00:30', '09:35', '00:25', 'Present'),
    (65, 1, '2025-05-05', '08:05:00', '17:15:00', '127.0.0.1', '127.0.0.1', 0, '00:05', '00:00', '00:00', '09:10', '00:50', 'Present'),
    (65, 1, '2025-05-06', '08:00:00', '18:00:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:45', '10:00', '00:00', 'Present'),
    (65, 1, '2025-05-07', '08:20:00', '17:15:00', '127.0.0.1', '127.0.0.1', 0, '00:20', '00:00', '00:00', '08:55', '01:05', 'Present'),
    (65, 1, '2025-05-08', '08:00:00', '17:15:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:00', '09:15', '00:45', 'Present'),
    (65, 1, '2025-05-09', '08:30:00', '17:15:00', '127.0.0.1', '127.0.0.1', 0, '00:30', '00:00', '00:00', '08:45', '01:15', 'Present'),
    (65, 1, '2025-05-12', '08:00:00', '17:30:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:15', '09:30', '00:30', 'Present'),
    (65, 1, '2025-05-13', '08:15:00', '17:15:00', '127.0.0.1', '127.0.0.1', 0, '00:15', '00:00', '00:00', '09:00', '01:00', 'Present'),
    (65, 1, '2025-05-14', '08:00:00', '17:45:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:30', '09:45', '00:15', 'Present');

-- Second half of May: SHIFT-A (7:00 AM to 3:45 PM) - starting May 15
INSERT INTO attendances (employee_id, office_shift_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status) VALUES
    (65, 2, '2025-05-15', '07:00:00', '16:00:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:15', '09:00', '01:00', 'Present'),
    (65, 2, '2025-05-16', '07:10:00', '16:15:00', '127.0.0.1', '127.0.0.1', 0, '00:10', '00:00', '00:30', '09:05', '00:55', 'Present'),
    (65, 2, '2025-05-19', '07:00:00', '15:45:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:00', '08:45', '01:15', 'Present'),
    (65, 2, '2025-05-20', '07:05:00', '16:30:00', '127.0.0.1', '127.0.0.1', 0, '00:05', '00:00', '00:45', '09:25', '00:35', 'Present'),
    (65, 2, '2025-05-21', '07:20:00', '15:45:00', '127.0.0.1', '127.0.0.1', 0, '00:20', '00:00', '00:00', '08:25', '01:35', 'Present'),
    (65, 2, '2025-05-22', '07:00:00', '16:00:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:15', '09:00', '01:00', 'Present'),
    (65, 2, '2025-05-23', '07:30:00', '15:45:00', '127.0.0.1', '127.0.0.1', 0, '00:30', '00:00', '00:00', '08:15', '01:45', 'Present'),
    (65, 2, '2025-05-26', '07:00:00', '16:15:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:30', '09:15', '00:45', 'Present'),
    (65, 2, '2025-05-27', '07:15:00', '15:45:00', '127.0.0.1', '127.0.0.1', 0, '00:15', '00:00', '00:00', '08:30', '01:30', 'Present'),
    (65, 2, '2025-05-28', '07:00:00', '16:30:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:45', '09:30', '00:30', 'Present');

-- Step 3: Update employee's current shift to SHIFT-A
UPDATE employees SET office_shift_id = 2 WHERE id = 65;

-- Step 4: Clear and recalculate overtime calculations for the entire month
DELETE FROM overtime_calculations WHERE employee_id = 65 AND attendance_date BETWEEN '2025-05-01' AND '2025-05-31';

-- Verification Queries

-- 1. Show attendance with different shifts in the same month
SELECT
    attendance_date,
    os.shift_name,
    CONCAT(
        TIME(CASE
            WHEN DAYNAME(attendance_date) = 'Monday' THEN os.monday_in
            WHEN DAYNAME(attendance_date) = 'Tuesday' THEN os.tuesday_in
            WHEN DAYNAME(attendance_date) = 'Wednesday' THEN os.wednesday_in
            WHEN DAYNAME(attendance_date) = 'Thursday' THEN os.thursday_in
            WHEN DAYNAME(attendance_date) = 'Friday' THEN os.friday_in
            WHEN DAYNAME(attendance_date) = 'Saturday' THEN os.saturday_in
            ELSE os.sunday_in
        END), ' - ',
        TIME(CASE
            WHEN DAYNAME(attendance_date) = 'Monday' THEN os.monday_out
            WHEN DAYNAME(attendance_date) = 'Tuesday' THEN os.tuesday_out
            WHEN DAYNAME(attendance_date) = 'Wednesday' THEN os.wednesday_out
            WHEN DAYNAME(attendance_date) = 'Thursday' THEN os.thursday_out
            WHEN DAYNAME(attendance_date) = 'Friday' THEN os.friday_out
            WHEN DAYNAME(attendance_date) = 'Saturday' THEN os.saturday_out
            ELSE os.sunday_out
        END)
    ) as shift_timing,
    CONCAT(TIME(clock_in), ' - ', TIME(clock_out)) as actual_timing
FROM attendances a
JOIN office_shifts os ON a.office_shift_id = os.id
WHERE a.employee_id = 65 AND a.attendance_date BETWEEN '2025-05-01' AND '2025-05-31'
ORDER BY a.attendance_date;

-- 2. Show shift change history
SELECT
    effective_date,
    CONCAT(COALESCE(os1.shift_name, 'None'), ' → ', os2.shift_name) as shift_change,
    reason
FROM employee_shift_changes esc
LEFT JOIN office_shifts os1 ON esc.old_shift_id = os1.id
JOIN office_shifts os2 ON esc.new_shift_id = os2.id
WHERE esc.employee_id = 65
ORDER BY esc.effective_date DESC;