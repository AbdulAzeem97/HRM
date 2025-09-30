<?php
// Test if DateHelper class can be loaded properly

// Try to include the Laravel autoloader
try {
    require_once 'vendor/autoload.php';
    echo "✅ Autoloader loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Autoloader failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test if DateHelper class exists
if (class_exists('App\Helpers\DateHelper')) {
    echo "✅ DateHelper class found\n";

    // Test the method
    try {
        $result = App\Helpers\DateHelper::parseToddmmyyyy('28-02-2027');
        echo "✅ DateHelper method works: $result\n";
    } catch (Exception $e) {
        echo "❌ DateHelper method failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ DateHelper class not found\n";

    // Check if the file exists
    if (file_exists('app/Helpers/DateHelper.php')) {
        echo "ℹ️  DateHelper.php file exists but class not autoloaded\n";

        // Check composer autoload
        $composerJson = json_decode(file_get_contents('composer.json'), true);
        if (isset($composerJson['autoload']['psr-4']['App\\'])) {
            echo "ℹ️  PSR-4 autoloading configured for App\\ namespace\n";
        } else {
            echo "❌ PSR-4 autoloading not configured properly\n";
        }
    } else {
        echo "❌ DateHelper.php file does not exist\n";
    }
}

// Test Employee model loading
try {
    if (class_exists('App\Models\Employee')) {
        echo "✅ Employee model loads successfully\n";

        // Check if it uses DateHelper
        $reflection = new ReflectionClass('App\Models\Employee');
        $fileContent = file_get_contents($reflection->getFileName());

        if (strpos($fileContent, 'use App\Helpers\DateHelper;') !== false) {
            echo "✅ Employee model imports DateHelper\n";
        } else {
            echo "❌ Employee model does not import DateHelper\n";
        }

        if (strpos($fileContent, 'DateHelper::parseToddmmyyyy') !== false) {
            echo "✅ Employee model uses DateHelper methods\n";
        } else {
            echo "❌ Employee model does not use DateHelper methods\n";
        }
    } else {
        echo "❌ Employee model not found\n";
    }
} catch (Exception $e) {
    echo "❌ Employee model error: " . $e->getMessage() . "\n";
}

echo "\nDone testing.\n";
?>