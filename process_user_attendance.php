<?php
/**
 * Process User Attendance Data and Upload to Database
 * This script will:
 * 1. Convert the data format
 * 2. Check if staff IDs exist in database
 * 3. Import attendance records
 */

$inputData = 'staff_id	attendance_date	clock_in	clock_out
35	4/1/2025	0:00	0:00
35	4/2/2025	0:00	0:00
35	4/3/2025	8:21	17:16
35	4/4/2025	8:09	17:15
35	4/5/2025	8:13	17:16
35	4/6/2025	0:00	0:00
35	4/7/2025	8:10	17:16
35	4/8/2025	8:06	17:16
35	4/9/2025	0:00	0:00
35	4/10/2025	8:18	17:16
35	4/11/2025	8:14	17:16
35	4/12/2025	8:14	17:16
35	4/13/2025	0:00	0:00
35	4/14/2025	8:16	17:18
35	4/15/2025	8:14	17:15
35	4/16/2025	8:18	17:15
35	4/17/2025	8:15	17:15
35	4/18/2025	8:13	17:15
35	4/19/2025	8:14	17:17
35	4/20/2025	0:00	0:00
35	4/21/2025	8:15	17:17
35	4/22/2025	8:13	17:16
35	4/23/2025	8:15	17:17
35	4/24/2025	8:12	17:16
35	4/25/2025	8:11	17:15
35	4/26/2025	8:24	17:15
35	4/27/2025	0:00	0:00
35	4/28/2025	8:10	17:15
35	4/29/2025	8:13	17:16
35	4/30/2025	8:11	19:15
1	4/1/2025	0:00	0:00
1	4/2/2025	0:00	0:00
1	4/3/2025	8:10	17:17
1	4/4/2025	8:10	17:16
1	4/5/2025	8:11	17:27
1	4/6/2025	0:00	0:00
1	4/7/2025	8:14	17:17
1	4/8/2025	8:14	17:28
1	4/9/2025	8:12	17:19
1	4/10/2025	0:00	0:00
1	4/11/2025	8:08	17:20
1	4/12/2025	8:12	17:20
1	4/13/2025	0:00	0:00
1	4/14/2025	8:10	17:20
1	4/15/2025	8:09	17:20
1	4/16/2025	8:13	17:22
1	4/17/2025	8:13	17:19
1	4/18/2025	8:13	17:22
1	4/19/2025	8:13	17:19
1	4/20/2025	0:00	0:00
1	4/21/2025	8:14	17:19
1	4/22/2025	8:09	17:18
1	4/23/2025	0:00	0:00
1	4/24/2025	8:08	17:21
1	4/25/2025	8:07	17:22
1	4/26/2025	8:09	17:16
1	4/27/2025	0:00	0:00
1	4/28/2025	8:12	17:18
1	4/29/2025	8:11	17:21
1	4/30/2025	8:10	17:18
2	4/1/2025	0:00	0:00
2	4/2/2025	0:00	0:00
2	4/3/2025	7:54	17:26
2	4/4/2025	8:09	17:26
2	4/5/2025	8:04	17:27
2	4/6/2025	0:00	0:00
2	4/7/2025	8:11	17:28
2	4/8/2025	8:08	17:28
2	4/9/2025	8:10	17:32
2	4/10/2025	8:02	17:20
2	4/11/2025	8:08	12:14
2	4/12/2025	8:12	17:20
2	4/13/2025	0:00	0:00
2	4/14/2025	8:08	17:31
2	4/15/2025	8:10	17:24
2	4/16/2025	8:08	17:33
2	4/17/2025	7:57	17:20
2	4/18/2025	8:08	17:01
2	4/19/2025	8:09	17:20
2	4/20/2025	0:00	0:00
2	4/21/2025	7:55	17:19
2	4/22/2025	8:09	17:21
2	4/23/2025	8:09	17:26
2	4/24/2025	8:08	17:22
2	4/25/2025	8:07	16:56
2	4/26/2025	8:12	17:19
2	4/27/2025	0:00	0:00
2	4/28/2025	8:07	17:27
2	4/29/2025	8:11	16:49
2	4/30/2025	8:04	17:30';

function convertDateFormat($dateStr) {
    // Convert "4/1/2025" to "2025-04-01"
    $date = DateTime::createFromFormat('n/j/Y', $dateStr);
    if (!$date) {
        $date = DateTime::createFromFormat('m/d/Y', $dateStr);
    }
    return $date ? $date->format('Y-m-d') : null;
}

function formatTime($timeStr) {
    // Convert "8:21" to "08:21" or keep "17:16" as is
    if ($timeStr === '0:00') return '00:00';
    
    $parts = explode(':', $timeStr);
    if (count($parts) == 2) {
        return sprintf('%02d:%02d', $parts[0], $parts[1]);
    }
    return $timeStr;
}

// Process the data
$lines = explode("\n", trim($inputData));
$csvData = "staff_id,attendance_date,clock_in,clock_out\n";

$processedCount = 0;
$skippedCount = 0;
$errorCount = 0;
$uniqueStaffIds = [];

echo "=== PROCESSING ATTENDANCE DATA ===\n\n";

foreach ($lines as $lineNum => $line) {
    if ($lineNum == 0) continue; // Skip header
    
    $parts = preg_split('/\s+/', trim($line));
    
    if (count($parts) >= 4) {
        $staffId = $parts[0];
        $date = $parts[1];
        $clockIn = $parts[2];
        $clockOut = $parts[3];
        
        $uniqueStaffIds[$staffId] = true;
        
        // Skip records with 0:00 times (weekends/holidays)
        if ($clockIn === '0:00' || $clockOut === '0:00') {
            $skippedCount++;
            continue;
        }
        
        try {
            $formattedDate = convertDateFormat($date);
            $formattedClockIn = formatTime($clockIn);
            $formattedClockOut = formatTime($clockOut);
            
            if ($formattedDate) {
                $csvData .= "$staffId,$formattedDate,$formattedClockIn,$formattedClockOut\n";
                $processedCount++;
            } else {
                echo "❌ Error processing date: $date on line " . ($lineNum + 1) . "\n";
                $errorCount++;
            }
        } catch (Exception $e) {
            echo "❌ Error processing line " . ($lineNum + 1) . ": " . $e->getMessage() . "\n";
            $errorCount++;
        }
    }
}

// Save to file
$outputFile = 'public/sample_file/user_attendance_final.csv';
file_put_contents($outputFile, $csvData);

echo "✅ CONVERSION COMPLETED!\n";
echo "================================\n";
echo "📊 Records processed: $processedCount\n";
echo "⚠️  Records skipped (0:00 times): $skippedCount\n";
echo "❌ Records with errors: $errorCount\n";
echo "👥 Unique Staff IDs found: " . implode(', ', array_keys($uniqueStaffIds)) . "\n";
echo "💾 Output file: $outputFile\n";

echo "\n=== STAFF ID VALIDATION ===\n";
echo "The following staff IDs were found in your data:\n";
foreach (array_keys($uniqueStaffIds) as $id) {
    echo "• $id\n";
}

echo "\n🔍 NEXT STEPS:\n";
echo "1. Visit /attendances/debug-employees to check if these staff IDs exist in your database\n";
echo "2. If they don't exist, you may need to create employees with these staff IDs first\n";
echo "3. Upload the file '$outputFile' through /attendances/page/import\n";

echo "\n💡 TIP: If staff IDs don't match, you can:\n";
echo "   - Update the staff_id values in your employees table\n";
echo "   - Or modify the CSV file to use existing staff IDs\n";

echo "\n🚀 Ready to upload: $outputFile\n";
?>