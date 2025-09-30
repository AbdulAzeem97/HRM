-- Add overtime_allowed and required_hours_per_day columns to employees table
ALTER TABLE employees 
ADD COLUMN overtime_allowed BOOLEAN DEFAULT TRUE AFTER is_labor_employee,
ADD COLUMN required_hours_per_day INT DEFAULT 9 AFTER overtime_allowed;

-- Update all existing employees to have overtime allowed by default
UPDATE employees SET overtime_allowed = TRUE WHERE overtime_allowed IS NULL;
UPDATE employees SET required_hours_per_day = 9 WHERE required_hours_per_day IS NULL;

-- Verify the changes
SELECT id, first_name, last_name, overtime_allowed, required_hours_per_day 
FROM employees 
LIMIT 10;