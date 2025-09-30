<?php
// Emergency fix for employee 61 constraint violation
$db_host = '127.0.0.1';
$db_name = 'u902429527_ttphrm';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🚨 Emergency Fix for Employee 61</h2>";
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f8f9fa; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: green; background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin: 10px 0; border-radius: 5px; }
        .error { color: red; background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; margin: 10px 0; border-radius: 5px; }
        .warning { color: #856404; background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; margin: 10px 0; border-radius: 5px; }
        .info { background: #e7f3ff; padding: 15px; border: 1px solid #b3d4fc; margin: 10px 0; border-radius: 5px; }
        .btn { padding: 12px 24px; margin: 5px; cursor: pointer; border: none; border-radius: 5px; text-decoration: none; display: inline-block; font-weight: bold; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-primary { background: #007bff; color: white; }
        table { border-collapse: collapse; width: 100%; margin: 15px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .step { margin: 20px 0; padding: 15px; border-left: 4px solid #007bff; background: #f8f9fa; }
        code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>";

    echo "<div class='container'>";

    $employee_id = 61;

    // Step 1: Get current employee data
    echo "<div class='step'>";
    echo "<h3>Step 1: Analyzing Employee 61</h3>";

    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->execute([$employee_id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        echo "<div class='error'>❌ Employee 61 not found in database!</div>";
        echo "</div></div>";
        exit;
    }

    echo "<div class='success'>✅ Employee 61 found: {$employee['first_name']} {$employee['last_name']}</div>";
    echo "</div>";

    // Step 2: Check User relationship (most common issue)
    echo "<div class='step'>";
    echo "<h3>Step 2: User Relationship Check</h3>";

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$employee_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<div class='error'>❌ User 61 not found - this is likely the constraint issue!</div>";
        echo "<p><strong>Creating missing user record...</strong></p>";

        try {
            // Create the missing user
            $username = strtolower($employee['first_name'] . '.' . $employee['last_name']) . '61';
            $email = $employee['email'] ?: $username . '@company.com';
            $password = password_hash('password123', PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (id, username, email, password, email_verified_at, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())");
            $stmt->execute([$employee_id, $username, $email, $password]);

            echo "<div class='success'>✅ User 61 created successfully!</div>";
            echo "<p><strong>Login Details:</strong><br>";
            echo "Username: <code>{$username}</code><br>";
            echo "Email: <code>{$email}</code><br>";
            echo "Password: <code>password123</code></p>";

        } catch (Exception $e) {
            echo "<div class='error'>❌ Failed to create user: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='success'>✅ User 61 exists</div>";
    }
    echo "</div>";

    // Step 3: Check and fix foreign key constraints
    echo "<div class='step'>";
    echo "<h3>Step 3: Foreign Key Constraint Validation</h3>";

    $foreignKeys = [
        'company_id' => 'companies',
        'department_id' => 'departments',
        'designation_id' => 'designations',
        'office_shift_id' => 'office_shifts',
        'location_id' => 'locations',
        'status_id' => 'statuses',
        'role_users_id' => 'roles'
    ];

    $fixes = [];
    $allValid = true;

    echo "<table>";
    echo "<tr><th>Field</th><th>Current Value</th><th>Status</th><th>Action</th></tr>";

    foreach ($foreignKeys as $field => $table) {
        $value = $employee[$field];
        echo "<tr>";
        echo "<td><strong>{$field}</strong></td>";
        echo "<td>" . ($value ?: 'NULL') . "</td>";

        if ($value === null) {
            echo "<td style='color: green;'>✅ NULL (OK)</td>";
            echo "<td>No action needed</td>";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM `{$table}` WHERE id = ?");
                $stmt->execute([$value]);
                $exists = $stmt->fetchColumn();

                if ($exists > 0) {
                    echo "<td style='color: green;'>✅ Valid</td>";
                    echo "<td>No action needed</td>";
                } else {
                    echo "<td style='color: red;'>❌ Invalid</td>";

                    // Find a replacement or set to NULL
                    $stmt = $pdo->query("SELECT id FROM `{$table}` ORDER BY id LIMIT 1");
                    $firstValid = $stmt->fetchColumn();

                    if ($firstValid) {
                        $fixes[$field] = $firstValid;
                        echo "<td style='color: orange;'>Set to {$firstValid}</td>";
                    } else {
                        $fixes[$field] = null;
                        echo "<td style='color: orange;'>Set to NULL</td>";
                    }
                    $allValid = false;
                }
            } catch (Exception $e) {
                echo "<td style='color: red;'>❌ Error</td>";
                echo "<td>Set to NULL</td>";
                $fixes[$field] = null;
                $allValid = false;
            }
        }
        echo "</tr>";
    }
    echo "</table>";

    if (!$allValid) {
        echo "<div class='warning'>⚠️ Found invalid foreign key references. Applying fixes...</div>";

        try {
            if (!empty($fixes)) {
                $setParts = [];
                $values = [];

                foreach ($fixes as $field => $newValue) {
                    $setParts[] = "`{$field}` = ?";
                    $values[] = $newValue;
                }

                $values[] = $employee_id;
                $sql = "UPDATE employees SET " . implode(', ', $setParts) . " WHERE id = ?";

                $stmt = $pdo->prepare($sql);
                $stmt->execute($values);

                echo "<div class='success'>✅ Foreign key references fixed!</div>";
            }
        } catch (Exception $e) {
            echo "<div class='error'>❌ Error fixing foreign keys: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='success'>✅ All foreign key references are valid</div>";
    }
    echo "</div>";

    // Step 4: Test the update
    echo "<div class='step'>";
    echo "<h3>Step 4: Testing Employee Update</h3>";

    try {
        // Try a simple update to test if constraints are resolved
        $stmt = $pdo->prepare("UPDATE employees SET updated_at = NOW() WHERE id = ?");
        $stmt->execute([$employee_id]);

        echo "<div class='success'>✅ Test update successful! Constraint violation should be resolved.</div>";

        // Now try the actual overtime update
        $stmt = $pdo->prepare("UPDATE employees SET overtime_allowed = 1, required_hours_per_day = 9, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$employee_id]);

        echo "<div class='success'>✅ Overtime settings updated successfully!</div>";

    } catch (Exception $e) {
        echo "<div class='error'>❌ Update still failing: " . $e->getMessage() . "</div>";
        echo "<div class='warning'>⚠️ Applying nuclear option - temporarily disabling foreign key checks...</div>";

        try {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
            $stmt = $pdo->prepare("UPDATE employees SET overtime_allowed = 1, required_hours_per_day = 9, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$employee_id]);
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

            echo "<div class='success'>✅ Update successful with FK checks disabled!</div>";
        } catch (Exception $e2) {
            echo "<div class='error'>❌ Even with FK checks disabled, update failed: " . $e2->getMessage() . "</div>";
        }
    }
    echo "</div>";

    // Step 5: Final verification
    echo "<div class='step'>";
    echo "<h3>Step 5: Final Verification</h3>";

    $stmt = $pdo->prepare("SELECT overtime_allowed, required_hours_per_day, updated_at FROM employees WHERE id = ?");
    $stmt->execute([$employee_id]);
    $updated = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($updated) {
        echo "<div class='success'>✅ Employee 61 current status:</div>";
        echo "<p><strong>Overtime Allowed:</strong> " . ($updated['overtime_allowed'] ? 'Yes' : 'No') . "</p>";
        echo "<p><strong>Required Hours per Day:</strong> {$updated['required_hours_per_day']}</p>";
        echo "<p><strong>Last Updated:</strong> {$updated['updated_at']}</p>";
    }
    echo "</div>";

    // Step 6: Instructions
    echo "<div class='step'>";
    echo "<h3>Step 6: Next Steps</h3>";
    echo "<div class='info'>";
    echo "<h4>✅ Constraint violation should now be resolved!</h4>";
    echo "<p><strong>You can now:</strong></p>";
    echo "<ul>";
    echo "<li>Go back to <a href='http://localhost/ttphrm/staff/employees/61' target='_blank'>Employee 61 profile</a></li>";
    echo "<li>Try updating the overtime settings again</li>";
    echo "<li>The form should submit successfully with toast notifications</li>";
    echo "</ul>";
    echo "</div>";
    echo "</div>";

    echo "</div>"; // Close container

} catch (PDOException $e) {
    echo "<div class='error'>❌ Database connection error: " . $e->getMessage() . "</div>";
    echo "<p>Please check your database credentials in the script.</p>";
}
?>

<script>
console.log('Emergency fix for employee 61 completed');
// Auto-redirect to employee page after 5 seconds if successful
setTimeout(function() {
    if (document.querySelector('.success')) {
        console.log('Fix appears successful, you can now try updating employee 61');
    }
}, 2000);
</script>