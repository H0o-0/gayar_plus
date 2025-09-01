<?php
// Test the database connection in the same way as the AJAX files
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
                } else {
                    echo "<p>Error executing simple query: " . $conn->error . "</p>";
                }
            } else {
                echo "<p>Database connection is not active</p>";
            }
        } else {
            echo "<p>Database connection object not found</p>";
        }
    } catch (Exception $e) {
        echo "<p>Error including initialize.php: " . $e->getMessage() . "</p>";
        echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
    }
} else {
    echo "<p>initialize.php does not exist</p>";
}
?>