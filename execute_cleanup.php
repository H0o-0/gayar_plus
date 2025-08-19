<?php
require_once('config.php');

// Path to the SQL file
$sql_file = 'cleanup_database.sql';

// Read the SQL file
$sql_commands = file_get_contents($sql_file);

if ($sql_commands === false) {
    die("Error: Could not read the SQL file.");
}

// Execute the SQL commands
if ($conn->multi_query($sql_commands)) {
    // Loop through all result sets
    do {
        // Store first result set
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    
    echo "Database cleanup was successful.\n";
} else {
    echo "Error executing SQL commands: " . $conn->error . "\n";
}

// Close the database connection
$conn->close();
?>
