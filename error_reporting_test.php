<?php
echo "<h1>Error Reporting Test</h1>";

echo "<p>Current error reporting level: " . error_reporting() . "</p>";

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<p>Error reporting level after enabling: " . error_reporting() . "</p>";

// Test if we can write to the error log
error_log("Error reporting test message");

echo "<p>Test message written to error log</p>";

// Test if we can trigger an error
echo "<p>Testing error trigger:</p>";

try {
    // This should trigger a warning
    $undefined_variable++;
} catch (Error $e) {
    echo "<p>Caught Error: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p>Caught Exception: " . $e->getMessage() . "</p>";
}

echo "<p>Error reporting test completed</p>";
?>