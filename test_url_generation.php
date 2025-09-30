<?php
// Simple test to check URL generation
$employee_id = 61;
$base_url = "http://localhost/ttphrm"; // Adjust if needed

echo "<h2>URL Generation Test</h2>";

// Test different URL formats
$urls = [
    "Direct path" => "/ttphrm/staff/employees/{$employee_id}/overtime_settings_update",
    "Without ttphrm" => "/staff/employees/{$employee_id}/overtime_settings_update",
    "Full URL" => "{$base_url}/staff/employees/{$employee_id}/overtime_settings_update",
];

foreach ($urls as $name => $url) {
    echo "<p><strong>{$name}:</strong> {$url}</p>";
}

// Test with curl to see which one works
echo "<h3>Testing URLs:</h3>";

foreach ($urls as $name => $url) {
    if (strpos($url, 'http') !== 0) {
        $test_url = "http://localhost" . $url;
    } else {
        $test_url = $url;
    }

    echo "<p><strong>{$name}:</strong> ";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $test_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 404) {
        echo "<span style='color: red;'>❌ 404 Not Found</span>";
    } elseif ($http_code == 405) {
        echo "<span style='color: orange;'>⚠️ 405 Method Not Allowed (Route exists but wrong method)</span>";
    } elseif ($http_code == 200 || $http_code == 302) {
        echo "<span style='color: green;'>✅ {$http_code} Route exists</span>";
    } else {
        echo "<span style='color: blue;'>ℹ️ {$http_code} Other response</span>";
    }
    echo "</p>";
}

echo "<h3>Recommendation:</h3>";
echo "<p>Use the URL that shows either 200, 302, or 405 status (405 means the route exists but POST method might need CSRF token).</p>";
?>

<script>
// Test JavaScript URL generation
console.log('Testing JavaScript URL generation:');
console.log('Current page URL:', window.location.href);
console.log('Base URL:', window.location.origin);
console.log('Path:', window.location.pathname);

// Generate different URL formats
var employeeId = 61;
var baseUrl = window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/');
var testUrls = [
    'Absolute: ' + baseUrl + '/staff/employees/' + employeeId + '/overtime_settings_update',
    'Relative: /staff/employees/' + employeeId + '/overtime_settings_update',
    'With ttphrm: /ttphrm/staff/employees/' + employeeId + '/overtime_settings_update'
];

testUrls.forEach(function(url) {
    console.log(url);
});
</script>