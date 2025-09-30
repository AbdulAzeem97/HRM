<?php
// Debug integrity constraint violation for employee 61
$db_host = '127.0.0.1';
$db_name = 'u902429527_ttphrm';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🔍 Integrity Constraint Violation Debug - Employee 61</h2>";
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .error { color: red; background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; }
        .success { color: green; background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; }
        .warning { color: orange; background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; }
        .info { background: #e7f3ff; padding: 10px; border: 1px solid #b3d4fc; }
    </style>";

    // 1. Check if employee 61 exists
    echo "<h3>1. Employee 61 Existence Check</h3>";
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->execute([61]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($employee) {
        echo "<div class='success'>✅ Employee 61 exists</div>";
        echo "<p><strong>Name:</strong> {$employee['first_name']} {$employee['last_name']}</p>";
        echo "<p><strong>Email:</strong> {$employee['email']}</p>";
        echo "<p><strong>Status ID:</strong> " . ($employee['status_id'] ?? 'NULL') . "</p>";
    } else {
        echo "<div class='error'>❌ Employee 61 does not exist</div>";
        exit;
    }

    // 2. Check foreign key constraints on employees table
    echo "<h3>2. Foreign Key Constraints Analysis</h3>";
    $stmt = $pdo->query("
        SELECT
            CONSTRAINT_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_NAME = 'employees'
        AND TABLE_SCHEMA = '{$db_name}'
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    $constraints = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($constraints) > 0) {
        echo "<table>";
        echo "<tr><th>Constraint Name</th><th>Column</th><th>References Table</th><th>References Column</th><th>Status</th></tr>";

        foreach ($constraints as $constraint) {
            echo "<tr>";
            echo "<td>{$constraint['CONSTRAINT_NAME']}</td>";
            echo "<td>{$constraint['COLUMN_NAME']}</td>";
            echo "<td>{$constraint['REFERENCED_TABLE_NAME']}</td>";
            echo "<td>{$constraint['REFERENCED_COLUMN_NAME']}</td>";

            // Check if the referenced value exists
            $column = $constraint['COLUMN_NAME'];
            $refTable = $constraint['REFERENCED_TABLE_NAME'];
            $refColumn = $constraint['REFERENCED_COLUMN_NAME'];

            $employeeValue = $employee[$column] ?? null;

            if ($employeeValue === null) {
                echo "<td class='info'>NULL (OK if nullable)</td>";
            } else {
                try {
                    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM `{$refTable}` WHERE `{$refColumn}` = ?");
                    $checkStmt->execute([$employeeValue]);
                    $exists = $checkStmt->fetchColumn();

                    if ($exists > 0) {
                        echo "<td class='success'>✅ Valid ({$employeeValue})</td>";
                    } else {
                        echo "<td class='error'>❌ Invalid ({$employeeValue})</td>";
                    }
                } catch (Exception $e) {
                    echo "<td class='error'>❌ Check failed</td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No foreign key constraints found.</p>";
    }

    // 3. Check specific problematic fields
    echo "<h3>3. Specific Field Validation</h3>";

    $fieldsToCheck = [
        'company_id' => 'companies',
        'department_id' => 'departments',
        'designation_id' => 'designations',
        'office_shift_id' => 'office_shifts',
        'location_id' => 'locations',
        'status_id' => 'statuses',
        'role_users_id' => 'roles'
    ];

    foreach ($fieldsToCheck as $field => $table) {
        $value = $employee[$field] ?? null;
        echo "<p><strong>{$field}:</strong> ";

        if ($value === null) {
            echo "<span class='info'>NULL</span>";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM `{$table}` WHERE id = ?");
                $stmt->execute([$value]);
                $exists = $stmt->fetchColumn();

                if ($exists > 0) {
                    echo "<span class='success'>✅ {$value} (valid)</span>";
                } else {
                    echo "<span class='error'>❌ {$value} (INVALID - not found in {$table})</span>";
                }
            } catch (Exception $e) {
                echo "<span class='warning'>⚠️ {$value} (table {$table} not found or error)</span>";
            }
        }
        echo "</p>";
    }

    // 4. Check users table relationship
    echo "<h3>4. Users Table Relationship</h3>";
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([61]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "<div class='success'>✅ User 61 exists</div>";
        echo "<p><strong>Username:</strong> {$user['username']}</p>";
        echo "<p><strong>Email:</strong> {$user['email']}</p>";
    } else {
        echo "<div class='error'>❌ User 61 does not exist - this might be the issue!</div>";
    }

    // 5. Recommendations
    echo "<h3>5. Recommendations</h3>";
    echo "<div class='info'>";
    echo "<h4>Possible Solutions:</h4>";
    echo "<ul>";
    echo "<li><strong>Check NULL values:</strong> Some foreign key fields might not allow NULL</li>";
    echo "<li><strong>Verify referenced records:</strong> Ensure all referenced IDs exist in their tables</li>";
    echo "<li><strong>Check users table:</strong> Employee ID should match user ID</li>";
    echo "<li><strong>Disable foreign key checks temporarily:</strong> For urgent fixes</li>";
    echo "</ul>";
    echo "</div>";

    // 6. Quick fix suggestions
    echo "<h3>6. Quick Fix SQL Commands</h3>";
    echo "<div style='background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; font-family: monospace;'>";
    echo "<p><strong>To temporarily disable foreign key checks:</strong></p>";
    echo "<code>SET FOREIGN_KEY_CHECKS = 0;</code><br>";
    echo "<code>-- Your UPDATE statement here --</code><br>";
    echo "<code>SET FOREIGN_KEY_CHECKS = 1;</code><br><br>";

    echo "<p><strong>To check for missing references:</strong></p>";
    foreach ($fieldsToCheck as $field => $table) {
        $value = $employee[$field] ?? null;
        if ($value !== null) {
            echo "<code>SELECT COUNT(*) FROM {$table} WHERE id = {$value};</code><br>";
        }
    }
    echo "</div>";

} catch (PDOException $e) {
    echo "<div class='error'>❌ Database error: " . $e->getMessage() . "</div>";
}
?>