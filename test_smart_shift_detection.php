<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Attendance;
use App\Services\AttendanceProcessor;
use Carbon\Carbon;

// Test the smart shift detection with your example
// Employee comes at 11:30 AM and punches out at 9:30 PM

echo "=== Smart Shift Detection Test ===\n\n";

// Your example case
$punchIn = '11:30';
$punchOut = '21:30'; // 9:30 PM

echo "Test Case: Employee punch in at {$punchIn}, punch out at {$punchOut}\n";
echo "Expected: Should auto-select 11:00-20:15 shift and calculate overtime\n\n";

// Test the smart detection
$result = Attendance::smartShiftDetection($punchIn, $punchOut);

echo "Smart Detection Results:\n";
echo "========================\n";
echo "Shift Selected: " . ($result['shift_selected'] ?? 'None') . "\n";
echo "Total Working Hours: " . $result['total_working_hours'] . " hours\n";
echo "Regular Hours: " . $result['regular_hours'] . " hours\n";
echo "Overtime Hours: " . $result['overtime_hours'] . " hours\n";
echo "Validation Passed: " . ($result['validation_passed'] ? 'Yes' : 'No') . "\n";
echo "Message: " . $result['message'] . "\n\n";

if (isset($result['shift_details'])) {
    echo "Shift Details:\n";
    echo "- Shift Start: " . $result['shift_details']['shift_start'] . "\n";
    echo "- Shift End: " . $result['shift_details']['shift_end'] . "\n";
    echo "- Shift Duration: " . $result['shift_details']['shift_duration'] . " hours\n";
    echo "- Match Score: " . round($result['shift_details']['score'], 2) . "/100\n";
    echo "- Punch-in Difference: " . $result['shift_details']['punch_in_diff_minutes'] . " minutes\n\n";
}

// Test other scenarios
echo "\n=== Additional Test Cases ===\n\n";

$testCases = [
    ['07:30', '16:30', 'Morning Shift'],
    ['15:30', '00:30', 'Evening Shift'],
    ['08:00', '17:00', 'General Shift'],
    ['23:30', '08:30', 'Night Shift'],
    ['11:00', '19:00', 'Short Day - Should Fail'],
];

foreach ($testCases as $index => $case) {
    echo "Test Case " . ($index + 1) . ": {$case[2]}\n";
    echo "Punch: {$case[0]} to {$case[1]}\n";
    
    $testResult = Attendance::smartShiftDetection($case[0], $case[1]);
    
    echo "Result: " . ($testResult['shift_selected'] ?? 'No shift selected') . "\n";
    echo "Hours: " . $testResult['total_working_hours'] . " (Regular: " . ($testResult['regular_hours'] ?? 0) . ", OT: " . ($testResult['overtime_hours'] ?? 0) . ")\n";
    echo "Valid: " . ($testResult['validation_passed'] ? 'Yes' : 'No') . "\n";
    echo "Message: " . $testResult['message'] . "\n";
    echo "---\n\n";
}

echo "=== Test Complete ===\n";