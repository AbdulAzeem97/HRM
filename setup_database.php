<?php
/**
 * Database Setup Script for TTPHRM Laravel Project
 * This script will execute the SQL file to recreate the database
 */

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'u902429527_ttphrm';

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MySQL server successfully!\n";

    // Read the SQL file
    $sqlFile = __DIR__ . '/complete_database_structure.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: $sqlFile");
    }

    $sql = file_get_contents($sqlFile);

    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    echo "Executing SQL statements...\n";

    foreach ($statements as $index => $statement) {
        if (empty($statement) || substr($statement, 0, 2) === '--') {
            continue;
        }

        try {
            $pdo->exec($statement);
            echo "✓ Statement " . ($index + 1) . " executed successfully\n";
        } catch (Exception $e) {
            echo "✗ Error in statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
            echo "Statement: " . substr($statement, 0, 100) . "...\n";
        }
    }

    // Verify database creation
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $result = $pdo->query("SHOW TABLES");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);

    echo "\nDatabase '$database' created successfully!\n";
    echo "Total tables created: " . count($tables) . "\n";
    echo "\nTables:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }

    // Test with a simple query
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "\nDefault users created: $userCount\n";

    $employeeCount = $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
    echo "Default employees created: $employeeCount\n";

    echo "\n✅ Database setup completed successfully!\n";
    echo "\nYou can now run your Laravel application.\n";
    echo "Default login credentials:\n";
    echo "Username: superadmin\n";
    echo "Password: password\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>