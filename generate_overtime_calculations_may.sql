-- ===============================================
-- GENERATE OVERTIME CALCULATIONS FOR MAY 2025
-- Process attendance data to populate overtime_calculations table
-- ===============================================

USE `u902429527_ttphrm`;

-- Delete existing overtime calculations for May 2025 for clean start
DELETE FROM overtime_calculations
WHERE attendance_date BETWEEN '2025-05-01' AND '2025-05-31';

-- Insert overtime calculations based on attendance data
INSERT INTO overtime_calculations (
    employee_id,
    attendance_date,
    clock_in,
    clock_out,
    shift_start_time,
    shift_end_time,
    working_minutes,
    shift_minutes,
    late_minutes,
    overtime_minutes,
    net_overtime_minutes,
    hourly_rate,
    overtime_rate,
    overtime_amount,
    overtime_eligible,
    required_hours_per_day,
    basic_salary,
    shift_name,
    status
)
SELECT
    a.employee_id,
    a.attendance_date,
    TIME(a.clock_in) as clock_in,
    TIME(a.clock_out) as clock_out,
    TIME(COALESCE(os.monday_in, '08:00:00')) as shift_start_time,
    TIME(COALESCE(os.monday_out, '17:15:00')) as shift_end_time,

    -- Working minutes (total time from clock_in to clock_out)
    TIMESTAMPDIFF(MINUTE,
        CONCAT(a.attendance_date, ' ', a.clock_in),
        CONCAT(a.attendance_date, ' ', a.clock_out)
    ) as working_minutes,

    -- Shift minutes (standard shift duration)
    TIMESTAMPDIFF(MINUTE,
        CONCAT(a.attendance_date, ' ', COALESCE(os.monday_in, '08:00:00')),
        CONCAT(a.attendance_date, ' ', COALESCE(os.monday_out, '17:15:00'))
    ) as shift_minutes,

    -- Late minutes after 15-minute grace period
    CASE
        WHEN TIME(a.clock_in) > TIME(COALESCE(os.monday_in, '08:00:00')) THEN
            CASE
                WHEN TIMESTAMPDIFF(MINUTE,
                    CONCAT(a.attendance_date, ' ', COALESCE(os.monday_in, '08:00:00')),
                    CONCAT(a.attendance_date, ' ', a.clock_in)
                ) > 15 THEN
                    TIMESTAMPDIFF(MINUTE,
                        CONCAT(a.attendance_date, ' ', COALESCE(os.monday_in, '08:00:00')),
                        CONCAT(a.attendance_date, ' ', a.clock_in)
                    )
                ELSE 0
            END
        ELSE 0
    END as late_minutes,

    -- Gross overtime minutes (time beyond shift end)
    CASE
        WHEN TIME(a.clock_out) > TIME(COALESCE(os.monday_out, '17:15:00')) THEN
            TIMESTAMPDIFF(MINUTE,
                CONCAT(a.attendance_date, ' ', COALESCE(os.monday_out, '17:15:00')),
                CONCAT(a.attendance_date, ' ', a.clock_out)
            )
        ELSE 0
    END as overtime_minutes,

    -- Net overtime minutes (gross overtime minus late deduction)
    GREATEST(0,
        CASE
            WHEN TIME(a.clock_out) > TIME(COALESCE(os.monday_out, '17:15:00')) THEN
                TIMESTAMPDIFF(MINUTE,
                    CONCAT(a.attendance_date, ' ', COALESCE(os.monday_out, '17:15:00')),
                    CONCAT(a.attendance_date, ' ', a.clock_out)
                )
            ELSE 0
        END
        -
        CASE
            WHEN TIME(a.clock_in) > TIME(COALESCE(os.monday_in, '08:00:00')) THEN
                CASE
                    WHEN TIMESTAMPDIFF(MINUTE,
                        CONCAT(a.attendance_date, ' ', COALESCE(os.monday_in, '08:00:00')),
                        CONCAT(a.attendance_date, ' ', a.clock_in)
                    ) > 15 THEN
                        TIMESTAMPDIFF(MINUTE,
                            CONCAT(a.attendance_date, ' ', COALESCE(os.monday_in, '08:00:00')),
                            CONCAT(a.attendance_date, ' ', a.clock_in)
                        )
                    ELSE 0
                END
            ELSE 0
        END
    ) as net_overtime_minutes,

    -- Hourly rate (basic salary / 26 working days / required hours per day)
    ROUND((sb.basic_salary / 26 / COALESCE(e.required_hours_per_day, 9)), 2) as hourly_rate,

    -- Overtime rate (double the hourly rate)
    ROUND((sb.basic_salary / 26 / COALESCE(e.required_hours_per_day, 9)) * 2, 2) as overtime_rate,

    -- Overtime amount (net overtime minutes * overtime rate / 60)
    ROUND(
        (GREATEST(0,
            CASE
                WHEN TIME(a.clock_out) > TIME(COALESCE(os.monday_out, '17:15:00')) THEN
                    TIMESTAMPDIFF(MINUTE,
                        CONCAT(a.attendance_date, ' ', COALESCE(os.monday_out, '17:15:00')),
                        CONCAT(a.attendance_date, ' ', a.clock_out)
                    )
                ELSE 0
            END
            -
            CASE
                WHEN TIME(a.clock_in) > TIME(COALESCE(os.monday_in, '08:00:00')) THEN
                    CASE
                        WHEN TIMESTAMPDIFF(MINUTE,
                            CONCAT(a.attendance_date, ' ', COALESCE(os.monday_in, '08:00:00')),
                            CONCAT(a.attendance_date, ' ', a.clock_in)
                        ) > 15 THEN
                            TIMESTAMPDIFF(MINUTE,
                                CONCAT(a.attendance_date, ' ', COALESCE(os.monday_in, '08:00:00')),
                                CONCAT(a.attendance_date, ' ', a.clock_in)
                            )
                        ELSE 0
                    END
                ELSE 0
            END
        ) / 60) * ((sb.basic_salary / 26 / COALESCE(e.required_hours_per_day, 9)) * 2), 2
    ) as overtime_amount,

    -- Overtime eligible (1 if employee.overtime_allowed = 1)
    CASE WHEN e.overtime_allowed = 1 THEN 1 ELSE 0 END as overtime_eligible,

    -- Required hours per day
    COALESCE(e.required_hours_per_day, 9) as required_hours_per_day,

    -- Basic salary
    sb.basic_salary,

    -- Shift name
    COALESCE(os.shift_name, 'GENERAL') as shift_name,

    -- Status
    'calculated' as status

FROM attendances a
LEFT JOIN employees e ON a.employee_id = e.id
LEFT JOIN office_shifts os ON COALESCE(a.office_shift_id, e.office_shift_id) = os.id
LEFT JOIN salary_basics sb ON e.id = sb.employee_id
WHERE a.clock_out IS NOT NULL
AND a.clock_out != ''
AND a.attendance_date BETWEEN '2025-05-01' AND '2025-05-31'
AND e.overtime_allowed = 1
AND sb.first_date <= '2025-05-01'
ORDER BY sb.first_date DESC, a.employee_id, a.attendance_date;

-- Show the results for employee 63
SELECT
    'GENERATED OVERTIME CALCULATIONS FOR EMPLOYEE 63' as title,
    '' as details
UNION ALL
SELECT
    'Date', 'Clock In | Clock Out | Late Min | OT Min | OT Amount'
UNION ALL
SELECT
    attendance_date,
    CONCAT(
        clock_in, ' | ',
        clock_out, ' | ',
        late_minutes, ' min | ',
        net_overtime_minutes, ' min | ',
        '$', overtime_amount
    )
FROM overtime_calculations
WHERE employee_id = 63
AND attendance_date BETWEEN '2025-05-01' AND '2025-05-31'
ORDER BY attendance_date;

-- Summary for all employees
SELECT
    'SUMMARY FOR MAY 2025' as title,
    '' as details
UNION ALL
SELECT
    'Total Records Created', COUNT(*)
FROM overtime_calculations
WHERE attendance_date BETWEEN '2025-05-01' AND '2025-05-31'
UNION ALL
SELECT
    'Employees with Overtime', COUNT(DISTINCT employee_id)
FROM overtime_calculations
WHERE attendance_date BETWEEN '2025-05-01' AND '2025-05-31'
AND net_overtime_minutes > 0
UNION ALL
SELECT
    'Total Overtime Hours', ROUND(SUM(net_overtime_minutes)/60, 2)
FROM overtime_calculations
WHERE attendance_date BETWEEN '2025-05-01' AND '2025-05-31'
UNION ALL
SELECT
    'Total Overtime Pay', CONCAT('$', ROUND(SUM(overtime_amount), 2))
FROM overtime_calculations
WHERE attendance_date BETWEEN '2025-05-01' AND '2025-05-31';