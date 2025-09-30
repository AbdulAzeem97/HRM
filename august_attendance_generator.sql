-- August 2025 Attendance Data Generator for All Employees
-- This script creates diverse attendance scenarios including:
-- - Normal work days
-- - Overtime scenarios
-- - Early leaving
-- - Late arrivals
-- - Half days
-- - Mixed patterns for testing

-- First, let's get all active employees
-- You can run this to see employee list: SELECT id, first_name, last_name FROM employees WHERE is_active = 1 AND exit_date IS NULL;

-- August 2025 attendance data with mixed scenarios
-- August has 31 days (working days exclude weekends based on your shift pattern)

-- Sample data for Employee ID 1 (you can modify employee IDs as needed)
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status) VALUES

-- Week 1 August (Aug 1-7, 2025)
-- Employee 1 - Mixed scenarios
(1, '2025-08-01', '08:00', '17:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:00', '09:00', '01:00', 'Present'), -- Normal day
(1, '2025-08-02', '08:15', '17:30', '127.0.0.1', '127.0.0.1', 0, '00:15', '00:00', '00:30', '09:15', '00:45', 'Present'), -- Late arrival + overtime
(1, '2025-08-04', '08:00', '16:30', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:30', '00:00', '08:30', '01:30', 'Early Leave'), -- Early leaving
(1, '2025-08-05', '08:30', '18:00', '127.0.0.1', '127.0.0.1', 0, '00:30', '00:00', '01:00', '09:30', '00:30', 'Present'), -- Late + overtime
(1, '2025-08-06', '08:00', '17:15', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:15', '09:15', '00:45', 'Present'), -- Normal + small overtime
(1, '2025-08-07', '08:00', '12:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '05:00', '00:00', '04:00', '00:00', 'Half Day'), -- Half day

-- Week 2 August (Aug 8-14, 2025)
(1, '2025-08-08', '07:45', '17:45', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '01:00', '10:00', '00:00', 'Present'), -- Early arrival + overtime
(1, '2025-08-09', '08:45', '17:00', '127.0.0.1', '127.0.0.1', 0, '00:45', '00:00', '00:00', '08:15', '01:45', 'Present'), -- Very late arrival
(1, '2025-08-11', '08:00', '19:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '03:00', '11:00', '00:00', 'Present'), -- Heavy overtime
(1, '2025-08-12', '08:00', '16:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '01:00', '00:00', '08:00', '02:00', 'Early Leave'), -- Early leaving
(1, '2025-08-13', '08:10', '17:20', '127.0.0.1', '127.0.0.1', 0, '00:10', '00:00', '00:20', '09:10', '00:50', 'Present'), -- Normal with small variations
(1, '2025-08-14', '08:00', '17:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:00', '09:00', '01:00', 'Present'), -- Perfect timing

-- Week 3 August (Aug 15-21, 2025)
(1, '2025-08-15', '08:20', '18:30', '127.0.0.1', '127.0.0.1', 0, '00:20', '00:00', '01:30', '10:10', '00:00', 'Present'), -- Late + heavy overtime
(1, '2025-08-16', '08:00', '13:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '04:00', '00:00', '05:00', '00:00', 'Half Day'), -- Half day afternoon off
(1, '2025-08-18', '07:30', '16:30', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:30', '00:00', '09:00', '01:00', 'Present'), -- Early arrival, early leave
(1, '2025-08-19', '08:00', '20:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '04:00', '12:00', '00:00', 'Present'), -- Maximum overtime
(1, '2025-08-20', '09:00', '17:00', '127.0.0.1', '127.0.0.1', 0, '01:00', '00:00', '00:00', '08:00', '02:00', 'Present'), -- Very late arrival
(1, '2025-08-21', '08:00', '17:30', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:30', '09:30', '00:30', 'Present'), -- Normal + overtime

-- Week 4 August (Aug 22-28, 2025)
(1, '2025-08-22', '08:05', '16:55', '127.0.0.1', '127.0.0.1', 0, '00:05', '00:05', '00:00', '08:50', '01:10', 'Present'), -- Slight variations
(1, '2025-08-23', '08:00', '12:30', '127.0.0.1', '127.0.0.1', 0, '00:00', '04:30', '00:00', '04:30', '00:00', 'Half Day'), -- Half day
(1, '2025-08-25', '07:45', '18:15', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '01:30', '10:30', '00:00', 'Present'), -- Early + overtime
(1, '2025-08-26', '08:30', '17:00', '127.0.0.1', '127.0.0.1', 0, '00:30', '00:00', '00:00', '08:30', '01:30', 'Present'), -- Late arrival
(1, '2025-08-27', '08:00', '19:30', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '02:30', '11:30', '00:00', 'Present'), -- Heavy overtime
(1, '2025-08-28', '08:00', '17:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:00', '09:00', '01:00', 'Present'), -- Normal day

-- Final days of August (Aug 29-31, 2025)
(1, '2025-08-29', '08:15', '16:45', '127.0.0.1', '127.0.0.1', 0, '00:15', '00:15', '00:00', '08:30', '01:30', 'Present'), -- Late + early leave
(1, '2025-08-30', '08:00', '18:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '01:00', '10:00', '00:00', 'Present'), -- Overtime
(1, '2025-08-31', '08:00', '17:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:00', '09:00', '01:00', 'Present'); -- Month end normal

-- Employee 2 - Different pattern
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status) VALUES
(2, '2025-08-01', '08:30', '17:30', '127.0.0.1', '127.0.0.1', 0, '00:30', '00:00', '00:30', '09:00', '01:00', 'Present'),
(2, '2025-08-02', '08:00', '16:30', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:30', '00:00', '08:30', '01:30', 'Early Leave'),
(2, '2025-08-04', '07:45', '18:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '01:15', '10:15', '00:00', 'Present'),
(2, '2025-08-05', '08:45', '17:00', '127.0.0.1', '127.0.0.1', 0, '00:45', '00:00', '00:00', '08:15', '01:45', 'Present'),
(2, '2025-08-06', '08:00', '12:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '05:00', '00:00', '04:00', '00:00', 'Half Day'),
(2, '2025-08-07', '08:00', '19:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '03:00', '11:00', '00:00', 'Present');

-- Employee 3 - Another pattern
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status) VALUES
(3, '2025-08-01', '08:00', '17:15', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:15', '09:15', '00:45', 'Present'),
(3, '2025-08-02', '08:20', '17:00', '127.0.0.1', '127.0.0.1', 0, '00:20', '00:00', '00:00', '08:40', '01:20', 'Present'),
(3, '2025-08-04', '08:00', '20:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '04:00', '12:00', '00:00', 'Present'),
(3, '2025-08-05', '09:00', '17:00', '127.0.0.1', '127.0.0.1', 0, '01:00', '00:00', '00:00', '08:00', '02:00', 'Present'),
(3, '2025-08-06', '08:00', '13:30', '127.0.0.1', '127.0.0.1', 0, '00:00', '03:30', '00:00', '05:30', '00:00', 'Half Day'),
(3, '2025-08-07', '07:30', '17:30', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '01:00', '10:00', '00:00', 'Present');

-- Continue similar patterns for more employees...
-- For bulk generation, you can use the template below and modify employee_id

-- TEMPLATE FOR MORE EMPLOYEES (Replace {EMPLOYEE_ID} with actual employee ID)
/*
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status) VALUES
({EMPLOYEE_ID}, '2025-08-01', '08:00', '17:00', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:00', '00:00', '09:00', '01:00', 'Present'),
({EMPLOYEE_ID}, '2025-08-02', '08:15', '17:30', '127.0.0.1', '127.0.0.1', 0, '00:15', '00:00', '00:30', '09:15', '00:45', 'Present'),
({EMPLOYEE_ID}, '2025-08-04', '08:00', '16:30', '127.0.0.1', '127.0.0.1', 0, '00:00', '00:30', '00:00', '08:30', '01:30', 'Early Leave'),
-- Add more days as needed...
*/

-- Verification Query
-- SELECT employee_id, COUNT(*) as total_days,
--        SUM(CASE WHEN attendance_status = 'Present' THEN 1 ELSE 0 END) as present_days,
--        SUM(CASE WHEN attendance_status = 'Half Day' THEN 1 ELSE 0 END) as half_days,
--        SUM(CASE WHEN attendance_status = 'Early Leave' THEN 1 ELSE 0 END) as early_leaves
-- FROM attendances
-- WHERE attendance_date BETWEEN '2025-08-01' AND '2025-08-31'
-- GROUP BY employee_id
-- ORDER BY employee_id;