-- ===============================================
-- GENERATE OVERTIME CALCULATIONS FOR EMPLOYEE 62 - MAY 2025
-- ===============================================

USE `u902429527_ttphrm`;

-- Delete existing overtime calculations for employee 62 in May 2025
DELETE FROM overtime_calculations WHERE employee_id = 62 AND attendance_date BETWEEN '2025-05-01' AND '2025-05-31';
DELETE FROM salary_overtimes WHERE employee_id = 62 AND month_year = 'May-2025';

-- Insert overtime calculations for employee 62
INSERT INTO overtime_calculations (
    employee_id, attendance_date, clock_in, clock_out, shift_start_time, shift_end_time,
    working_minutes, shift_minutes, late_minutes, overtime_minutes, net_overtime_minutes,
    hourly_rate, overtime_rate, overtime_amount, overtime_eligible, required_hours_per_day,
    basic_salary, shift_name, status
)
SELECT
    a.employee_id, a.attendance_date, TIME(a.clock_in), TIME(a.clock_out),
    TIME('08:00:00'), TIME('17:15:00'),
    TIMESTAMPDIFF(MINUTE, CONCAT(a.attendance_date, ' ', a.clock_in), CONCAT(a.attendance_date, ' ', a.clock_out)),
    555, -- Standard shift minutes (9h 15m)

    -- Late minutes after 15-minute grace period
    CASE
        WHEN TIME(a.clock_in) > TIME('08:00:00') THEN
            CASE
                WHEN TIMESTAMPDIFF(MINUTE, CONCAT(a.attendance_date, ' 08:00:00'), CONCAT(a.attendance_date, ' ', a.clock_in)) > 15 THEN
                    TIMESTAMPDIFF(MINUTE, CONCAT(a.attendance_date, ' 08:00:00'), CONCAT(a.attendance_date, ' ', a.clock_in))
                ELSE 0
            END
        ELSE 0
    END,

    -- Gross overtime minutes
    CASE
        WHEN TIME(a.clock_out) > TIME('17:15:00') THEN
            TIMESTAMPDIFF(MINUTE, CONCAT(a.attendance_date, ' 17:15:00'), CONCAT(a.attendance_date, ' ', a.clock_out))
        ELSE 0
    END,

    -- Net overtime minutes (gross overtime minus late deduction)
    GREATEST(0,
        CASE
            WHEN TIME(a.clock_out) > TIME('17:15:00') THEN
                TIMESTAMPDIFF(MINUTE, CONCAT(a.attendance_date, ' 17:15:00'), CONCAT(a.attendance_date, ' ', a.clock_out))
            ELSE 0
        END
        -
        CASE
            WHEN TIME(a.clock_in) > TIME('08:00:00') THEN
                CASE
                    WHEN TIMESTAMPDIFF(MINUTE, CONCAT(a.attendance_date, ' 08:00:00'), CONCAT(a.attendance_date, ' ', a.clock_in)) > 15 THEN
                        TIMESTAMPDIFF(MINUTE, CONCAT(a.attendance_date, ' 08:00:00'), CONCAT(a.attendance_date, ' ', a.clock_in))
                    ELSE 0
                END
            ELSE 0
        END
    ),

    ROUND((41440 / 26 / 9), 2), -- hourly_rate
    ROUND((41440 / 26 / 9) * 2, 2), -- overtime_rate

    -- overtime_amount
    ROUND(
        (GREATEST(0,
            CASE
                WHEN TIME(a.clock_out) > TIME('17:15:00') THEN
                    TIMESTAMPDIFF(MINUTE, CONCAT(a.attendance_date, ' 17:15:00'), CONCAT(a.attendance_date, ' ', a.clock_out))
                ELSE 0
            END
            -
            CASE
                WHEN TIME(a.clock_in) > TIME('08:00:00') THEN
                    CASE
                        WHEN TIMESTAMPDIFF(MINUTE, CONCAT(a.attendance_date, ' 08:00:00'), CONCAT(a.attendance_date, ' ', a.clock_in)) > 15 THEN
                            TIMESTAMPDIFF(MINUTE, CONCAT(a.attendance_date, ' 08:00:00'), CONCAT(a.attendance_date, ' ', a.clock_in))
                        ELSE 0
                    END
                ELSE 0
            END
        ) / 60.0) * ((41440 / 26 / 9) * 2), 2
    ),

    1, 9, 41440, 'GENERAL', 'calculated'

FROM attendances a
WHERE a.employee_id = 62
AND a.clock_out IS NOT NULL
AND a.attendance_date BETWEEN '2025-05-01' AND '2025-05-31'
GROUP BY a.employee_id, a.attendance_date, a.clock_in, a.clock_out; -- Remove duplicates

-- Create salary_overtimes entry
INSERT INTO salary_overtimes (
    employee_id, month_year, first_date, overtime_title, no_of_days,
    overtime_hours, overtime_rate, overtime_amount, created_at, updated_at
)
SELECT
    62, 'May-2025', '2025-05-01', 'Attendance Overtime',
    COUNT(CASE WHEN net_overtime_minutes > 0 THEN 1 END),
    FORMAT(SUM(net_overtime_minutes) / 60, 2),
    FORMAT(AVG(overtime_rate), 2),
    FORMAT(SUM(overtime_amount), 2),
    NOW(), NOW()
FROM overtime_calculations
WHERE employee_id = 62 AND attendance_date BETWEEN '2025-05-01' AND '2025-05-31'
AND net_overtime_minutes > 0;

-- Show results
SELECT
    'EMPLOYEE 62 OVERTIME SUMMARY' as title,
    '' as details
UNION ALL
SELECT
    'Total OT Days', COUNT(CASE WHEN net_overtime_minutes > 0 THEN 1 END)
FROM overtime_calculations
WHERE employee_id = 62 AND attendance_date BETWEEN '2025-05-01' AND '2025-05-31'
UNION ALL
SELECT
    'Total OT Hours', ROUND(SUM(net_overtime_minutes) / 60, 2)
FROM overtime_calculations
WHERE employee_id = 62 AND attendance_date BETWEEN '2025-05-01' AND '2025-05-31'
UNION ALL
SELECT
    'Total OT Amount', CONCAT('$', FORMAT(SUM(overtime_amount), 2))
FROM overtime_calculations
WHERE employee_id = 62 AND attendance_date BETWEEN '2025-05-01' AND '2025-05-31';