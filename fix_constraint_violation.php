<?php
// Fix integrity constraint violation for employee updates
$db_host = '127.0.0.1';
$db_name = 'u902429527_ttphrm';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🔧 Fix Integrity Constraint Violation</h2>";
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; margin: 10px 0; }
        .warning { color: orange; background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; margin: 10px 0; }
        .info { background: #e7f3ff; padding: 10px; border: 1px solid #b3d4fc; margin: 10px 0; }
        .btn { padding: 10px 20px; margin: 5px; cursor: pointer; border: none; border-radius: 4px; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-success { background: #28a745; color: white; }
    </style>";

    $employee_id = 61;

    // Step 1: Check current employee data
    echo "<h3>1. Current Employee Data Analysis</h3>";
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->execute([$employee_id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        echo "<div class='error'>❌ Employee {$employee_id} not found!</div>";
        exit;
    }

    echo "<div class='info'>Found employee: {$employee['first_name']} {$employee['last_name']}</div>";

    // Step 2: Check and fix foreign key references
    echo "<h3>2. Foreign Key Reference Validation & Fix</h3>";

    $foreignKeys = [
        'company_id' => ['table' => 'companies', 'name' => 'Company'],
        'department_id' => ['table' => 'departments', 'name' => 'Department'],
        'designation_id' => ['table' => 'designations', 'name' => 'Designation'],
        'office_shift_id' => ['table' => 'office_shifts', 'name' => 'Office Shift'],
        'location_id' => ['table' => 'locations', 'name' => 'Location'],
        'status_id' => ['table' => 'statuses', 'name' => 'Status'],
        'role_users_id' => ['table' => 'roles', 'name' => 'Role']
    ];

    $fixedFields = [];
    $issues = [];

    foreach ($foreignKeys as $field => $config) {
        $value = $employee[$field];

        echo "<p><strong>{$config['name']} ({$field}):</strong> ";

        if ($value === null) {
            echo "<span style='color: blue;'>NULL (OK)</span></p>";
            continue;
        }

        // Check if reference exists
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `{$config['table']}` WHERE id = ?");
            $stmt->execute([$value]);
            $exists = $stmt->fetchColumn();

            if ($exists > 0) {
                echo "<span style='color: green;'>✅ Valid ({$value})</span></p>";
            } else {
                echo "<span style='color: red;'>❌ Invalid ({$value}) - Not found in {$config['table']}</span></p>";
                $issues[] = $field;

                // Try to find a valid alternative
                $stmt = $pdo->query("SELECT id FROM `{$config['table']}` ORDER BY id LIMIT 1");
                $firstValid = $stmt->fetchColumn();

                if ($firstValid) {
                    $fixedFields[$field] = $firstValid;
                    echo "<p style='margin-left: 20px; color: orange;'>⚠️ Will set to first available: {$firstValid}</p>";
                } else {
                    $fixedFields[$field] = null;
                    echo "<p style='margin-left: 20px; color: orange;'>⚠️ Will set to NULL (no records in {$config['table']})</p>";
                }
            }
        } catch (Exception $e) {
            echo "<span style='color: red;'>❌ Error checking {$config['table']}: {$e->getMessage()}</span></p>";
            $fixedFields[$field] = null;
        }
    }

    // Step 3: Check users table relationship
    echo "<h3>3. Users Table Relationship Check</h3>";
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
    $stmt->execute([$employee_id]);
    $userExists = $stmt->fetchColumn();

    if ($userExists > 0) {
        echo "<div class='success'>✅ User {$employee_id} exists</div>";
    } else {
        echo "<div class='error'>❌ User {$employee_id} does not exist - this could be the main issue!</div>";
        echo "<div class='warning'>Employee ID must match User ID for the foreign key constraint</div>";
    }

    // Step 4: Show fix options
    echo "<h3>4. Fix Options</h3>";

    if (count($issues) > 0 || $userExists == 0) {
        echo "<div class='warning'>";
        echo "<h4>Issues Found:</h4>";
        echo "<ul>";
        if ($userExists == 0) {
            echo "<li><strong>Missing User Record:</strong> No user with ID {$employee_id}</li>";
        }
        foreach ($issues as $issue) {
            echo "<li><strong>Invalid {$issue}:</strong> Referenced record doesn't exist</li>";
        }
        echo "</ul>";
        echo "</div>";

        // Option 1: Fix foreign key references
        if (count($fixedFields) > 0) {
            echo "<h4>Option 1: Fix Foreign Key References</h4>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='action' value='fix_foreign_keys'>";
            echo "<input type='hidden' name='employee_id' value='{$employee_id}'>";

            foreach ($fixedFields as $field => $newValue) {
                echo "<input type='hidden' name='fixes[{$field}]' value='{$newValue}'>";
            }

            echo "<p>This will update the following fields:</p>";
            echo "<ul>";
            foreach ($fixedFields as $field => $newValue) {
                $configName = $foreignKeys[$field]['name'];
                echo "<li><strong>{$configName}:</strong> " . ($newValue ?: 'NULL') . "</li>";
            }
            echo "</ul>";
            echo "<button type='submit' class='btn btn-success'>Fix Foreign Key References</button>";
            echo "</form>";
        }

        // Option 2: Disable constraints temporarily
        echo "<h4>Option 2: Disable Foreign Key Checks (Temporary)</h4>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='action' value='disable_checks'>";
        echo "<p style='color: red;'><strong>Warning:</strong> This temporarily disables foreign key checks for the update.</p>";
        echo "<button type='submit' class='btn btn-danger'>Disable FK Checks & Allow Update</button>";
        echo "</form>";

        // Option 3: Create missing user
        if ($userExists == 0) {
            echo "<h4>Option 3: Create Missing User Record</h4>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='action' value='create_user'>";
            echo "<input type='hidden' name='employee_id' value='{$employee_id}'>";
            echo "<p>Create a user record with ID {$employee_id} to match the employee.</p>";
            echo "<button type='submit' class='btn btn-success'>Create Missing User</button>";
            echo "</form>";
        }

    } else {
        echo "<div class='success'>✅ No constraint issues found. The error might be temporary or resolved.</div>";
    }

    // Handle form submissions
    if ($_POST) {
        echo "<h3>5. Processing Fix...</h3>";

        switch ($_POST['action']) {
            case 'fix_foreign_keys':
                try {
                    $fixes = $_POST['fixes'];
                    $setParts = [];
                    $values = [];

                    foreach ($fixes as $field => $value) {
                        $setParts[] = "`{$field}` = ?";
                        $values[] = $value ?: null;
                    }

                    $values[] = $employee_id;
                    $sql = "UPDATE employees SET " . implode(', ', $setParts) . " WHERE id = ?";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($values);

                    echo "<div class='success'>✅ Foreign key references fixed successfully!</div>";
                } catch (Exception $e) {
                    echo "<div class='error'>❌ Error fixing references: {$e->getMessage()}</div>";
                }
                break;

            case 'disable_checks':
                try {
                    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
                    echo "<div class='success'>✅ Foreign key checks disabled. You can now update employee {$employee_id}.</div>";
                    echo "<div class='warning'>⚠️ Remember to re-enable with: SET FOREIGN_KEY_CHECKS = 1</div>";
                } catch (Exception $e) {
                    echo "<div class='error'>❌ Error disabling checks: {$e->getMessage()}</div>";
                }
                break;

            case 'create_user':
                try {
                    // Create a basic user record
                    $stmt = $pdo->prepare("INSERT INTO users (id, username, email, password, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
                    $username = strtolower($employee['first_name'] . '.' . $employee['last_name']);
                    $email = $employee['email'] ?: $username . '@company.com';
                    $password = password_hash('password123', PASSWORD_DEFAULT);

                    $stmt->execute([$employee_id, $username, $email, $password]);

                    echo "<div class='success'>✅ User record created successfully!</div>";
                    echo "<div class='info'>Username: {$username}<br>Email: {$email}<br>Default Password: password123</div>";
                } catch (Exception $e) {
                    echo "<div class='error'>❌ Error creating user: {$e->getMessage()}</div>";
                }
                break;
        }

        echo "<script>setTimeout(function(){ location.reload(); }, 3000);</script>";
    }

} catch (PDOException $e) {
    echo "<div class='error'>❌ Database error: " . $e->getMessage() . "</div>";
}
?>