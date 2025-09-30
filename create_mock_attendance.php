<?php

echo "=== MOCK ATTENDANCE DATA GENERATOR ===\n";
echo "Creating attendance data for August & September 2025\n\n";

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

// Labor employees data
$laborEmployees = [
    ['id' => 61, 'name' => 'MUHAMMAD UZAIR SIDDIQUI', 'salary' => 50000],
    ['id' => 62, 'name' => 'M.SAEED KHAN', 'salary' => 41440],
    ['id' => 63, 'name' => 'M.ASIF', 'salary' => 40700],
    ['id' => 64, 'name' => 'M.TASLEEM', 'salary' => 37000],
    ['id' => 65, 'name' => 'HASEEB AHMED', 'salary' => 42750],
    ['id' => 66, 'name' => 'M HUZAIFA', 'salary' => 37000],
    ['id' => 67, 'name' => 'M.ZAYAN', 'salary' => 37000],
    ['id' => 68, 'name' => 'ADAN ISHAAQ', 'salary' => 37000],
    ['id' => 69, 'name' => 'AHMED', 'salary' => 37000],
    ['id' => 70, 'name' => 'M.ALI', 'salary' => 37000]
];

// Shift patterns with realistic variations
$shiftPatterns = [
    'morning' => [
        'base_in' => '07:00',
        'base_out' => '15:45',
        'variations' => [
            ['in' => '06:45', 'out' => '15:30'],
            ['in' => '07:15', 'out' => '16:00'],
            ['in' => '07:30', 'out' => '16:15'],
            ['in' => '07:00', 'out' => '15:45'],
            ['in' => '06:55', 'out' => '15:50']
        ]
    ],
    'general' => [
        'base_in' => '08:00',
        'base_out' => '17:15',
        'variations' => [
            ['in' => '08:00', 'out' => '17:15'],
            ['in' => '08:15', 'out' => '17:30'],
            ['in' => '07:45', 'out' => '17:00'],
            ['in' => '08:30', 'out' => '17:45'],
            ['in' => '08:10', 'out' => '17:25']
        ]
    ],
    'mid_shift' => [
        'base_in' => '11:00',
        'base_out' => '20:15',
        'variations' => [
            ['in' => '11:00', 'out' => '20:15'],
            ['in' => '10:45', 'out' => '20:00'],
            ['in' => '11:15', 'out' => '20:30'],
            ['in' => '11:30', 'out' => '20:45'],
            ['in' => '10:55', 'out' => '20:10']
        ]
    ],
    'evening' => [
        'base_in' => '15:00',
        'base_out' => '23:45',
        'variations' => [
            ['in' => '15:00', 'out' => '23:45'],
            ['in' => '15:20', 'out' => '00:05'],
            ['in' => '14:45', 'out' => '23:30'],
            ['in' => '15:30', 'out' => '00:15'],
            ['in' => '15:10', 'out' => '23:55']
        ]
    ],
    'night' => [
        'base_in' => '23:00',
        'base_out' => '07:15',
        'variations' => [
            ['in' => '23:00', 'out' => '07:15'],
            ['in' => '23:15', 'out' => '07:30'],
            ['in' => '22:45', 'out' => '07:00'],
            ['in' => '23:30', 'out' => '07:45'],
            ['in' => '23:05', 'out' => '07:20']
        ]
    ]
];

// Assign each employee a preferred shift pattern
$employeeShiftPatterns = [
    61 => 'morning',    // MUHAMMAD UZAIR SIDDIQUI
    62 => 'general',    // M.SAEED KHAN
    63 => 'mid_shift',  // M.ASIF
    64 => 'general',    // M.TASLEEM
    65 => 'evening',    // HASEEB AHMED
    66 => 'morning',    // M HUZAIFA
    67 => 'mid_shift',  // M.ZAYAN
    68 => 'mid_shift',  // ADAN ISHAAQ (our test case)
    69 => 'evening',    // AHMED
    70 => 'night'       // M.ALI
];

// Function to generate random time variations
function addTimeVariation($time, $maxMinutes = 30) {
    $timestamp = strtotime($time);
    $variation = rand(-$maxMinutes, $maxMinutes) * 60; // Convert to seconds
    return date('H:i', $timestamp + $variation);
}

// Function to create attendance scenarios
function createAttendanceScenarios($employeeId, $date, $shiftPattern, $shiftVariations) {
    $scenarios = [
        'normal' => 70,      // 70% normal attendance
        'late' => 15,        // 15% late arrival
        'early_leave' => 10, // 10% early leave
        'overtime' => 3,     // 3% overtime
        'absent' => 2        // 2% absent
    ];
    
    $rand = rand(1, 100);
    $cumulative = 0;
    
    foreach ($scenarios as $type => $probability) {
        $cumulative += $probability;
        if ($rand <= $cumulative) {
            return generateAttendance($type, $date, $shiftVariations);
        }
    }
    
    return generateAttendance('normal', $date, $shiftVariations);
}

// Function to generate specific attendance types
function generateAttendance($type, $date, $shiftVariations) {
    $variation = $shiftVariations[array_rand($shiftVariations)];
    
    switch ($type) {
        case 'normal':
            return [
                'clock_in' => $variation['in'],
                'clock_out' => $variation['out'],
                'type' => 'normal'
            ];
            
        case 'late':
            $lateIn = addTimeVariation($variation['in'], 60); // Up to 1 hour late
            return [
                'clock_in' => $lateIn,
                'clock_out' => $variation['out'],
                'type' => 'late'
            ];
            
        case 'early_leave':
            $earlyOut = addTimeVariation($variation['out'], -90); // Up to 1.5 hours early
            return [
                'clock_in' => $variation['in'],
                'clock_out' => $earlyOut,
                'type' => 'early_leave'
            ];
            
        case 'overtime':
            $overtimeOut = addTimeVariation($variation['out'], 120); // Up to 2 hours overtime
            return [
                'clock_in' => $variation['in'],
                'clock_out' => $overtimeOut,
                'type' => 'overtime'
            ];
            
        case 'absent':
            return null; // No attendance record
            
        default:
            return [
                'clock_in' => $variation['in'],
                'clock_out' => $variation['out'],
                'type' => 'normal'
            ];
    }
}

// Clear existing mock data for these months
echo "\n🗑️  Clearing existing attendance data...\n";
$pdo->exec("DELETE FROM attendances WHERE attendance_date >= '2025-08-01' AND attendance_date <= '2025-09-30'");

// Generate attendance for August 2025 (31 days)
echo "\n📅 Generating August 2025 attendance...\n";
$augustCount = 0;

for ($day = 1; $day <= 31; $day++) {
    $date = sprintf('2025-08-%02d', $day);
    $dayOfWeek = date('w', strtotime($date));
    
    // Skip Sundays (day 0) for most employees
    if ($dayOfWeek == 0) continue;
    
    foreach ($laborEmployees as $employee) {
        $employeeId = $employee['id'];
        $shiftType = $employeeShiftPatterns[$employeeId];
        $shiftData = $shiftPatterns[$shiftType];
        
        // Generate attendance scenario
        $attendance = createAttendanceScenarios($employeeId, $date, $shiftType, $shiftData['variations']);
        
        if ($attendance !== null) {
            $sql = "INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out) 
                    VALUES (?, ?, ?, ?, '192.168.1.100', '192.168.1.100', 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $employeeId,
                $date,
                $attendance['clock_in'],
                $attendance['clock_out']
            ]);
            $augustCount++;
        }
    }
}

echo "✅ Created $augustCount attendance records for August 2025\n";

// Generate attendance for September 2025 (30 days)
echo "\n📅 Generating September 2025 attendance...\n";
$septemberCount = 0;

for ($day = 1; $day <= 30; $day++) {
    $date = sprintf('2025-09-%02d', $day);
    $dayOfWeek = date('w', strtotime($date));
    
    // Skip Sundays (day 0) for most employees
    if ($dayOfWeek == 0) continue;
    
    foreach ($laborEmployees as $employee) {
        $employeeId = $employee['id'];
        $shiftType = $employeeShiftPatterns[$employeeId];
        $shiftData = $shiftPatterns[$shiftType];
        
        // Generate attendance scenario
        $attendance = createAttendanceScenarios($employeeId, $date, $shiftType, $shiftData['variations']);
        
        if ($attendance !== null) {
            $sql = "INSERT INTO attendances (employee_id, attendance_date, clock_in, clock_out, clock_in_ip, clock_out_ip, clock_in_out) 
                    VALUES (?, ?, ?, ?, '192.168.1.100', '192.168.1.100', 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $employeeId,
                $date,
                $attendance['clock_in'],
                $attendance['clock_out']
            ]);
            $septemberCount++;
        }
    }
}

echo "✅ Created $septemberCount attendance records for September 2025\n";

// Generate summary statistics
echo "\n📊 ATTENDANCE SUMMARY:\n";
echo "======================\n";

$totalRecords = $augustCount + $septemberCount;
echo "Total Attendance Records: $totalRecords\n";
echo "August 2025: $augustCount records\n";
echo "September 2025: $septemberCount records\n";

// Show sample data for verification
echo "\n🔍 SAMPLE DATA VERIFICATION:\n";
echo "============================\n";

$stmt = $pdo->query("
    SELECT 
        e.staff_id,
        e.first_name,
        a.attendance_date,
        a.clock_in,
        a.clock_out,
        TIMEDIFF(a.clock_out, a.clock_in) as working_hours
    FROM attendances a 
    JOIN employees e ON a.employee_id = e.id 
    WHERE a.attendance_date >= '2025-08-01' 
    ORDER BY a.attendance_date, e.staff_id 
    LIMIT 10
");

echo sprintf("%-10s %-20s %-12s %-10s %-10s %-15s\n", 
    'Staff ID', 'Name', 'Date', 'Clock In', 'Clock Out', 'Working Hours');
echo str_repeat('-', 80) . "\n";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("%-10s %-20s %-12s %-10s %-10s %-15s\n",
        $row['staff_id'],
        substr($row['first_name'], 0, 18),
        $row['attendance_date'],
        $row['clock_in'],
        $row['clock_out'],
        $row['working_hours']
    );
}

// Monthly attendance counts per employee
echo "\n📈 MONTHLY ATTENDANCE COUNTS:\n";
echo "============================\n";

$stmt = $pdo->query("
    SELECT 
        e.staff_id,
        e.first_name,
        COUNT(CASE WHEN MONTH(a.attendance_date) = 8 THEN 1 END) as august_days,
        COUNT(CASE WHEN MONTH(a.attendance_date) = 9 THEN 1 END) as september_days,
        COUNT(*) as total_days
    FROM employees e
    LEFT JOIN attendances a ON e.id = a.employee_id AND a.attendance_date >= '2025-08-01' AND a.attendance_date <= '2025-09-30'
    WHERE e.is_labor_employee = 1
    GROUP BY e.id, e.staff_id, e.first_name
    ORDER BY e.staff_id
");

echo sprintf("%-10s %-20s %-12s %-12s %-12s\n", 
    'Staff ID', 'Name', 'Aug Days', 'Sep Days', 'Total Days');
echo str_repeat('-', 70) . "\n";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("%-10s %-20s %-12s %-12s %-12s\n",
        $row['staff_id'],
        substr($row['first_name'], 0, 18),
        $row['august_days'],
        $row['september_days'],
        $row['total_days']
    );
}

echo "\n🎉 MOCK DATA GENERATION COMPLETED SUCCESSFULLY!\n";
echo "\n📝 NEXT STEPS:\n";
echo "1. Test auto-shift detection on this data\n";
echo "2. Process payroll calculations\n";
echo "3. Generate bulk payment processing\n";
echo "\n🔗 Access URLs:\n";
echo "- Labor Dashboard: http://localhost/ttphrm/public/labor\n";
echo "- Process Attendance: http://localhost/ttphrm/public/labor/attendance\n";

?>