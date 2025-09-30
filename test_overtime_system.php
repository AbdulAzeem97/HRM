<?php
// Test script for overtime business rules implementation

$db_host = '127.0.0.1';
$db_name = 'u902429527_ttphrm';  // UPDATE THIS
$db_user = 'root';                // UPDATE THIS  
$db_pass = '';                    // UPDATE THIS

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🧪 OVERTIME SYSTEM TEST\n";
    echo "===================\n\n";

    // Test 1: Check if columns exist
    echo "1. Database Schema Check:\n";
    $stmt = $pdo->query("DESCRIBE employees");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $has_overtime = false;
    $has_hours = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] == 'overtime_allowed') {
            $has_overtime = true;
            echo "   ✅ overtime_allowed column exists\n";
        }
        if ($col['Field'] == 'required_hours_per_day') {
            $has_hours = true;
            echo "   ✅ required_hours_per_day column exists\n";
        }
    }
    
    if (!$has_overtime || !$has_hours) {
        echo "   ❌ Missing required columns!\n";
        exit;
    }

    // Test 2: Check employee data
    echo "\n2. Employee Settings Check:\n";
    $stmt = $pdo->query("SELECT COUNT(*) as total, 
                         SUM(CASE WHEN overtime_allowed = 1 THEN 1 ELSE 0 END) as overtime_enabled,
                         AVG(required_hours_per_day) as avg_hours
                         FROM employees");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "   📊 Total employees: {$stats['total']}\n";
    echo "   📊 Overtime enabled: {$stats['overtime_enabled']}\n";
    echo "   📊 Average required hours: " . round($stats['avg_hours'], 1) . "\n";

    // Test 3: Sample employee data
    echo "\n3. Sample Employee Data:\n";
    $stmt = $pdo->query("SELECT id, first_name, last_name, basic_salary, overtime_allowed, required_hours_per_day 
                         FROM employees LIMIT 5");
    
    while ($emp = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("   ID: %d | %s %s | Salary: %s | OT: %s | Hours: %d\n",
            $emp['id'],
            $emp['first_name'],
            $emp['last_name'],
            $emp['basic_salary'] ? '$' . number_format($emp['basic_salary']) : 'Not set',
            $emp['overtime_allowed'] ? 'Yes' : 'No',
            $emp['required_hours_per_day']
        );
    }

    // Test 4: Check for attendance data
    echo "\n4. Attendance Data Check:\n";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM attendances WHERE MONTH(date) = MONTH(NOW())");
        $attendance_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "   📊 Current month attendance records: {$attendance_count}\n";
        
        if ($attendance_count > 0) {
            // Sample attendance record
            $stmt = $pdo->query("SELECT employee_id, date, clock_in, clock_out FROM attendances LIMIT 1");
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($sample) {
                echo "   📄 Sample record: Employee {$sample['employee_id']} on {$sample['date']} | In: {$sample['clock_in']} | Out: {$sample['clock_out']}\n";
            }
        } else {
            echo "   ⚠️  No attendance data found - add some test data to test overtime calculations\n";
        }
    } catch (PDOException $e) {
        echo "   ❌ Attendance table not found or error: " . $e->getMessage() . "\n";
    }

    // Test 5: Check office shifts
    echo "\n5. Office Shifts Check:\n";
    try {
        $stmt = $pdo->query("SELECT * FROM office_shifts LIMIT 1");
        $shift = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($shift) {
            echo "   ✅ Office shifts configured\n";
            echo "   📄 Sample: Start {$shift['start_time']} | End {$shift['end_time']}\n";
        } else {
            echo "   ⚠️  No office shifts configured - needed for late detection\n";
        }
    } catch (PDOException $e) {
        echo "   ❌ Office shifts table not found: " . $e->getMessage() . "\n";
    }

    echo "\n✅ System Check Complete!\n\n";
    
    echo "📋 NEXT STEPS:\n";
    echo "1. Configure overtime settings per employee via UI\n";
    echo "2. Add test attendance data if none exists\n";
    echo "3. Run payroll generation to test calculations\n";
    echo "4. Verify overtime and deductions in payslips\n";

} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    echo "\nPlease update the database credentials at the top of this file.\n";
}
?>