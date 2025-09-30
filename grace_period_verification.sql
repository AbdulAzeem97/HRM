-- ===============================================
-- 15-MINUTE GRACE PERIOD IMPLEMENTATION VERIFICATION
-- ===============================================
USE `u902429527_ttphrm`;

-- Display all test cases to verify grace period implementation
SELECT
    attendance_date,
    TIME(clock_in) as clock_in,
    TIME(clock_out) as clock_out,
    TIME(shift_start_time) as shift_start,
    TIME(shift_end_time) as shift_end,
    late_minutes,
    overtime_minutes,
    net_overtime_minutes,
    overtime_amount,
    calculation_notes,
    CASE
        WHEN TIME(clock_in) > TIME(shift_start_time) THEN
            TIMESTAMPDIFF(MINUTE, CONCAT(attendance_date, ' ', shift_start_time), CONCAT(attendance_date, ' ', clock_in))
        ELSE 0
    END as raw_late_minutes,
    CASE
        WHEN TIME(clock_in) > TIME(shift_start_time) THEN
            TIMESTAMPDIFF(MINUTE, CONCAT(attendance_date, ' ', shift_start_time), CONCAT(attendance_date, ' ', clock_in))
        ELSE 0
    END <= 15 as within_grace_period
FROM overtime_calculations
WHERE employee_id = 65
ORDER BY attendance_date;

-- Summary showing grace period rules
SELECT
    '15-Minute Grace Period Implementation Summary' as rule_summary,
    '' as explanation
UNION ALL
SELECT
    '• Late 1-15 minutes', 'No penalty, full overtime credited'
UNION ALL
SELECT
    '• Late 16+ minutes', 'Late minutes deducted from overtime'
UNION ALL
SELECT
    '• Example: 10 min late, 30 min OT', 'Result: 0 late penalty, 30 min overtime'
UNION ALL
SELECT
    '• Example: 20 min late, 60 min OT', 'Result: 20 min late penalty, 40 min net overtime';