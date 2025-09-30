<?php
// Simple overtime setup without Composer dependencies
// You'll need to update these with your actual database credentials

$db_host = 'localhost';
$db_name = 'your_database_name';  // UPDATE THIS
$db_user = 'root';                // UPDATE THIS
$db_pass = '';                    // UPDATE THIS

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database successfully.\n\n";

    // SQL commands to execute
    $commands = [
        "ALTER TABLE employees ADD COLUMN overtime_allowed BOOLEAN DEFAULT TRUE AFTER is_labor_employee",
        "ALTER TABLE employees ADD COLUMN required_hours_per_day INT DEFAULT 9 AFTER overtime_allowed",
        "UPDATE employees SET overtime_allowed = TRUE WHERE overtime_allowed IS NULL",
        "UPDATE employees SET required_hours_per_day = 9 WHERE required_hours_per_day IS NULL"
    ];

    foreach ($commands as $i => $sql) {
        try {
            $pdo->exec($sql);
            echo "✅ Command " . ($i + 1) . " executed\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                echo "⚠️  Column already exists - skipping\n";
            } else {
                echo "❌ Error: " . $e->getMessage() . "\n";
            }
        }
    }

    // Verify
    echo "\n📊 First 5 employees:\n";
    $stmt = $pdo->query("SELECT id, first_name, last_name, overtime_allowed, required_hours_per_day FROM employees LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']} | {$row['first_name']} {$row['last_name']} | Overtime: " . 
             ($row['overtime_allowed'] ? 'Yes' : 'No') . " | Hours: {$row['required_hours_per_day']}\n";
    }

} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    echo "\nPlease update the database credentials at the top of this file.\n";
}
?>