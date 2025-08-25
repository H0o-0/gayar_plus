<?php
include 'config.php';

echo "Admin users in the database:\n";
$result = $conn->query('SELECT * FROM users WHERE type = 1');
while($row = $result->fetch_assoc()) {
    echo 'ID: ' . $row['id'] . ' - Username: ' . $row['username'] . ' - Password: ' . $row['password'] . "\n";
}
?>