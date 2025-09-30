<?php

// Quick setup script to mark employees as labor employees
// This can be run directly without Laravel routes

require_once __DIR__ . '/bootstrap/app.php';

use App\Models\Employee;
use App\Services\LaborEmployeeService;

echo "=== Quick Labor Employee Setup ===\n\n";

try {
    // Example 1: Mark specific employees as labor
    echo "1. Marking specific employees as labor employees...\n";
    $employeeIds = [1, 2, 3, 4, 5]; // Replace with actual employee IDs
    $result = Employee::markAsLaborEmployee($employeeIds);
    echo "✓ Marked {$result} employees as labor employees\n\n";

    // Example 2: Get all labor employees
    echo "2. Getting all labor employees...\n";
    $laborEmployees = Employee::getAllLaborEmployees();
    echo "Found {$laborEmployees->count()} labor employees:\n";
    
    foreach ($laborEmployees as $emp) {
        echo "- {$emp->first_name} {$emp->last_name} (ID: {$emp->id})\n";
    }
    echo "\n";

    // Example 3: Mark employees by department
    echo "3. Marking employees by department...\n";
    $departmentIds = [1, 2]; // Replace with actual department IDs
    $result = LaborEmployeeService::markEmployeesByCategory('department', $departmentIds);
    
    if ($result['success']) {
        echo "✓ " . $result['message'] . "\n";
    } else {
        echo "✗ " . $result['error'] . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Make sure to:\n";
    echo "1. Run 'php artisan migrate' first\n";
    echo "2. Have employees in your database\n";
    echo "3. Check your database connection\n";
}

echo "\n=== Setup Complete ===\n";
echo "Your employees are now marked as labor employees and will use auto-shift detection!\n";

?>