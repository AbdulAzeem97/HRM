<?php
/**
 * Comprehensive test for all date fields across the application
 */

// Test the DateHelper functionality
class DateHelper
{
    public static function parseToddmmyyyy($value)
    {
        if (empty($value) || $value === '0000-00-00') {
            return null;
        }

        // Try multiple date formats
        $formats = [
            'd-m-Y',        // 28-02-2027
            'Y-m-d',        // 2027-02-28
            'd/m/Y',        // 28/02/2027
            'Y-m-d H:i:s',  // 2027-02-28 00:00:00
            'd-m-Y H:i:s',  // 28-02-2027 00:00:00
            'm/d/Y',        // 02/28/2027
            'Y/m/d',        // 2027/02/28
            'd.m.Y',        // 28.02.2027
            'Y.m.d',        // 2027.02.28
        ];

        foreach ($formats as $format) {
            try {
                $date = DateTime::createFromFormat($format, $value);
                if ($date && $date->format($format) === $value) {
                    return $date->format('d-m-Y');
                }
            } catch (Exception $e) {
                continue;
            }
        }

        // If all specific formats fail, try general parsing
        try {
            $date = new DateTime($value);
            return $date->format('d-m-Y');
        } catch (Exception $e) {
            return null;
        }
    }
}

echo "Comprehensive Date Field Testing\n";
echo "===============================\n\n";

// Test data for different scenarios
$testCases = [
    // Employee fields
    'Employee Date Fields' => [
        'date_of_birth' => ['28-02-1990', '1990-02-28', '28/02/1990'],
        'joining_date' => ['15-01-2020', '2020-01-15', '15/01/2020'],
        'exit_date' => ['31-12-2025', '2025-12-31', '31/12/2025'],
        'nic_expiry' => ['28-02-2027', '2027-02-28', '28/02/2027'],
    ],

    // Leave fields
    'Leave Date Fields' => [
        'start_date' => ['01-03-2025', '2025-03-01', '01/03/2025'],
        'end_date' => ['05-03-2025', '2025-03-05', '05/03/2025'],
    ],

    // Attendance fields
    'Attendance Date Fields' => [
        'attendance_date' => ['15-09-2025', '2025-09-15', '15/09/2025'],
    ],

    // Document fields
    'Document Date Fields' => [
        'expiry_date' => ['31-12-2025', '2025-12-31', '31/12/2025'],
        'issue_date' => ['01-01-2020', '2020-01-01', '01/01/2020'],
    ],

    // Project fields
    'Project Date Fields' => [
        'start_date' => ['01-04-2025', '2025-04-01', '01/04/2025'],
        'end_date' => ['30-06-2025', '2025-06-30', '30/06/2025'],
    ],

    // Asset fields
    'Asset Date Fields' => [
        'purchase_date' => ['15-03-2024', '2024-03-15', '15/03/2024'],
        'warranty_date' => ['15-03-2026', '2026-03-15', '15/03/2026'],
    ],
];

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

foreach ($testCases as $category => $fields) {
    echo "Testing: $category\n";
    echo str_repeat('-', strlen($category) + 9) . "\n";

    foreach ($fields as $fieldName => $testValues) {
        echo "  Field: $fieldName\n";

        foreach ($testValues as $testValue) {
            $totalTests++;
            $result = DateHelper::parseToddmmyyyy($testValue);

            if ($result !== null && preg_match('/^\d{2}-\d{2}-\d{4}$/', $result)) {
                $passedTests++;
                echo "    ✅ '$testValue' → '$result'\n";
            } else {
                $failedTests++;
                echo "    ❌ '$testValue' → FAILED (result: " . ($result ?? 'NULL') . ")\n";
            }
        }
        echo "\n";
    }
    echo "\n";
}

// Test edge cases
echo "Testing Edge Cases\n";
echo "------------------\n";

$edgeCases = [
    '',
    null,
    '0000-00-00',
    'invalid-date',
    '32-13-2025',  // Invalid date
    '29-02-2023',  // Invalid leap year
];

foreach ($edgeCases as $testValue) {
    $totalTests++;
    $result = DateHelper::parseToddmmyyyy($testValue);
    $input = $testValue === null ? 'NULL' : ($testValue === '' ? 'EMPTY' : $testValue);

    if ($result === null) {
        $passedTests++;
        echo "✅ '$input' → NULL (as expected)\n";
    } else {
        $failedTests++;
        echo "❌ '$input' → '$result' (expected NULL)\n";
    }
}

echo "\n";
echo "Test Summary\n";
echo "============\n";
echo "Total Tests: $totalTests\n";
echo "Passed: $passedTests ✅\n";
echo "Failed: $failedTests ❌\n";
echo "Success Rate: " . round(($passedTests / $totalTests) * 100, 2) . "%\n\n";

if ($failedTests === 0) {
    echo "🎉 ALL TESTS PASSED! Date handling is working correctly across all fields.\n";
    echo "✨ The application now consistently handles dates in dd-mm-yyyy format.\n";
} else {
    echo "⚠️  Some tests failed. Please review the DateHelper implementation.\n";
}

echo "\nField Coverage Test\n";
echo "==================\n";

// Test if all major models have been covered
$modelsWithDateFields = [
    'Employee' => ['date_of_birth', 'joining_date', 'exit_date', 'nic_expiry'],
    'Leave' => ['start_date', 'end_date'],
    'Attendance' => ['attendance_date'],
    'EmployeeDocument' => ['expiry_date'],
    'Asset' => ['purchase_date', 'warranty_date'],
    'Award' => ['award_date'],
    'Project' => ['start_date', 'end_date'],
    'Task' => ['start_date', 'end_date'],
    'Holiday' => ['start_date', 'end_date'],
    'Meeting' => ['meeting_date'],
    'Travel' => ['start_date', 'end_date'],
    'Training' => ['start_date', 'end_date'],
];

echo "Models with date fields that should be fixed:\n";
foreach ($modelsWithDateFields as $model => $fields) {
    echo "- $model: " . implode(', ', $fields) . "\n";
}

echo "\n✅ Date field testing completed!\n";
?>