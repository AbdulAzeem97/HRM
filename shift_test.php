<?php
echo "=== AUTO-SHIFT DETECTION TEST ===\n";
echo "Testing for: Mr. ADAN ISHAAQ (Staff ID: 8)\n";
echo "Punch In: 11:00 AM\n";
echo "Punch Out: 20:15 (8:15 PM)\n";
echo "Date: September 9, 2025\n\n";

// Manual calculation to test the logic
$punchInTime = '11:00';
$punchOutTime = '20:15';

// Available shifts in the system
$shifts = [
    'Shift-A' => ['start' => '07:00', 'end' => '15:45', 'duration' => 8.75],
    'General' => ['start' => '08:00', 'end' => '17:15', 'duration' => 9.25],
    '11:00-20:15' => ['start' => '11:00', 'end' => '20:15', 'duration' => 9.25],
    'Shift-B' => ['start' => '15:00', 'end' => '23:45', 'duration' => 8.75],
    '19:00-04:15' => ['start' => '19:00', 'end' => '04:15', 'duration' => 9.25],
    'Shift-C' => ['start' => '23:00', 'end' => '07:15', 'duration' => 8.25]
];

// Calculate working hours
$start = new DateTime($punchInTime);
$end = new DateTime($punchOutTime);
$diff = $start->diff($end);
$totalWorkingHours = $diff->h + ($diff->i / 60);

echo "=== MANUAL CALCULATION ===\n";
echo "Punch In: " . $punchInTime . "\n";
echo "Punch Out: " . $punchOutTime . "\n";
echo "Total Working Time: " . $diff->h . "h " . $diff->i . "m = " . $totalWorkingHours . " hours\n\n";

echo "=== SHIFT MATCHING ANALYSIS ===\n";
$bestShift = null;
$bestScore = -1;

foreach ($shifts as $shiftName => $shift) {
    echo "Testing Shift: {$shiftName}\n";
    echo "  Shift Times: {$shift['start']} - {$shift['end']} ({$shift['duration']} hours)\n";
    
    $shiftStart = new DateTime($shift['start']);
    $shiftEnd = new DateTime($shift['end']);
    
    // Calculate alignment score
    $punchInDiff = abs($start->getTimestamp() - $shiftStart->getTimestamp()) / 60;
    echo "  Punch-in difference: " . round($punchInDiff) . " minutes\n";
    
    $score = 0;
    
    // Punch-in alignment (within 3 hours tolerance)
    if ($punchInDiff <= 180) {
        $punchInScore = (180 - $punchInDiff) / 180 * 50;
        $score += $punchInScore;
        echo "  Punch-in score: " . round($punchInScore, 1) . "/50\n";
    } else {
        echo "  Punch-in score: 0/50 (outside tolerance)\n";
    }
    
    // Working time coverage
    $coverageScore = min($totalWorkingHours / 9, 1) * 30;
    $score += $coverageScore;
    echo "  Coverage score: " . round($coverageScore, 1) . "/30\n";
    
    // End time alignment
    $expectedEnd = clone $start;
    $expectedEnd->modify('+' . ($shift['duration'] * 3600) . ' seconds');
    $endTimeDiff = abs($end->getTimestamp() - $expectedEnd->getTimestamp()) / 60;
    
    if ($endTimeDiff <= 240) {
        $endTimeScore = (240 - $endTimeDiff) / 240 * 20;
        $score += $endTimeScore;
        echo "  End time score: " . round($endTimeScore, 1) . "/20\n";
    } else {
        echo "  End time score: 0/20 (outside tolerance)\n";
    }
    
    echo "  Total Score: " . round($score, 1) . "/100\n";
    
    if ($score > $bestScore) {
        $bestScore = $score;
        $bestShift = $shiftName;
    }
    
    echo "\n";
}

echo "=== FINAL RESULTS ===\n";
echo "Selected Shift: " . ($bestShift ?? 'None') . "\n";
echo "Best Score: " . round($bestScore, 1) . "/100\n";
echo "Total Working Hours: " . $totalWorkingHours . " hours\n";

if ($bestShift) {
    $selectedShift = $shifts[$bestShift];
    $regularHours = $selectedShift['duration'];
    $overtimeHours = max(0, $totalWorkingHours - $regularHours);
    
    echo "Regular Hours: " . $regularHours . " hours\n";
    echo "Overtime Hours: " . round($overtimeHours, 2) . " hours\n";
    
    if ($totalWorkingHours < $regularHours) {
        $shortageHours = $regularHours - $totalWorkingHours;
        $workingStatus = $shortageHours >= 4 ? 'Half Day' : 'Early Leave';
        echo "Status: " . $workingStatus . " (Short by " . round($shortageHours, 2) . " hours)\n";
    } else {
        echo "Status: Full Day\n";
    }
}

echo "\n=== EXPECTED vs ACTUAL ===\n";
echo "Expected: Shift '11:00-20:15' should be selected\n";
echo "Actual: Shift '{$bestShift}' was selected\n";
echo "Match Result: " . ($bestShift === '11:00-20:15' ? 'PERFECT MATCH!' : 'Unexpected result') . "\n";

echo "\n=== TEST COMPLETED ===\n";
?>