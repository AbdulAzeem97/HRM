-- Simple test data for attendance system
USE u902429527_ttphrm;

-- Add required columns to attendances table if they don't exist
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS working_hours DECIMAL(4,2) DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS overtime_hours DECIMAL(4,2) DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS late_minutes INT DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS early_leave_minutes INT DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS shift_id INT;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS is_half_day BOOLEAN DEFAULT FALSE;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS late_deduction DECIMAL(10,2) DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS overtime_amount DECIMAL(10,2) DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS attendance_status VARCHAR(20) DEFAULT 'Present';

-- Add test employee
INSERT INTO employees (first_name, last_name, email, salary, company_id, joining_date, is_active, office_shift_id) 
VALUES ('Test', 'Employee', 'test@example.com', 50000, 1, '2024-01-01', 1, 2)
ON DUPLICATE KEY UPDATE first_name = 'Test';

-- Show current data
SELECT 'SHIFTS:' as info;
SELECT id, shift_name, monday_in, monday_out FROM office_shifts;

SELECT 'EMPLOYEES:' as info;
SELECT id, first_name, last_name, email, salary FROM employees WHERE email = 'test@example.com';

SELECT 'Test data setup complete!' as result;