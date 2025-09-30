<?php
/**
 * Check Current Database Structure
 * This script analyzes your existing database and identifies what needs to be added/modified
 */

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'u902429527_ttphrm';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database: $database\n\n";

    // Get all existing tables
    $result = $pdo->query("SHOW TABLES");
    $existingTables = $result->fetchAll(PDO::FETCH_COLUMN);

    echo "=== EXISTING TABLES ===\n";
    foreach ($existingTables as $table) {
        echo "- $table\n";
    }
    echo "\nTotal tables: " . count($existingTables) . "\n\n";

    // Check employees table structure
    if (in_array('employees', $existingTables)) {
        echo "=== EMPLOYEES TABLE STRUCTURE ===\n";
        $result = $pdo->query("DESCRIBE employees");
        $employeeColumns = $result->fetchAll(PDO::FETCH_ASSOC);

        foreach ($employeeColumns as $column) {
            echo "- {$column['Field']} ({$column['Type']}) - {$column['Null']} - {$column['Default']}\n";
        }

        // Check for missing columns that should exist based on the model
        $requiredColumns = [
            'is_labor_employee',
            'overtime_allowed',
            'required_hours_per_day',
            'nic',
            'nic_expiry'
        ];

        $existingColumns = array_column($employeeColumns, 'Field');
        $missingColumns = array_diff($requiredColumns, $existingColumns);

        if (!empty($missingColumns)) {
            echo "\n❌ MISSING COLUMNS IN EMPLOYEES TABLE:\n";
            foreach ($missingColumns as $column) {
                echo "- $column\n";
            }
        } else {
            echo "\n✅ All required columns exist in employees table\n";
        }
    } else {
        echo "❌ Employees table does not exist!\n";
    }

    // Check users table
    if (in_array('users', $existingTables)) {
        echo "\n=== USERS TABLE STRUCTURE ===\n";
        $result = $pdo->query("DESCRIBE users");
        $userColumns = $result->fetchAll(PDO::FETCH_ASSOC);

        foreach ($userColumns as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
        }

        // Count users
        $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        echo "\nTotal users: $userCount\n";
    }

    // Check attendances table
    if (in_array('attendances', $existingTables)) {
        echo "\n=== ATTENDANCES TABLE STRUCTURE ===\n";
        $result = $pdo->query("DESCRIBE attendances");
        $attendanceColumns = $result->fetchAll(PDO::FETCH_ASSOC);

        foreach ($attendanceColumns as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
        }

        // Count attendance records
        $attendanceCount = $pdo->query("SELECT COUNT(*) FROM attendances")->fetchColumn();
        echo "\nTotal attendance records: $attendanceCount\n";
    }

    // List essential tables that should exist
    $essentialTables = [
        'users', 'employees', 'attendances', 'companies', 'departments',
        'designations', 'locations', 'office_shifts', 'statuses', 'roles',
        'permissions', 'countries'
    ];

    $missingTables = array_diff($essentialTables, $existingTables);

    if (!empty($missingTables)) {
        echo "\n❌ MISSING ESSENTIAL TABLES:\n";
        foreach ($missingTables as $table) {
            echo "- $table\n";
        }
    } else {
        echo "\n✅ All essential tables exist\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>