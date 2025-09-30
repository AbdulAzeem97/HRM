-- ===============================================
-- OVERTIME SUMMARY WITH SHIFT CHANGES
-- ===============================================

USE `u902429527_ttphrm`;

-- Show overtime calculations by shift
SELECT
    attendance_date,
    shift_name,
    TIME(clock_in) as clock_in,
    TIME(clock_out) as clock_out,
    TIME(shift_start_time) as shift_start,
    TIME(shift_end_time) as shift_end,
    late_minutes,
    overtime_minutes,
    net_overtime_minutes,
    overtime_amount,
    calculation_notes
FROM overtime_calculations
WHERE employee_id = 65
ORDER BY attendance_date;

-- Monthly summary by shift
SELECT
    shift_name,
    COUNT(*) as calculation_days,
    SUM(net_overtime_minutes) as total_ot_minutes,
    ROUND(SUM(net_overtime_minutes) / 60.0, 2) as total_ot_hours,
    SUM(overtime_amount) as total_ot_pay,
    COUNT(CASE WHEN net_overtime_minutes > 0 THEN 1 END) as days_with_overtime
FROM overtime_calculations
WHERE employee_id = 65 AND attendance_date BETWEEN '2025-05-01' AND '2025-05-31'
GROUP BY shift_name
ORDER BY MIN(attendance_date);

-- Overall monthly summary
SELECT
    'OVERALL MAY 2025 SUMMARY' as summary_type,
    COUNT(*) as total_days,
    SUM(net_overtime_minutes) as total_ot_minutes,
    ROUND(SUM(net_overtime_minutes) / 60.0, 2) as total_ot_hours,
    SUM(overtime_amount) as total_ot_pay,
    COUNT(CASE WHEN net_overtime_minutes > 0 THEN 1 END) as days_with_overtime,
    COUNT(CASE WHEN late_minutes > 0 THEN 1 END) as late_days
FROM overtime_calculations
WHERE employee_id = 65 AND attendance_date BETWEEN '2025-05-01' AND '2025-05-31';