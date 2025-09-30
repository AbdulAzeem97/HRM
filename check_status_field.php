<?php
// Check Status field and options
$db_host = '127.0.0.1';
$db_name = 'u902429527_ttphrm';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>📋 Status Field Analysis</h2>";
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { background: #e7f3ff; padding: 15px; margin: 10px 0; border-left: 4px solid #2196F3; }
    </style>";

    // Check status table structure
    echo "<h3>1. Status Table Structure</h3>";
    try {
        $stmt = $pdo->query("DESCRIBE statuses");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<table>";
        echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Status table not found: " . $e->getMessage() . "</p>";
    }

    // Check current status options
    echo "<h3>2. Current Status Options</h3>";
    try {
        $stmt = $pdo->query("SELECT * FROM statuses ORDER BY id");
        $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($statuses) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Status Title</th><th>Employee ID</th><th>Created At</th></tr>";

            $has_active = false;
            $has_inactive = false;

            foreach ($statuses as $status) {
                echo "<tr>";
                echo "<td>{$status['id']}</td>";
                echo "<td>{$status['status_title']}</td>";
                echo "<td>" . ($status['employee_id'] ?? 'NULL') . "</td>";
                echo "<td>" . ($status['created_at'] ?? 'NULL') . "</td>";
                echo "</tr>";

                // Check for active/inactive options
                $title = strtolower($status['status_title']);
                if (strpos($title, 'active') !== false && strpos($title, 'inactive') === false) {
                    $has_active = true;
                }
                if (strpos($title, 'inactive') !== false || strpos($title, 'deactive') !== false) {
                    $has_inactive = true;
                }
            }
            echo "</table>";

            echo "<h3>3. Active/Inactive Options Analysis</h3>";
            echo "<p><strong>Has Active option:</strong> " . ($has_active ? '<span class="success">✅ YES</span>' : '<span class="error">❌ NO</span>') . "</p>";
            echo "<p><strong>Has Inactive option:</strong> " . ($has_inactive ? '<span class="success">✅ YES</span>' : '<span class="error">❌ NO</span>') . "</p>";

        } else {
            echo "<p class='warning'>⚠️ No status options found in the table.</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error reading status options: " . $e->getMessage() . "</p>";
    }

    // Check employee status usage
    echo "<h3>4. Employee Status Usage</h3>";
    try {
        $stmt = $pdo->query("SELECT
            e.status_id,
            s.status_title,
            COUNT(*) as employee_count
            FROM employees e
            LEFT JOIN statuses s ON e.status_id = s.id
            GROUP BY e.status_id, s.status_title
            ORDER BY employee_count DESC");
        $usage = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($usage) > 0) {
            echo "<table>";
            echo "<tr><th>Status ID</th><th>Status Title</th><th>Employee Count</th></tr>";
            foreach ($usage as $row) {
                echo "<tr>";
                echo "<td>" . ($row['status_id'] ?? 'NULL') . "</td>";
                echo "<td>" . ($row['status_title'] ?? 'No Status') . "</td>";
                echo "<td>{$row['employee_count']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No employees found.</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error checking employee status usage: " . $e->getMessage() . "</p>";
    }

    // Recommendations
    echo "<h3>5. Recommendations</h3>";
    echo "<div class='info'>";
    echo "<h4>Status Field Requirements:</h4>";
    echo "<ul>";
    echo "<li><strong>Make Optional:</strong> Remove required attribute and asterisk (*) from Status field</li>";
    echo "<li><strong>Active/Inactive Options:</strong> Ensure status table has both Active and Inactive options</li>";
    echo "<li><strong>Default Handling:</strong> Handle NULL status_id gracefully in application</li>";
    echo "</ul>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div style='color: red;'>❌ Database error: " . $e->getMessage() . "</div>";
    echo "<p>Please check your database credentials.</p>";
}
?>

<script>
// Auto-refresh every 30 seconds to see changes
setTimeout(function() {
    console.log('Status field analysis loaded');
}, 1000);
</script>