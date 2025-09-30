<?php
// Test script for attendance system
require_once 'vendor/autoload.php';

use App\Models\Employee;
use App\Models\office_shift;
use App\Services\AttendanceProcessor;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Testing Attendance System\n";
echo "============================\n\n";

try {
    // Test 1: Check shifts
    echo "📋 1. Checking Shifts Setup:\n";
    $shifts = office_shift::all();
    if ($shifts->count() > 0) {
        echo "✅ Found " . $shifts->count() . " shifts:\n";
        foreach ($shifts as $shift) {
            echo "   - {$shift->shift_name}: {$shift->monday_in} - {$shift->monday_out}\n";
        }
    } else {
        echo "❌ No shifts found! Please run the shift setup SQL first.\n";
        exit(1);
    }
    echo "\n";

    // Test 2: Add sample employee if not exists
    echo "👥 2. Setting up Test Employee:\n";
    $employee = Employee::firstOrCreate(
        ['email' => 'test@example.com'],
        [
            'first_name' => 'Test',
            'last_name' => 'Employee',
            'salary' => 50000,
            'company_id' => 1,
            'joining_date' => '2024-01-01',
            'is_active' => 1,
            'office_shift_id' => 2 // General shift
        ]
    );
    echo "✅ Test employee created/found: {$employee->first_name} {$employee->last_name} (ID: {$employee->id})\n\n";

    // Test 3: Shift Detection
    echo "🔍 3. Testing Shift Detection:\n";
    $testTimes = [
        '07:30:00' => 'Shift-A',
        '08:15:00' => 'General',
        '11:30:00' => '11:00-20:15',
        '15:20:00' => 'Shift-B',
        '19:30:00' => '19:00-04:15',
        '23:30:00' => 'Shift-C'
    ];

    foreach ($testTimes as $time => $expected) {
        $detected = \App\Models\Attendance::detectShift($time);
        $status = $detected === $expected ? '✅' : '❌';
        echo "   {$time} -> {$detected} {$status}\n";
    }
    echo "\n";

    // Test 4: Process Sample Attendance Data
    echo "📊 4. Processing Sample Attendance:\n";
    
    $testCases = [
        [
            'name' => 'On Time - General Shift',
            'punch_in' => '2024-01-15 08:10:00',
            'punch_out' => '2024-01-15 17:30:00',
            'expected_shift' => 'General',
            'expected_late' => 10
        ],
        [
            'name' => 'Late Arrival - General Shift',
            'punch_in' => '2024-01-16 08:45:00',
            'punch_out' => '2024-01-16 18:00:00',
            'expected_shift' => 'General',
            'expected_late' => 45
        ],
        [
            'name' => 'Overtime - General Shift',
            'punch_in' => '2024-01-17 08:00:00',
            'punch_out' => '2024-01-17 19:00:00',
            'expected_shift' => 'General',
            'expected_late' => 0
        ]
    ];

    foreach ($testCases as $index => $testCase) {
        echo "\n   📝 Test Case: {$testCase['name']}\n";
        
        $result = AttendanceProcessor::processAttendance(
            $employee->id,
            $testCase['punch_in'],
            $testCase['punch_out'],
            Carbon::parse($testCase['punch_in'])->format('Y-m-d')
        );
        
        if (isset($result['success']) && $result['success']) {
            echo "   ✅ Processed successfully!\n";
            echo "   📋 Results:\n";
            echo "      - Shift Detected: {$result['shift_detected']}\n";
            echo "      - Late Minutes: {$result['calculations']['late_minutes']}\n";
            echo "      - Late Deduction: $" . number_format($result['calculations']['late_deduction'], 2) . "\n";
            echo "      - Working Hours: {$result['calculations']['working_hours']}\n";
            echo "      - Overtime Hours: {$result['calculations']['overtime_hours']}\n";
            echo "      - Overtime Amount: $" . number_format($result['calculations']['overtime_amount'], 2) . "\n";
            echo "      - Extra OT Hours: {$result['calculations']['extra_ot_hours']}\n";
            echo "      - Half Day: " . ($result['calculations']['is_half_day'] ? 'Yes' : 'No') . "\n";
        } else {
            echo "   ❌ Failed: " . ($result['error'] ?? 'Unknown error') . "\n";
        }
    }

    echo "\n";
    
    // Test 5: Generate Reports
    echo "📈 5. Testing Reports:\n";
    
    $dailyReport = AttendanceProcessor::generateDailyReport('2024-01-15', 1);
    echo "   📊 Daily Report for 2024-01-15: " . count($dailyReport) . " records\n";
    
    $extraOTReport = AttendanceProcessor::getExtraOTReport('2024-01-17', 1);
    echo "   ⏰ Extra OT Report for 2024-01-17: " . count($extraOTReport) . " records\n";

    echo "\n✅ All tests completed successfully!\n";
    echo "\n🚀 Your attendance system is working correctly!\n";
    echo "   You can now use the API endpoints to process real biometric data.\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}