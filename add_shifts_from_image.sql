-- ===============================================
-- ADD SHIFTS AS PER IMAGE SPECIFICATION
-- All shifts have 15-minute grace period for lateness
-- ===============================================

USE `u902429527_ttphrm`;

-- Insert SHIFT-A (7:00 AM TO 15:45 PM)
INSERT INTO office_shifts (
    shift_name, default_shift, company_id,
    monday_in, monday_out, tuesday_in, tuesday_out, wednesday_in, wednesday_out,
    thursday_in, thursday_out, friday_in, friday_out, saturday_in, saturday_out,
    sunday_in, sunday_out, created_at, updated_at
) VALUES (
    'SHIFT-A', 'Normal', 1,
    '07:00:00', '15:45:00', '07:00:00', '15:45:00', '07:00:00', '15:45:00',
    '07:00:00', '15:45:00', '07:00:00', '15:45:00', '07:00:00', '15:45:00',
    NULL, NULL, NOW(), NOW()
);

-- Update existing GENERAL SHIFT to match image timing (8:00 AM TO 5:15 PM)
UPDATE office_shifts
SET
    monday_in = '08:00:00', monday_out = '17:15:00',
    tuesday_in = '08:00:00', tuesday_out = '17:15:00',
    wednesday_in = '08:00:00', wednesday_out = '17:15:00',
    thursday_in = '08:00:00', thursday_out = '17:15:00',
    friday_in = '08:00:00', friday_out = '17:15:00',
    saturday_in = '08:00:00', saturday_out = '17:15:00',
    updated_at = NOW()
WHERE shift_name = 'GENERAL';

-- Insert 11:00 TO 20:15 shift
INSERT INTO office_shifts (
    shift_name, default_shift, company_id,
    monday_in, monday_out, tuesday_in, tuesday_out, wednesday_in, wednesday_out,
    thursday_in, thursday_out, friday_in, friday_out, saturday_in, saturday_out,
    sunday_in, sunday_out, created_at, updated_at
) VALUES (
    '11:00 TO 20:15', 'Normal', 1,
    '11:00:00', '20:15:00', '11:00:00', '20:15:00', '11:00:00', '20:15:00',
    '11:00:00', '20:15:00', '11:00:00', '20:15:00', '11:00:00', '20:15:00',
    NULL, NULL, NOW(), NOW()
);

-- Insert SHIFT-B (3:00 PM TO 11:45 PM)
INSERT INTO office_shifts (
    shift_name, default_shift, company_id,
    monday_in, monday_out, tuesday_in, tuesday_out, wednesday_in, wednesday_out,
    thursday_in, thursday_out, friday_in, friday_out, saturday_in, saturday_out,
    sunday_in, sunday_out, created_at, updated_at
) VALUES (
    'SHIFT-B', 'Normal', 1,
    '15:00:00', '23:45:00', '15:00:00', '23:45:00', '15:00:00', '23:45:00',
    '15:00:00', '23:45:00', '15:00:00', '23:45:00', '15:00:00', '23:45:00',
    NULL, NULL, NOW(), NOW()
);

-- Insert 19:00 TO 04:15 night shift
INSERT INTO office_shifts (
    shift_name, default_shift, company_id,
    monday_in, monday_out, tuesday_in, tuesday_out, wednesday_in, wednesday_out,
    thursday_in, thursday_out, friday_in, friday_out, saturday_in, saturday_out,
    sunday_in, sunday_out, created_at, updated_at
) VALUES (
    '19:00 TO 04:15', 'Night', 1,
    '19:00:00', '04:15:00', '19:00:00', '04:15:00', '19:00:00', '04:15:00',
    '19:00:00', '04:15:00', '19:00:00', '04:15:00', '19:00:00', '04:15:00',
    NULL, NULL, NOW(), NOW()
);

-- Insert SHIFT-C (11:00 PM TO 7:15 AM)
INSERT INTO office_shifts (
    shift_name, default_shift, company_id,
    monday_in, monday_out, tuesday_in, tuesday_out, wednesday_in, wednesday_out,
    thursday_in, thursday_out, friday_in, friday_out, saturday_in, saturday_out,
    sunday_in, sunday_out, created_at, updated_at
) VALUES (
    'SHIFT-C', 'Night', 1,
    '23:00:00', '07:15:00', '23:00:00', '07:15:00', '23:00:00', '07:15:00',
    '23:00:00', '07:15:00', '23:00:00', '07:15:00', '23:00:00', '07:15:00',
    NULL, NULL, NOW(), NOW()
);

-- Verification query
SELECT
    id,
    shift_name,
    CONCAT(monday_in, ' TO ', monday_out) as shift_timing,
    default_shift,
    'AFTER 15 MIN' as late_policy,
    CASE
        WHEN shift_name = 'SHIFT-A' THEN '9:00 AM (INCOMING) / 11:00 AM (OUTGOING)'
        WHEN shift_name = 'GENERAL' THEN '10:00 AM (INCOMING) / 12:00 PM (OUTGOING)'
        WHEN shift_name = '11:00 TO 20:15' THEN '1:00 PM (INCOMING) / 3:00 PM (OUTGOING)'
        WHEN shift_name = 'SHIFT-B' THEN '5:00 PM (INCOMING) / 7:00 PM (OUTGOING)'
        WHEN shift_name = '19:00 TO 04:15' THEN '9:00 PM (INCOMING) / 11:00 PM (OUTGOING)'
        WHEN shift_name = 'SHIFT-C' THEN '1:00 AM (INCOMING) / 3:00 AM (OUTGOING)'
        ELSE 'N/A'
    END as half_day_times
FROM office_shifts
WHERE company_id = 1
ORDER BY
    CASE
        WHEN shift_name = 'SHIFT-A' THEN 1
        WHEN shift_name = 'GENERAL' THEN 2
        WHEN shift_name = '11:00 TO 20:15' THEN 3
        WHEN shift_name = 'SHIFT-B' THEN 4
        WHEN shift_name = '19:00 TO 04:15' THEN 5
        WHEN shift_name = 'SHIFT-C' THEN 6
        ELSE 7
    END;