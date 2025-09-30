<?php
// Database verification script for overtime settings
$db_host = '127.0.0.1';
$db_name = 'u902429527_ttphrm';  // UPDATE THIS if different
$db_user = 'root';                // UPDATE THIS if different
$db_pass = '';                    // UPDATE THIS if different

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🔍 Database Verification - Overtime Settings</h2>";
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { background: #e7f3ff; padding: 15px; margin: 10px 0; border-left: 4px solid #2196F3; }
    </style>";

    // Check if overtime columns exist
    echo "<h3>1. Database Schema Check</h3>";
    $stmt = $pdo->query("DESCRIBE employees");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $has_overtime = false;
    $has_hours = false;

    echo "<table>";
    echo "<tr><th>Column Name</th><th>Type</th><th>Status</th></tr>";

    foreach ($columns as $col) {
        if ($col['Field'] == 'overtime_allowed') {
            $has_overtime = true;
            echo "<tr><td>overtime_allowed</td><td>{$col['Type']}</td><td class='success'>✅ Found</td></tr>";
        }
        if ($col['Field'] == 'required_hours_per_day') {
            $has_hours = true;
            echo "<tr><td>required_hours_per_day</td><td>{$col['Type']}</td><td class='success'>✅ Found</td></tr>";
        }
    }
    echo "</table>";

    if (!$has_overtime || !$has_hours) {
        echo "<div class='error'>❌ Missing required columns! Please run the migration.</div>";
        exit;
    }

    // Check specific employee (ID 61)
    echo "<h3>2. Employee ID 61 - Current Overtime Settings</h3>";
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, overtime_allowed, required_hours_per_day, updated_at FROM employees WHERE id = ?");
    $stmt->execute([61]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($employee) {
        echo "<table>";
        echo "<tr><th>Field</th><th>Value</th><th>Status</th></tr>";
        echo "<tr><td>Employee ID</td><td>{$employee['id']}</td><td>✅</td></tr>";
        echo "<tr><td>Name</td><td>{$employee['first_name']} {$employee['last_name']}</td><td>✅</td></tr>";
        echo "<tr><td>Overtime Allowed</td><td>" . ($employee['overtime_allowed'] ? 'Yes (1)' : 'No (0)') . "</td><td class='" . ($employee['overtime_allowed'] ? 'success' : 'error') . "'>" . ($employee['overtime_allowed'] ? '✅ Enabled' : '❌ Disabled') . "</td></tr>";
        echo "<tr><td>Required Hours/Day</td><td>{$employee['required_hours_per_day']} hours</td><td class='success'>✅</td></tr>";
        $formatted_date = date('d-m-Y H:i:s', strtotime($employee['updated_at']));
        echo "<tr><td>Last Updated</td><td>{$formatted_date}</td><td class='info'>ℹ️ Recent update?</td></tr>";
        echo "</table>";
    } else {
        echo "<div class='error'>❌ Employee ID 61 not found!</div>";
    }

    // Check all employees with overtime enabled
    echo "<h3>3. All Employees with Overtime Enabled</h3>";
    $stmt = $pdo->query("SELECT id, first_name, last_name, overtime_allowed, required_hours_per_day, updated_at FROM employees WHERE overtime_allowed = 1 ORDER BY updated_at DESC LIMIT 10");
    $overtime_employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($overtime_employees) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Overtime</th><th>Hours/Day</th><th>Last Updated</th></tr>";

        foreach ($overtime_employees as $emp) {
            echo "<tr>";
            echo "<td>{$emp['id']}</td>";
            echo "<td>{$emp['first_name']} {$emp['last_name']}</td>";
            echo "<td class='success'>✅ Enabled</td>";
            echo "<td>{$emp['required_hours_per_day']} hours</td>";
            echo "<td>" . date('d-m-Y H:i:s', strtotime($emp['updated_at'])) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='info'>ℹ️ No employees currently have overtime enabled.</div>";
    }

    // Statistics
    echo "<h3>4. Overtime Statistics</h3>";
    $stmt = $pdo->query("SELECT
        COUNT(*) as total_employees,
        SUM(CASE WHEN overtime_allowed = 1 THEN 1 ELSE 0 END) as overtime_enabled,
        SUM(CASE WHEN overtime_allowed = 0 THEN 1 ELSE 0 END) as overtime_disabled,
        AVG(required_hours_per_day) as avg_hours
        FROM employees");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<table>";
    echo "<tr><th>Metric</th><th>Value</th></tr>";
    echo "<tr><td>Total Employees</td><td>{$stats['total_employees']}</td></tr>";
    echo "<tr><td>Overtime Enabled</td><td class='success'>{$stats['overtime_enabled']}</td></tr>";
    echo "<tr><td>Overtime Disabled</td><td>{$stats['overtime_disabled']}</td></tr>";
    echo "<tr><td>Average Required Hours</td><td>" . round($stats['avg_hours'], 1) . " hours</td></tr>";
    echo "</table>";

    // Test the controller method response
    echo "<h3>5. Controller Method Test</h3>";
    echo "<div class='info'>";
    echo "<p><strong>Recent Changes Check:</strong></p>";
    echo "<p>• If you just updated employee 61's overtime settings, the 'Last Updated' timestamp should be very recent.</p>";
    echo "<p>• The 'Overtime Allowed' should match what you selected (checked = Yes, unchecked = No).</p>";
    echo "<p>• The 'Required Hours/Day' should match what you entered in the form.</p>";
    echo "</div>";

    // Quick test button
    echo "<h3>6. Quick Test</h3>";
    echo "<button onclick='testForm()' style='padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;'>🔄 Refresh Data</button>";
    echo "<script>
    function testForm() {
        location.reload();
    }
    </script>";

} catch (PDOException $e) {
    echo "<div class='error'>❌ Database error: " . $e->getMessage() . "</div>";
    echo "<p>Please check your database credentials at the top of this file.</p>";
}
?>

<style>
.highlight { background-color: yellow; }
</style>

<script>
// Highlight recent updates (within last 5 minutes)
document.addEventListener('DOMContentLoaded', function() {
    var rows = document.querySelectorAll('td');
    var now = new Date();

    rows.forEach(function(cell) {
        var text = cell.textContent;
        if (text.match(/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/)) {
            var cellDate = new Date(text);
            var diffMinutes = (now - cellDate) / (1000 * 60);

            if (diffMinutes < 5) {
                cell.classList.add('highlight');
                cell.title = 'Updated ' + Math.round(diffMinutes) + ' minutes ago';
            }
        }
    });
});
</script>