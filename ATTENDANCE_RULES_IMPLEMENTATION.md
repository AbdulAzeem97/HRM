# Enhanced Attendance System Implementation

## Overview
The PeoplePro HR system has been enhanced with automatic shift detection, late policy calculations, overtime management, and comprehensive reporting according to your specified requirements.

## Features Implemented

### 1. Shift Auto-Detection
- **Smart Attendance**: System automatically detects employee shift based on punch-in time
- **Supported Shifts**:
  - Shift-A: 07:00 – 15:45
  - General: 08:00 – 17:15
  - 11:00–20:15 Shift
  - Shift-B: 15:00 – 23:45
  - 19:00–04:15 Shift
  - Shift-C: 23:00 – 07:15
- **Tolerance**: 2-hour tolerance window for shift detection

### 2. Late Policy Implementation
- **Grace Period**: 15 minutes grace period (no deduction)
- **Deduction Formula**: `(Monthly Salary / 26 / 8 / 60) × Late Minutes`
- **Half Day Cutoffs**:
  - Shift-A: 09:00 AM
  - General: 10:00 AM
  - 11:00–20:15: 01:00 PM
  - Shift-B: 05:00 PM
  - 19:00–04:15: 09:00 PM
  - Shift-C: 01:00 AM

### 3. Outgoing Half Day Policy
- **Cutoff Times**:
  - Shift-A: 11:00 AM
  - General: 12:00 PM
  - 11:00–20:15: 03:00 PM
  - Shift-B: 07:00 PM
  - 19:00–04:15: 11:00 PM
  - Shift-C: 03:00 AM

### 4. Overtime Calculations (Labor Class)
- **Minimum OT**: Only counted after 15 minutes of duty end time
- **Formula**: `(Monthly Salary / 26 / 8) × 2 × OT Hours`
- **Late Adjustment**: Late minutes are deducted from OT minutes
- **Daily Limit**: Maximum 2 hours per day added to payroll
- **Extra OT**: Hours above 2 exported to separate report

### 5. Per Day Salary Formula
- **Formula**: `Monthly Salary / 26`

## API Endpoints

### Process Biometric Attendance
```
POST /api/attendance/process-biometric

Body:
{
    "employee_id": 1,
    "punch_in_time": "2024-01-15 08:30:00",
    "punch_out_time": "2024-01-15 17:45:00",
    "attendance_date": "2024-01-15"
}

Response:
{
    "success": true,
    "attendance": {...},
    "shift_detected": "General",
    "calculations": {
        "late_minutes": 30,
        "late_deduction": 125.50,
        "overtime_hours": 0.5,
        "overtime_amount": 250.00,
        "extra_ot_hours": 0,
        "is_half_day": false,
        "working_hours": 9.25,
        "shift_duration": 8.25
    }
}
```

### Test Shift Detection
```
GET /api/attendance/test-shift-detection?punch_time=08:15:00

Response:
{
    "punch_time": "08:15:00",
    "detected_shift": "General"
}
```

### Daily Attendance Report
```
GET /api/attendance/daily-report?date=2024-01-15&company_id=1

Response:
{
    "success": true,
    "date": "2024-01-15",
    "report": [
        {
            "employee_name": "John Doe",
            "employee_id": 1,
            "shift_name": "General",
            "in_time": "08:30:00",
            "out_time": "17:45:00",
            "working_hours": 9.25,
            "late_minutes": 30,
            "late_deduction": "125.50",
            "overtime_hours": 0.5,
            "overtime_amount": "250.00",
            "is_half_day": "No",
            "attendance_status": "Present",
            "per_day_salary": "1923.08"
        }
    ]
}
```

### Extra OT Report
```
GET /api/attendance/extra-ot-report?date=2024-01-15

Response:
{
    "success": true,
    "date": "2024-01-15",
    "extra_ot_report": [
        {
            "employee_name": "Jane Smith",
            "employee_id": 2,
            "shift_name": "General",
            "total_ot_hours": 3.5,
            "payroll_ot_hours": 2,
            "extra_ot_hours": 1.5,
            "extra_ot_rate": "240.38",
            "extra_ot_amount": "360.58"
        }
    ]
}
```

### Attendance Summary
```
GET /api/attendance/summary?employee_id=1&start_date=2024-01-01&end_date=2024-01-31

Response:
{
    "employee_id": 1,
    "period": "2024-01-01 to 2024-01-31",
    "summary": {
        "total_days": 22,
        "total_working_hours": 184.5,
        "total_overtime_hours": 15,
        "total_late_minutes": 120,
        "total_late_deduction": 1250.50,
        "total_overtime_amount": 7500.00,
        "half_days": 2,
        "on_time_days": 18,
        "late_days": 4
    }
}
```

## Web Routes

### Enhanced Reports
- `GET /report/daily_attendance_enhanced` - Enhanced daily attendance report
- `GET /report/extra_ot_report` - Extra OT hours report
- `GET /report/attendance_summary` - Employee attendance summary

## Database Requirements

### Additional Attendance Table Columns
Make sure your `attendances` table has these columns:
```sql
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS working_hours DECIMAL(4,2) DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS overtime_hours DECIMAL(4,2) DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS late_minutes INT DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS early_leave_minutes INT DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS shift_id INT;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS is_half_day BOOLEAN DEFAULT FALSE;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS late_deduction DECIMAL(10,2) DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS overtime_amount DECIMAL(10,2) DEFAULT 0;
ALTER TABLE attendances ADD COLUMN IF NOT EXISTS attendance_status VARCHAR(20) DEFAULT 'Present';
```

### Office Shifts Setup
Run this SQL to set up your shifts:
```sql
INSERT INTO office_shifts (shift_name, monday_in, monday_out, tuesday_in, tuesday_out, wednesday_in, wednesday_out, thursday_in, thursday_out, friday_in, friday_out, saturday_in, saturday_out, sunday_in, sunday_out, company_id, default_shift) VALUES
('Shift-A', '07:00:00', '15:45:00', '07:00:00', '15:45:00', '07:00:00', '15:45:00', '07:00:00', '15:45:00', '07:00:00', '15:45:00', '07:00:00', '15:45:00', '07:00:00', '15:45:00', 1, 0),
('General', '08:00:00', '17:15:00', '08:00:00', '17:15:00', '08:00:00', '17:15:00', '08:00:00', '17:15:00', '08:00:00', '17:15:00', '08:00:00', '17:15:00', '08:00:00', '17:15:00', 1, 1),
('11:00-20:15', '11:00:00', '20:15:00', '11:00:00', '20:15:00', '11:00:00', '20:15:00', '11:00:00', '20:15:00', '11:00:00', '20:15:00', '11:00:00', '20:15:00', '11:00:00', '20:15:00', 1, 0),
('Shift-B', '15:00:00', '23:45:00', '15:00:00', '23:45:00', '15:00:00', '23:45:00', '15:00:00', '23:45:00', '15:00:00', '23:45:00', '15:00:00', '23:45:00', '15:00:00', '23:45:00', 1, 0),
('19:00-04:15', '19:00:00', '04:15:00', '19:00:00', '04:15:00', '19:00:00', '04:15:00', '19:00:00', '04:15:00', '19:00:00', '04:15:00', '19:00:00', '04:15:00', '19:00:00', '04:15:00', 1, 0),
('Shift-C', '23:00:00', '07:15:00', '23:00:00', '07:15:00', '23:00:00', '07:15:00', '23:00:00', '07:15:00', '23:00:00', '07:15:00', '23:00:00', '07:15:00', '23:00:00', '07:15:00', 1, 0);
```

## Usage Examples

### 1. Processing Biometric Data
When your biometric device sends punch data:
```php
use App\Services\AttendanceProcessor;

$result = AttendanceProcessor::processAttendance(
    $employeeId = 1,
    $punchInTime = '2024-01-15 08:30:00',
    $punchOutTime = '2024-01-15 17:45:00',
    $attendanceDate = '2024-01-15'
);
```

### 2. Generating Reports
```php
// Daily report
$dailyReport = AttendanceProcessor::generateDailyReport('2024-01-15', $companyId = 1);

// Extra OT report
$extraOTReport = AttendanceProcessor::getExtraOTReport('2024-01-15', $companyId = 1);
```

## Files Modified/Created

1. **Enhanced Models**:
   - `app/Models/Attendance.php` - Added calculation methods
   
2. **New Service**:
   - `app/Services/AttendanceProcessor.php` - Main processing logic
   
3. **Enhanced Controller**:
   - `app/Http/Controllers/AttendanceController.php` - Added new methods
   
4. **Routes**:
   - `routes/web.php` - Added web routes for reports
   - `routes/api.php` - Added API endpoints

## Testing

### Test Shift Detection
```bash
curl -X GET "http://localhost/ttphrm/api/attendance/test-shift-detection?punch_time=08:15:00"
```

### Test Biometric Processing
```bash
curl -X POST "http://localhost/ttphrm/api/attendance/process-biometric" \
  -H "Content-Type: application/json" \
  -d '{
    "employee_id": 1,
    "punch_in_time": "2024-01-15 08:30:00",
    "punch_out_time": "2024-01-15 17:45:00",
    "attendance_date": "2024-01-15"
  }'
```

## Next Steps

1. **Database Migration**: Run the SQL commands to add required columns
2. **Shift Setup**: Insert shift data using provided SQL
3. **Testing**: Test with sample data to verify calculations
4. **Integration**: Connect your biometric devices to the new API endpoints
5. **Reporting**: Use the enhanced reports for payroll processing

The system now automatically handles all your attendance rules including shift detection, late calculations, half-day policies, and overtime management according to your specifications.