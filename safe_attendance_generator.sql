-- Safe September 2025 Attendance Generator
-- This script only uses existing employee IDs to avoid foreign key errors

-- Step 1: Check existing employees (run this first to see your employee IDs)
SELECT 'Your active employees:' as info;
SELECT id, first_name, last_name, company_id, department_id
FROM employees
WHERE is_active = 1 AND exit_date IS NULL
ORDER BY id
LIMIT 20;

-- Step 2: Clear any existing September 2025 data (optional)
-- DELETE FROM attendances WHERE attendance_date BETWEEN '2025-09-01' AND '2025-09-30';

-- Step 3: Insert attendance data only for existing employees
-- This uses a safer approach by checking if employee exists

-- Method 1: Insert for Employee 65 (we know this exists)
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status)
SELECT 65, '2025-09-01', '08:00', '17:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:00', '09:00', '01:00', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-02', '08:15', '17:30', '127.0.0.1', '127.0.0.1', 0, '00:15', '00:00', '00:30', '09:15', '00:45', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-04', '08:00', '16:30', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:30', '00:00', '08:30', '01:30', 'Early Leave'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-05', '08:30', '18:00', '127.0.0.1', '127.0.0.1', 0, '00:30', '00:00', '01:00', '09:30', '00:30', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-06', '08:00', '17:15', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:15', '09:15', '00:45', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-07', '08:00', '12:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '05:00', '00:00', '04:00', '00:00', 'Half Day'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-08', '07:45', '17:45', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '01:00', '10:00', '00:00', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-09', '08:45', '17:00', '127.0.0.1', '127.0.0.1', 0, '00:45', '00:00', '00:00', '08:15', '01:45', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-11', '08:00', '19:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '03:00', '11:00', '00:00', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-12', '08:00', '16:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '01:00', '00:00', '08:00', '02:00', 'Early Leave'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-13', '08:10', '17:20', '127.0.0.1', '127.0.0.1', 0, '00:10', '00:00', '00:20', '09:10', '00:50', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-14', '08:00', '17:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:00', '09:00', '01:00', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-15', '08:20', '18:30', '127.0.0.1', '127.0.0.1', 0, '00:20', '00:00', '01:30', '10:10', '00:00', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-16', '08:00', '13:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '04:00', '00:00', '05:00', '00:00', 'Half Day'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-18', '07:30', '16:30', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:30', '00:00', '09:00', '01:00', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-19', '08:00', '20:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '04:00', '12:00', '00:00', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-20', '09:00', '17:00', '127.0.0.1', '127.0.0.1', 0, '01:00', '00:00', '00:00', '08:00', '02:00', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-21', '08:00', '17:30', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:30', '09:30', '00:30', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-22', '08:05', '16:55', '127.0.0.1', '127.0.0.1', 0, '00:05', '00:05', '00:00', '08:50', '01:10', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-23', '08:00', '12:30', '127.0.0.1', '127.0.0.1', 0, '00:00', '04:30', '00:00', '04:30', '00:00', 'Half Day'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-25', '07:45', '18:15', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '01:30', '10:30', '00:00', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-26', '08:30', '17:00', '127.0.0.1', '127.0.0.1', 0, '00:30', '00:00', '00:00', '08:30', '01:30', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-27', '08:00', '19:30', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '02:30', '11:30', '00:00', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-28', '08:00', '17:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:00', '09:00', '01:00', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-29', '08:15', '16:45', '127.0.0.1', '127.0.0.1', 0, '00:15', '00:15', '00:00', '08:30', '01:30', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-30', '08:00', '18:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '01:00', '10:00', '00:00', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL)
UNION ALL
SELECT 65, '2025-09-31', '08:00', '17:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:00', '09:00', '01:00', 'Present'
WHERE EXISTS (SELECT 1 FROM employees WHERE id = 65 AND is_active = 1 AND exit_date IS NULL);

-- Method 2: Bulk insert for ALL existing employees at once
-- This creates attendance for ALL active employees automatically

INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status)
SELECT
    e.id as employee_id,
    '2025-09-01' as attendance_date,
    '08:00' as clock_in,
    '17:00' as clock_out,
    '127.0.0.1' as clock_in_ip,
    '127.0.0.1' as clock_out_ip,
    0 as clock_in_out,
    '00:00' as time_late,
    '00:00' as early_leaving,
    '00:00' as overtime,
    '09:00' as total_work,
    '01:00' as total_rest,
    'Present' as attendance_status
FROM employees e
WHERE e.is_active = 1 AND e.exit_date IS NULL
AND NOT EXISTS (SELECT 1 FROM attendances a WHERE a.employee_id = e.id AND a.attendance_date = '2025-09-01');

-- Add more days with different patterns
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status)
SELECT
    e.id as employee_id,
    '2025-09-02' as attendance_date,
    CASE
        WHEN e.id % 3 = 0 THEN '08:30'
        WHEN e.id % 5 = 0 THEN '07:45'
        ELSE '08:15'
    END as clock_in,
    CASE
        WHEN e.id % 2 = 0 THEN '17:30'
        WHEN e.id % 4 = 0 THEN '18:00'
        ELSE '17:00'
    END as clock_out,
    '127.0.0.1' as clock_in_ip,
    '127.0.0.1' as clock_out_ip,
    0 as clock_in_out,
    CASE
        WHEN e.id % 3 = 0 THEN '00:30'
        ELSE '00:15'
    END as time_late,
    '00:00' as early_leaving,
    CASE
        WHEN e.id % 2 = 0 THEN '00:30'
        WHEN e.id % 4 = 0 THEN '01:00'
        ELSE '00:00'
    END as overtime,
    CASE
        WHEN e.id % 2 = 0 THEN '09:30'
        WHEN e.id % 4 = 0 THEN '10:00'
        ELSE '09:00'
    END as total_work,
    CASE
        WHEN e.id % 2 = 0 THEN '00:30'
        ELSE '01:00'
    END as total_rest,
    'Present' as attendance_status
FROM employees e
WHERE e.is_active = 1 AND e.exit_date IS NULL
AND NOT EXISTS (SELECT 1 FROM attendances a WHERE a.employee_id = e.id AND a.attendance_date = '2025-09-02');

-- Continue with more dates...
-- Add Aug 4 with early leaving pattern
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status)
SELECT
    e.id as employee_id,
    '2025-09-04' as attendance_date,
    '08:00' as clock_in,
    CASE
        WHEN e.id % 7 = 0 THEN '16:00'
        WHEN e.id % 3 = 0 THEN '16:30'
        ELSE '17:00'
    END as clock_out,
    '127.0.0.1' as clock_in_ip,
    '127.0.0.1' as clock_out_ip,
    0 as clock_in_out,
    '00:00' as time_late,
    CASE
        WHEN e.id % 7 = 0 THEN '01:00'
        WHEN e.id % 3 = 0 THEN '00:30'
        ELSE '00:00'
    END as early_leaving,
    '00:00' as overtime,
    CASE
        WHEN e.id % 7 = 0 THEN '08:00'
        WHEN e.id % 3 = 0 THEN '08:30'
        ELSE '09:00'
    END as total_work,
    CASE
        WHEN e.id % 7 = 0 THEN '02:00'
        WHEN e.id % 3 = 0 THEN '01:30'
        ELSE '01:00'
    END as total_rest,
    CASE
        WHEN e.id % 7 = 0 THEN 'Early Leave'
        ELSE 'Present'
    END as attendance_status
FROM employees e
WHERE e.is_active = 1 AND e.exit_date IS NULL
AND NOT EXISTS (SELECT 1 FROM attendances a WHERE a.employee_id = e.id AND a.attendance_date = '2025-09-04');

-- Add Aug 5 with overtime pattern
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status)
SELECT
    e.id as employee_id,
    '2025-09-05' as attendance_date,
    CASE
        WHEN e.id % 4 = 0 THEN '08:30'
        ELSE '08:00'
    END as clock_in,
    CASE
        WHEN e.id % 5 = 0 THEN '19:00'
        WHEN e.id % 3 = 0 THEN '18:30'
        ELSE '17:00'
    END as clock_out,
    '127.0.0.1' as clock_in_ip,
    '127.0.0.1' as clock_out_ip,
    0 as clock_in_out,
    CASE
        WHEN e.id % 4 = 0 THEN '00:30'
        ELSE '00:00'
    END as time_late,
    '00:00' as early_leaving,
    CASE
        WHEN e.id % 5 = 0 THEN '03:00'
        WHEN e.id % 3 = 0 THEN '01:30'
        ELSE '00:00'
    END as overtime,
    CASE
        WHEN e.id % 5 = 0 THEN '11:00'
        WHEN e.id % 3 = 0 THEN '10:30'
        ELSE '09:00'
    END as total_work,
    CASE
        WHEN e.id % 5 = 0 THEN '00:00'
        WHEN e.id % 3 = 0 THEN '00:00'
        ELSE '01:00'
    END as total_rest,
    'Present' as attendance_status
FROM employees e
WHERE e.is_active = 1 AND e.exit_date IS NULL
AND NOT EXISTS (SELECT 1 FROM attendances a WHERE a.employee_id = e.id AND a.attendance_date = '2025-09-05');

-- Add Aug 6 with half day pattern
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status)
SELECT
    e.id as employee_id,
    '2025-09-06' as attendance_date,
    '08:00' as clock_in,
    CASE
        WHEN e.id % 10 = 0 THEN '12:30'
        WHEN e.id % 6 = 0 THEN '13:00'
        ELSE '17:15'
    END as clock_out,
    '127.0.0.1' as clock_in_ip,
    '127.0.0.1' as clock_out_ip,
    0 as clock_in_out,
    '00:00' as time_late,
    CASE
        WHEN e.id % 10 = 0 THEN '04:30'
        WHEN e.id % 6 = 0 THEN '04:00'
        ELSE '00:00'
    END as early_leaving,
    CASE
        WHEN e.id % 10 = 0 OR e.id % 6 = 0 THEN '00:00'
        ELSE '00:15'
    END as overtime,
    CASE
        WHEN e.id % 10 = 0 THEN '04:30'
        WHEN e.id % 6 = 0 THEN '05:00'
        ELSE '09:15'
    END as total_work,
    CASE
        WHEN e.id % 10 = 0 OR e.id % 6 = 0 THEN '00:00'
        ELSE '00:45'
    END as total_rest,
    CASE
        WHEN e.id % 10 = 0 OR e.id % 6 = 0 THEN 'Half Day'
        ELSE 'Present'
    END as attendance_status
FROM employees e
WHERE e.is_active = 1 AND e.exit_date IS NULL
AND NOT EXISTS (SELECT 1 FROM attendances a WHERE a.employee_id = e.id AND a.attendance_date = '2025-09-06');

-- Verify the results
SELECT 'Attendance Summary:' as info;
SELECT
    employee_id,
    COUNT(*) as total_days,
    SUM(CASE WHEN attendance_status = 'Present' THEN 1 ELSE 0 END) as present_days,
    SUM(CASE WHEN attendance_status = 'Half Day' THEN 1 ELSE 0 END) as half_days,
    SUM(CASE WHEN attendance_status = 'Early Leave' THEN 1 ELSE 0 END) as early_leaves
FROM attendances
WHERE attendance_date BETWEEN '2025-09-01' AND '2025-09-31'
GROUP BY employee_id
ORDER BY employee_id
LIMIT 10;