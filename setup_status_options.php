<?php
// Setup Status Options - Ensure Active/Inactive options exist
$db_host = '127.0.0.1';
$db_name = 'u902429527_ttphrm';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🔧 Setting Up Status Options</h2>";
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .result { padding: 10px; margin: 10px 0; border-left: 4px solid #ccc; }
    </style>";

    // Define standard status options
    $statusOptions = [
        'Active',
        'Inactive',
        'On Leave',
        'Terminated',
        'Suspended'
    ];

    echo "<h3>1. Checking Current Status Options</h3>";

    // Check what currently exists
    $stmt = $pdo->query("SELECT * FROM statuses ORDER BY id");
    $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<p><strong>Current statuses in database:</strong></p>";
    if (count($existing) > 0) {
        echo "<ul>";
        foreach ($existing as $status) {
            echo "<li>ID: {$status['id']} - {$status['status_title']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='info'>No status options found.</p>";
    }

    echo "<h3>2. Adding Missing Status Options</h3>";

    foreach ($statusOptions as $statusTitle) {
        // Check if status already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM statuses WHERE status_title = ?");
        $stmt->execute([$statusTitle]);
        $exists = $stmt->fetchColumn();

        if ($exists == 0) {
            // Insert new status
            $stmt = $pdo->prepare("INSERT INTO statuses (status_title, employee_id, created_at, updated_at) VALUES (?, NULL, NOW(), NOW())");
            $stmt->execute([$statusTitle]);

            echo "<div class='result success'>✅ Added: {$statusTitle}</div>";
        } else {
            echo "<div class='result info'>ℹ️ Already exists: {$statusTitle}</div>";
        }
    }

    echo "<h3>3. Final Status Options</h3>";

    // Show final list
    $stmt = $pdo->query("SELECT * FROM statuses ORDER BY id");
    $final = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f2f2f2;'><th style='border: 1px solid #ddd; padding: 8px;'>ID</th><th style='border: 1px solid #ddd; padding: 8px;'>Status Title</th><th style='border: 1px solid #ddd; padding: 8px;'>Created</th></tr>";

    $has_active = false;
    $has_inactive = false;

    foreach ($final as $status) {
        echo "<tr>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$status['id']}</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$status['status_title']}</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$status['created_at']}</td>";
        echo "</tr>";

        if (strtolower($status['status_title']) === 'active') $has_active = true;
        if (strtolower($status['status_title']) === 'inactive') $has_inactive = true;
    }
    echo "</table>";

    echo "<h3>4. Verification</h3>";
    echo "<p><strong>Has 'Active' option:</strong> " . ($has_active ? '<span class="success">✅ YES</span>' : '<span class="error">❌ NO</span>') . "</p>";
    echo "<p><strong>Has 'Inactive' option:</strong> " . ($has_inactive ? '<span class="success">✅ YES</span>' : '<span class="error">❌ NO</span>') . "</p>";

    if ($has_active && $has_inactive) {
        echo "<div class='result success'>";
        echo "<h4>🎉 Success!</h4>";
        echo "<p>Status field now has both Active and Inactive options available.</p>";
        echo "<p>The Status field is also now optional (no required validation).</p>";
        echo "</div>";
    }

    echo "<h3>5. Usage Instructions</h3>";
    echo "<div style='background: #e7f3ff; padding: 15px; border: 1px solid #b3d4fc;'>";
    echo "<p><strong>For Users:</strong></p>";
    echo "<ul>";
    echo "<li>Status field is now optional - you can leave it blank</li>";
    echo "<li>Select 'Active' for currently working employees</li>";
    echo "<li>Select 'Inactive' for employees who are not currently working</li>";
    echo "<li>Other options are available for specific situations</li>";
    echo "</ul>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div class='error'>❌ Database error: " . $e->getMessage() . "</div>";
    echo "<p>Please check your database credentials.</p>";
}
?>

<script>
console.log('Status options setup completed');
</script>