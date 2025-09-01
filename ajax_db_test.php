<?php
// Test the database connection in the same way as the AJAX files
$root_path = dirname(__FILE__);
require_once $root_path . '/initialize.php';

echo "<h1>AJAX Database Connection Test</h1>";

echo "<p>Database connection object exists: " . (isset($conn) ? 'Yes' : 'No') . "</p>";

if (isset($conn)) {
    echo "<p>Connection object class: " . get_class($conn) . "</p>";
    
    // Test if the connection is working
    if ($conn->ping()) {
        echo "<p>Database connection is active</p>";
    } else {
        echo "<p>Database connection is not active</p>";
    }
    
    // Test the charset
    $charset = $conn->character_set_name();
    echo "<p>Current charset: " . $charset . "</p>";
    
    // Test a simple query
    $result = $conn->query("SELECT 1 as test");
    if ($result) {
        echo "<p>Simple query successful</p>";
    } else {
        echo "<p>Error executing simple query: " . $conn->error . "</p>";
    }
} else {
    echo "<p>Database connection object not found</p>";
}

// Test if we can access the SystemSettings object
echo "<p>SystemSettings object exists: " . (isset($_settings) ? 'Yes' : 'No') . "</p>";

if (isset($_settings)) {
    echo "<p>SystemSettings object class: " . get_class($_settings) . "</p>";
}
?>