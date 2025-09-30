-- Emergency SQL Fix for Employee 61 Constraint Violation
-- Run this in phpMyAdmin or MySQL client

-- Step 1: Check if User 61 exists (most common issue)
SELECT 'Checking User 61...' as step;
SELECT COUNT(*) as user_exists FROM users WHERE id = 61;

-- Step 2: Create User 61 if it doesn't exist
INSERT IGNORE INTO users (id, username, email, password, email_verified_at, created_at, updated_at)
VALUES (61, 'employee.61', 'employee61@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW(), NOW());

-- Step 3: Check current employee 61 data
SELECT 'Current Employee 61 Data:' as step;
SELECT id, first_name, last_name, company_id, department_id, designation_id, office_shift_id, location_id, status_id, role_users_id
FROM employees WHERE id = 61;

-- Step 4: Check which foreign keys are invalid
SELECT 'Checking foreign key constraints...' as step;

-- Check company_id
SELECT 'Company ID Check:' as check_type,
       CASE WHEN EXISTS(SELECT 1 FROM companies WHERE id = (SELECT company_id FROM employees WHERE id = 61))
            THEN 'VALID'
            ELSE 'INVALID'
       END as status;

-- Check department_id
SELECT 'Department ID Check:' as check_type,
       CASE WHEN EXISTS(SELECT 1 FROM departments WHERE id = (SELECT department_id FROM employees WHERE id = 61))
            THEN 'VALID'
            ELSE 'INVALID'
       END as status;

-- Check designation_id
SELECT 'Designation ID Check:' as check_type,
       CASE WHEN EXISTS(SELECT 1 FROM designations WHERE id = (SELECT designation_id FROM employees WHERE id = 61))
            THEN 'VALID'
            ELSE 'INVALID'
       END as status;

-- Check office_shift_id
SELECT 'Office Shift ID Check:' as check_type,
       CASE WHEN EXISTS(SELECT 1 FROM office_shifts WHERE id = (SELECT office_shift_id FROM employees WHERE id = 61))
            THEN 'VALID'
            ELSE 'INVALID'
       END as status;

-- Check status_id
SELECT 'Status ID Check:' as check_type,
       CASE WHEN (SELECT status_id FROM employees WHERE id = 61) IS NULL
            THEN 'NULL (OK)'
            WHEN EXISTS(SELECT 1 FROM statuses WHERE id = (SELECT status_id FROM employees WHERE id = 61))
            THEN 'VALID'
            ELSE 'INVALID'
       END as status;

-- Step 5: Create default records if missing (optional)
INSERT IGNORE INTO companies (id, company_name, created_at, updated_at) VALUES (1, 'Default Company', NOW(), NOW());
INSERT IGNORE INTO departments (id, department_name, created_at, updated_at) VALUES (1, 'General', NOW(), NOW());
INSERT IGNORE INTO designations (id, designation_name, created_at, updated_at) VALUES (1, 'Employee', NOW(), NOW());
INSERT IGNORE INTO office_shifts (id, shift_name, start_time, end_time, created_at, updated_at) VALUES (1, 'Standard', '09:00:00', '17:00:00', NOW(), NOW());
INSERT IGNORE INTO locations (id, location, created_at, updated_at) VALUES (1, 'Main Office', NOW(), NOW());
INSERT IGNORE INTO statuses (id, status_title, created_at, updated_at) VALUES (1, 'Active', NOW(), NOW());
INSERT IGNORE INTO roles (id, name, created_at, updated_at) VALUES (1, 'Employee', NOW(), NOW());

-- Step 6: Fix invalid foreign keys by setting them to valid defaults
UPDATE employees SET
    company_id = CASE
        WHEN company_id IS NULL OR NOT EXISTS(SELECT 1 FROM companies WHERE id = employees.company_id)
        THEN (SELECT MIN(id) FROM companies)
        ELSE company_id
    END,
    department_id = CASE
        WHEN department_id IS NULL OR NOT EXISTS(SELECT 1 FROM departments WHERE id = employees.department_id)
        THEN (SELECT MIN(id) FROM departments)
        ELSE department_id
    END,
    designation_id = CASE
        WHEN designation_id IS NULL OR NOT EXISTS(SELECT 1 FROM designations WHERE id = employees.designation_id)
        THEN (SELECT MIN(id) FROM designations)
        ELSE designation_id
    END,
    office_shift_id = CASE
        WHEN office_shift_id IS NULL OR NOT EXISTS(SELECT 1 FROM office_shifts WHERE id = employees.office_shift_id)
        THEN (SELECT MIN(id) FROM office_shifts)
        ELSE office_shift_id
    END,
    location_id = CASE
        WHEN location_id IS NULL OR NOT EXISTS(SELECT 1 FROM locations WHERE id = employees.location_id)
        THEN (SELECT MIN(id) FROM locations)
        ELSE location_id
    END,
    status_id = CASE
        WHEN status_id IS NOT NULL AND NOT EXISTS(SELECT 1 FROM statuses WHERE id = employees.status_id)
        THEN NULL
        ELSE status_id
    END,
    role_users_id = CASE
        WHEN role_users_id IS NULL OR NOT EXISTS(SELECT 1 FROM roles WHERE id = employees.role_users_id)
        THEN (SELECT MIN(id) FROM roles)
        ELSE role_users_id
    END
WHERE id = 61;

-- Step 7: Test the update that was failing
UPDATE employees SET
    overtime_allowed = 1,
    required_hours_per_day = 9,
    updated_at = NOW()
WHERE id = 61;

-- Step 8: Verify the fix
SELECT 'Final verification:' as step;
SELECT id, first_name, last_name, overtime_allowed, required_hours_per_day, updated_at
FROM employees WHERE id = 61;

SELECT 'Fix completed! Employee 61 should now update without constraint violations.' as result;