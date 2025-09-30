<?php
/**
 * Direct Employee Date Update Script
 * This script allows you to directly update date fields for employee 61
 */

echo "Employee Date Update Script\n";
echo "=========================\n\n";

// Check if we can access Laravel
try {
    require 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    echo "✓ Laravel bootstrap successful\n";
} catch (Exception $e) {
    echo "✗ Laravel bootstrap failed: " . $e->getMessage() . "\n";
    echo "This script requires Laravel to be properly configured.\n";
    exit(1);
}

// Check if employee 61 exists
echo "\nChecking Employee 61:\n";
echo "--------------------\n";
try {
    $emp = DB::table('employees')->where('id', 61)->first();
    if ($emp) {
        echo "✓ Employee 61 found: " . $emp->first_name . " " . $emp->last_name . "\n";
        echo "Current values:\n";
        echo "  date_of_birth: " . ($emp->date_of_birth ?? 'NULL') . "\n";
        echo "  joining_date: " . ($emp->joining_date ?? 'NULL') . "\n";
        echo "  nic_expiry: " . ($emp->nic_expiry ?? 'NULL') . "\n";
    } else {
        echo "✗ Employee 61 not found\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ Error checking employee 61: " . $e->getMessage() . "\n";
    exit(1);
}

// Get input from user
echo "\nEnter new date values (press Enter to keep current value):\n";
echo "=======================================================\n";

// Date of Birth
echo "Date of Birth (format: dd-mm-yyyy, e.g., 28-02-1990): ";
$dateOfBirth = trim(fgets(STDIN));
if (empty($dateOfBirth)) {
    $dateOfBirth = $emp->date_of_birth;
}

// Joining Date
echo "Joining Date (format: dd-mm-yyyy, e.g., 01-01-2020): ";
$joiningDate = trim(fgets(STDIN));
if (empty($joiningDate)) {
    $joiningDate = $emp->joining_date;
}

// NIC Expiry
echo "NIC Expiry (format: dd-mm-yyyy, e.g., 31-12-2030): ";
$nicExpiry = trim(fgets(STDIN));
if (empty($nicExpiry)) {
    $nicExpiry = $emp->nic_expiry;
}

// Show what will be updated
echo "\nValues to be updated:\n";
echo "====================\n";
echo "date_of_birth: " . ($dateOfBirth ?: 'NULL') . "\n";
echo "joining_date: " . ($joiningDate ?: 'NULL') . "\n";
echo "nic_expiry: " . ($nicExpiry ?: 'NULL') . "\n";

// Confirm update
echo "\nDo you want to proceed with this update? (y/n): ";
$confirm = trim(fgets(STDIN));
if (strtolower($confirm) !== 'y') {
    echo "Update cancelled.\n";
    exit(0);
}

// Perform the update
echo "\nUpdating employee 61...\n";
echo "======================\n";

try {
    $result = DB::table('employees')
        ->where('id', 61)
        ->update([
            'date_of_birth' => $dateOfBirth ?: null,
            'joining_date' => $joiningDate ?: null,
            'nic_expiry' => $nicExpiry ?: null
        ]);
    
    if ($result) {
        echo "✓ Update successful! $result row(s) updated.\n";
    } else {
        echo "⚠ No rows were updated (values might be the same).\n";
    }
    
    // Verify the update
    echo "\nVerifying update:\n";
    echo "================\n";
    $updatedEmp = DB::table('employees')->where('id', 61)->first();
    echo "date_of_birth: " . ($updatedEmp->date_of_birth ?? 'NULL') . "\n";
    echo "joining_date: " . ($updatedEmp->joining_date ?? 'NULL') . "\n";
    echo "nic_expiry: " . ($updatedEmp->nic_expiry ?? 'NULL') . "\n";
    
    // Check if any values are 0000-00-00
    $hasInvalidDates = false;
    if ($updatedEmp->date_of_birth === '0000-00-00' || $updatedEmp->date_of_birth === '00-00-0000') {
        echo "⚠ WARNING: date_of_birth is still showing as 0000-00-00!\n";
        $hasInvalidDates = true;
    }
    if ($updatedEmp->joining_date === '0000-00-00' || $updatedEmp->joining_date === '00-00-0000') {
        echo "⚠ WARNING: joining_date is still showing as 0000-00-00!\n";
        $hasInvalidDates = true;
    }
    if ($updatedEmp->nic_expiry === '0000-00-00' || $updatedEmp->nic_expiry === '00-00-0000') {
        echo "⚠ WARNING: nic_expiry is still showing as 0000-00-00!\n";
        $hasInvalidDates = true;
    }
    
    if (!$hasInvalidDates) {
        echo "\n✓ SUCCESS: No 0000-00-00 values found!\n";
    } else {
        echo "\n✗ ISSUE: Some values are still showing as 0000-00-00\n";
        echo "This indicates a database-level issue (constraints, triggers, or default values).\n";
    }
    
} catch (Exception $e) {
    echo "✗ Update failed: " . $e->getMessage() . "\n";
}

echo "\nScript completed.\n";

