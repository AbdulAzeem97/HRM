# TTPHRM Database Restoration Guide

## Overview
Your Laravel project database has been analyzed and reconstructed. This guide provides step-by-step instructions to restore your database schema.

## Database Analysis Summary

### Detected Tables and Columns:

#### Core Tables:
1. **users** - Authentication system (Laravel default + customizations)
2. **employees** - Main employee management table with additional fields:
   - `is_labor_employee` (boolean) - Labor/contract employee flag
   - `overtime_allowed` (boolean) - Overtime eligibility
   - `required_hours_per_day` (integer) - Required working hours
   - `nic`, `nic_expiry` - National ID fields
   - Date fields stored as VARCHAR in dd-mm-yyyy format
3. **attendances** - Employee attendance tracking
4. **companies**, **departments**, **designations**, **locations** - Organizational structure
5. **office_shifts**, **statuses**, **roles**, **permissions** - Configuration tables

#### Additional HR Tables:
- Salary management (salary_basics, salary_allowances, salary_deductions, etc.)
- Leave management (leaves, leave_types)
- Project management (projects, tasks)
- Training and performance management
- Finance management (invoices, transactions, deposits)

### Key Features Discovered:
- **Labor Employee System** - Special handling for contract/labor employees
- **Advanced Attendance System** - Multiple shift support with overtime calculations
- **Salary Management** - Complete payroll system with allowances/deductions
- **Custom Date Format** - Uses dd-mm-yyyy format stored as VARCHAR
- **Multi-company Support** - Hierarchical company/department structure

## Restoration Methods

### Method 1: Using phpMyAdmin (Recommended)

1. **Open phpMyAdmin**
   - Go to `http://localhost/phpmyadmin/`
   - Login with your MySQL credentials

2. **Create Database**
   - Click "New" to create a new database
   - Enter database name: `u902429527_ttphrm`
   - Set collation to: `utf8mb4_unicode_ci`
   - Click "Create"

3. **Import SQL File**
   - Select the newly created database
   - Click "Import" tab
   - Click "Choose File" and select: `complete_database_structure.sql`
   - Click "Go" to execute

### Method 2: Using MySQL Command Line

```bash
# Navigate to XAMPP MySQL bin directory
cd C:\xampp\mysql\bin

# Connect to MySQL and create database
mysql.exe -u root -p
CREATE DATABASE u902429527_ttphrm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE u902429527_ttphrm;
SOURCE C:\xampp\htdocs\ttphrm\complete_database_structure.sql;
EXIT;
```

### Method 3: Using Laravel Artisan (If PHP MySQL driver is fixed)

1. **Fix PHP MySQL Extension**
   - Edit `C:\xampp\php\php.ini`
   - Uncomment these lines:
     ```
     extension=pdo_mysql
     extension=mysqli
     ```
   - Restart Apache

2. **Run Laravel Commands**
   ```bash
   php artisan migrate:fresh --seed
   ```

## Database Schema Details

### Core Employee Fields:
```sql
-- Employee table structure
employees (
    id, first_name, last_name, staff_id, email, contact_no,
    date_of_birth (VARCHAR dd-mm-yyyy), gender, office_shift_id,
    company_id, department_id, designation_id, location_id,
    role_users_id, status_id, joining_date (VARCHAR dd-mm-yyyy),
    exit_date (VARCHAR dd-mm-yyyy), marital_status, address, city,
    state, country, zip_code, nic, nic_expiry (VARCHAR dd-mm-yyyy),
    cv, skype_id, fb_id, twitter_id, linkedIn_id, whatsapp_id,
    basic_salary, payslip_type, attendance_type, pension_type,
    pension_amount, is_active, is_labor_employee, overtime_allowed,
    required_hours_per_day, created_at, updated_at
)
```

### Attendance System:
```sql
-- Attendance table structure
attendances (
    id, employee_id, attendance_date, clock_in, clock_in_ip,
    clock_out, clock_out_ip, clock_in_out, time_late,
    early_leaving, overtime, total_work, total_rest,
    attendance_status
)
```

## Default Data Included

### Users & Roles:
- **Super Admin**: username: `superadmin`, email: `admin@ttphrm.com`
- **Roles**: super-admin, admin, hr, employee
- **Permissions**: manage-employees, manage-attendance, manage-payroll, view-reports, manage-settings

### Organizational Structure:
- **Company**: TTPHRM Company
- **Departments**: HR, IT, Finance
- **Designations**: Manager, Senior Developer, HR Officer
- **Office Shifts**: General (08:00-17:15), Shift-A (07:00-15:45), Shift-B (15:00-23:45), Shift-C (23:00-07:15)

### Status Options:
- Active, Inactive, Suspended, Terminated

## Post-Installation Steps

1. **Update .env file** (if needed):
   ```
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=u902429527_ttphrm
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. **Clear Laravel cache**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

3. **Test the application**:
   - Visit: `http://localhost/ttphrm`
   - Login with: username: `superadmin`, password: `password`

## Troubleshooting

### Common Issues:

1. **MySQL Driver Missing**:
   - Enable `pdo_mysql` and `mysqli` extensions in php.ini
   - Restart Apache server

2. **Database Connection Error**:
   - Verify MySQL service is running
   - Check .env database credentials
   - Ensure database exists

3. **Foreign Key Constraints**:
   - Tables are created in proper order
   - All foreign key relationships are maintained

4. **Date Format Issues**:
   - Employee date fields use VARCHAR(10) for dd-mm-yyyy format
   - Model accessors/mutators handle date conversion

## Files Created

1. `complete_database_structure.sql` - Complete database schema with data
2. `database_reconstruction_script.php` - PHP script for database creation
3. `setup_database.php` - Alternative PHP setup script
4. `DATABASE_RESTORATION_GUIDE.md` - This guide

## Contact Information

If you encounter any issues:
1. Verify XAMPP MySQL is running
2. Check PHP MySQL extensions are enabled
3. Ensure database credentials are correct
4. Review Laravel logs for specific errors

The database structure is now fully compatible with your current Laravel project code and includes all necessary relationships and data types.