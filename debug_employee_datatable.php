<?php
/**
 * Debug script to test employee datatable functionality
 */

echo "Employee DataTable Debug Test\n";
echo "============================\n\n";

// Test database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=u902429527_ttphrm;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection successful\n";
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test basic employee query
try {
    echo "\n1. Testing basic employee query...\n";
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, staff_id, email, contact_no,
               company_id, department_id, designation_id, office_shift_id,
               date_of_birth, joining_date, exit_date, nic_expiry, is_active
        FROM employees
        WHERE is_active = 1
        LIMIT 5
    ");
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "   Found " . count($employees) . " active employees\n";

    if (count($employees) > 0) {
        echo "   Sample employee data:\n";
        $sample = $employees[0];
        foreach ($sample as $key => $value) {
            $displayValue = $value === null ? 'NULL' : ($value === '' ? 'EMPTY' : $value);
            echo "   - $key: $displayValue\n";
        }
    }

} catch (PDOException $e) {
    echo "   ❌ Employee query failed: " . $e->getMessage() . "\n";
}

// Test date fields specifically
try {
    echo "\n2. Testing date fields...\n";
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, date_of_birth, joining_date, exit_date, nic_expiry
        FROM employees
        WHERE id = 61
    ");
    $stmt->execute();
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($employee) {
        echo "   Employee 61 found:\n";
        echo "   - Name: {$employee['first_name']} {$employee['last_name']}\n";
        echo "   - Date of Birth: " . ($employee['date_of_birth'] ?: 'NULL') . "\n";
        echo "   - Joining Date: " . ($employee['joining_date'] ?: 'NULL') . "\n";
        echo "   - Exit Date: " . ($employee['exit_date'] ?: 'NULL') . "\n";
        echo "   - NIC Expiry: " . ($employee['nic_expiry'] ?: 'NULL') . "\n";
    } else {
        echo "   ❌ Employee 61 not found\n";
    }

} catch (PDOException $e) {
    echo "   ❌ Date field query failed: " . $e->getMessage() . "\n";
}

// Test the query used by DataTables
try {
    echo "\n3. Testing DataTables query logic...\n";
    $stmt = $pdo->prepare("
        SELECT e.id, e.first_name, e.last_name, e.staff_id, e.email, e.contact_no,
               e.basic_salary, e.payslip_type, e.gender,
               c.company_name, d.department_name, des.designation_name, os.shift_name
        FROM employees e
        LEFT JOIN companies c ON e.company_id = c.id
        LEFT JOIN departments d ON e.department_id = d.id
        LEFT JOIN designations des ON e.designation_id = des.id
        LEFT JOIN office_shifts os ON e.office_shift_id = os.id
        WHERE e.is_active = 1
        AND (e.exit_date IS NULL OR e.exit_date = '' OR e.exit_date = '0000-00-00')
        LIMIT 3
    ");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "   Found " . count($results) . " employees matching DataTables criteria\n";

    if (count($results) > 0) {
        foreach ($results as $emp) {
            echo "   - ID: {$emp['id']}, Name: {$emp['first_name']} {$emp['last_name']}, Company: " . ($emp['company_name'] ?: 'NULL') . "\n";
        }
    }

} catch (PDOException $e) {
    echo "   ❌ DataTables query failed: " . $e->getMessage() . "\n";
}

// Test for common issues
echo "\n4. Checking for common issues...\n";

// Check for invalid date formats
try {
    $stmt = $pdo->prepare("
        SELECT id, date_of_birth, joining_date, exit_date, nic_expiry
        FROM employees
        WHERE (date_of_birth NOT REGEXP '^[0-9]{2}-[0-9]{2}-[0-9]{4}$' AND date_of_birth IS NOT NULL AND date_of_birth != '' AND date_of_birth != '0000-00-00')
        OR (joining_date NOT REGEXP '^[0-9]{2}-[0-9]{2}-[0-9]{4}$' AND joining_date IS NOT NULL AND joining_date != '' AND joining_date != '0000-00-00')
        OR (exit_date NOT REGEXP '^[0-9]{2}-[0-9]{2}-[0-9]{4}$' AND exit_date IS NOT NULL AND exit_date != '' AND exit_date != '0000-00-00')
        OR (nic_expiry NOT REGEXP '^[0-9]{2}-[0-9]{2}-[0-9]{4}$' AND nic_expiry IS NOT NULL AND nic_expiry != '' AND nic_expiry != '0000-00-00')
        LIMIT 5
    ");
    $stmt->execute();
    $badDates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($badDates) > 0) {
        echo "   ⚠️  Found " . count($badDates) . " employees with invalid date formats:\n";
        foreach ($badDates as $emp) {
            echo "   - Employee ID {$emp['id']}: DOB='{$emp['date_of_birth']}', Join='{$emp['joining_date']}', Exit='{$emp['exit_date']}', NIC='{$emp['nic_expiry']}'\n";
        }
    } else {
        echo "   ✅ All date fields appear to be in correct dd-mm-yyyy format\n";
    }

} catch (PDOException $e) {
    echo "   ❌ Date format check failed: " . $e->getMessage() . "\n";
}

echo "\n✅ Debug test completed!\n";
echo "If there are issues above, they may be causing the DataTables 500 error.\n";
?>