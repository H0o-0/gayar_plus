<?php
echo "<h1>AJAX Path Resolution Test</h1>";

echo "<p>Current file: " . __FILE__ . "</p>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Parent directory: " . dirname(__DIR__) . "</p>";

$root_path = dirname(__DIR__);
echo "<p>Root path: " . $root_path . "</p>";

$initialize_path = $root_path . '/initialize.php';
echo "<p>Initialize path: " . $initialize_path . "</p>";

if (file_exists($initialize_path)) {
    echo "<p>initialize.php exists</p>";
    
    // Try to include initialize.php
    try {
        require_once $initialize_path;
        echo "<p>initialize.php included successfully</p>";
        
        // Check if the database connection is available
        echo "<p>Database connection object exists: " . (isset($conn) ? 'Yes' : 'No') . "</p>";
        
        if (isset($conn)) {
            echo "<p>Connection object class: " . get_class($conn) . "</p>";
            
            // Test if the connection is working
            if ($conn->ping()) {
                echo "<p>Database connection is active</p>";
            } else {
                echo "<p>Database connection is not active</p>";
            }
        } else {
            echo "<p>Database connection object not found</p>";
        }
    } catch (Exception $e) {
        echo "<p>Error including initialize.php: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>initialize.php does not exist</p>";
}
?>