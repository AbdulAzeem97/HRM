<?php
// Quick test to verify the overtime route
echo "<h2>Overtime Route Test</h2>";

$employee_id = 61;
$base_url = "http://localhost/ttphrm"; // Adjust as needed
$test_url = "{$base_url}/staff/employees/{$employee_id}/overtime_settings_update";

echo "<p><strong>Expected URL:</strong> {$test_url}</p>";

// Test form
echo '<form method="POST" action="' . $test_url . '">';
echo '<input type="hidden" name="_token" value="test_token">';
echo '<input type="checkbox" name="overtime_allowed" value="1" checked> Allow Overtime<br>';
echo '<input type="number" name="required_hours_per_day" value="9"> Required Hours<br>';
echo '<input type="submit" value="Test Submit">';
echo '</form>';

echo "<p><strong>Note:</strong> This form will fail with CSRF token, but should show the correct route error if route is working.</p>";

// JavaScript test
echo '<script>
console.log("Testing AJAX to:", "' . $test_url . '");

function testAjax() {
    fetch("' . $test_url . '", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "test_token"
        },
        body: JSON.stringify({
            overtime_allowed: 1,
            required_hours_per_day: 9
        })
    })
    .then(response => {
        console.log("Response status:", response.status);
        console.log("Response headers:", response.headers);
        return response.text();
    })
    .then(data => {
        console.log("Response:", data);
    })
    .catch(error => {
        console.error("Error:", error);
    });
}
</script>';

echo '<button onclick="testAjax()">Test AJAX Request</button>';
echo '<p>Check browser console for AJAX test results.</p>';
?>