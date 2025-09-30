<?php
// Simple test script without Laravel dependencies
$host = 'localhost';
$dbname = 'u902429527_ttphrm';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Testing Employee Date Format:\n";
    echo "============================\n";

    // Test employee with ID 61
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, date_of_birth, joining_date, exit_date, nic_expiry FROM employees WHERE id = 61");
    $stmt->execute();
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($employee) {
        echo "Employee ID: " . $employee['id'] . "\n";
        echo "Name: " . $employee['first_name'] . " " . $employee['last_name'] . "\n";
        echo "Date of Birth: " . ($employee['date_of_birth'] ?: 'NULL') . "\n";
        echo "Joining Date: " . ($employee['joining_date'] ?: 'NULL') . "\n";
        echo "Exit Date: " . ($employee['exit_date'] ?: 'NULL') . "\n";
        echo "NIC Expiry: " . ($employee['nic_expiry'] ?: 'NULL') . "\n";
    } else {
        echo "Employee with ID 61 not found.\n";
    }

    echo "\nTesting Salary Commission Date Format:\n";
    echo "=====================================\n";

    $stmt = $pdo->prepare("SELECT id, employee_id, commission_title, first_date FROM salary_commissions WHERE employee_id = 61 LIMIT 1");
    $stmt->execute();
    $commission = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($commission) {
        echo "Commission ID: " . $commission['id'] . "\n";
        echo "Employee ID: " . $commission['employee_id'] . "\n";
        echo "Commission Title: " . $commission['commission_title'] . "\n";
        echo "First Date: " . ($commission['first_date'] ?: 'NULL') . "\n";
    } else {
        echo "No salary commission found for employee 61.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>