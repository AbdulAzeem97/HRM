# Overtime Calculation System - Tables and Policy

## 📊 **Primary Tables Used for Overtime Calculation**

### 1. **`attendances` Table** (Main Source)
**Location**: Primary attendance data
```sql
-- Key columns for overtime:
- employee_id
- attendance_date
- clock_in
- clock_out
- overtime (calculated field)
- attendance_status
```

### 2. **`employees` Table** (Employee Settings)
**Location**: Employee overtime configuration
```sql
-- Key columns for overtime:
- overtime_allowed (TINYINT) - Whether employee is eligible for OT
- required_hours_per_day (INT) - Default: 9 hours
- office_shift_id - Links to shift schedule
- basic_salary - Used for OT rate calculation
```

### 3. **`office_shifts` Table** (Shift Schedules)
**Location**: Defines working hours per day
```sql
-- Key columns:
- shift_name
- start_time, end_time
- monday_in, monday_out
- tuesday_in, tuesday_out
- ... (for each day of week)
```

### 4. **`salary_overtimes` Table** (Manual OT Records)
**Location**: Additional/manual overtime entries
```sql
-- Structure:
- employee_id
- month_year
- overtime_title
- no_of_days
- overtime_hours
- overtime_rate
- overtime_amount
```

### 5. **`payslips` Table** (Final Calculation Storage)
**Location**: Consolidated overtime in payroll
```sql
-- Stores final calculated:
- overtimes (JSON field)
- total_overtime
- overtime_hours
```

## 🧮 **Overtime Calculation Policy**

### **Core Business Rules:**

#### **1. Eligibility Check**
```php
// Only employees with overtime_allowed = true get OT
if ($employee->overtime_allowed) {
    // Calculate overtime
}
```

#### **2. Overtime Detection**
```php
// OT starts after shift end time
$overtimeMinutes = $clockOutTime->gt($expectedEndTime)
    ? $clockOutTime->diffInMinutes($expectedEndTime) : 0;
```

#### **3. Late Time Adjustment**
```php
// If employee was late AND worked overtime:
if ($lateMinutes > 0 && $overtimeMinutes > 0) {
    $overtimeMinutes = max(0, $overtimeMinutes - $lateMinutes);
}
```

#### **4. Overtime Rate Calculation**
```php
// Rate = (Daily Salary ÷ Required Hours) × 2 (Double Pay)
$daily_salary = $basic_salary / 26; // 26 working days
$hourly_rate = $daily_salary / $required_hours_per_day; // Default: 9 hours
$overtime_rate = $hourly_rate * 2; // Double time
$overtime_pay = ($overtime_minutes / 60) * $overtime_rate;
```

#### **5. Maximum Limits (From Attendance Model)**
```php
// Max 2 hours per day counted for salary
$payrollOT = min($overtimeHours, 2);
$extraOT = max(0, $overtimeHours - 2);
```

## 🔄 **Calculation Flow**

### **1. Data Sources Priority:**
1. **Primary**: `attendances` table (automatic from clock in/out)
2. **Secondary**: `salary_overtimes` table (manual entries)
3. **Final**: Combined in `payslips` table

### **2. Processing Location:**
- **Main Logic**: `app/Http/traits/TotalSalaryTrait.php` → `calculateAttendanceDeductionsAndOvertime()`
- **Static Methods**: `app/Models/Attendance.php` → `calculateOvertime()`
- **Controller**: `app/Http/Controllers/PayrollController.php`

### **3. When Calculated:**
- **Real-time**: During attendance entry
- **Payroll**: When generating payslips
- **Bulk**: During bulk payroll processing

## 📋 **Policy Summary**

| **Aspect** | **Rule** | **Implementation** |
|------------|----------|-------------------|
| **Eligibility** | `overtime_allowed = true` | Employee table flag |
| **Grace Period** | 15 minutes | No OT if ≤ 15 min past shift |
| **Late Penalty** | Deduct from OT | Late time subtracted from OT |
| **Rate** | Double pay | 2x hourly rate |
| **Daily Limit** | 2 hours max** | Additional hours tracked separately |
| **Calculation** | Per attendance day | Summed monthly for payroll |

### **Formula:**
```
Overtime Pay = (Total OT Minutes ÷ 60) × (Monthly Salary ÷ 26 ÷ 9) × 2
```

**Where:**
- 26 = Working days per month
- 9 = Required hours per day (configurable per employee)
- 2 = Double pay multiplier

## 🗂️ **Database Query Example**

```sql
-- Get employee overtime data for payroll
SELECT
    e.id,
    e.first_name,
    e.overtime_allowed,
    e.required_hours_per_day,
    e.basic_salary,
    COUNT(a.id) as attendance_days,
    SUM(
        CASE
            WHEN TIME_TO_SEC(a.overtime) > 900 -- 15 minutes
            THEN TIME_TO_SEC(a.overtime) / 3600 -- Convert to hours
            ELSE 0
        END
    ) as total_overtime_hours
FROM employees e
LEFT JOIN attendances a ON e.id = a.employee_id
    AND a.attendance_date BETWEEN '2025-05-01' AND '2025-05-31'
WHERE e.overtime_allowed = 1
GROUP BY e.id;
```

## 🎯 **Key Points:**

1. **Primary Source**: Overtime is calculated from `attendances` table based on actual clock in/out times
2. **Employee Control**: `employees.overtime_allowed` flag controls eligibility
3. **Rate Calculation**: Uses `basic_salary` and `required_hours_per_day` from employees table
4. **Shift Awareness**: Uses `office_shifts` table for expected work hours
5. **Payroll Integration**: Final calculations stored in `payslips.overtimes` as JSON
6. **Manual Override**: Additional overtime can be added via `salary_overtimes` table

The system provides flexible, policy-driven overtime calculation that integrates seamlessly with attendance tracking and payroll processing.