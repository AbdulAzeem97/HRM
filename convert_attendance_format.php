<?php
/**
 * CSV Format Converter for Attendance Data
 * Converts from: Staff_Id, Date, Clock_In, Clock_Out
 * To: staff_id, attendance_date, clock_in, clock_out
 */

$inputData = '2722      01-Apr-25    00:00     00:00 
2722      02-Apr-25    00:00     00:00 
2722      03-Apr-25    08:21     17:16 
2722      04-Apr-25    08:09     17:15 
2722      05-Apr-25    08:13     17:16 
2722      06-Apr-25    00:00     00:00 
2722      07-Apr-25    08:10     17:16 
2722      08-Apr-25    08:06     17:16 
2722      09-Apr-25    00:00     00:00 
2722      10-Apr-25    08:18     17:16 
2722      11-Apr-25    08:14     17:16 
2722      12-Apr-25    08:14     17:16 
2722      13-Apr-25    00:00     00:00 
2722      14-Apr-25    08:16     17:18 
2722      15-Apr-25    08:14     17:15 
2722      16-Apr-25    08:18     17:15 
2722      17-Apr-25    08:15     17:15 
2722      18-Apr-25    08:13     17:15 
2722      19-Apr-25    08:14     17:17 
2722      20-Apr-25    00:00     00:00 
2722      21-Apr-25    08:15     17:17 
2722      22-Apr-25    08:13     17:16 
2722      23-Apr-25    08:15     17:17 
2722      24-Apr-25    08:12     17:16 
2722      25-Apr-25    08:11     17:15 
2722      26-Apr-25    08:24     17:15 
2722      27-Apr-25    00:00     00:00 
2722      28-Apr-25    08:10     17:15 
2722      29-Apr-25    08:13     17:16 
2722      30-Apr-25    08:11     19:15 
2508      01-Apr-25    00:00     00:00 
2508      02-Apr-25    00:00     00:00 
2508      03-Apr-25    08:10     17:17 
2508      04-Apr-25    08:10     17:16 
2508      05-Apr-25    08:11     17:27 
2508      06-Apr-25    00:00     00:00 
2508      07-Apr-25    08:14     17:17 
2508      08-Apr-25    08:14     17:28 
2508      09-Apr-25    08:12     17:19 
2508      10-Apr-25    00:00     00:00 
2508      11-Apr-25    08:08     17:20 
2508      12-Apr-25    08:12     17:20 
2508      13-Apr-25    00:00     00:00 
2508      14-Apr-25    08:10     17:20 
2508      15-Apr-25    08:09     17:20 
2508      16-Apr-25    08:13     17:22 
2508      17-Apr-25    08:13     17:19 
2508      18-Apr-25    08:13     17:22 
2508      19-Apr-25    08:13     17:19 
2508      20-Apr-25    00:00     00:00 
2508      21-Apr-25    08:14     17:19 
2508      22-Apr-25    08:09     17:18 
2508      23-Apr-25    00:00     00:00 
2508      24-Apr-25    08:08     17:21 
2508      25-Apr-25    08:07     17:22 
2508      26-Apr-25    08:09     17:16 
2508      27-Apr-25    00:00     00:00 
2508      28-Apr-25    08:12     17:18 
2508      29-Apr-25    08:11     17:21 
2508      30-Apr-25    08:10     17:18 
271      01-Apr-25    00:00     00:00 
271      02-Apr-25    00:00     00:00 
271      03-Apr-25    07:54     17:26 
271      04-Apr-25    08:09     17:26 
271      05-Apr-25    08:04     17:27 
271      06-Apr-25    00:00     00:00 
271      07-Apr-25    08:11     17:28 
271      08-Apr-25    08:08     17:28 
271      09-Apr-25    08:10     17:32 
271      10-Apr-25    08:02     17:20 
271      11-Apr-25    08:08     12:14 
271      12-Apr-25    08:12     17:20 
271      13-Apr-25    00:00     00:00 
271      14-Apr-25    08:08     17:31 
271      15-Apr-25    08:10     17:24 
271      16-Apr-25    08:08     17:33 
271      17-Apr-25    07:57     17:20 
271      18-Apr-25    08:08     17:01 
271      19-Apr-25    08:09     17:20 
271      20-Apr-25    00:00     00:00 
271      21-Apr-25    07:55     17:19 
271      22-Apr-25    08:09     17:21 
271      23-Apr-25    08:09     17:26 
271      24-Apr-25    08:08     17:22 
271      25-Apr-25    08:07     16:56 
271      26-Apr-25    08:12     17:19 
271      27-Apr-25    00:00     00:00 
271      28-Apr-25    08:07     17:27 
271      29-Apr-25    08:11     16:49 
271      30-Apr-25    08:04     17:30';

function convertDateFormat($dateStr) {
    // Convert "01-Apr-25" to "2025-04-01"
    $date = DateTime::createFromFormat('d-M-y', $dateStr);
    return $date->format('Y-m-d');
}

// Process the data
$lines = explode("\n", trim($inputData));
$csvData = "staff_id,attendance_date,clock_in,clock_out\n";

$processedCount = 0;
$skippedCount = 0;

foreach ($lines as $line) {
    $parts = preg_split('/\s+/', trim($line));
    
    if (count($parts) >= 4) {
        $staffId = $parts[0];
        $date = $parts[1];
        $clockIn = $parts[2];
        $clockOut = $parts[3];
        
        // Skip records with 00:00 times (weekends/holidays)
        if ($clockIn === '00:00' || $clockOut === '00:00') {
            $skippedCount++;
            continue;
        }
        
        try {
            $formattedDate = convertDateFormat($date);
            $csvData .= "$staffId,$formattedDate,$clockIn,$clockOut\n";
            $processedCount++;
        } catch (Exception $e) {
            echo "Error processing date: $date\n";
        }
    }
}

// Save to file
$outputFile = 'public/sample_file/your_converted_attendance.csv';
file_put_contents($outputFile, $csvData);

echo "✅ Conversion completed!\n";
echo "📊 Records processed: $processedCount\n";
echo "⚠️  Records skipped (00:00 times): $skippedCount\n";
echo "💾 Output file: $outputFile\n";
echo "\n🚀 You can now upload '$outputFile' through the attendance import interface!\n";
?>