-- Check what employees exist in the database
-- Run this first to find the correct employee IDs

-- Option 1: Check if employees with staff_id 35, 1, 2 exist
SELECT id, staff_id, first_name, last_name 
FROM employees 
WHERE staff_id IN ('35', '1', '2') AND is_active = 1;

-- Option 2: Show all active employees (first 20)
SELECT id, staff_id, first_name, last_name 
FROM employees 
WHERE is_active = 1 
ORDER BY id 
LIMIT 20;

-- Option 3: Search for employees with similar staff_ids
SELECT id, staff_id, first_name, last_name 
FROM employees 
WHERE (staff_id LIKE '%35%' OR staff_id LIKE '%1%' OR staff_id LIKE '%2%') 
  AND is_active = 1
ORDER BY staff_id;

-- Option 4: Check employees table structure
DESCRIBE employees;

-- After running these queries:
-- 1. If you find employees with staff_id 35, 1, 2 - note their actual 'id' values
-- 2. If those staff_ids don't exist, choose 3 employees from the list above
-- 3. Replace the employee_id values in the INSERT statements with the correct 'id' values