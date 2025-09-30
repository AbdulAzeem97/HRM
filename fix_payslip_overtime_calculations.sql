-- ===============================================
-- FIX PAYSLIP OVERTIME CALCULATIONS
-- Update existing payslips with proper overtime data from salary_overtimes table
-- ===============================================

USE `u902429527_ttphrm`;

-- Show current payslip data before update
SELECT
    'CURRENT PAYSLIP DATA (BEFORE UPDATE)' as title,
    '' as details
UNION ALL
SELECT
    'Employee ID', 'Basic Salary | Net Salary | Overtimes'
UNION ALL
SELECT
    employee_id,
    CONCAT('$', basic_salary, ' | $', net_salary, ' | ', overtimes)
FROM payslips
WHERE month_year = 'May-2025'
AND employee_id IN (63, 64, 65);

-- Update payslips with proper overtime data
UPDATE payslips p
JOIN salary_overtimes so ON p.employee_id = so.employee_id AND p.month_year = so.month_year
SET
    p.overtimes = JSON_ARRAY(
        JSON_OBJECT(
            'overtime_title', so.overtime_title,
            'no_of_days', so.no_of_days,
            'overtime_hours', so.overtime_hours,
            'overtime_rate', so.overtime_rate,
            'overtime_amount', so.overtime_amount
        )
    ),
    -- Recalculate net salary to include overtime
    p.net_salary = p.basic_salary + CAST(REPLACE(so.overtime_amount, ',', '') AS DECIMAL(10,2))
WHERE p.month_year = 'May-2025'
AND so.month_year = 'May-2025';

-- Show updated payslip data
SELECT
    'UPDATED PAYSLIP DATA (AFTER UPDATE)' as title,
    '' as details
UNION ALL
SELECT
    'Employee ID', 'Basic Salary | Net Salary | Overtime Amount'
UNION ALL
SELECT
    p.employee_id,
    CONCAT(
        '$', p.basic_salary, ' | $', p.net_salary, ' | $',
        CASE
            WHEN so.overtime_amount IS NOT NULL THEN so.overtime_amount
            ELSE '0.00'
        END
    )
FROM payslips p
LEFT JOIN salary_overtimes so ON p.employee_id = so.employee_id AND p.month_year = so.month_year
WHERE p.month_year = 'May-2025'
AND p.employee_id IN (63, 64, 65);

-- Show detailed overtime data in payslips
SELECT
    'OVERTIME DATA IN PAYSLIPS' as title,
    '' as details
UNION ALL
SELECT
    'Employee ID', 'Overtime JSON Data'
UNION ALL
SELECT
    employee_id,
    CASE
        WHEN overtimes = '[]' OR overtimes IS NULL THEN 'NO OVERTIME'
        ELSE SUBSTRING(overtimes, 1, 100)
    END
FROM payslips
WHERE month_year = 'May-2025'
AND employee_id IN (63, 64, 65);

-- Summary of changes
SELECT
    'SUMMARY OF CHANGES' as title,
    '' as details
UNION ALL
SELECT
    'Payslips Updated', COUNT(*)
FROM payslips p
JOIN salary_overtimes so ON p.employee_id = so.employee_id AND p.month_year = so.month_year
WHERE p.month_year = 'May-2025'
UNION ALL
SELECT
    'Total Overtime Added', CONCAT('$', FORMAT(SUM(CAST(REPLACE(so.overtime_amount, ',', '') AS DECIMAL(10,2))), 2))
FROM payslips p
JOIN salary_overtimes so ON p.employee_id = so.employee_id AND p.month_year = so.month_year
WHERE p.month_year = 'May-2025';