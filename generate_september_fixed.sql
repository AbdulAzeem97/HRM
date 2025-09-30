-- Generate comprehensive September 2025 attendance for all employees
USE u902429527_ttphrm;

-- First, create basic attendance for all active employees (22 working days in September)
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status)
SELECT
    e.id,
    d.attendance_date,
    '08:05:00',
    '17:20:00',
    '192.168.1.100',
    '192.168.1.100',
    '08:05:00 - 17:20:00',
    5, -- 5 minutes late (within tolerance)
    0,
    5, -- 5 minutes overtime
    555, -- 9h15m total work
    60,
    'Present'
FROM employees e
CROSS JOIN (
    SELECT '2025-09-01' as attendance_date
    UNION ALL SELECT '2025-09-02' UNION ALL SELECT '2025-09-03' UNION ALL SELECT '2025-09-04' UNION ALL SELECT '2025-09-05'
    UNION ALL SELECT '2025-09-08' UNION ALL SELECT '2025-09-09' UNION ALL SELECT '2025-09-10' UNION ALL SELECT '2025-09-11' UNION ALL SELECT '2025-09-12'
    UNION ALL SELECT '2025-09-15' UNION ALL SELECT '2025-09-16' UNION ALL SELECT '2025-09-17' UNION ALL SELECT '2025-09-18' UNION ALL SELECT '2025-09-19'
    UNION ALL SELECT '2025-09-22' UNION ALL SELECT '2025-09-23' UNION ALL SELECT '2025-09-24' UNION ALL SELECT '2025-09-25' UNION ALL SELECT '2025-09-26'
    UNION ALL SELECT '2025-09-29' UNION ALL SELECT '2025-09-30'
) d
WHERE e.is_active = 1 AND e.exit_date IS NULL;

-- Add varied patterns for testing
-- Late arrivals (16-60 minutes) for some employees
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status) VALUES
(61, '2025-09-06', '08:35:00', '17:15:00', '192.168.1.100', '192.168.1.100', '08:35:00 - 17:15:00', 35, 0, 0, 520, 60, 'Present'),
(62, '2025-09-09', '08:45:00', '17:30:00', '192.168.1.100', '192.168.1.100', '08:45:00 - 17:30:00', 45, 0, 15, 525, 60, 'Present'),
(64, '2025-09-12', '08:25:00', '18:00:00', '192.168.1.100', '192.168.1.100', '08:25:00 - 18:00:00', 25, 0, 45, 575, 60, 'Present'),
(66, '2025-09-15', '08:55:00', '17:40:00', '192.168.1.100', '192.168.1.100', '08:55:00 - 17:40:00', 55, 0, 25, 525, 60, 'Present'),
(68, '2025-09-18', '08:40:00', '17:50:00', '192.168.1.100', '192.168.1.100', '08:40:00 - 17:50:00', 40, 0, 35, 550, 60, 'Present');

-- Half-day arrivals (>120 minutes late)
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status) VALUES
(65, '2025-09-05', '10:30:00', '17:15:00', '192.168.1.100', '192.168.1.100', '10:30:00 - 17:15:00', 150, 0, 0, 405, 60, 'Half Day'),
(67, '2025-09-10', '11:00:00', '18:00:00', '192.168.1.100', '192.168.1.100', '11:00:00 - 18:00:00', 180, 0, 45, 420, 60, 'Half Day'),
(69, '2025-09-16', '10:45:00', '17:30:00', '192.168.1.100', '192.168.1.100', '10:45:00 - 17:30:00', 165, 0, 15, 405, 60, 'Half Day'),
(70, '2025-09-23', '11:30:00', '17:45:00', '192.168.1.100', '192.168.1.100', '11:30:00 - 17:45:00', 210, 0, 30, 375, 60, 'Half Day');

-- High overtime days
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status) VALUES
(63, '2025-09-27', '08:00:00', '20:00:00', '192.168.1.100', '192.168.1.100', '08:00:00 - 20:00:00', 0, 0, 165, 720, 60, 'Present'),
(61, '2025-09-28', '08:10:00', '19:30:00', '192.168.1.100', '192.168.1.100', '08:10:00 - 19:30:00', 10, 0, 135, 680, 60, 'Present'),
(62, '2025-09-25', '08:00:00', '19:00:00', '192.168.1.100', '192.168.1.100', '08:00:00 - 19:00:00', 0, 0, 105, 660, 60, 'Present'),
(64, '2025-09-26', '08:30:00', '20:30:00', '192.168.1.100', '192.168.1.100', '08:30:00 - 20:30:00', 30, 0, 195, 720, 60, 'Present');

-- Show summary
SELECT
    'Total Records' as metric, COUNT(*) as value FROM attendances WHERE attendance_date BETWEEN '2025-09-01' AND '2025-09-30'
UNION ALL SELECT 'Unique Employees', COUNT(DISTINCT employee_id) FROM attendances WHERE attendance_date BETWEEN '2025-09-01' AND '2025-09-30'
UNION ALL SELECT 'Late Days (>15min)', COUNT(*) FROM attendances WHERE attendance_date BETWEEN '2025-09-01' AND '2025-09-30' AND time_late > 15
UNION ALL SELECT 'Half Days (>120min)', COUNT(*) FROM attendances WHERE attendance_date BETWEEN '2025-09-01' AND '2025-09-30' AND time_late > 120
UNION ALL SELECT 'Overtime Days', COUNT(*) FROM attendances WHERE attendance_date BETWEEN '2025-09-01' AND '2025-09-30' AND overtime > 0;