-- SQL Script to update date format from YYYY-MM-DD to DD-MM-YYYY

-- Step 1: Add temporary columns for employees table
ALTER TABLE employees
ADD COLUMN date_of_birth_new VARCHAR(10) NULL,
ADD COLUMN joining_date_new VARCHAR(10) NULL,
ADD COLUMN exit_date_new VARCHAR(10) NULL,
ADD COLUMN nic_expiry_new VARCHAR(10) NULL;

-- Step 2: Convert date formats for employees
UPDATE employees
SET date_of_birth_new = CASE
    WHEN date_of_birth IS NOT NULL AND date_of_birth != '0000-00-00'
    THEN DATE_FORMAT(date_of_birth, '%d-%m-%Y')
    ELSE NULL
END;

UPDATE employees
SET joining_date_new = CASE
    WHEN joining_date IS NOT NULL AND joining_date != '0000-00-00'
    THEN DATE_FORMAT(joining_date, '%d-%m-%Y')
    ELSE NULL
END;

UPDATE employees
SET exit_date_new = CASE
    WHEN exit_date IS NOT NULL AND exit_date != '0000-00-00'
    THEN DATE_FORMAT(exit_date, '%d-%m-%Y')
    ELSE NULL
END;

UPDATE employees
SET nic_expiry_new = CASE
    WHEN nic_expiry IS NOT NULL AND nic_expiry != '0000-00-00'
    THEN DATE_FORMAT(nic_expiry, '%d-%m-%Y')
    ELSE NULL
END;

-- Step 3: Drop old columns and rename new ones for employees
ALTER TABLE employees
DROP COLUMN date_of_birth,
DROP COLUMN joining_date,
DROP COLUMN exit_date,
DROP COLUMN nic_expiry;

ALTER TABLE employees
CHANGE date_of_birth_new date_of_birth VARCHAR(10) NULL,
CHANGE joining_date_new joining_date VARCHAR(10) NULL,
CHANGE exit_date_new exit_date VARCHAR(10) NULL,
CHANGE nic_expiry_new nic_expiry VARCHAR(10) NULL;

-- Step 4: Update salary_commissions table
ALTER TABLE salary_commissions
ADD COLUMN first_date_new VARCHAR(10) NULL;

UPDATE salary_commissions
SET first_date_new = CASE
    WHEN first_date IS NOT NULL AND first_date != '0000-00-00'
    THEN DATE_FORMAT(first_date, '%d-%m-%Y')
    ELSE NULL
END;

ALTER TABLE salary_commissions
DROP COLUMN first_date;

ALTER TABLE salary_commissions
CHANGE first_date_new first_date VARCHAR(10) NULL;

-- Display completion message
SELECT 'Database update completed! All dates are now stored in dd-mm-yyyy format.' as message;