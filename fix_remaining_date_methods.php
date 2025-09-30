<?php
/**
 * Script to fix remaining date methods in all models
 */

$baseDir = 'app/Models/';
$files = glob($baseDir . '*.php');

echo "Fixing remaining date methods...\n";
echo "===============================\n\n";

foreach ($files as $filePath) {
    $filename = basename($filePath);
    $content = file_get_contents($filePath);
    $originalContent = $content;

    // Add DateHelper import if Carbon is used but DateHelper is not imported
    if (strpos($content, 'use Carbon\Carbon;') !== false && strpos($content, 'use App\Helpers\DateHelper;') === false) {
        $content = str_replace(
            'use Carbon\Carbon;',
            "use App\Helpers\DateHelper;\nuse Carbon\Carbon;",
            $content
        );
        echo "📦 Added DateHelper import to: $filename\n";
    }

    // Fix setter methods that still use Carbon::createFromFormat
    $setterPattern = '/public function set(\w+)DateAttribute\(\$value\)\s*\{[^}]*Carbon::createFromFormat[^}]*\}/s';
    if (preg_match_all($setterPattern, $content, $matches)) {
        foreach ($matches[0] as $match) {
            // Extract field name from method name
            preg_match('/set(\w+)DateAttribute/', $match, $methodMatches);
            if (isset($methodMatches[1])) {
                $fieldName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $methodMatches[1])) . '_date';
                if ($methodMatches[1] === 'Attendance') {
                    $fieldName = 'attendance_date';
                } elseif ($methodMatches[1] === 'NicExpiry') {
                    $fieldName = 'nic_expiry';
                }

                $newMethod = "public function set{$methodMatches[1]}DateAttribute(\$value)\n\t{\n\t\t\$this->attributes['{$fieldName}'] = DateHelper::parseToddmmyyyy(\$value);\n\t}";
                $content = str_replace($match, $newMethod, $content);
                echo "  ✅ Fixed setter: set{$methodMatches[1]}DateAttribute in $filename\n";
            }
        }
    }

    // Fix getter methods that still use Carbon::parse
    $getterPattern = '/public function get(\w+)DateAttribute\(\$value\)\s*\{[^}]*Carbon::parse[^}]*\}/s';
    if (preg_match_all($getterPattern, $content, $matches)) {
        foreach ($matches[0] as $match) {
            preg_match('/get(\w+)DateAttribute/', $match, $methodMatches);
            if (isset($methodMatches[1])) {
                $newMethod = "public function get{$methodMatches[1]}DateAttribute(\$value)\n\t{\n\t\treturn \$value;\n\t}";
                $content = str_replace($match, $newMethod, $content);
                echo "  ✅ Fixed getter: get{$methodMatches[1]}DateAttribute in $filename\n";
            }
        }
    }

    // Also fix any remaining date fields that don't end with "Date"
    $otherDateFields = [
        'setStartDateAttribute', 'setEndDateAttribute', 'setExpiryDateAttribute',
        'setDateAttribute', 'setIssueDateAttribute', 'setApplicationDateAttribute',
        'setInterviewDateAttribute', 'setMeetingDateAttribute', 'setBugDateAttribute',
        'setDiscussionDateAttribute', 'setFileDateAttribute', 'setCommentDateAttribute'
    ];

    foreach ($otherDateFields as $methodName) {
        $pattern = "/public function {$methodName}\(\\\$value\)\s*\{[^}]*Carbon::createFromFormat[^}]*\}/s";
        if (preg_match($pattern, $content, $matches)) {
            // Extract field name
            $fieldName = strtolower(str_replace(['set', 'Attribute'], '', $methodName));
            $fieldName = preg_replace('/([a-z])([A-Z])/', '$1_$2', $fieldName);

            $newMethod = "public function {$methodName}(\$value)\n\t{\n\t\t\$this->attributes['{$fieldName}'] = DateHelper::parseToddmmyyyy(\$value);\n\t}";
            $content = str_replace($matches[0], $newMethod, $content);
            echo "  ✅ Fixed setter: {$methodName} in $filename\n";
        }

        $getterName = str_replace('set', 'get', $methodName);
        $getterPattern = "/public function {$getterName}\(\\\$value\)\s*\{[^}]*Carbon::parse[^}]*\}/s";
        if (preg_match($getterPattern, $content, $matches)) {
            $newMethod = "public function {$getterName}(\$value)\n\t{\n\t\treturn \$value;\n\t}";
            $content = str_replace($matches[0], $newMethod, $content);
            echo "  ✅ Fixed getter: {$getterName} in $filename\n";
        }
    }

    // Save file only if changes were made
    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        echo "  💾 Saved: $filename\n";
    }

    echo "\n";
}

echo "✨ All remaining date methods have been fixed!\n";
?>