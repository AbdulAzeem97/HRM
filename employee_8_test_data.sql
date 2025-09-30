-- =====================================================
-- EMPLOYEE 68 - MR. ADAN ISHAAQ (Test Case for 11:00-20:15)
-- Perfect test case for shift detection
-- =====================================================

-- Clear existing data for Employee 68 (ADAN ISHAAQ) in January 2025
DELETE FROM attendances WHERE employee_id = 68 AND attendance_date >= '2025-01-01' AND attendance_date <= '2025-01-31';

-- Add required columns if they don't exist
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS working_hours DECIMAL(4,2) DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS overtime_hours DECIMAL(4,2) DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS late_minutes INT DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS early_leave_minutes INT DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS shift_id INT;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS is_half_day BOOLEAN DEFAULT FALSE;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS late_deduction DECIMAL(10,2) DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS overtime_amount DECIMAL(10,2) DEFAULT 0;

INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, attendance_status) VALUES
-- Week 1: Perfect 11:00-20:15 pattern
(68, '2025-01-01', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),
(68, '2025-01-02', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),
(68, '2025-01-03', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),
(68, '2025-01-04', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),
(68, '2025-01-05', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),
(68, '2025-01-06', '11:00', '16:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '05:15', 'half_day'),

-- Week 2: Variations
(68, '2025-01-08', '11:15', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:15', '00:00', '00:00', '09:00', 'present'),
(68, '2025-01-09', '11:00', '21:00', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:45', '10:00', 'present'),
(68, '2025-01-10', '11:00', '19:45', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:30', '00:00', '08:45', 'present'),
(68, '2025-01-12', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),
(68, '2025-01-13', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),

-- Week 3: More variations
(68, '2025-01-15', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),
(68, '2025-01-16', '11:30', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:30', '00:00', '00:00', '08:45', 'present'),
(68, '2025-01-17', '11:00', '21:30', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '01:15', '10:30', 'present'),
(68, '2025-01-18', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),
(68, '2025-01-19', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),
(68, '2025-01-20', '11:00', '16:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '05:15', 'half_day'),

-- Week 4: Edge cases
(68, '2025-01-22', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),
(68, '2025-01-23', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),
(68, '2025-01-24', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),
(68, '2025-01-25', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),
(68, '2025-01-26', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),
(68, '2025-01-27', '11:00', '16:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '05:15', 'half_day'),

-- Week 5: Final week
(68, '2025-01-29', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present'),
(68, '2025-01-31', '11:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:00', '09:15', 'present');

-- =====================================================
-- ADDITIONAL TEST SCENARIOS FOR EMPLOYEE 8
-- =====================================================

-- Add some extreme test cases
INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, attendance_status) VALUES
-- Very late arrival (should be half day)
(68, '2025-01-07', '13:00', '20:15', '192.168.1.101', '192.168.1.101', 1, '02:00', '00:00', '00:00', '07:15', 'half_day'),

-- Maximum overtime (2 hours)
(68, '2025-01-11', '11:00', '22:15', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '02:00', '11:15', 'present'),

-- Very early leave
(68, '2025-01-14', '11:00', '15:00', '192.168.1.101', '192.168.1.101', 1, '00:00', '05:15', '00:00', '04:00', 'half_day'),

-- Late with overtime
(68, '2025-01-21', '11:45', '21:00', '192.168.1.101', '192.168.1.101', 1, '00:45', '00:00', '00:45', '09:15', 'present'),

-- Perfect attendance with slight variations
(68, '2025-01-28', '10:55', '20:20', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:05', '09:25', 'present'),

-- Early arrival with overtime
(68, '2025-01-30', '10:45', '20:30', '192.168.1.101', '192.168.1.101', 1, '00:00', '00:00', '00:15', '09:45', 'present');

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================

-- Show Employee 8's attendance summary
SELECT 
    'EMPLOYEE 8 ATTENDANCE SUMMARY' as info,
    COUNT(*) as total_days,
    SUM(CASE WHEN time_late > '00:00' THEN 1 ELSE 0 END) as late_days,
    SUM(CASE WHEN overtime > '00:00' THEN 1 ELSE 0 END) as overtime_days,
    SUM(CASE WHEN early_leaving > '00:00' THEN 1 ELSE 0 END) as early_leave_days,
    SUM(CASE WHEN attendance_status = 'half_day' THEN 1 ELSE 0 END) as half_days,
    SUM(CASE WHEN attendance_status = 'present' THEN 1 ELSE 0 END) as full_days
FROM attendances 
WHERE employee_id = 8 AND attendance_date >= '2025-01-01' AND attendance_date <= '2025-01-31';

-- Show detailed attendance records for Employee 8
SELECT 
    attendance_date,
    clock_in,
    clock_out,
    total_work,
    time_late,
    early_leaving,
    overtime,
    attendance_status,
    CASE 
        WHEN clock_in = '11:00' AND clock_out = '20:15' THEN 'Perfect 11:00-20:15'
        WHEN clock_in BETWEEN '10:45' AND '11:15' AND clock_out BETWEEN '20:00' AND '20:30' THEN 'Near Perfect'
        WHEN time_late > '00:00' THEN 'Late Arrival'
        WHEN overtime > '00:00' THEN 'Overtime'
        WHEN early_leaving > '00:00' THEN 'Early Leave'
        WHEN attendance_status = 'half_day' THEN 'Half Day'
        ELSE 'Other'
    END as pattern_type
FROM attendances 
WHERE employee_id = 8 AND attendance_date >= '2025-01-01' AND attendance_date <= '2025-01-31'
ORDER BY attendance_date;

-- Test shift detection for Employee 8
SELECT 
    attendance_date,
    clock_in,
    clock_out,
    CASE 
        WHEN clock_in BETWEEN '10:30' AND '12:30' THEN '11:00-20:15 Shift Detected'
        ELSE 'Other Shift'
    END as shift_detection,
    total_work as working_hours
FROM attendances 
WHERE employee_id = 8 AND attendance_date >= '2025-01-01' AND attendance_date <= '2025-01-31'
ORDER BY attendance_date;

SELECT 'Employee 8 test data inserted successfully!' as result;
