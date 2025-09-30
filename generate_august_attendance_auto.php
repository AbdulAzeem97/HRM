<?php
/**
 * Automatic August 2025 Attendance Generator
 * This PHP script generates attendance data for ALL active employees
 * Run this file via: php generate_august_attendance_auto.php
 */

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "Starting August 2025 Attendance Generation...\n";

// Get all active employees
$employees = Employee::where('is_active', 1)
    ->whereNull('exit_date')
    ->select('id', 'first_name', 'last_name')
    ->get();

echo "Found " . $employees->count() . " active employees\n";

// August 2025 working days (excluding weekends - adjust based on your business days)
$workingDays = [
    '2025-08-01', '2025-08-02', '2025-08-04', '2025-08-05', '2025-08-06', '2025-08-07', '2025-08-08',
    '2025-08-09', '2025-08-11', '2025-08-12', '2025-08-13', '2025-08-14', '2025-08-15', '2025-08-16',
    '2025-08-18', '2025-08-19', '2025-08-20', '2025-08-21', '2025-08-22', '2025-08-23', '2025-08-25',
    '2025-08-26', '2025-08-27', '2025-08-28', '2025-08-29', '2025-08-30', '2025-08-31'
];

// Different attendance patterns
$patterns = [
    'normal' => [
        'clock_in' => '08:00',
        'clock_out' => '17:00',
        'time_late' => '00:00',
        'early_leaving' => '00:00',
        'overtime' => '00:00',
        'total_work' => '09:00',
        'total_rest' => '01:00',
        'status' => 'Present'
    ],
    'late_arrival' => [
        'clock_in' => '08:30',
        'clock_out' => '17:00',
        'time_late' => '00:30',
        'early_leaving' => '00:00',
        'overtime' => '00:00',
        'total_work' => '08:30',
        'total_rest' => '01:30',
        'status' => 'Present'
    ],
    'overtime' => [
        'clock_in' => '08:00',
        'clock_out' => '18:30',
        'time_late' => '00:00',
        'early_leaving' => '00:00',
        'overtime' => '01:30',
        'total_work' => '10:30',
        'total_rest' => '00:00',
        'status' => 'Present'
    ],
    'early_leave' => [
        'clock_in' => '08:00',
        'clock_out' => '16:00',
        'time_late' => '00:00',
        'early_leaving' => '01:00',
        'overtime' => '00:00',
        'total_work' => '08:00',
        'total_rest' => '02:00',
        'status' => 'Early Leave'
    ],
    'half_day' => [
        'clock_in' => '08:00',
        'clock_out' => '12:30',
        'time_late' => '00:00',
        'early_leaving' => '04:30',
        'overtime' => '00:00',
        'total_work' => '04:30',
        'total_rest' => '00:00',
        'status' => 'Half Day'
    ],
    'heavy_overtime' => [
        'clock_in' => '08:00',
        'clock_out' => '20:00',
        'time_late' => '00:00',
        'early_leaving' => '00:00',
        'overtime' => '04:00',
        'total_work' => '12:00',
        'total_rest' => '00:00',
        'status' => 'Present'
    ],
    'late_with_overtime' => [
        'clock_in' => '08:45',
        'clock_out' => '18:15',
        'time_late' => '00:45',
        'early_leaving' => '00:00',
        'overtime' => '00:30',
        'total_work' => '09:30',
        'total_rest' => '00:30',
        'status' => 'Present'
    ]
];

// Clear existing August 2025 data (optional)
echo "Clearing existing August 2025 data...\n";
DB::table('attendances')
    ->whereBetween('attendance_date', ['2025-08-01', '2025-08-31'])
    ->delete();

$totalRecords = 0;

foreach ($employees as $employee) {
    echo "Processing Employee ID: {$employee->id} - {$employee->first_name} {$employee->last_name}\n";

    foreach ($workingDays as $index => $date) {
        // Create varied patterns for different employees and days
        $patternType = 'normal'; // Default

        // Add variation based on employee ID and day
        $dayOfMonth = (int) substr($date, -2);
        $employeeId = $employee->id;

        // Generate different patterns based on employee ID and date
        if ($dayOfMonth % 7 == 0) {
            $patternType = 'half_day';
        } elseif ($employeeId % 5 == 0 && $dayOfMonth % 3 == 0) {
            $patternType = 'heavy_overtime';
        } elseif ($employeeId % 3 == 0 && $dayOfMonth % 5 == 0) {
            $patternType = 'early_leave';
        } elseif ($employeeId % 2 == 0 && $dayOfMonth % 4 == 0) {
            $patternType = 'overtime';
        } elseif ($employeeId % 4 == 0) {
            $patternType = 'late_arrival';
        } elseif (($employeeId + $dayOfMonth) % 6 == 0) {
            $patternType = 'late_with_overtime';
        }

        // Skip some days randomly (simulate absences)
        if (($employeeId + $dayOfMonth) % 15 == 0) {
            continue; // Skip this day (absence)
        }

        $pattern = $patterns[$patternType];

        // Add some random variation to times
        $clockIn = $pattern['clock_in'];
        $clockOut = $pattern['clock_out'];

        // Random variation of ±10 minutes for more realistic data
        if (rand(1, 10) > 7) {
            $variation = rand(-10, 10);
            $clockInTime = Carbon::parse($clockIn)->addMinutes($variation);
            $clockIn = $clockInTime->format('H:i');

            // Adjust other fields based on the variation
            if ($variation > 0) {
                $pattern['time_late'] = sprintf('%02d:%02d', 0, $variation);
            }
        }

        try {
            Attendance::create([
                'employee_id' => $employee->id,
                'attendance_date' => $date,
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'clock_in_ip' => '127.0.0.1',
                'clock_out_ip' => '127.0.0.1',
                'clock_in_out' => 0,
                'time_late' => $pattern['time_late'],
                'early_leaving' => $pattern['early_leaving'],
                'overtime' => $pattern['overtime'],
                'total_work' => $pattern['total_work'],
                'total_rest' => $pattern['total_rest'],
                'attendance_status' => $pattern['status']
            ]);

            $totalRecords++;
        } catch (Exception $e) {
            echo "Error for Employee {$employee->id} on {$date}: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== ATTENDANCE GENERATION COMPLETE ===\n";
echo "Total records created: {$totalRecords}\n";
echo "Employees processed: " . $employees->count() . "\n";

// Generate summary report
echo "\n=== SUMMARY REPORT ===\n";
$summary = DB::table('attendances')
    ->select(
        'employee_id',
        DB::raw('COUNT(*) as total_days'),
        DB::raw('SUM(CASE WHEN attendance_status = "Present" THEN 1 ELSE 0 END) as present_days'),
        DB::raw('SUM(CASE WHEN attendance_status = "Half Day" THEN 1 ELSE 0 END) as half_days'),
        DB::raw('SUM(CASE WHEN attendance_status = "Early Leave" THEN 1 ELSE 0 END) as early_leaves')
    )
    ->whereBetween('attendance_date', ['2025-08-01', '2025-08-31'])
    ->groupBy('employee_id')
    ->orderBy('employee_id')
    ->limit(10)
    ->get();

echo "Sample Summary (First 10 employees):\n";
echo "Employee ID | Total Days | Present | Half Days | Early Leaves\n";
echo "------------|------------|---------|-----------|-------------\n";
foreach ($summary as $record) {
    printf("%-11s | %-10s | %-7s | %-9s | %-12s\n",
        $record->employee_id,
        $record->total_days,
        $record->present_days,
        $record->half_days,
        $record->early_leaves
    );
}

echo "\nGeneration completed successfully!\n";
echo "You can now check attendance data in your application.\n";
?>