-- ===============================================
-- APPLY ATTENDANCE CALCULATION CORRECTIONS
-- Update existing records with corrected business rules
-- ===============================================

USE `u902429527_ttphrm`;

-- Recreate the correction calculation table
CREATE TEMPORARY TABLE temp_attendance_corrections AS
SELECT
    a.id,
    a.employee_id,
    a.attendance_date,

    -- Apply 15-minute grace period for late time
    CASE
        WHEN TIME(a.clock_in) > TIME(COALESCE(os.monday_in, '08:00:00')) THEN
            CASE
                WHEN TIMESTAMPDIFF(MINUTE,
                    CONCAT(a.attendance_date, ' ', COALESCE(os.monday_in, '08:00:00')),
                    CONCAT(a.attendance_date, ' ', a.clock_in)
                ) > 15 THEN
                    TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND,
                        CONCAT(a.attendance_date, ' ', COALESCE(os.monday_in, '08:00:00')),
                        CONCAT(a.attendance_date, ' ', a.clock_in)
                    )), '%H:%i')
                ELSE '00:00'
            END
        ELSE '00:00'
    END as corrected_late_time,

    -- Calculate corrected overtime
    TIME_FORMAT(SEC_TO_TIME(
        GREATEST(0,
            -- Gross overtime minutes
            CASE
                WHEN TIME(a.clock_out) > TIME(COALESCE(os.monday_out, '17:15:00')) THEN
                    TIMESTAMPDIFF(MINUTE,
                        CONCAT(a.attendance_date, ' ', COALESCE(os.monday_out, '17:15:00')),
                        CONCAT(a.attendance_date, ' ', a.clock_out)
                    )
                ELSE 0
            END
            -
            -- Late deduction (only if beyond 15-minute grace)
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
        ) * 60
    ), '%H:%i') as corrected_overtime

FROM attendances a
LEFT JOIN employees e ON a.employee_id = e.id
LEFT JOIN office_shifts os ON COALESCE(a.office_shift_id, e.office_shift_id) = os.id
WHERE a.clock_out IS NOT NULL
AND a.clock_out != ''
AND a.attendance_date >= '2025-05-01';

-- Apply the corrections
UPDATE attendances a
JOIN temp_attendance_corrections t ON a.id = t.id
SET
    a.time_late = t.corrected_late_time,
    a.overtime = t.corrected_overtime
WHERE a.id = t.id;

-- Show results for employee 65
SELECT
    'CORRECTED ATTENDANCE FOR EMPLOYEE 65' as title,
    '' as details
UNION ALL
SELECT
    CONCAT('Date: ', attendance_date),
    CONCAT(
        'In: ', TIME_FORMAT(TIME(clock_in), '%H:%i'),
        ' | Out: ', TIME_FORMAT(TIME(clock_out), '%H:%i'),
        ' | Late: ', time_late,
        ' | OT: ', overtime,
        ' | Status: ',
        CASE
            WHEN time_late = '00:00' AND TIME(clock_in) > '08:00:00' THEN 'GRACE APPLIED'
            WHEN time_late != '00:00' THEN 'LATE PENALTY'
            ELSE 'ON TIME'
        END
    )
FROM attendances
WHERE employee_id = 65
AND attendance_date BETWEEN '2025-05-01' AND '2025-05-31'
ORDER BY attendance_date;

-- Summary of corrections applied
SELECT
    COUNT(*) as total_updated,
    COUNT(CASE WHEN time_late = '00:00' AND TIME(clock_in) > TIME('08:00:00') THEN 1 END) as grace_periods_applied,
    COUNT(CASE WHEN time_late != '00:00' THEN 1 END) as late_penalties,
    SUM(TIME_TO_SEC(overtime) / 3600) as total_overtime_hours
FROM attendances
WHERE employee_id = 65
AND attendance_date >= '2025-05-01';