<?php
// Simple test to check login functionality
require_once 'initialize.php';

// Simulate POST data
$_POST['username'] = 'admin';
$_POST['password'] = 'admin123';

echo "Testing login with username: {$_POST['username']} and password: {$_POST['password']}\n";

// Include the Login class
require_once 'classes/Login.php';

// Create Login instance
$login = new Login();

// Test the login method
$result = $login->login();
echo "Login result: $result\n";

// Decode the result to see what's happening
$response = json_decode($result, true);
echo "Decoded response:\n";
print_r($response);
?>