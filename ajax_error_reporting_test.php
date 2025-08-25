<?php
// Test the error reporting in the same way as the AJAX files
$root_path = dirname(__DIR__);
require_once $root_path . '/initialize.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>AJAX Error Reporting Test</h1>";

echo "<p>Current error reporting level: " . error_reporting() . "</p>";

// Test if we can write to the error log
error_log("AJAX error reporting test message");
echo "<p>Test message written to error log</p>";

// Test if we can trigger an error
echo "<p>Testing error trigger:</p>";

try {
    // This should trigger a warning
    $undefined_variable++;
} catch (Error $e) {
    echo "<p>Caught Error: " . $e->getMessage() . "</p>";
    error_log("Caught Error: " . $e->getMessage());
} catch (Exception $e) {
    echo "<p>Caught Exception: " . $e->getMessage() . "</p>";
    error_log("Caught Exception: " . $e->getMessage());
}

echo "<p>Error reporting test completed</p>";

// Test if we can access the database connection
echo "<p>Database connection object exists: " . (isset($conn) ? 'Yes' : 'No') . "</p>";

if (isset($conn)) {
    echo "<p>Connection object class: " . get_class($conn) . "</p>";
    
    // Test if the connection is working
    if ($conn->ping()) {
        echo "<p>Database connection is active</p>";
    } else {
        echo "<p>Database connection is not active</p>";
    }
}
?>