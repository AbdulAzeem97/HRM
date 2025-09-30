<?php
// Quick Bulk Attendance Generator for Staff IDs 1, 2, 3
// Run this script directly: php bulk_attendance_generator.php

// Database configuration
$host = 'localhost';
$dbname = 'u902429527_ttphrm';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully!\n";
    
    // Employee mapping (Staff ID => Employee ID)
    $employees = [
        '1' => 61,  // MUHAMMAD UZAIR SIDDIQUI
        '2' => 62,  // M.SAEED KHAN  
        '3' => 63   // M.ASIF
    ];
    
    // Generate 30 days of attendance (skip weekends)
    $startDate = new DateTime('2025-08-12');
    $endDate = new DateTime('2025-09-10');
    $totalRecords = 0;
    
    // Prepare the insert statement
    $sql = "INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out, time_late, early_leaving, overtime, total_work, total_rest, attendance_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    foreach ($employees as $staffId => $employeeId) {
        echo "Processing Staff ID: $staffId (Employee ID: $employeeId)\n";
        
        $current = clone $startDate;
        $dayCount = 0;
        
        while ($current <= $endDate && $dayCount < 20) { // 20 working days
            // Skip weekends
            if ($current->format('w') == 0 || $current->format('w') == 6) {
                $current->add(new DateInterval('P1D'));
                continue;
            }
            
            // Generate realistic attendance times
            $clockIn = generateClockInTime();
            $clockOut = generateClockOutTime($clockIn);
            $lateness = calculateLateness($clockIn);
            $earlyLeaving = calculateEarlyLeaving($clockOut);
            $overtime = calculateOvertime($clockIn, $clockOut);
            $totalWork = calculateTotalWork($clockIn, $clockOut);
            
            // Execute insert
            $result = $stmt->execute([
                $employeeId,
                $current->format('Y-m-d'),
                $clockIn,
                $clockOut,
                "192.168.1." . (100 + $employeeId),
                "192.168.1." . (100 + $employeeId),
                0, // clock_in_out
                $lateness,
                $earlyLeaving,
                $overtime,
                $totalWork,
                '00:00', // total_rest
                'present'
            ]);
            
            if ($result) {
                $totalRecords++;
                echo "  Added: " . $current->format('Y-m-d') . " - In: $clockIn, Out: $clockOut\n";
            }
            
            $current->add(new DateInterval('P1D'));
            $dayCount++;
        }
        echo "Completed Staff ID: $staffId ($dayCount days)\n\n";
    }
    
    echo "✅ Bulk insertion completed successfully!\n";
    echo "📊 Total records inserted: $totalRecords\n";
    
    // Show summary
    $stmt = $pdo->prepare("SELECT e.staff_id, e.first_name, COUNT(a.id) as total_days, MIN(a.attendance_date) as first_date, MAX(a.attendance_date) as last_date FROM attendances a JOIN employees e ON a.employee_id = e.id WHERE e.staff_id IN ('1', '2', '3') AND a.attendance_date >= '2025-08-12' GROUP BY e.staff_id ORDER BY e.staff_id");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n📈 Summary Report:\n";
    echo str_repeat('-', 80) . "\n";
    printf("%-10s %-25s %-12s %-12s %-12s\n", "Staff ID", "Name", "Total Days", "First Date", "Last Date");
    echo str_repeat('-', 80) . "\n";
    
    foreach ($results as $row) {
        printf("%-10s %-25s %-12s %-12s %-12s\n", 
            $row['staff_id'], 
            $row['first_name'], 
            $row['total_days'], 
            $row['first_date'], 
            $row['last_date']
        );
    }
    echo str_repeat('-', 80) . "\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Helper functions
function generateClockInTime() {
    // Generate time between 8:00 AM to 8:45 AM
    $baseMinutes = 8 * 60;
    $variationMinutes = rand(0, 55);
    $totalMinutes = $baseMinutes + $variationMinutes;
    $hours = floor($totalMinutes / 60);
    $minutes = $totalMinutes % 60;
    return sprintf('%02d:%02d', $hours, $minutes);
}

function generateClockOutTime($clockIn) {
    list($inHour, $inMinute) = explode(':', $clockIn);
    $inTotalMinutes = ($inHour * 60) + $inMinute;
    $workMinutes = 555 + rand(-15, 45); // 9:15 ± variation
    $outTotalMinutes = $inTotalMinutes + $workMinutes;
    $outHour = floor($outTotalMinutes / 60);
    $outMinute = $outTotalMinutes % 60;
    return sprintf('%02d:%02d', $outHour, $outMinute);
}

function calculateLateness($clockIn) {
    list($hour, $minute) = explode(':', $clockIn);
    $inMinutes = ($hour * 60) + $minute;
    $standardStart = 8 * 60;
    if ($inMinutes > $standardStart) {
        $lateMinutes = $inMinutes - $standardStart;
        return sprintf('%02d:%02d', floor($lateMinutes / 60), $lateMinutes % 60);
    }
    return '00:00';
}

function calculateEarlyLeaving($clockOut) {
    list($hour, $minute) = explode(':', $clockOut);
    $outMinutes = ($hour * 60) + $minute;
    $standardEnd = 17 * 60 + 15;
    if ($outMinutes < $standardEnd) {
        $earlyMinutes = $standardEnd - $outMinutes;
        return sprintf('%02d:%02d', floor($earlyMinutes / 60), $earlyMinutes % 60);
    }
    return '00:00';
}

function calculateOvertime($clockIn, $clockOut) {
    list($inHour, $inMinute) = explode(':', $clockIn);
    list($outHour, $outMinute) = explode(':', $clockOut);
    $workMinutes = (($outHour * 60) + $outMinute) - (($inHour * 60) + $inMinute);
    $standardWork = 9 * 60 + 15;
    if ($workMinutes > $standardWork) {
        $overtimeMinutes = $workMinutes - $standardWork;
        return sprintf('%02d:%02d', floor($overtimeMinutes / 60), $overtimeMinutes % 60);
    }
    return '00:00';
}

function calculateTotalWork($clockIn, $clockOut) {
    list($inHour, $inMinute) = explode(':', $clockIn);
    list($outHour, $outMinute) = explode(':', $clockOut);
    $workMinutes = (($outHour * 60) + $outMinute) - (($inHour * 60) + $inMinute);
    return sprintf('%02d:%02d', floor($workMinutes / 60), $workMinutes % 60);
}
?>