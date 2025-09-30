<?php
// Check if NIC Expiry field exists in employees table
$db_host = '127.0.0.1';
$db_name = 'u902429527_ttphrm';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🔍 NIC Expiry Field Analysis</h2>";
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
    </style>";

    // Check employees table structure
    echo "<h3>1. Employees Table Structure</h3>";
    $stmt = $pdo->query("DESCRIBE employees");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $has_nic = false;
    $has_nic_expiry = false;
    $nic_expiry_type = '';

    echo "<table>";
    echo "<tr><th>Column Name</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

    foreach ($columns as $col) {
        $highlight = '';
        if (stripos($col['Field'], 'nic') !== false) {
            if ($col['Field'] == 'nic') {
                $has_nic = true;
                $highlight = ' style="background-color: #e7f3ff;"';
            }
            if ($col['Field'] == 'nic_expiry') {
                $has_nic_expiry = true;
                $nic_expiry_type = $col['Type'];
                $highlight = ' style="background-color: #fff3cd;"';
            }
        }

        echo "<tr{$highlight}>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "<td>{$col['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Summary
    echo "<h3>2. NIC Fields Summary</h3>";
    echo "<p><strong>NIC field exists:</strong> " . ($has_nic ? '<span class="success">✅ YES</span>' : '<span class="error">❌ NO</span>') . "</p>";
    echo "<p><strong>NIC Expiry field exists:</strong> " . ($has_nic_expiry ? '<span class="success">✅ YES</span>' : '<span class="error">❌ NO</span>') . "</p>";

    if ($has_nic_expiry) {
        echo "<p><strong>NIC Expiry field type:</strong> <code>{$nic_expiry_type}</code></p>";

        // Check if it's already a date type
        if (stripos($nic_expiry_type, 'date') !== false) {
            echo "<p class='success'>✅ Field is already a DATE type - no migration needed!</p>";
        } else {
            echo "<p class='warning'>⚠️ Field is currently {$nic_expiry_type} - needs conversion to DATE</p>";
        }

        // Sample data
        echo "<h3>3. Sample NIC Expiry Data</h3>";
        $stmt = $pdo->query("SELECT id, first_name, last_name, nic, nic_expiry FROM employees WHERE nic_expiry IS NOT NULL LIMIT 5");
        $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($samples) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>NIC</th><th>NIC Expiry</th><th>Format Analysis</th></tr>";

            foreach ($samples as $sample) {
                echo "<tr>";
                echo "<td>{$sample['id']}</td>";
                echo "<td>{$sample['first_name']} {$sample['last_name']}</td>";
                echo "<td>{$sample['nic']}</td>";
                echo "<td>{$sample['nic_expiry']}</td>";

                // Analyze format
                $expiry = $sample['nic_expiry'];
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiry)) {
                    echo "<td class='success'>✅ Already YYYY-MM-DD format</td>";
                } elseif (preg_match('/^\d{2}-\d{2}-\d{4}$/', $expiry)) {
                    echo "<td class='warning'>⚠️ DD-MM-YYYY format (needs conversion)</td>";
                } elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $expiry)) {
                    echo "<td class='warning'>⚠️ DD/MM/YYYY format (needs conversion)</td>";
                } else {
                    echo "<td class='error'>❌ Unknown format: {$expiry}</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No employees have NIC expiry data yet.</p>";
        }
    }

    // Recommendations
    echo "<h3>4. Recommendations</h3>";
    if (!$has_nic_expiry) {
        echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb;'>";
        echo "<strong>Action needed:</strong> Add nic_expiry field to employees table";
        echo "</div>";
    } elseif (stripos($nic_expiry_type, 'date') === false) {
        echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7;'>";
        echo "<strong>Action needed:</strong> Convert nic_expiry field from {$nic_expiry_type} to DATE type";
        echo "</div>";
    } else {
        echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb;'>";
        echo "<strong>✅ Ready:</strong> NIC Expiry field is already a DATE type";
        echo "</div>";
    }

} catch (PDOException $e) {
    echo "<div style='color: red;'>❌ Database error: " . $e->getMessage() . "</div>";
    echo "<p>Please check your database credentials.</p>";
}
?>