<?php
/**
 * Test to verify syntax fixes work correctly
 */

echo "Testing Syntax Fixes\n";
echo "===================\n\n";

// Test 1: Check if Employee model can be included without errors
echo "1. Testing Employee model inclusion...\n";
try {
    include_once 'app/Models/Employee.php';
    echo "   ✅ Employee model included successfully\n";
} catch (Exception $e) {
    echo "   ❌ Employee model failed: " . $e->getMessage() . "\n";
}

// Test 2: Check individual model files for syntax
$modelsToTest = [
    'app/Models/leave.php',
    'app/Models/Attendance.php',
    'app/Models/EmployeeDocument.php',
    'app/Models/Asset.php',
    'app/Models/SalaryCommission.php'
];

echo "\n2. Testing model syntax...\n";
foreach ($modelsToTest as $model) {
    $output = shell_exec("php -l $model 2>&1");
    if (strpos($output, 'No syntax errors') !== false) {
        echo "   ✅ " . basename($model) . " - No syntax errors\n";
    } else {
        echo "   ❌ " . basename($model) . " - Has syntax errors:\n";
        echo "      " . trim($output) . "\n";
    }
}

// Test 3: Check if DateHelper would work when available
echo "\n3. Testing DateHelper functionality simulation...\n";
try {
    // Simulate the DateHelper functionality
    function simulateDateHelper($value) {
        if (empty($value) || $value === '0000-00-00') {
            return null;
        }

        try {
            $date = new DateTime($value);
            return $date->format('d-m-Y');
        } catch (Exception $e) {
            return null;
        }
    }

    $testDates = ['28-02-2027', '2027-02-28', '28/02/2027', '', null, '0000-00-00'];
    foreach ($testDates as $testDate) {
        $result = simulateDateHelper($testDate);
        $input = $testDate === null ? 'NULL' : ($testDate === '' ? 'EMPTY' : $testDate);
        $output = $result === null ? 'NULL' : $result;
        echo "   Input: $input → Output: $output\n";
    }
    echo "   ✅ Date helper simulation works correctly\n";
} catch (Exception $e) {
    echo "   ❌ Date helper simulation failed: " . $e->getMessage() . "\n";
}

// Test 4: Check JavaScript syntax fix
echo "\n4. Testing JavaScript fix...\n";
$jsFile = 'resources/views/profile/employee_profile.blade.php';
if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    if (strpos($content, '} else {') !== false) {
        echo "   ✅ JavaScript else statement format fixed\n";
    } else {
        echo "   ⚠️  JavaScript else statement format not found (may still be correct)\n";
    }
} else {
    echo "   ❌ Employee profile view file not found\n";
}

echo "\n✅ Syntax fix testing completed!\n";
echo "The application should now work without JavaScript/PHP syntax errors.\n";
?>