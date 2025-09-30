<?php

// Convert time strings to minutes for easier calculation
function timeToMinutes($timeStr) {
    list($hours, $minutes) = explode(':', $timeStr);
    return $hours * 60 + $minutes;
}

function minutesToHours($minutes) {
    return round($minutes / 60, 2);
}

// Enhanced smart shift detection that works for all employees
function smartShiftDetection($punchInTime, $punchOutTime, $requireMinimumHours = false)
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

    // Only enforce minimum hours if specifically required
    if ($requireMinimumHours && $totalWorkingHours < 9) {
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
        
        // Check if working time covers shift appropriately
        $shiftCoverage = min($totalWorkingHours / ($shift['duration'] * 0.8), 1) * 30; // 80% coverage acceptable
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

    // Calculate overtime and early leave
    $regularHours = isset($bestMatch['shift_duration']) ? $bestMatch['shift_duration'] : 8;
    $overtimeHours = max(0, $totalWorkingHours - $regularHours);
    
    // Determine early leave status
    $isEarlyLeave = false;
    $earlyLeaveMinutes = 0;
    $workingStatus = 'Present';
    
    if ($bestShift && isset($bestMatch['shift_duration'])) {
        $expectedHours = $bestMatch['shift_duration'];
        if ($totalWorkingHours < $expectedHours) {
            $shortageHours = $expectedHours - $totalWorkingHours;
            $earlyLeaveMinutes = $shortageHours * 60;
            $isEarlyLeave = true;
            
            // Determine if it's half day or early leave
            if ($shortageHours >= 4) {
                $workingStatus = 'Half Day';
            } else if ($shortageHours >= 1) {
                $workingStatus = 'Early Leave';
            }
        }
    }

    return [
        'shift_selected' => $bestShift,
        'total_working_hours' => $totalWorkingHours,
        'regular_hours' => $regularHours,
        'overtime_hours' => round($overtimeHours, 2),
        'shift_details' => $bestMatch,
        'early_leave' => [
            'is_early_leave' => $isEarlyLeave,
            'early_leave_minutes' => round($earlyLeaveMinutes, 0),
            'working_status' => $workingStatus,
            'shortage_hours' => isset($shortageHours) ? round($shortageHours, 2) : 0
        ],
        'validation_passed' => $bestShift !== null,
        'message' => $bestShift ? 
            "Auto-selected {$bestShift}. Worked " . $totalWorkingHours . "h (Status: {$workingStatus})" :
            'No suitable shift found.'
    ];
}

function calculateEarlyLeaveDeduction($earlyLeaveMinutes, $monthlySalary = 50000) {
    if ($earlyLeaveMinutes <= 0) return 0;
    $perMinuteRate = $monthlySalary / (26 * 8 * 60);
    return round($perMinuteRate * $earlyLeaveMinutes, 2);
}

echo "=== Comprehensive Smart Shift Detection Test ===\n\n";

// Test cases including employees with less than 9 hours
$testCases = [
    ['11:30', '21:30', 'Full day worker (10h) - Your example'],
    ['08:00', '17:00', 'Regular full day (9h)'],
    ['07:30', '15:00', 'Morning shift with early leave (7.5h)'],
    ['11:00', '18:00', 'Mid-day short shift (7h)'],
    ['15:00', '21:00', 'Evening short shift (6h)'],
    ['08:00', '12:00', 'Half day morning (4h)'],
    ['11:00', '15:30', 'Short afternoon (4.5h)'],
    ['23:30', '06:00', 'Night shift short (6.5h)'],
    ['08:30', '16:30', 'Regular with minor early leave (8h)'],
    ['07:00', '18:00', 'Long day with overtime (11h)']
];

foreach ($testCases as $i => $case) {
    echo ($i + 1) . ". {$case[2]}\n";
    echo "   Punch: {$case[0]} to {$case[1]}\n";
    
    $result = smartShiftDetection($case[0], $case[1], false); // No minimum hours required
    
    echo "   → Shift: " . ($result['shift_selected'] ?? 'None') . "\n";
    echo "   → Hours: {$result['total_working_hours']}h\n";
    echo "   → Status: " . $result['early_leave']['working_status'] . "\n";
    
    if ($result['early_leave']['is_early_leave']) {
        $shortage = $result['early_leave']['shortage_hours'];
        $deduction = calculateEarlyLeaveDeduction($result['early_leave']['early_leave_minutes']);
        echo "   → Early Leave: {$shortage}h shortage (₹{$deduction} deduction)\n";
    }
    
    if ($result['overtime_hours'] > 0) {
        echo "   → Overtime: {$result['overtime_hours']}h\n";
    }
    
    echo "   → Valid: " . ($result['validation_passed'] ? 'YES' : 'NO') . "\n";
    echo "   → Message: " . $result['message'] . "\n";
    echo "   ---\n\n";
}

echo "=== Policy Summary ===\n";
echo "✓ System now detects best shift for ANY working hours\n";
echo "✓ Applies early leave policy for shortage hours\n";
echo "✓ Calculates deductions for early leave\n";
echo "✓ Still calculates overtime for extra hours\n";
echo "✓ Supports Half Day (4+ hours shortage) and Early Leave (1-4 hours shortage)\n";
echo "✓ Works for all shifts: Morning, General, Mid-day, Evening, Night\n\n";

echo "=== Early Leave Deduction Formula ===\n";
echo "Per minute rate = Monthly Salary ÷ (26 days × 8 hours × 60 minutes)\n";
echo "Early Leave Deduction = Per minute rate × Early leave minutes\n";
echo "Example: ₹50,000 salary → ₹4.01 per minute → 60 min early = ₹240.38 deduction\n";

?>