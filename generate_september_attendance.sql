-- Generate September 2025 attendance data for all active employees
-- This creates realistic attendance patterns for comprehensive testing

USE u902429527_ttphrm;

-- Delete existing September 2025 data to avoid duplicates
DELETE FROM attendances WHERE attendance_date BETWEEN '2025-09-01' AND '2025-09-30';

-- Generate attendance for all active employees for September 2025 (30 days)
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status)
SELECT
    e.id as employee_id,
    attendance_date,
    clock_in,
    clock_out,
    '192.168.1.100' as clock_in_ip,
    '192.168.1.100' as clock_out_ip,
    CONCAT(clock_in, ' - ', clock_out) as clock_in_out,
    CASE
        -- Time late calculation (minutes past 8:00 AM)
        WHEN TIME(clock_in) <= '08:15:00' THEN 0
        WHEN TIME(clock_in) <= '08:30:00' THEN TIMESTAMPDIFF(MINUTE, '08:00:00', TIME(clock_in))
        WHEN TIME(clock_in) <= '09:00:00' THEN TIMESTAMPDIFF(MINUTE, '08:00:00', TIME(clock_in))
        WHEN TIME(clock_in) <= '10:30:00' THEN TIMESTAMPDIFF(MINUTE, '08:00:00', TIME(clock_in))
        ELSE TIMESTAMPDIFF(MINUTE, '08:00:00', TIME(clock_in))
    END as time_late,
    CASE
        -- Early leaving calculation (minutes before 5:15 PM)
        WHEN TIME(clock_out) >= '17:15:00' THEN 0
        ELSE TIMESTAMPDIFF(MINUTE, TIME(clock_out), '17:15:00')
    END as early_leaving,
    CASE
        -- Overtime calculation (minutes after 5:15 PM)
        WHEN TIME(clock_out) <= '17:15:00' THEN 0
        ELSE TIMESTAMPDIFF(MINUTE, '17:15:00', TIME(clock_out))
    END as overtime,
    TIMESTAMPDIFF(MINUTE, TIME(clock_in), TIME(clock_out)) as total_work,
    60 as total_rest, -- 1 hour break
    'Present' as attendance_status
FROM employees e
CROSS JOIN (
    -- Generate varied attendance patterns for September 2025 (30 days)
    -- Pattern 1: Regular employees (mostly on time)
    SELECT '2025-09-01' as attendance_date, '08:05:00' as clock_in, '17:25:00' as clock_out, 1 as pattern_type
    UNION ALL SELECT '2025-09-02', '08:10:00', '17:30:00', 1
    UNION ALL SELECT '2025-09-03', '08:02:00', '17:20:00', 1
    UNION ALL SELECT '2025-09-04', '08:15:00', '17:45:00', 1
    UNION ALL SELECT '2025-09-05', '08:08:00', '17:35:00', 1
    UNION ALL SELECT '2025-09-08', '08:12:00', '17:40:00', 1
    UNION ALL SELECT '2025-09-09', '08:06:00', '17:25:00', 1
    UNION ALL SELECT '2025-09-10', '08:18:00', '17:50:00', 1
    UNION ALL SELECT '2025-09-11', '08:04:00', '17:30:00', 1
    UNION ALL SELECT '2025-09-12', '08:22:00', '17:55:00', 1
    UNION ALL SELECT '2025-09-15', '08:03:00', '17:20:00', 1
    UNION ALL SELECT '2025-09-16', '08:14:00', '17:45:00', 1
    UNION ALL SELECT '2025-09-17', '08:07:00', '17:35:00', 1
    UNION ALL SELECT '2025-09-18', '08:20:00', '18:00:00', 1
    UNION ALL SELECT '2025-09-19', '08:11:00', '17:40:00', 1
    UNION ALL SELECT '2025-09-22', '08:16:00', '17:30:00', 1
    UNION ALL SELECT '2025-09-23', '08:05:00', '17:25:00', 1
    UNION ALL SELECT '2025-09-24', '08:25:00', '18:15:00', 1
    UNION ALL SELECT '2025-09-25', '08:09:00', '17:50:00', 1
    UNION ALL SELECT '2025-09-26', '08:13:00', '17:45:00', 1
    UNION ALL SELECT '2025-09-29', '08:08:00', '17:35:00', 1
    UNION ALL SELECT '2025-09-30', '08:19:00', '18:10:00', 1
) patterns
WHERE e.is_active = 1
AND e.exit_date IS NULL
AND (
    -- Different patterns for different employee groups
    (e.id % 3 = 0 AND patterns.pattern_type = 1) OR  -- Every 3rd employee gets pattern 1
    (e.id % 3 != 0 AND patterns.pattern_type = 1)    -- Others also get pattern 1 for now
);

-- Add some late arrivals and half-day patterns for specific employees
-- Late arrivals (16-60 minutes late)
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status)
SELECT
    employee_id,
    attendance_date,
    clock_in,
    clock_out,
    '192.168.1.100',
    '192.168.1.100',
    CONCAT(clock_in, ' - ', clock_out),
    TIMESTAMPDIFF(MINUTE, '08:00:00', TIME(clock_in)) as time_late,
    0 as early_leaving,
    CASE WHEN TIME(clock_out) > '17:15:00' THEN TIMESTAMPDIFF(MINUTE, '17:15:00', TIME(clock_out)) ELSE 0 END as overtime,
    TIMESTAMPDIFF(MINUTE, TIME(clock_in), TIME(clock_out)) as total_work,
    60 as total_rest,
    'Present' as attendance_status
FROM (
    SELECT 61 as employee_id, '2025-09-06' as attendance_date, '08:35:00' as clock_in, '17:20:00' as clock_out -- 35 min late
    UNION ALL SELECT 62, '2025-09-09', '08:45:00', '17:30:00'  -- 45 min late
    UNION ALL SELECT 64, '2025-09-12', '08:25:00', '18:00:00'  -- 25 min late + overtime
    UNION ALL SELECT 66, '2025-09-15', '08:55:00', '17:40:00'  -- 55 min late
    UNION ALL SELECT 68, '2025-09-18', '08:40:00', '17:50:00'  -- 40 min late + overtime
) late_patterns;

-- Add half-day patterns (more than 2 hours late)
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status)
SELECT
    employee_id,
    attendance_date,
    clock_in,
    clock_out,
    '192.168.1.100',
    '192.168.1.100',
    CONCAT(clock_in, ' - ', clock_out),
    TIMESTAMPDIFF(MINUTE, '08:00:00', TIME(clock_in)) as time_late,
    0 as early_leaving,
    CASE WHEN TIME(clock_out) > '17:15:00' THEN TIMESTAMPDIFF(MINUTE, '17:15:00', TIME(clock_out)) ELSE 0 END as overtime,
    TIMESTAMPDIFF(MINUTE, TIME(clock_in), TIME(clock_out)) as total_work,
    60 as total_rest,
    'Half Day' as attendance_status
FROM (
    -- Half-day entries (>120 minutes late)
    SELECT 65 as employee_id, '2025-09-05' as attendance_date, '10:30:00' as clock_in, '17:15:00' as clock_out -- 150 min late
    UNION ALL SELECT 67, '2025-09-10', '11:00:00', '18:00:00'  -- 180 min late + overtime
    UNION ALL SELECT 69, '2025-09-16', '10:45:00', '17:30:00'  -- 165 min late + overtime
    UNION ALL SELECT 70, '2025-09-23', '11:30:00', '17:45:00'  -- 210 min late + overtime
) half_day_patterns;

-- Add some high overtime days
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status)
SELECT
    employee_id,
    attendance_date,
    clock_in,
    clock_out,
    '192.168.1.100',
    '192.168.1.100',
    CONCAT(clock_in, ' - ', clock_out),
    CASE WHEN TIME(clock_in) > '08:15:00' THEN TIMESTAMPDIFF(MINUTE, '08:00:00', TIME(clock_in)) ELSE 0 END as time_late,
    0 as early_leaving,
    TIMESTAMPDIFF(MINUTE, '17:15:00', TIME(clock_out)) as overtime,
    TIMESTAMPDIFF(MINUTE, TIME(clock_in), TIME(clock_out)) as total_work,
    60 as total_rest,
    'Present' as attendance_status
FROM (
    -- High overtime entries
    SELECT 63 as employee_id, '2025-09-27' as attendance_date, '08:00:00' as clock_in, '20:00:00' as clock_out -- 2h45m OT
    UNION ALL SELECT 61, '2025-09-28', '08:10:00', '19:30:00'  -- 2h15m OT (but 10m late)
    UNION ALL SELECT 62, '2025-09-25', '08:00:00', '19:00:00'  -- 1h45m OT
    UNION ALL SELECT 64, '2025-09-26', '08:30:00', '20:30:00'  -- 3h15m OT (but 30m late)
) overtime_patterns;

-- Verify the data
SELECT
    'Total September Records' as metric,
    COUNT(*) as value
FROM attendances
WHERE attendance_date BETWEEN '2025-09-01' AND '2025-09-30'

UNION ALL

SELECT
    'Employees with Attendance',
    COUNT(DISTINCT employee_id)
FROM attendances
WHERE attendance_date BETWEEN '2025-09-01' AND '2025-09-30'

UNION ALL

SELECT
    'Late Days (>15min)',
    COUNT(*)
FROM attendances
WHERE attendance_date BETWEEN '2025-09-01' AND '2025-09-30'
AND time_late > 15

UNION ALL

SELECT
    'Half Days (>120min late)',
    COUNT(*)
FROM attendances
WHERE attendance_date BETWEEN '2025-09-01' AND '2025-09-30'
AND time_late > 120

UNION ALL

SELECT
    'Overtime Days',
    COUNT(*)
FROM attendances
WHERE attendance_date BETWEEN '2025-09-01' AND '2025-09-30'
AND overtime > 0;