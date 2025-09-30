<?php
// Comprehensive fix for employee constraint violations
$db_host = '127.0.0.1';
$db_name = 'u902429527_ttphrm';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🛠️ Comprehensive Employee Constraint Fix</h2>";
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; margin: 10px 0; }
        .warning { color: orange; background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; margin: 10px 0; }
        .info { background: #e7f3ff; padding: 10px; border: 1px solid #b3d4fc; margin: 10px 0; }
        .btn { padding: 10px 20px; margin: 5px; cursor: pointer; border: none; border-radius: 4px; text-decoration: none; display: inline-block; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        .btn-info { background: #17a2b8; color: white; }
    </style>";

    // Handle different actions
    if (isset($_GET['action'])) {
        echo "<h3>Processing Action: " . htmlspecialchars($_GET['action']) . "</h3>";

        switch ($_GET['action']) {
            case 'check_all':
                checkAllEmployees($pdo);
                break;
            case 'fix_employee_61':
                fixSpecificEmployee($pdo, 61);
                break;
            case 'create_defaults':
                createDefaultRecords($pdo);
                break;
            case 'disable_fk':
                disableForeignKeyChecks($pdo);
                break;
            case 'enable_fk':
                enableForeignKeyChecks($pdo);
                break;
            default:
                echo "<div class='error'>Unknown action</div>";
        }
    } else {
        // Show main menu
        showMainMenu();
    }

} catch (PDOException $e) {
    echo "<div class='error'>❌ Database error: " . $e->getMessage() . "</div>";
}

function showMainMenu() {
    echo "<h3>Available Actions:</h3>";
    echo "<div class='info'>";
    echo "<h4>Choose an action to resolve constraint violations:</h4>";
    echo "<a href='?action=check_all' class='btn btn-info'>1. Check All Employees for Constraint Issues</a><br><br>";
    echo "<a href='?action=fix_employee_61' class='btn btn-success'>2. Fix Employee 61 Specifically</a><br><br>";
    echo "<a href='?action=create_defaults' class='btn btn-warning'>3. Create Default Records for Missing References</a><br><br>";
    echo "<a href='?action=disable_fk' class='btn btn-warning'>4. Temporarily Disable Foreign Key Checks</a><br><br>";
    echo "<a href='?action=enable_fk' class='btn btn-info'>5. Re-enable Foreign Key Checks</a><br><br>";
    echo "</div>";

    echo "<h3>Quick Diagnostics:</h3>";
    echo "<p><a href='debug_constraint_violation.php' target='_blank'>🔍 Run Detailed Diagnostics</a></p>";
    echo "<p><a href='fix_constraint_violation.php' target='_blank'>🔧 Interactive Fix Tool</a></p>";
}

function checkAllEmployees($pdo) {
    echo "<h4>Checking All Employees for Constraint Violations...</h4>";

    $stmt = $pdo->query("SELECT id, first_name, last_name, company_id, department_id, designation_id, office_shift_id, location_id, status_id, role_users_id FROM employees");
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $issues = [];

    foreach ($employees as $emp) {
        $empIssues = [];

        // Check each foreign key
        $checks = [
            'company_id' => 'companies',
            'department_id' => 'departments',
            'designation_id' => 'designations',
            'office_shift_id' => 'office_shifts',
            'location_id' => 'locations',
            'status_id' => 'statuses',
            'role_users_id' => 'roles'
        ];

        foreach ($checks as $field => $table) {
            $value = $emp[$field];
            if ($value !== null) {
                try {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `{$table}` WHERE id = ?");
                    $stmt->execute([$value]);
                    if ($stmt->fetchColumn() == 0) {
                        $empIssues[] = "{$field} = {$value} (not found in {$table})";
                    }
                } catch (Exception $e) {
                    $empIssues[] = "{$field} = {$value} (table {$table} error)";
                }
            }
        }

        // Check user relationship
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
        $stmt->execute([$emp['id']]);
        if ($stmt->fetchColumn() == 0) {
            $empIssues[] = "No corresponding user record";
        }

        if (!empty($empIssues)) {
            $issues[$emp['id']] = [
                'name' => $emp['first_name'] . ' ' . $emp['last_name'],
                'issues' => $empIssues
            ];
        }
    }

    if (empty($issues)) {
        echo "<div class='success'>✅ No constraint violations found in any employee records!</div>";
    } else {
        echo "<div class='warning'>⚠️ Found constraint violations in " . count($issues) . " employees:</div>";
        foreach ($issues as $empId => $data) {
            echo "<p><strong>Employee {$empId} ({$data['name']}):</strong></p>";
            echo "<ul>";
            foreach ($data['issues'] as $issue) {
                echo "<li>{$issue}</li>";
            }
            echo "</ul>";
        }
    }
}

function fixSpecificEmployee($pdo, $employeeId) {
    echo "<h4>Fixing Employee {$employeeId}...</h4>";

    try {
        $pdo->beginTransaction();

        // Get employee data
        $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
        $stmt->execute([$employeeId]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            throw new Exception("Employee {$employeeId} not found");
        }

        // Check if user exists, create if not
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
        $stmt->execute([$employeeId]);
        if ($stmt->fetchColumn() == 0) {
            // Create user record
            $stmt = $pdo->prepare("INSERT INTO users (id, username, email, password, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $username = strtolower($employee['first_name'] . '.' . $employee['last_name']);
            $email = $employee['email'] ?: $username . '@company.com';
            $password = password_hash('password123', PASSWORD_DEFAULT);
            $stmt->execute([$employeeId, $username, $email, $password]);
            echo "<div class='success'>✅ Created missing user record</div>";
        }

        // Fix foreign key references
        $defaults = getDefaultReferences($pdo);
        $updates = [];

        foreach ($defaults as $field => $defaultId) {
            $currentValue = $employee[$field];
            if ($currentValue !== null) {
                // Check if current value is valid
                $table = str_replace('_id', 's', $field); // Simple pluralization
                if ($field === 'role_users_id') $table = 'roles';

                $stmt = $pdo->prepare("SELECT COUNT(*) FROM `{$table}` WHERE id = ?");
                $stmt->execute([$currentValue]);
                if ($stmt->fetchColumn() == 0) {
                    $updates[$field] = $defaultId;
                    echo "<div class='warning'>⚠️ Fixed invalid {$field}: {$currentValue} → {$defaultId}</div>";
                }
            }
        }

        // Apply updates if any
        if (!empty($updates)) {
            $setParts = [];
            $values = [];
            foreach ($updates as $field => $value) {
                $setParts[] = "`{$field}` = ?";
                $values[] = $value;
            }
            $values[] = $employeeId;

            $sql = "UPDATE employees SET " . implode(', ', $setParts) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
        }

        $pdo->commit();
        echo "<div class='success'>✅ Employee {$employeeId} fixed successfully!</div>";

    } catch (Exception $e) {
        $pdo->rollback();
        echo "<div class='error'>❌ Error fixing employee {$employeeId}: " . $e->getMessage() . "</div>";
    }
}

function createDefaultRecords($pdo) {
    echo "<h4>Creating Default Records...</h4>";

    $defaults = [
        ['table' => 'companies', 'name' => 'Default Company'],
        ['table' => 'departments', 'name' => 'General'],
        ['table' => 'designations', 'name' => 'Employee'],
        ['table' => 'office_shifts', 'name' => 'Standard Shift'],
        ['table' => 'locations', 'name' => 'Main Office'],
        ['table' => 'statuses', 'name' => 'Active'],
        ['table' => 'roles', 'name' => 'Employee']
    ];

    foreach ($defaults as $default) {
        try {
            // Check if any record exists
            $stmt = $pdo->query("SELECT COUNT(*) FROM {$default['table']}");
            if ($stmt->fetchColumn() == 0) {
                // Create default record
                $nameField = getNameField($default['table']);
                $stmt = $pdo->prepare("INSERT INTO {$default['table']} ({$nameField}, created_at, updated_at) VALUES (?, NOW(), NOW())");
                $stmt->execute([$default['name']]);
                echo "<div class='success'>✅ Created default {$default['table']}: {$default['name']}</div>";
            } else {
                echo "<div class='info'>ℹ️ {$default['table']} already has records</div>";
            }
        } catch (Exception $e) {
            echo "<div class='error'>❌ Error creating {$default['table']}: {$e->getMessage()}</div>";
        }
    }
}

function disableForeignKeyChecks($pdo) {
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        echo "<div class='success'>✅ Foreign key checks disabled</div>";
        echo "<div class='warning'>⚠️ Remember to re-enable them after your updates!</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error: {$e->getMessage()}</div>";
    }
}

function enableForeignKeyChecks($pdo) {
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        echo "<div class='success'>✅ Foreign key checks re-enabled</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error: {$e->getMessage()}</div>";
    }
}

function getDefaultReferences($pdo) {
    $defaults = [];

    $tables = ['companies', 'departments', 'designations', 'office_shifts', 'locations', 'statuses', 'roles'];

    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT id FROM {$table} ORDER BY id LIMIT 1");
            $id = $stmt->fetchColumn();

            $field = rtrim($table, 's') . '_id';
            if ($table === 'roles') $field = 'role_users_id';

            $defaults[$field] = $id ?: 1;
        } catch (Exception $e) {
            $defaults[rtrim($table, 's') . '_id'] = null;
        }
    }

    return $defaults;
}

function getNameField($table) {
    $nameFields = [
        'companies' => 'company_name',
        'departments' => 'department_name',
        'designations' => 'designation_name',
        'office_shifts' => 'shift_name',
        'locations' => 'location',
        'statuses' => 'status_title',
        'roles' => 'name'
    ];

    return $nameFields[$table] ?? 'name';
}
?>