<?php
require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Attendance;

echo "=== AUTO-SHIFT DETECTION TEST ===\n";
echo "Testing for: Mr. ADAN ISHAAQ (Staff ID: 8)\n";
echo "Punch In: 11:00 AM\n";
echo "Punch Out: 20:15 (8:15 PM)\n";
echo "Date: September 9, 2025\n\n";

// Test the smart shift detection
$punchIn = '11:00';
$punchOut = '20:15';

echo "Calling smartShiftDetection function...\n\n";

$result = Attendance::smartShiftDetection($punchIn, $punchOut, false);

echo "=== RESULTS ===\n";
echo "Selected Shift: " . ($result['shift_selected'] ?? 'None') . "\n";
echo "Total Working Hours: " . $result['total_working_hours'] . " hours\n";
echo "Regular Hours: " . $result['regular_hours'] . " hours\n";
echo "Overtime Hours: " . $result['overtime_hours'] . " hours\n";
echo "Working Status: " . $result['early_leave']['working_status'] . "\n";
echo "Is Early Leave: " . ($result['early_leave']['is_early_leave'] ? 'Yes' : 'No') . "\n";
echo "Early Leave Minutes: " . $result['early_leave']['early_leave_minutes'] . " minutes\n";
echo "Message: " . $result['message'] . "\n";
echo "Validation Passed: " . ($result['validation_passed'] ? 'Yes' : 'No') . "\n\n";

if (isset($result['shift_details'])) {
    echo "=== SHIFT MATCH DETAILS ===\n";
    echo "Shift Name: " . $result['shift_details']['shift_name'] . "\n";
    echo "Shift Start: " . $result['shift_details']['shift_start'] . "\n";
    echo "Shift End: " . $result['shift_details']['shift_end'] . "\n";
    echo "Shift Duration: " . $result['shift_details']['shift_duration'] . " hours\n";
    echo "Match Score: " . round($result['shift_details']['score'], 2) . "/100\n";
    echo "Punch-in Difference: " . $result['shift_details']['punch_in_diff_minutes'] . " minutes\n";
    echo "End Time Difference: " . $result['shift_details']['end_time_diff_minutes'] . " minutes\n";
    echo "Coverage Score: " . round($result['shift_details']['coverage_score'], 2) . "\n\n";
}

echo "=== EXPECTED OUTCOME ===\n";
echo "Expected Shift: 11:00-20:15 (Perfect Match)\n";
echo "Expected Hours: 9.25 hours (11:00 to 20:15 = 9h 15m)\n";
echo "Expected Overtime: 0 hours (exact shift duration)\n";
echo "Expected Status: Full Day (no early leave)\n\n";

// Calculate the actual worked hours
$start = new DateTime('11:00');
$end = new DateTime('20:15');
$diff = $start->diff($end);
$actualHours = $diff->h + ($diff->i / 60);

echo "Manual Calculation:\n";
echo "Actual Working Time: " . $diff->h . "h " . $diff->i . "m = " . $actualHours . " hours\n";
echo "Expected Shift Duration (11:00-20:15): 9h 15m = 9.25 hours\n";
echo "Match Status: " . ($actualHours == 9.25 ? 'PERFECT MATCH ✓' : 'Mismatch ✗') . "\n\n";

echo "=== TEST COMPLETED ===\n";
?>