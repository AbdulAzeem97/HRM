-- ===============================================
-- FIX EXISTING ATTENDANCE CALCULATIONS
-- Apply 15-minute grace period and correct overtime logic to existing records
-- ===============================================

USE `u902429527_ttphrm`;

-- Create a temporary table to store corrected calculations
CREATE TEMPORARY TABLE temp_attendance_corrections AS
SELECT
    a.id,
    a.employee_id,
    a.attendance_date,
    TIME(a.clock_in) as clock_in,
    TIME(a.clock_out) as clock_out,

    -- Get shift times (using Monday as default for simplicity)
    TIME(COALESCE(os.monday_in, '08:00:00')) as shift_start,
    TIME(COALESCE(os.monday_out, '17:15:00')) as shift_end,

    -- Calculate raw late minutes
    CASE
        WHEN TIME(a.clock_in) > TIME(COALESCE(os.monday_in, '08:00:00')) THEN
            TIMESTAMPDIFF(MINUTE,
                CONCAT(a.attendance_date, ' ', COALESCE(os.monday_in, '08:00:00')),
                CONCAT(a.attendance_date, ' ', a.clock_in)
            )
        ELSE 0
    END as raw_late_minutes,

    -- Apply 15-minute grace period for late time
    CASE
        WHEN TIME(a.clock_in) > TIME(COALESCE(os.monday_in, '08:00:00')) THEN
            CASE
                WHEN TIMESTAMPDIFF(MINUTE,
                    CONCAT(a.attendance_date, ' ', COALESCE(os.monday_in, '08:00:00')),
                    CONCAT(a.attendance_date, ' ', a.clock_in)
                ) > 15 THEN
                    SEC_TO_TIME(TIMESTAMPDIFF(SECOND,
                        CONCAT(a.attendance_date, ' ', COALESCE(os.monday_in, '08:00:00')),
                        CONCAT(a.attendance_date, ' ', a.clock_in)
                    ))
                ELSE '00:00:00'
            END
        ELSE '00:00:00'
    END as corrected_late_time,

    -- Calculate gross overtime (time beyond shift end)
    CASE
        WHEN TIME(a.clock_out) > TIME(COALESCE(os.monday_out, '17:15:00')) THEN
            TIMESTAMPDIFF(MINUTE,
                CONCAT(a.attendance_date, ' ', COALESCE(os.monday_out, '17:15:00')),
                CONCAT(a.attendance_date, ' ', a.clock_out)
            )
        ELSE 0
    END as gross_overtime_minutes,

    -- Calculate late deduction (only if beyond 15-minute grace)
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
    END as late_deduction_minutes,

    -- Current values for comparison
    a.time_late as old_late_time,
    a.overtime as old_overtime

FROM attendances a
LEFT JOIN employees e ON a.employee_id = e.id
LEFT JOIN office_shifts os ON COALESCE(a.office_shift_id, e.office_shift_id) = os.id
WHERE a.clock_out IS NOT NULL
AND a.clock_out != ''
AND a.attendance_date >= '2025-05-01'  -- Only recent records
ORDER BY a.attendance_date, a.employee_id;

-- Calculate corrected overtime (gross overtime minus late deduction)
ALTER TABLE temp_attendance_corrections
ADD COLUMN net_overtime_minutes INT DEFAULT 0,
ADD COLUMN corrected_overtime_time TIME DEFAULT '00:00:00';

UPDATE temp_attendance_corrections
SET
    net_overtime_minutes = GREATEST(0, gross_overtime_minutes - late_deduction_minutes),
    corrected_overtime_time = SEC_TO_TIME(GREATEST(0, gross_overtime_minutes - late_deduction_minutes) * 60);

-- Show comparison of old vs new calculations
SELECT
    'ATTENDANCE CALCULATION CORRECTIONS' as title,
    COUNT(*) as total_records,
    SUM(CASE WHEN old_late_time != TIME_FORMAT(corrected_late_time, '%H:%i') THEN 1 ELSE 0 END) as late_corrections,
    SUM(CASE WHEN old_overtime != TIME_FORMAT(corrected_overtime_time, '%H:%i') THEN 1 ELSE 0 END) as overtime_corrections
FROM temp_attendance_corrections;

-- Show sample corrections for verification
SELECT
    attendance_date,
    employee_id,
    TIME_FORMAT(clock_in, '%H:%i') as clock_in,
    TIME_FORMAT(clock_out, '%H:%i') as clock_out,
    TIME_FORMAT(shift_start, '%H:%i') as shift_start,
    TIME_FORMAT(shift_end, '%H:%i') as shift_end,
    raw_late_minutes,
    old_late_time,
    TIME_FORMAT(corrected_late_time, '%H:%i') as new_late_time,
    gross_overtime_minutes,
    late_deduction_minutes,
    net_overtime_minutes,
    old_overtime,
    TIME_FORMAT(corrected_overtime_time, '%H:%i') as new_overtime,
    CASE
        WHEN old_late_time != TIME_FORMAT(corrected_late_time, '%H:%i') OR
             old_overtime != TIME_FORMAT(corrected_overtime_time, '%H:%i')
        THEN 'NEEDS UPDATE'
        ELSE 'CORRECT'
    END as status
FROM temp_attendance_corrections
WHERE employee_id = 65
ORDER BY attendance_date
LIMIT 10;

-- UPDATE STATEMENT (uncomment to apply corrections)
/*
UPDATE attendances a
JOIN temp_attendance_corrections t ON a.id = t.id
SET
    a.time_late = TIME_FORMAT(t.corrected_late_time, '%H:%i'),
    a.overtime = TIME_FORMAT(t.corrected_overtime_time, '%H:%i')
WHERE a.id = t.id;
*/

-- Business Rules Summary
SELECT
    '' as separator,
    'CORRECTED BUSINESS RULES:' as rules
UNION ALL
SELECT
    '1. Late Calculation', 'Apply 15-minute grace period'
UNION ALL
SELECT
    '2. Overtime Calculation', 'Start after shift end time'
UNION ALL
SELECT
    '3. Late Deduction', 'Deduct only if beyond 15-min grace'
UNION ALL
SELECT
    '4. Net Overtime', 'Gross overtime minus late deduction';