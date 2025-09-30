-- ===============================================
-- CREATE SALARY OVERTIMES ENTRIES FOR MAY 2025
-- Populate salary_overtimes table from overtime_calculations data
-- ===============================================

USE `u902429527_ttphrm`;

-- Delete existing salary overtime entries for May 2025
DELETE FROM salary_overtimes WHERE month_year = 'May-2025';

-- Insert summarized overtime data from overtime_calculations into salary_overtimes
INSERT INTO salary_overtimes (
    employee_id,
    month_year,
    first_date,
    overtime_title,
    no_of_days,
    overtime_hours,
    overtime_rate,
    overtime_amount,
    created_at,
    updated_at
)
SELECT
    oc.employee_id,
    'May-2025' as month_year,
    '2025-05-01' as first_date,
    'Attendance Overtime' as overtime_title,
    COUNT(CASE WHEN oc.net_overtime_minutes > 0 THEN 1 END) as no_of_days,
    FORMAT(SUM(oc.net_overtime_minutes) / 60, 2) as overtime_hours,
    FORMAT(AVG(oc.overtime_rate), 2) as overtime_rate,
    FORMAT(SUM(oc.overtime_amount), 2) as overtime_amount,
    NOW() as created_at,
    NOW() as updated_at
FROM overtime_calculations oc
WHERE oc.attendance_date BETWEEN '2025-05-01' AND '2025-05-31'
AND oc.net_overtime_minutes > 0
GROUP BY oc.employee_id;

-- Show results
SELECT
    'SALARY OVERTIMES CREATED FOR MAY 2025' as title,
    '' as details
UNION ALL
SELECT
    'Employee ID', 'OT Title | Days | Hours | Rate | Amount'
UNION ALL
SELECT
    employee_id,
    CONCAT(
        overtime_title, ' | ',
        no_of_days, ' days | ',
        overtime_hours, ' hrs | ',
        '$', overtime_rate, '/hr | ',
        '$', overtime_amount
    )
FROM salary_overtimes
WHERE month_year = 'May-2025';

-- Summary
SELECT
    'SUMMARY FOR MAY 2025 SALARY OVERTIMES' as title,
    '' as details
UNION ALL
SELECT
    'Total Employees with OT', COUNT(*)
FROM salary_overtimes
WHERE month_year = 'May-2025'
UNION ALL
SELECT
    'Total OT Hours', SUM(CAST(REPLACE(overtime_hours, ',', '') AS DECIMAL(10,2)))
FROM salary_overtimes
WHERE month_year = 'May-2025'
UNION ALL
SELECT
    'Total OT Amount', CONCAT('$', FORMAT(SUM(CAST(REPLACE(overtime_amount, ',', '') AS DECIMAL(10,2))), 2))
FROM salary_overtimes
WHERE month_year = 'May-2025';

-- Verify employee 63 specifically
SELECT
    'EMPLOYEE 63 OVERTIME VERIFICATION' as title,
    '' as details
UNION ALL
SELECT
    'Field', 'Value'
UNION ALL
SELECT
    'Employee ID', employee_id
FROM salary_overtimes
WHERE employee_id = 63 AND month_year = 'May-2025'
UNION ALL
SELECT
    'Month Year', month_year
FROM salary_overtimes
WHERE employee_id = 63 AND month_year = 'May-2025'
UNION ALL
SELECT
    'Overtime Hours', overtime_hours
FROM salary_overtimes
WHERE employee_id = 63 AND month_year = 'May-2025'
UNION ALL
SELECT
    'Overtime Amount', CONCAT('$', overtime_amount)
FROM salary_overtimes
WHERE employee_id = 63 AND month_year = 'May-2025';