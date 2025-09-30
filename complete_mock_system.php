<?php
echo "=== COMPREHENSIVE MOCK DATA & PAYROLL SYSTEM ===\n";
echo "Creating complete attendance data and payroll calculations\n\n";

// Database connection
$host = 'localhost';
$dbname = 'u902429527_ttphrm';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connected successfully\n";
} catch(PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}

// Clear existing mock data
echo "\n🗑️  Clearing existing attendance data...\n";
$pdo->exec("DELETE FROM attendances WHERE attendance_date >= '2025-08-01' AND attendance_date <= '2025-09-30'");

// Labor employees with their details
$employees = [
    61 => ['name' => 'MUHAMMAD UZAIR SIDDIQUI', 'salary' => 50000, 'shift_pattern' => 'morning'],
    62 => ['name' => 'M.SAEED KHAN', 'salary' => 41440, 'shift_pattern' => 'general'],
    63 => ['name' => 'M.ASIF', 'salary' => 40700, 'shift_pattern' => 'mid'],
    64 => ['name' => 'M.TASLEEM', 'salary' => 37000, 'shift_pattern' => 'general'],
    65 => ['name' => 'HASEEB AHMED', 'salary' => 42750, 'shift_pattern' => 'evening'],
    66 => ['name' => 'M HUZAIFA', 'salary' => 37000, 'shift_pattern' => 'morning'],
    67 => ['name' => 'M.ZAYAN', 'salary' => 37000, 'shift_pattern' => 'mid'],
    68 => ['name' => 'ADAN ISHAAQ', 'salary' => 37000, 'shift_pattern' => 'mid'],
    69 => ['name' => 'AHMED', 'salary' => 37000, 'shift_pattern' => 'evening'],
    70 => ['name' => 'M.ALI', 'salary' => 37000, 'shift_pattern' => 'night']
];

// Shift patterns with base times
$shifts = [
    'morning' => ['in' => '07:00', 'out' => '15:45', 'duration' => 8.75],
    'general' => ['in' => '08:00', 'out' => '17:15', 'duration' => 9.25],
    'mid' => ['in' => '11:00', 'out' => '20:15', 'duration' => 9.25],
    'evening' => ['in' => '15:00', 'out' => '23:45', 'duration' => 8.75],
    'night' => ['in' => '23:00', 'out' => '07:15', 'duration' => 8.25]
];

// Function to add random variation to time
function varyTime($time, $maxMinutes = 30) {
    $timestamp = strtotime($time);
    $variation = rand(-$maxMinutes, $maxMinutes) * 60;
    return date('H:i', $timestamp + $variation);
}

// Generate comprehensive attendance data
echo "\n📅 Generating comprehensive attendance data...\n";
$totalRecords = 0;

// August 2025 (31 days)
for ($day = 1; $day <= 31; $day++) {
    $date = sprintf('2025-08-%02d', $day);
    $dayOfWeek = date('w', strtotime($date));
    
    // Skip Sundays for most scenarios
    if ($dayOfWeek == 0) continue;
    
    foreach ($employees as $empId => $empData) {
        $shiftData = $shifts[$empData['shift_pattern']];
        
        // Create attendance scenarios (85% normal, 10% variations, 5% absent)
        $scenario = rand(1, 100);
        
        if ($scenario <= 85) {
            // Normal attendance
            $clockIn = varyTime($shiftData['in'], 15);
            $clockOut = varyTime($shiftData['out'], 15);
        } elseif ($scenario <= 90) {
            // Late arrival
            $clockIn = varyTime($shiftData['in'], 60);
            $clockOut = $shiftData['out'];
        } elseif ($scenario <= 95) {
            // Early leave or overtime
            if (rand(1, 2) == 1) {
                // Early leave
                $clockIn = $shiftData['in'];
                $clockOut = varyTime($shiftData['out'], -90);
            } else {
                // Overtime
                $clockIn = $shiftData['in'];
                $clockOut = varyTime($shiftData['out'], 120);
            }
        } else {
            // Absent - skip this record
            continue;
        }
        
        // Insert attendance record
        $sql = "INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out) VALUES (?, ?, ?, ?, '192.168.1.100', '192.168.1.100', 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$empId, $date, $clockIn, $clockOut]);
        $totalRecords++;
    }
}

// September 2025 (30 days)
for ($day = 1; $day <= 30; $day++) {
    $date = sprintf('2025-09-%02d', $day);
    $dayOfWeek = date('w', strtotime($date));
    
    // Skip Sundays
    if ($dayOfWeek == 0) continue;
    
    foreach ($employees as $empId => $empData) {
        $shiftData = $shifts[$empData['shift_pattern']];
        
        // Create attendance scenarios
        $scenario = rand(1, 100);
        
        if ($scenario <= 85) {
            // Normal attendance
            $clockIn = varyTime($shiftData['in'], 15);
            $clockOut = varyTime($shiftData['out'], 15);
        } elseif ($scenario <= 90) {
            // Late arrival
            $clockIn = varyTime($shiftData['in'], 60);
            $clockOut = $shiftData['out'];
        } elseif ($scenario <= 95) {
            // Early leave or overtime
            if (rand(1, 2) == 1) {
                // Early leave
                $clockIn = $shiftData['in'];
                $clockOut = varyTime($shiftData['out'], -90);
            } else {
                // Overtime
                $clockIn = $shiftData['in'];
                $clockOut = varyTime($shiftData['out'], 120);
            }
        } else {
            // Absent - skip this record
            continue;
        }
        
        // Insert attendance record
        $sql = "INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out) VALUES (?, ?, ?, ?, '192.168.1.100', '192.168.1.100', 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$empId, $date, $clockIn, $clockOut]);
        $totalRecords++;
    }
}

echo "✅ Created $totalRecords attendance records\n";

// Now create payroll calculations table
echo "\n💰 Creating payroll calculation system...\n";

// Create payroll table if not exists
$createPayrollTable = "
CREATE TABLE IF NOT EXISTS payroll_calculations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    month TINYINT NOT NULL,
    year SMALLINT NOT NULL,
    basic_salary DECIMAL(10,2) NOT NULL,
    total_working_days INT DEFAULT 0,
    present_days INT DEFAULT 0,
    absent_days INT DEFAULT 0,
    late_days INT DEFAULT 0,
    early_leave_days INT DEFAULT 0,
    overtime_hours DECIMAL(5,2) DEFAULT 0,
    overtime_amount DECIMAL(10,2) DEFAULT 0,
    late_deduction DECIMAL(10,2) DEFAULT 0,
    early_leave_deduction DECIMAL(10,2) DEFAULT 0,
    absent_deduction DECIMAL(10,2) DEFAULT 0,
    gross_salary DECIMAL(10,2) DEFAULT 0,
    net_salary DECIMAL(10,2) DEFAULT 0,
    auto_shift_detected VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$pdo->exec($createPayrollTable);
echo "✅ Payroll calculations table created\n";

// Function to calculate working hours between two times
function calculateHours($timeIn, $timeOut) {
    $in = new DateTime($timeIn);
    $out = new DateTime($timeOut);
    
    // Handle overnight shifts
    if ($out < $in) {
        $out->add(new DateInterval('P1D'));
    }
    
    $diff = $in->diff($out);
    return $diff->h + ($diff->i / 60);
}

// Function to detect shift automatically
function detectShift($timeIn, $timeOut) {
    $shifts = [
        'Shift-A' => ['start' => '07:00', 'end' => '15:45', 'duration' => 8.75],
        'General' => ['start' => '08:00', 'end' => '17:15', 'duration' => 9.25],
        '11:00-20:15' => ['start' => '11:00', 'end' => '20:15', 'duration' => 9.25],
        'Shift-B' => ['start' => '15:00', 'end' => '23:45', 'duration' => 8.75],
        '19:00-04:15' => ['start' => '19:00', 'end' => '04:15', 'duration' => 9.25],
        'Shift-C' => ['start' => '23:00', 'end' => '07:15', 'duration' => 8.25]
    ];
    
    $bestShift = null;
    $bestScore = -1;
    
    $punchIn = new DateTime($timeIn);
    $totalHours = calculateHours($timeIn, $timeOut);
    
    foreach ($shifts as $name => $shift) {
        $shiftStart = new DateTime($shift['start']);
        $score = 0;
        
        // Calculate alignment score
        $timeDiff = abs($punchIn->getTimestamp() - $shiftStart->getTimestamp()) / 60; // minutes
        
        if ($timeDiff <= 180) { // 3 hour tolerance
            $score += (180 - $timeDiff) / 180 * 50;
        }
        
        // Hours coverage score
        $coverage = min($totalHours / 9, 1) * 30;
        $score += $coverage;
        
        // Duration match score
        $durationDiff = abs($totalHours - $shift['duration']) * 60; // minutes
        if ($durationDiff <= 240) { // 4 hour tolerance
            $score += (240 - $durationDiff) / 240 * 20;
        }
        
        if ($score > $bestScore) {
            $bestScore = $score;
            $bestShift = $name;
        }
    }
    
    return $bestShift;
}

// Calculate payroll for August and September 2025
foreach ([8, 9] as $month) {
    $monthName = $month == 8 ? 'August' : 'September';
    echo "\n📊 Processing $monthName 2025 payroll...\n";
    
    foreach ($employees as $empId => $empData) {
        // Get attendance data for this employee and month
        $sql = "SELECT * FROM attendances WHERE employee_id = ? AND MONTH(attendance_date) = ? AND YEAR(attendance_date) = 2025 ORDER BY attendance_date";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$empId, $month]);
        $attendances = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate metrics
        $presentDays = count($attendances);
        $totalWorkingDays = $month == 8 ? 27 : 26; // Excluding Sundays
        $absentDays = $totalWorkingDays - $presentDays;
        
        $lateDays = 0;
        $earlyLeaveDays = 0;
        $totalOvertimeHours = 0;
        $lateMinutes = 0;
        $earlyLeaveMinutes = 0;
        $shiftDetections = [];
        
        foreach ($attendances as $att) {
            $workingHours = calculateHours($att['clock_in'], $att['clock_out']);
            $detectedShift = detectShift($att['clock_in'], $att['clock_out']);
            
            if ($detectedShift) {
                $shiftDetections[] = $detectedShift;
            }
            
            // Assume standard 9.25 hour shift for calculations
            $standardHours = 9.25;
            $expectedIn = '11:00'; // Default expected time
            
            // Check for late arrival (more than 15 minutes)
            $punchIn = new DateTime($att['clock_in']);
            $expectedInTime = new DateTime($expectedIn);
            $lateDiff = ($punchIn->getTimestamp() - $expectedInTime->getTimestamp()) / 60;
            
            if ($lateDiff > 15) {
                $lateDays++;
                $lateMinutes += $lateDiff;
            }
            
            // Check for early leave
            if ($workingHours < $standardHours) {
                $shortage = ($standardHours - $workingHours) * 60;
                if ($shortage > 30) { // More than 30 minutes short
                    $earlyLeaveDays++;
                    $earlyLeaveMinutes += $shortage;
                }
            }
            
            // Calculate overtime (beyond standard hours)
            if ($workingHours > $standardHours) {
                $overtime = $workingHours - $standardHours;
                $totalOvertimeHours += min($overtime, 2); // Max 2 hours OT per day for pay
            }
        }
        
        // Most common shift detected
        $commonShift = null;
        if (!empty($shiftDetections)) {
            $shiftCounts = array_count_values($shiftDetections);
            $commonShift = array_keys($shiftCounts, max($shiftCounts))[0];
        }
        
        // Calculate deductions and amounts
        $basicSalary = $empData['salary'];
        $perDaySalary = $basicSalary / 26;
        $perMinuteSalary = $basicSalary / (26 * 8 * 60);
        
        // Absent deduction
        $absentDeduction = $absentDays * $perDaySalary;
        
        // Late deduction (after 15 min grace period)
        $lateDeduction = max(0, $lateMinutes - (15 * $lateDays)) * $perMinuteSalary;
        
        // Early leave deduction
        $earlyLeaveDeduction = $earlyLeaveMinutes * $perMinuteSalary;
        
        // Overtime amount (2x hourly rate)
        $hourlyRate = $basicSalary / (26 * 8);
        $overtimeAmount = $totalOvertimeHours * $hourlyRate * 2;
        
        // Calculate final salary
        $grossSalary = $basicSalary + $overtimeAmount;
        $totalDeductions = $absentDeduction + $lateDeduction + $earlyLeaveDeduction;
        $netSalary = $grossSalary - $totalDeductions;
        
        // Insert payroll calculation
        $insertPayroll = "INSERT INTO payroll_calculations 
            (employee_id, month, year, basic_salary, total_working_days, present_days, absent_days, 
             late_days, early_leave_days, overtime_hours, overtime_amount, late_deduction, 
             early_leave_deduction, absent_deduction, gross_salary, net_salary, auto_shift_detected) 
            VALUES (?, ?, 2025, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($insertPayroll);
        $stmt->execute([
            $empId, $month, $basicSalary, $totalWorkingDays, $presentDays, $absentDays,
            $lateDays, $earlyLeaveDays, round($totalOvertimeHours, 2), round($overtimeAmount, 2),
            round($lateDeduction, 2), round($earlyLeaveDeduction, 2), round($absentDeduction, 2),
            round($grossSalary, 2), round($netSalary, 2), $commonShift
        ]);
        
        echo sprintf("✅ %s: Present=%d, Absent=%d, OT=%.1fh, Net=₹%.0f, Shift=%s\n", 
            substr($empData['name'], 0, 15), $presentDays, $absentDays, 
            $totalOvertimeHours, $netSalary, $commonShift ?? 'Auto');
    }
}

// Generate summary report
echo "\n📈 PAYROLL SUMMARY REPORT\n";
echo str_repeat('=', 50) . "\n";

$summary = $pdo->query("
    SELECT 
        month,
        COUNT(*) as total_employees,
        SUM(basic_salary) as total_basic,
        SUM(overtime_amount) as total_overtime,
        SUM(gross_salary) as total_gross,
        SUM(absent_deduction + late_deduction + early_leave_deduction) as total_deductions,
        SUM(net_salary) as total_net,
        AVG(present_days) as avg_present_days
    FROM payroll_calculations 
    GROUP BY month
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($summary as $row) {
    $monthName = $row['month'] == 8 ? 'August' : 'September';
    echo sprintf("%s 2025:\n", $monthName);
    echo sprintf("  Total Employees: %d\n", $row['total_employees']);
    echo sprintf("  Basic Salary: ₹%s\n", number_format($row['total_basic']));
    echo sprintf("  Overtime: ₹%s\n", number_format($row['total_overtime']));
    echo sprintf("  Gross Salary: ₹%s\n", number_format($row['total_gross']));
    echo sprintf("  Deductions: ₹%s\n", number_format($row['total_deductions']));
    echo sprintf("  Net Payable: ₹%s\n", number_format($row['total_net']));
    echo sprintf("  Avg Present Days: %.1f\n\n", $row['avg_present_days']);
}

// Show auto-shift detection results
echo "🎯 AUTO-SHIFT DETECTION RESULTS:\n";
echo str_repeat('=', 50) . "\n";

$shiftResults = $pdo->query("
    SELECT auto_shift_detected, COUNT(*) as count
    FROM payroll_calculations 
    WHERE auto_shift_detected IS NOT NULL
    GROUP BY auto_shift_detected
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($shiftResults as $result) {
    echo sprintf("%-15s: %d employees\n", $result['auto_shift_detected'], $result['count']);
}

echo "\n🎉 COMPREHENSIVE SYSTEM COMPLETED!\n";
echo "\n🔗 Next Steps:\n";
echo "1. Access Labor Dashboard: http://localhost/ttphrm/public/labor\n";
echo "2. Process bulk attendance: http://localhost/ttphrm/public/labor/attendance\n";
echo "3. View payroll calculations in database\n";
echo "4. Test bulk payment processing\n";
?>