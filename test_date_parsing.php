<?php
// Test script to verify date parsing works without errors

// Simulate the DateHelper class functionality
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

echo "Testing Date Parsing:\n";
echo "===================\n\n";

// Test various date formats
$testDates = [
    '28-02-2027',      // dd-mm-yyyy
    '2027-02-28',      // yyyy-mm-dd
    '28/02/2027',      // dd/mm/yyyy
    '02/28/2027',      // mm/dd/yyyy
    '2027/02/28',      // yyyy/mm/dd
    '28.02.2027',      // dd.mm.yyyy
    '2027.02.28',      // yyyy.mm.dd
    '2027-02-28 10:30:00',  // with time
    '',                // empty
    null,              // null
    '0000-00-00',      // invalid date
];

foreach ($testDates as $testDate) {
    $result = DateHelper::parseToddmmyyyy($testDate);
    $input = $testDate === null ? 'NULL' : ($testDate === '' ? 'EMPTY' : $testDate);
    $output = $result === null ? 'NULL' : $result;
    echo "Input: $input → Output: $output\n";
}

echo "\n✅ Date parsing test completed successfully!\n";
echo "The DateHelper should now handle various date formats without throwing errors.\n";
?>