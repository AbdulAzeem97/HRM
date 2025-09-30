<?php

// Convert time strings to minutes for easier calculation
function timeToMinutes($timeStr) {
    list($hours, $minutes) = explode(':', $timeStr);
    return $hours * 60 + $minutes;
}

function minutesToHours($minutes) {
    return round($minutes / 60, 2);
}

// Simple test without Laravel dependencies
// Simulating the smart shift detection logic
function smartShiftDetection($punchInTime, $punchOutTime)
{
    $shifts = [
        'Shift-A' => ['start' => '07:00', 'end' => '15:45', 'duration' => 8.75],
        'General' => ['start' => '08:00', 'end' => '17:15', 'duration' => 9.25],
        '11:00-20:15' => ['start' => '11:00', 'end' => '20:15', 'duration' => 9.25],
        'Shift-B' => ['start' => '15:00', 'end' => '23:45', 'duration' => 8.75],
        '19:00-04:15' => ['start' => '19:00', 'end' => '04:15', 'duration' => 9.25],
        'Shift-C' => ['start' => '23:00', 'end' => '07:15', 'duration' => 8.25]
    ];


    $punchInMinutes = timeToMinutes($punchInTime);
    $punchOutMinutes = timeToMinutes($punchOutTime);
    
    // Handle overnight punch out
    if ($punchOutMinutes < $punchInMinutes) {
        $punchOutMinutes += 24 * 60; // Add 24 hours
    }
    
    $totalWorkingMinutes = $punchOutMinutes - $punchInMinutes;
    $totalWorkingHours = minutesToHours($totalWorkingMinutes);

    // Check if employee worked at least 9 hours
    if ($totalWorkingHours < 9) {
        return [
            'shift_selected' => null,
            'reason' => 'Less than 9 hours worked',
            'total_hours' => $totalWorkingHours,
            'message' => 'Employee worked only ' . $totalWorkingHours . ' hours. Minimum 9 hours required.',
            'validation_passed' => false
        ];
    }

    $bestShift = null;
    $bestScore = -1;
    $bestMatch = [];

    foreach ($shifts as $shiftName => $shift) {
        $shiftStartMinutes = timeToMinutes($shift['start']);
        $score = 0;
        
        // Check punch-in alignment (within 3 hours tolerance = 180 minutes)
        $punchInDiff = abs($punchInMinutes - $shiftStartMinutes);
        if ($punchInDiff <= 180) {
            $score += (180 - $punchInDiff) / 180 * 50; // Max 50 points
        }
        
        // Check if working time covers 9+ hours (preferred for labor)
        $shiftCoverage = min($totalWorkingHours / 9, 1) * 30; // Max 30 points
        $score += $shiftCoverage;
        
        // Bonus for matching shift duration
        $expectedWorkMinutes = $shift['duration'] * 60;
        $durationDiff = abs($totalWorkingMinutes - $expectedWorkMinutes);
        if ($durationDiff <= 240) { // 4 hours tolerance
            $score += (240 - $durationDiff) / 240 * 20; // Max 20 points
        }

        if ($score > $bestScore) {
            $bestScore = $score;
            $bestShift = $shiftName;
            $bestMatch = [
                'shift_name' => $shiftName,
                'shift_start' => $shift['start'],
                'shift_end' => $shift['end'],
                'shift_duration' => $shift['duration'],
                'score' => round($score, 2),
                'punch_in_diff_minutes' => $punchInDiff
            ];
        }
    }

    // Calculate overtime
    $regularHours = isset($bestMatch['shift_duration']) ? $bestMatch['shift_duration'] : 8;
    $overtimeHours = max(0, $totalWorkingHours - $regularHours);

    return [
        'shift_selected' => $bestShift,
        'total_working_hours' => $totalWorkingHours,
        'regular_hours' => $regularHours,
        'overtime_hours' => round($overtimeHours, 2),
        'shift_details' => $bestMatch,
        'validation_passed' => $bestShift !== null && $totalWorkingHours >= 9,
        'message' => $bestShift ? 
            "Auto-selected {$bestShift}. Worked {$totalWorkingHours}h (Regular: {$regularHours}h, OT: " . round($overtimeHours, 2) . "h)" :
            'No suitable shift found.'
    ];
}

// Test your example
echo "=== Smart Shift Detection Test ===\n\n";
echo "Your Example: 11:30 AM to 9:30 PM\n";
echo "Expected: Should select 11:00-20:15 shift\n\n";

$result = smartShiftDetection('11:30', '21:30');

echo "Results:\n";
echo "========\n";
echo "Shift Selected: " . ($result['shift_selected'] ?? 'None') . "\n";
echo "Total Hours: " . $result['total_working_hours'] . "\n";
echo "Regular Hours: " . $result['regular_hours'] . "\n";
echo "Overtime Hours: " . $result['overtime_hours'] . "\n";
echo "Valid: " . ($result['validation_passed'] ? 'YES' : 'NO') . "\n";
echo "Message: " . $result['message'] . "\n\n";

if (isset($result['shift_details'])) {
    echo "Shift Details:\n";
    echo "- Start: " . $result['shift_details']['shift_start'] . "\n";
    echo "- End: " . $result['shift_details']['shift_end'] . "\n";
    echo "- Duration: " . $result['shift_details']['shift_duration'] . "h\n";
    echo "- Match Score: " . $result['shift_details']['score'] . "/100\n\n";
}

// Additional tests
echo "=== Additional Tests ===\n\n";

$tests = [
    ['07:30', '16:30', 'Morning worker'],
    ['08:00', '17:00', 'Regular worker'],  
    ['15:30', '00:30', 'Evening worker'],
    ['23:30', '08:30', 'Night worker'],
    ['12:00', '20:00', 'Short day (8h)'],
];

foreach ($tests as $i => $test) {
    echo ($i + 1) . ". {$test[2]}: {$test[0]} to {$test[1]}\n";
    $r = smartShiftDetection($test[0], $test[1]);
    echo "   → " . ($r['shift_selected'] ?? 'No shift') . " | " . $r['total_working_hours'] . "h | " . ($r['validation_passed'] ? 'PASS' : 'FAIL') . "\n\n";
}

?>