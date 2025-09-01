<?php
// Simple direct test of login query
require_once 'initialize.php';

// Simulate POST data
$username = 'admin';
$password = 'admin123';

echo "Testing login with username: $username and password: $password\n";

// Create the query directly
$query = "SELECT * from users where username = '$username' and password = md5('$password') ";
echo "Query: $query\n";

$result = $conn->query($query);

if($result->num_rows > 0){
    echo "Login successful! Found " . $result->num_rows . " user(s)\n";
    $user = $result->fetch_assoc();
    print_r($user);
} else {
    echo "Login failed! No users found\n";
}
?>