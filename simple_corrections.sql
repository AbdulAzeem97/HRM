-- ===============================================
-- SIMPLE ATTENDANCE CORRECTIONS
-- Apply 15-minute grace period to existing records
-- ===============================================

USE `u902429527_ttphrm`;

-- Update existing attendance records to apply 15-minute grace period
UPDATE attendances a
LEFT JOIN employees e ON a.employee_id = e.id
LEFT JOIN office_shifts os ON COALESCE(a.office_shift_id, e.office_shift_id) = os.id
SET
    a.time_late = CASE
        WHEN TIME(a.clock_in) <= TIME(COALESCE(os.monday_in, '08:00:00')) THEN '00:00'
        WHEN TIMESTAMPDIFF(MINUTE,
            CONCAT(a.attendance_date, ' ', COALESCE(os.monday_in, '08:00:00')),
            CONCAT(a.attendance_date, ' ', a.clock_in)
        ) <= 15 THEN '00:00'
        ELSE a.time_late
    END
WHERE a.clock_out IS NOT NULL
AND a.attendance_date >= '2025-05-01';

-- Show corrected results for employee 65
SELECT
    a.attendance_date,
    TIME(a.clock_in) as clock_in,
    TIME(a.clock_out) as clock_out,
    a.time_late,
    a.overtime,
    CASE
        WHEN a.time_late = '00:00' AND TIME(a.clock_in) > '08:00:00' THEN 'GRACE PERIOD APPLIED'
        WHEN a.time_late != '00:00' THEN 'LATE PENALTY APPLIED'
        ELSE 'ON TIME'
    END as grace_status
FROM attendances a
WHERE a.employee_id = 65
AND a.attendance_date BETWEEN '2025-05-01' AND '2025-05-20'
ORDER BY a.attendance_date;