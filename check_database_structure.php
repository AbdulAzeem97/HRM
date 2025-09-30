<?php
// Check actual database structure for attendance and office_shifts tables

$db_host = '127.0.0.1';
$db_name = 'u902429527_ttphrm';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🔍 DATABASE STRUCTURE CHECK\n";
    echo "========================\n\n";

    // Check attendance table structure
    echo "📋 ATTENDANCE TABLE STRUCTURE:\n";
    try {
        $stmt = $pdo->query("DESCRIBE attendances");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $col) {
            echo "   {$col['Field']} | {$col['Type']} | {$col['Null']} | {$col['Key']}\n";
        }
        
        // Get sample data
        echo "\n📄 Sample attendance record:\n";
        $stmt = $pdo->query("SELECT * FROM attendances LIMIT 1");
        $sample = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($sample) {
            foreach ($sample as $key => $value) {
                echo "   {$key}: {$value}\n";
            }
        } else {
            echo "   No records found\n";
        }
        
    } catch (PDOException $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }

    echo "\n📋 OFFICE_SHIFTS TABLE STRUCTURE:\n";
    try {
        $stmt = $pdo->query("DESCRIBE office_shifts");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $col) {
            echo "   {$col['Field']} | {$col['Type']} | {$col['Null']} | {$col['Key']}\n";
        }
        
        // Get sample data
        echo "\n📄 Sample office shift record:\n";
        $stmt = $pdo->query("SELECT * FROM office_shifts LIMIT 1");
        $sample = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($sample) {
            foreach ($sample as $key => $value) {
                echo "   {$key}: {$value}\n";
            }
        } else {
            echo "   No records found\n";
        }
        
    } catch (PDOException $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }

    // Check payslips table
    echo "\n📋 PAYSLIPS TABLE STRUCTURE:\n";
    try {
        $stmt = $pdo->query("DESCRIBE payslips");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $col) {
            echo "   {$col['Field']} | {$col['Type']} | {$col['Null']} | {$col['Key']}\n";
        }
        
    } catch (PDOException $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }

} catch (PDOException $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "\n";
}
?>