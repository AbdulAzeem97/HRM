<?php
require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    // Database connection
    $pdo = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'],
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database successfully.\n\n";

    // Execute the SQL commands from update_overtime_settings.sql
    $sql_commands = [
        "ALTER TABLE employees 
         ADD COLUMN overtime_allowed BOOLEAN DEFAULT TRUE AFTER is_labor_employee,
         ADD COLUMN required_hours_per_day INT DEFAULT 9 AFTER overtime_allowed",
        
        "UPDATE employees SET overtime_allowed = TRUE WHERE overtime_allowed IS NULL",
        
        "UPDATE employees SET required_hours_per_day = 9 WHERE required_hours_per_day IS NULL"
    ];

    foreach ($sql_commands as $index => $sql) {
        try {
            $pdo->exec($sql);
            echo "✅ Command " . ($index + 1) . " executed successfully\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "⚠️  Columns already exist - skipping command " . ($index + 1) . "\n";
            } else {
                throw $e;
            }
        }
    }

    // Verify the changes
    echo "\n📊 Verifying changes - showing first 10 employees:\n";
    echo "ID | Name | Overtime Allowed | Required Hours\n";
    echo "---|------|------------------|---------------\n";
    
    $stmt = $pdo->query("SELECT id, CONCAT(first_name, ' ', last_name) as name, overtime_allowed, required_hours_per_day FROM employees LIMIT 10");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%2d | %-20s | %-16s | %d\n", 
            $row['id'], 
            $row['name'], 
            $row['overtime_allowed'] ? 'Yes' : 'No',
            $row['required_hours_per_day']
        );
    }

    echo "\n✅ Overtime setup completed successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Go to Employee Profile → Salary → Overtime Settings to configure per employee\n";
    echo "2. Test payroll generation with attendance data\n";
    echo "3. Verify overtime calculations are working correctly\n";

} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>