<?php
// Test the database connection in the same way as the AJAX files
$root_path = dirname(__FILE__);
require_once $root_path . '/initialize.php';

echo "<h1>Database Connection Test</h1>";

if ($conn) {
    echo "<p>Database connection successful</p>";
    
    // Test a simple query
    $result = $conn->query("SELECT 1 as test");
    if ($result) {
        echo "<p>Simple query successful</p>";
        
        // Test if we can access the brands table
        $result = $conn->query("SELECT COUNT(*) as count FROM brands WHERE status = 1");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>Number of active brands: " . $row['count'] . "</p>";
        } else {
            echo "<p>Error accessing brands table: " . $conn->error . "</p>";
        }
        
        // Test if we can access the series table
        $result = $conn->query("SELECT COUNT(*) as count FROM series WHERE status = 1");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>Number of active series: " . $row['count'] . "</p>";
        } else {
            echo "<p>Error accessing series table: " . $conn->error . "</p>";
        }
        
        // Test if we can access the models table
        $result = $conn->query("SELECT COUNT(*) as count FROM models WHERE status = 1");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>Number of active models: " . $row['count'] . "</p>";
        } else {
            echo "<p>Error accessing models table: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Error executing simple query: " . $conn->error . "</p>";
    }
} else {
    echo "<p>Database connection failed</p>";
}
?>