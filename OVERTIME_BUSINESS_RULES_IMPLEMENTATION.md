# Overtime Business Rules Implementation Summary

## Business Rules Implemented

1. **Late = if employee clocks in more than 15 minutes after shift start**
2. **Half-Day = if employee clocks in more than 2 hours late → counts as 0.5 day absent (deduct 50% daily salary)**
3. **Late accumulation: every 3 late days in a month = 1 full day salary deduction**
4. **Overtime:**
   - Shift length = required_hours_per_day (default 9)
   - Overtime starts only after completing shift hours
   - Overtime hourly rate = (per-day salary ÷ required_hours) × 2 (double pay)
5. **If employee was late and also did overtime, subtract the late time from overtime first, then pay only remaining overtime**
6. **Overtime applies only if employee.overtime_allowed = true**

## Files Modified/Created

### 1. Database Changes
- **Migration**: `database/migrations/modify/2024_12_11_000000_add_overtime_allowed_to_employees_table.php`
- **SQL Script**: `update_overtime_settings.sql` (manual execution required)
- **Employee Model**: Added `overtime_allowed` and `required_hours_per_day` to fillable fields

### 2. Business Logic Changes
- **TotalSalaryTrait**: `app/Http/traits/TotalSalaryTrait.php` - Completely rewritten with new business rules
  - Added `calculateAttendanceDeductionsAndOvertime()` method
  - Added `getShiftStartTime()` method
  - Integrated attendance-based deductions and overtime calculations

### 3. Controller Changes
- **PayrollController**: `app/Http/Controllers/PayrollController.php`
  - Updated all `totalSalary()` method calls to pass `month_year` parameter
  - Updated employee select statements to include new fields
- **EmployeeController**: `app/Http/Controllers/EmployeeController.php`
  - Added `employeesOvertimeSettingsUpdate()` method

### 4. UI Changes
- **Employee Salary View**: `resources/views/employee/salary/index.blade.php`
  - Added "Overtime Settings" tab with checkbox for overtime_allowed
  - Added input for required_hours_per_day
  - Added business rules explanation
- **Profile View**: `resources/views/profile/employee_profile.blade.php`
  - Added JavaScript handlers for overtime settings tab
- **JavaScript**: `resources/views/employee/salary/overtime_settings_js.blade.php`
  - Form submission handling for overtime settings

### 5. Routing
- **Web Routes**: `routes/web.php`
  - Added route for overtime settings update

## How the New System Works

### Salary Calculation Flow
1. **Monthly Payroll Generation**: System calculates for selected month/year
2. **Attendance Analysis**: Reviews all attendance records for the month
3. **Late Detection**: Checks each day for late arrivals (> 15 minutes)
4. **Half-Day Detection**: Flags days with > 2 hours late as half-days
5. **Overtime Calculation**: 
   - Only if `overtime_allowed = true`
   - Only after completing required shift hours
   - Subtracts late time from overtime if both occur same day
6. **Salary Calculation**: Applies all deductions and additions

### Key Features
- **Automatic Calculation**: No manual overtime entry needed
- **Fair Overtime**: Only pays overtime after completing required hours
- **Late Penalty System**: Progressive penalties for late attendance
- **Configurable Settings**: Per-employee overtime permission and shift hours
- **Audit Trail**: All calculations stored in payslips table

## Installation Steps

1. **Run the Migration**:
   ```bash
   php artisan migrate
   ```
   OR manually execute: `update_overtime_settings.sql`

2. **Update Employee Settings**:
   - Go to Employee Profile → Salary → Overtime Settings
   - Configure overtime_allowed and required_hours_per_day for each employee

3. **Test Payroll Generation**:
   - Ensure attendance data exists with proper shift times
   - Run payroll for a test month
   - Verify calculations include new overtime and deduction rules

## Testing Checklist

### Database Setup
- [ ] Migration executed successfully
- [ ] All employees have `overtime_allowed` and `required_hours_per_day` columns
- [ ] Default values set (overtime_allowed = true, required_hours_per_day = 9)

### UI Testing
- [ ] Employee salary page shows "Overtime Settings" tab
- [ ] Overtime settings form saves correctly
- [ ] Business rules are clearly displayed
- [ ] Checkbox and hours input work properly

### Payroll Testing
- [ ] Monthly payroll calculation includes new rules
- [ ] Late deductions calculated correctly
- [ ] Overtime pay calculated correctly (double rate after shift completion)
- [ ] Late time subtracted from overtime when both occur
- [ ] Employees with overtime_allowed = false don't get overtime pay
- [ ] Payslip shows all calculation components

### Edge Cases to Test
- [ ] Employee late and overtime same day
- [ ] Employee with overtime_allowed = false
- [ ] Employee with different required_hours_per_day
- [ ] Month with multiple late days (test 3-day rule)
- [ ] Half-day scenarios (> 2 hours late)

## Technical Notes

- **Overtime Rate**: Calculated as (daily_salary ÷ required_hours_per_day) × 2
- **Daily Salary**: Assumes 30 days per month (basic_salary ÷ 30)
- **Shift Time**: Retrieved from office_shifts table based on day of week
- **Late Calculation**: Uses Carbon library for precise time differences
- **Data Storage**: Final calculations stored as JSON in payslips table

## Troubleshooting

1. **Migration Issues**: Execute `update_overtime_settings.sql` manually if migration fails
2. **UI Not Loading**: Clear cache and check JavaScript console for errors
3. **Calculation Errors**: Verify attendance data has proper clock_in/clock_out times
4. **Shift Time Issues**: Ensure office_shifts table has proper start times for each day

## Summary

The implementation successfully converts the manual overtime system to an automated, attendance-based system that follows all specified business rules. The UI provides easy configuration per employee, and the system automatically calculates fair overtime pay while applying appropriate late penalties.