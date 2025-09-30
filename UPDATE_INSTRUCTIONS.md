# Database Update Instructions for Existing TTPHRM Database

## Overview
These instructions will update your existing `u902429527_ttphrm` database to match the current Laravel project requirements without losing any existing data.

## What Will Be Updated

### 1. **Employee Table Enhancements**
- ✅ Add `is_labor_employee` column (for labor/contract employee tracking)
- ✅ Add `overtime_allowed` column (overtime eligibility flag)
- ✅ Add `required_hours_per_day` column (working hours requirement)
- ✅ Add `nic` column (National Identity Card)
- ✅ Add `nic_expiry` column (NIC expiry date)
- ✅ Convert date columns to VARCHAR(10) for dd-mm-yyyy format

### 2. **New Support Tables**
- ✅ `company_types` - Company classification
- ✅ `loan_types` - Loan categories
- ✅ `relation_types` - Employee contact relationships
- ✅ `deduction_types` - Payroll deduction categories
- ✅ `job_experiences` - Experience level options

### 3. **Enhanced Data**
- ✅ Additional status options
- ✅ Default values for all new tables
- ✅ Foreign key relationships

## Step-by-Step Update Process

### Method 1: Using phpMyAdmin (Recommended)

1. **Open phpMyAdmin**
   - Go to `http://localhost/phpmyadmin/`
   - Login with your credentials

2. **Select Your Database**
   - Click on `u902429527_ttphrm` database

3. **Execute Updates**
   - Click on "SQL" tab
   - Copy and paste the contents of `simple_database_updates.sql`
   - Click "Go" to execute

### Method 2: Execute Commands Individually

If you get errors with the complete script, execute these commands one by one:

#### Step 1: Add Employee Columns
```sql
USE u902429527_ttphrm;

ALTER TABLE employees ADD COLUMN is_labor_employee TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE employees ADD COLUMN overtime_allowed TINYINT(1) NOT NULL DEFAULT 1;
ALTER TABLE employees ADD COLUMN required_hours_per_day INT(11) NOT NULL DEFAULT 9;
ALTER TABLE employees ADD COLUMN nic VARCHAR(50) NULL;
ALTER TABLE employees ADD COLUMN nic_expiry VARCHAR(10) NULL;
```

#### Step 2: Modify Date Columns
```sql
ALTER TABLE employees MODIFY COLUMN date_of_birth VARCHAR(10) NULL;
ALTER TABLE employees MODIFY COLUMN joining_date VARCHAR(10) NULL;
ALTER TABLE employees MODIFY COLUMN exit_date VARCHAR(10) NULL;
```

#### Step 3: Create Support Tables
```sql
CREATE TABLE company_types (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(191) NOT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
);

-- Repeat for other tables...
```

### Method 3: Laravel Artisan (If PHP MySQL driver works)

If you can fix the PHP MySQL driver issue:

1. **Enable MySQL Extensions**
   - Edit `C:\xampp\php\php.ini`
   - Uncomment: `extension=pdo_mysql` and `extension=mysqli`
   - Restart Apache

2. **Run Migration Commands**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

## Verification Steps

After running the updates, verify the changes:

### 1. Check Employee Table Structure
```sql
DESCRIBE employees;
```

Should show new columns:
- `is_labor_employee` TINYINT(1)
- `overtime_allowed` TINYINT(1)
- `required_hours_per_day` INT(11)
- `nic` VARCHAR(50)
- `nic_expiry` VARCHAR(10)

### 2. Check New Tables
```sql
SHOW TABLES;
```

Should include:
- company_types
- loan_types
- relation_types
- deduction_types
- job_experiences

### 3. Verify Data Counts
```sql
SELECT
    (SELECT COUNT(*) FROM employees) as total_employees,
    (SELECT COUNT(*) FROM company_types) as company_types,
    (SELECT COUNT(*) FROM loan_types) as loan_types;
```

## Important Notes

### ⚠️ Safety Measures
- **Backup First**: Always backup your database before running updates
- **Test Environment**: Run on a copy first if possible
- **Incremental Execution**: Execute commands in small batches if you encounter errors

### 🔧 Error Handling
- If you get "Column already exists" errors, it means some updates were already applied
- If foreign key errors occur, check that referenced tables exist
- Use `IF NOT EXISTS` clauses in the advanced script for safer execution

### 📊 Data Migration
- Existing employee data will be preserved
- Date formats will be automatically handled by Laravel models
- New columns will have sensible defaults

## Post-Update Actions

1. **Clear Laravel Cache**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Test Employee Creation**
   - Try creating a new employee through the application
   - Verify new fields appear in forms

3. **Test Attendance System**
   - Check attendance logging with new overtime features
   - Verify labor employee functionality

## Troubleshooting

### Common Issues:

1. **"Table doesn't exist" error**
   - Some tables might not exist in your database
   - Skip those specific commands and continue

2. **"Column already exists" error**
   - Column was already added in a previous run
   - This is safe to ignore

3. **Foreign key constraint errors**
   - Referenced table doesn't exist
   - Add the table first, then add the foreign key

## Files Created

- `simple_database_updates.sql` - Basic update script
- `update_existing_database.sql` - Advanced script with error handling
- `UPDATE_INSTRUCTIONS.md` - This instruction file

Your database will be fully compatible with your Laravel project after these updates!