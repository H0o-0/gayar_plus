<?php
// Check if the mysqli extension is loaded
if (!extension_loaded('mysqli')) {
    echo "The mysqli extension is not loaded. Please enable it in your php.ini file for CLI.\n";
    exit(1);
}

// If the extension is loaded, proceed with the database connection
require_once('config.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Database connection is successful.\n";
$conn->close();
?>
