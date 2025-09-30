<?php
require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Generate the route URL for employee ID 61
$employee_id = 61;
$route_url = "/staff/employees/{$employee_id}/overtime_settings_update";

echo "🔗 ROUTE TESTING\n";
echo "===============\n\n";
echo "Employee ID: {$employee_id}\n";
echo "Expected Route: {$route_url}\n";
echo "Full URL: http://localhost/ttphrm{$route_url}\n\n";

echo "📋 DEBUGGING STEPS:\n";
echo "1. Check if you're accessing the employee salary page at: /staff/employees/{$employee_id}\n";
echo "2. Click the 'Overtime Settings' tab\n";
echo "3. The form should submit to: {$route_url}\n";
echo "4. Make sure you're logged in with proper permissions\n\n";

echo "💡 If the error persists, try:\n";
echo "- Clear browser cache\n";
echo "- Check network tab in browser dev tools to see actual request URL\n";
echo "- Verify the JavaScript is loading correctly\n";
?>