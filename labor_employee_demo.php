<?php

// Demo script for Labor Employee Management System
// This script shows how to use the new labor employee features

echo "=== Labor Employee Management System Demo ===\n\n";

// Simulate the functionality since we can't run Laravel directly

class MockEmployee {
    public static function markAsLaborEmployee($employeeIds) {
        return count($employeeIds);
    }
    
    public static function getAllLaborEmployees() {
        return [
            ['id' => 1, 'name' => 'John Worker', 'dept' => 'Production'],
            ['id' => 2, 'name' => 'Jane Laborer', 'dept' => 'Manufacturing'],
            ['id' => 3, 'name' => 'Mike Mason', 'dept' => 'Construction'],
        ];
    }
}

class MockLaborEmployeeService {
    public static function markEmployeesByCategory($type, $ids) {
        $count = count($ids) * 5; // Simulate 5 employees per category
        return [
            'success' => true,
            'updated_count' => $count,
            'message' => "Successfully marked {$count} employees as labor employees based on {$type}"
        ];
    }
    
    public static function processLaborAttendanceForDate($date) {
        return [
            'success' => true,
            'processed_date' => $date,
            'total_labor_employees' => 15,
            'success_count' => 13,
            'error_count' => 2,
            'results' => [
                1 => ['name' => 'John Worker', 'shift' => '11:00-20:15', 'hours' => 9.5, 'status' => 'Present'],
                2 => ['name' => 'Jane Laborer', 'shift' => 'Shift-A', 'hours' => 7.5, 'status' => 'Early Leave'],
                3 => ['name' => 'Mike Mason', 'shift' => 'General', 'hours' => 4.0, 'status' => 'Half Day']
            ]
        ];
    }
}

// Demo 1: Mark individual employees as labor employees
echo "1. MARK INDIVIDUAL EMPLOYEES AS LABOR EMPLOYEES\n";
echo "===============================================\n";
$employeeIds = [101, 102, 103, 104, 105];
$updated = MockEmployee::markAsLaborEmployee($employeeIds);
echo "✓ Marked {$updated} employees as labor employees\n";
echo "Employee IDs: " . implode(', ', $employeeIds) . "\n\n";

// Demo 2: Bulk mark by department
echo "2. BULK MARK BY DEPARTMENT\n";
echo "==========================\n";
$departmentIds = [1, 2]; // Production, Manufacturing
$result = MockLaborEmployeeService::markEmployeesByCategory('department', $departmentIds);
echo "✓ " . $result['message'] . "\n";
echo "Departments processed: Production, Manufacturing\n\n";

// Demo 3: Bulk mark by designation
echo "3. BULK MARK BY DESIGNATION\n";
echo "===========================\n";
$designationIds = [5, 6, 7]; // Worker, Laborer, Operator
$result = MockLaborEmployeeService::markEmployeesByCategory('designation', $designationIds);
echo "✓ " . $result['message'] . "\n";
echo "Designations processed: Worker, Laborer, Operator\n\n";

// Demo 4: List all labor employees
echo "4. LIST ALL LABOR EMPLOYEES\n";
echo "===========================\n";
$laborEmployees = MockEmployee::getAllLaborEmployees();
foreach ($laborEmployees as $emp) {
    echo "• ID: {$emp['id']} | {$emp['name']} | {$emp['dept']}\n";
}
echo "\n";

// Demo 5: Process attendance with auto-shift detection
echo "5. PROCESS ATTENDANCE WITH AUTO-SHIFT DETECTION\n";
echo "===============================================\n";
$date = '2024-01-15';
$result = MockLaborEmployeeService::processLaborAttendanceForDate($date);
echo "Date: {$result['processed_date']}\n";
echo "Total Labor Employees: {$result['total_labor_employees']}\n";
echo "Successfully Processed: {$result['success_count']}\n";
echo "Errors: {$result['error_count']}\n\n";

echo "Sample Results:\n";
foreach ($result['results'] as $id => $data) {
    echo "• {$data['name']}: {$data['shift']} shift, {$data['hours']}h, Status: {$data['status']}\n";
}
echo "\n";

// Demo 6: Command line usage examples
echo "6. COMMAND LINE USAGE EXAMPLES\n";
echo "===============================\n";
echo "Mark employees as labor by IDs:\n";
echo "php artisan labor:manage mark --employees=101,102,103\n\n";

echo "Mark all employees in Production dept as labor:\n";
echo "php artisan labor:manage mark --departments=1\n\n";

echo "Mark all Workers and Laborers as labor employees:\n";
echo "php artisan labor:manage mark --designations=5,6\n\n";

echo "List all labor employees:\n";
echo "php artisan labor:manage list\n\n";

echo "Remove shift assignments (enable auto-detection):\n";
echo "php artisan labor:manage remove-shifts\n\n";

echo "Process attendance for specific date:\n";
echo "php artisan labor:manage process-attendance --date=2024-01-15\n\n";

echo "Show labor employee statistics:\n";
echo "php artisan labor:manage stats\n\n";

// Demo 7: API endpoints
echo "7. API ENDPOINTS FOR INTEGRATION\n";
echo "=================================\n";
echo "GET /api/labor/stats - Get labor employee statistics\n";
echo "GET /api/labor/employees - Get all labor employees\n";
echo "POST /api/labor/mark-department - Bulk mark by department\n";
echo "POST /api/labor/mark-designation - Bulk mark by designation\n\n";

// Demo 8: How auto-shift detection works
echo "8. AUTO-SHIFT DETECTION IN ACTION\n";
echo "==================================\n";
echo "Example scenarios:\n\n";

$scenarios = [
    ['11:30', '21:30', 'Employee working 10h → Auto-selects 11:00-20:15 shift + 0.75h OT'],
    ['07:45', '15:00', 'Employee working 7.25h → Auto-selects Shift-A + 1.5h early leave'],
    ['15:30', '23:00', 'Employee working 7.5h → Auto-selects Shift-B + 1.25h early leave'],
    ['08:00', '12:00', 'Employee working 4h → Auto-selects General shift + Half Day status'],
    ['23:30', '06:30', 'Employee working 7h → Auto-selects Shift-C + 1.25h early leave'],
];

foreach ($scenarios as $i => $scenario) {
    echo ($i + 1) . ". {$scenario[0]} to {$scenario[1]}: {$scenario[2]}\n";
}
echo "\n";

echo "9. BENEFITS OF THE SYSTEM\n";
echo "==========================\n";
echo "✓ No manual shift assignment needed for labor employees\n";
echo "✓ Automatic best-shift detection based on actual working hours\n";
echo "✓ Proper overtime and early leave calculations\n";
echo "✓ Bulk operations for easy setup\n";
echo "✓ Command-line tools for automation\n";
echo "✓ API endpoints for system integration\n";
echo "✓ Works with any working hours (not just 9+ hours)\n";
echo "✓ Proper deduction calculations for early leave\n\n";

echo "=== Demo Complete ===\n";
echo "The system is now ready to handle labor employees with automatic shift detection!\n";

?>