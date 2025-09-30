<?php
/**
 * Debug script to check existing employees and their staff_ids
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Employee;

echo "=== EMPLOYEE DATABASE CHECK ===\n\n";

// Get all active employees
$employees = Employee::where('is_active', 1)->select('id', 'staff_id', 'first_name', 'last_name')->get();

echo "📊 Total active employees: " . $employees->count() . "\n\n";

if ($employees->count() > 0) {
    echo "📋 Existing Staff IDs:\n";
    echo str_repeat("-", 50) . "\n";
    
    foreach ($employees as $employee) {
        echo sprintf(
            "ID: %-3d | Staff ID: %-15s | Name: %s %s\n",
            $employee->id,
            $employee->staff_id ?: 'NULL',
            $employee->first_name,
            $employee->last_name
        );
    }
    
    echo "\n" . str_repeat("-", 50) . "\n";
    echo "📋 Staff IDs for CSV (copy these):\n";
    
    $staffIds = $employees->pluck('staff_id')->filter()->take(3);
    foreach ($staffIds as $staffId) {
        echo $staffId . "\n";
    }
    
    if ($staffIds->count() < 3) {
        echo "\n⚠️  Warning: Less than 3 employees with staff_id found!\n";
    }
} else {
    echo "❌ No active employees found!\n";
}

echo "\n=== SAMPLE CSV FORMAT ===\n";
echo "staff_id,attendance_date,clock_in,clock_out\n";

if ($employees->count() > 0) {
    $sampleStaffIds = $employees->pluck('staff_id')->filter()->take(3);
    foreach ($sampleStaffIds as $index => $staffId) {
        echo $staffId . ",2024-01-0" . ($index + 1) . ",08:00,17:00\n";
    }
} else {
    echo "EMP001,2024-01-01,08:00,17:00\n";
    echo "EMP002,2024-01-01,08:00,17:00\n";
    echo "EMP003,2024-01-01,08:00,17:00\n";
}

echo "\n✅ Copy the staff IDs above and use them in your CSV file!\n";
?>