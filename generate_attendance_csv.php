<?php
/**
 * Quick CSV Generator for Attendance Data
 * Usage: php generate_attendance_csv.php
 */

// Configuration
$staffIds = ['EMP001', 'EMP002', 'EMP003']; // Change these to your actual staff IDs
$startDate = '2024-01-01'; // Start date
$endDate = '2024-01-31';   // End date
$outputFile = 'public/sample_file/generated_attendance.csv';

// Time variations for more realistic data
$clockInVariations = ['08:00', '08:15', '07:45', '08:30', '08:05', '07:55', '08:20'];
$clockOutVariations = ['17:00', '17:15', '17:30', '16:45', '17:45', '17:10', '17:25'];

// Create CSV content
$csvContent = "staff_id,attendance_date,clock_in,clock_out\n";

$currentDate = new DateTime($startDate);
$endDateObj = new DateTime($endDate);

while ($currentDate <= $endDateObj) {
    // Skip weekends (optional - remove these lines if you want to include weekends)
    if ($currentDate->format('N') >= 6) { // 6 = Saturday, 7 = Sunday
        $currentDate->add(new DateInterval('P1D'));
        continue;
    }

    foreach ($staffIds as $staffId) {
        $clockIn = $clockInVariations[array_rand($clockInVariations)];
        $clockOut = $clockOutVariations[array_rand($clockOutVariations)];
        
        $csvContent .= sprintf(
            "%s,%s,%s,%s\n",
            $staffId,
            $currentDate->format('Y-m-d'),
            $clockIn,
            $clockOut
        );
    }
    
    $currentDate->add(new DateInterval('P1D'));
}

// Write to file
file_put_contents($outputFile, $csvContent);

echo "✅ CSV file generated successfully: {$outputFile}\n";
echo "📊 Total records: " . (substr_count($csvContent, "\n") - 1) . "\n";
echo "👥 Employees: " . implode(', ', $staffIds) . "\n";
echo "📅 Date range: {$startDate} to {$endDate}\n";
echo "\n🚀 You can now upload this file through the attendance import interface!\n";
?>