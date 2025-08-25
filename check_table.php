<?php
require_once 'config.php';

// Check orders table structure
$result = $conn->query("SHOW CREATE TABLE orders");
if ($result) {
    $row = $result->fetch_row();
    echo "<pre>";
    echo htmlspecialchars($row[1]);
    echo "</pre>";
} else {
    echo "Error: " . $conn->error;
}
?>