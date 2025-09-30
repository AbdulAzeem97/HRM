<?php
// Quick database check for employee 61
$db_host = '127.0.0.1';
$db_name = 'u902429527_ttphrm';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Quick check for employee 61
    $stmt = $pdo->prepare("SELECT first_name, last_name, overtime_allowed, required_hours_per_day, updated_at FROM employees WHERE id = 61");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<h2>Employee 61 - Quick Check</h2>";

    if ($result) {
        echo "<p><strong>Name:</strong> {$result['first_name']} {$result['last_name']}</p>";
        echo "<p><strong>Overtime Allowed:</strong> " . ($result['overtime_allowed'] ? 'YES ✅' : 'NO ❌') . "</p>";
        echo "<p><strong>Required Hours per Day:</strong> {$result['required_hours_per_day']} hours</p>";
        $formatted_date = date('d-m-Y H:i:s', strtotime($result['updated_at']));
        echo "<p><strong>Last Updated:</strong> {$formatted_date}</p>";

        // Check if updated recently (within last hour)
        $updated_time = strtotime($result['updated_at']);
        $current_time = time();
        $minutes_ago = round(($current_time - $updated_time) / 60);

        if ($minutes_ago < 60) {
            echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; color: #155724; margin: 10px 0;'>";
            echo "🕒 <strong>Recently Updated:</strong> {$minutes_ago} minutes ago - Changes are working!";
            echo "</div>";
        } else {
            echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; color: #856404; margin: 10px 0;'>";
            echo "⏰ <strong>Last Updated:</strong> More than 1 hour ago";
            echo "</div>";
        }

    } else {
        echo "<p style='color: red;'>❌ Employee 61 not found in database!</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
}
?>

<!-- Quick refresh button -->
<button onclick="location.reload()" style="background: #007bff; color: white; padding: 10px 15px; border: none; cursor: pointer; margin: 10px 0;">🔄 Refresh</button>

<!-- Direct SQL queries you can run -->
<h3>Direct SQL Queries (for phpMyAdmin or database tool):</h3>
<div style="background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; font-family: monospace;">
<p><strong>Check Employee 61:</strong></p>
<code>SELECT id, first_name, last_name, overtime_allowed, required_hours_per_day, updated_at FROM employees WHERE id = 61;</code>

<p><strong>Check All Overtime Enabled:</strong></p>
<code>SELECT id, first_name, last_name, overtime_allowed, required_hours_per_day FROM employees WHERE overtime_allowed = 1;</code>

<p><strong>Recent Updates:</strong></p>
<code>SELECT id, first_name, last_name, overtime_allowed, required_hours_per_day, updated_at FROM employees WHERE updated_at > DATE_SUB(NOW(), INTERVAL 1 HOUR) ORDER BY updated_at DESC;</code>
</div>