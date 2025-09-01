<?php
require_once 'config.php';

$result = $conn->query('SELECT id, username, password FROM users WHERE username = "admin"');
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "User found:\n";
    echo "ID: " . $user['id'] . "\n";
    echo "Username: " . $user['username'] . "\n";
    echo "Password hash: " . $user['password'] . "\n";
    echo "Expected hash for 'admin123': " . md5('admin123') . "\n";
} else {
    echo "Admin user not found\n";
    
    // Let's check all users
    $all_users = $conn->query('SELECT id, username, password FROM users');
    echo "All users in database:\n";
    while ($user = $all_users->fetch_assoc()) {
        echo "- ID: " . $user['id'] . ", Username: " . $user['username'] . ", Password hash: " . $user['password'] . "\n";
    }
}
?>