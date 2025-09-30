<?php
/**
 * Direct Database Upload for Attendance Data
 * This script uploads attendance data directly to the database
 * without requiring Laravel framework loading
 */

// Database configuration (from .env file)
$host = 'localhost';
$dbname = 'u902429527_ttphrm';   // From DB_DATABASE in .env
$username = 'root';              // From DB_USERNAME in .env
$password = '';                  // From DB_PASSWORD in .env (empty)

// Attendance data (excluding 0:00 records)
$attendanceData = [
    ['35', '4/3/2025', '8:21', '17:16'],
    ['35', '4/4/2025', '8:09', '17:15'],
    ['35', '4/5/2025', '8:13', '17:16'],
    ['35', '4/7/2025', '8:10', '17:16'],
    ['35', '4/8/2025', '8:06', '17:16'],
    ['35', '4/10/2025', '8:18', '17:16'],
    ['35', '4/11/2025', '8:14', '17:16'],
    ['35', '4/12/2025', '8:14', '17:16'],
    ['35', '4/14/2025', '8:16', '17:18'],
    ['35', '4/15/2025', '8:14', '17:15'],
    ['35', '4/16/2025', '8:18', '17:15'],
    ['35', '4/17/2025', '8:15', '17:15'],
    ['35', '4/18/2025', '8:13', '17:15'],
    ['35', '4/19/2025', '8:14', '17:17'],
    ['35', '4/21/2025', '8:15', '17:17'],
    ['35', '4/22/2025', '8:13', '17:16'],
    ['35', '4/23/2025', '8:15', '17:17'],
    ['35', '4/24/2025', '8:12', '17:16'],
    ['35', '4/25/2025', '8:11', '17:15'],
    ['35', '4/26/2025', '8:24', '17:15'],
    ['35', '4/28/2025', '8:10', '17:15'],
    ['35', '4/29/2025', '8:13', '17:16'],
    ['35', '4/30/2025', '8:11', '19:15'],
    ['1', '4/3/2025', '8:10', '17:17'],
    ['1', '4/4/2025', '8:10', '17:16'],
    ['1', '4/5/2025', '8:11', '17:27'],
    ['1', '4/7/2025', '8:14', '17:17'],
    ['1', '4/8/2025', '8:14', '17:28'],
    ['1', '4/9/2025', '8:12', '17:19'],
    ['1', '4/11/2025', '8:08', '17:20'],
    ['1', '4/12/2025', '8:12', '17:20'],
    ['1', '4/14/2025', '8:10', '17:20'],
    ['1', '4/15/2025', '8:09', '17:20'],
    ['1', '4/16/2025', '8:13', '17:22'],
    ['1', '4/17/2025', '8:13', '17:19'],
    ['1', '4/18/2025', '8:13', '17:22'],
    ['1', '4/19/2025', '8:13', '17:19'],
    ['1', '4/21/2025', '8:14', '17:19'],
    ['1', '4/22/2025', '8:09', '17:18'],
    ['1', '4/24/2025', '8:08', '17:21'],
    ['1', '4/25/2025', '8:07', '17:22'],
    ['1', '4/26/2025', '8:09', '17:16'],
    ['1', '4/28/2025', '8:12', '17:18'],
    ['1', '4/29/2025', '8:11', '17:21'],
    ['1', '4/30/2025', '8:10', '17:18'],
    ['2', '4/3/2025', '7:54', '17:26'],
    ['2', '4/4/2025', '8:09', '17:26'],
    ['2', '4/5/2025', '8:04', '17:27'],
    ['2', '4/7/2025', '8:11', '17:28'],
    ['2', '4/8/2025', '8:08', '17:28'],
    ['2', '4/9/2025', '8:10', '17:32'],
    ['2', '4/10/2025', '8:02', '17:20'],
    ['2', '4/11/2025', '8:08', '12:14'],
    ['2', '4/12/2025', '8:12', '17:20'],
    ['2', '4/14/2025', '8:08', '17:31'],
    ['2', '4/15/2025', '8:10', '17:24'],
    ['2', '4/16/2025', '8:08', '17:33'],
    ['2', '4/17/2025', '7:57', '17:20'],
    ['2', '4/18/2025', '8:08', '17:01'],
    ['2', '4/19/2025', '8:09', '17:20'],
    ['2', '4/21/2025', '7:55', '17:19'],
    ['2', '4/22/2025', '8:09', '17:21'],
    ['2', '4/23/2025', '8:09', '17:26'],
    ['2', '4/24/2025', '8:08', '17:22'],
    ['2', '4/25/2025', '8:07', '16:56'],
    ['2', '4/26/2025', '8:12', '17:19'],
    ['2', '4/28/2025', '8:07', '17:27'],
    ['2', '4/29/2025', '8:11', '16:49'],
    ['2', '4/30/2025', '8:04', '17:30']
];

function formatTime($timeStr) {
    // Convert "8:21" to "08:21"
    $parts = explode(':', $timeStr);
    if (count($parts) == 2) {
        return sprintf('%02d:%02d', $parts[0], $parts[1]);
    }
    return $timeStr;
}

function convertDate($dateStr) {
    // Convert "4/3/2025" to "2025-04-03"
    $date = DateTime::createFromFormat('n/j/Y', $dateStr);
    if (!$date) {
        $date = DateTime::createFromFormat('m/d/Y', $dateStr);
    }
    return $date ? $date->format('Y-m-d') : null;
}

function calculateTimeDifference($time1, $time2) {
    $t1 = new DateTime($time1);
    $t2 = new DateTime($time2);
    return $t1->diff($t2)->format('%H:%I');
}

try {
    // Connect to database
    echo "🔌 Connecting to database...\n";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connected successfully!\n\n";

    // Get staff IDs from the data
    $staffIds = array_unique(array_column($attendanceData, 0));
    echo "🔍 Looking for employees with staff IDs: " . implode(', ', $staffIds) . "\n";

    // Check which employees exist
    $placeholders = str_repeat('?,', count($staffIds) - 1) . '?';
    $stmt = $pdo->prepare("SELECT id, staff_id, first_name, last_name FROM employees WHERE staff_id IN ($placeholders) AND is_active = 1");
    $stmt->execute($staffIds);
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($employees)) {
        echo "❌ No employees found with these staff IDs!\n";
        
        // Show available staff IDs
        $stmt = $pdo->prepare("SELECT staff_id FROM employees WHERE is_active = 1 AND staff_id IS NOT NULL LIMIT 20");
        $stmt->execute();
        $availableIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "📋 Available staff IDs in database:\n";
        foreach ($availableIds as $id) {
            echo "  • $id\n";
        }
        exit(1);
    }

    // Create mapping of staff_id to employee_id
    $staffToEmployeeId = [];
    echo "👥 Found employees:\n";
    foreach ($employees as $emp) {
        $staffToEmployeeId[$emp['staff_id']] = $emp['id'];
        echo "  • Staff ID {$emp['staff_id']} → {$emp['first_name']} {$emp['last_name']} (ID: {$emp['id']})\n";
    }
    echo "\n";

    // Process attendance data
    echo "📊 Processing " . count($attendanceData) . " attendance records...\n";
    
    $successCount = 0;
    $skippedCount = 0;
    $errorCount = 0;

    // Prepare insert statement
    $insertSql = "INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = $pdo->prepare($insertSql);

    // Prepare check for existing attendance
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM attendances WHERE employee_id = ? AND attendance_date = ?");

    foreach ($attendanceData as $record) {
        [$staffId, $dateStr, $clockIn, $clockOut] = $record;

        try {
            // Check if employee exists
            if (!isset($staffToEmployeeId[$staffId])) {
                echo "⚠️  Staff ID $staffId not found in database\n";
                $errorCount++;
                continue;
            }

            $employeeId = $staffToEmployeeId[$staffId];
            $attendanceDate = convertDate($dateStr);
            
            if (!$attendanceDate) {
                echo "❌ Invalid date format: $dateStr\n";
                $errorCount++;
                continue;
            }

            // Check if attendance already exists
            $checkStmt->execute([$employeeId, $attendanceDate]);
            if ($checkStmt->fetchColumn() > 0) {
                echo "⚠️  Attendance already exists for staff ID $staffId on $attendanceDate\n";
                $skippedCount++;
                continue;
            }

            // Format times
            $clockInFormatted = formatTime($clockIn);
            $clockOutFormatted = formatTime($clockOut);

            // Calculate total work time
            $totalWork = calculateTimeDifference($clockInFormatted, $clockOutFormatted);

            // Insert attendance record
            $insertStmt->execute([
                $employeeId,                    // employee_id
                $attendanceDate,               // attendance_date
                $clockInFormatted,             // clock_in
                $clockOutFormatted,            // clock_out
                '127.0.0.1',                   // clock_in_ip
                '127.0.0.1',                   // clock_out_ip
                0,                             // clock_in_out (0 = both in and out)
                '00:00',                       // time_late (will be calculated based on shift)
                '00:00',                       // early_leaving
                '00:00',                       // overtime
                $totalWork,                    // total_work
                '00:00',                       // total_rest
                'present'                      // attendance_status
            ]);

            $successCount++;
            echo "✅ Added attendance for staff ID $staffId on $attendanceDate\n";

        } catch (Exception $e) {
            echo "❌ Error processing staff ID $staffId on $dateStr: " . $e->getMessage() . "\n";
            $errorCount++;
        }
    }

    echo "\n=== UPLOAD COMPLETED ===\n";
    echo "✅ Successfully uploaded: $successCount records\n";
    echo "⚠️  Skipped (duplicates): $skippedCount records\n";
    echo "❌ Errors: $errorCount records\n";

    if ($successCount > 0) {
        echo "\n🎉 Attendance data has been successfully uploaded to the database!\n";
        echo "💡 Note: Late time, overtime, and other calculations will be based on employee shift settings.\n";
    }

} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    echo "\n💡 Please check your database configuration:\n";
    echo "   - Host: $host\n";
    echo "   - Database: $dbname\n";
    echo "   - Username: $username\n";
    echo "\n🔧 Update the database credentials at the top of this script.\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>