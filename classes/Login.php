<?php
// Use a more robust path resolution
$root_path = dirname(__DIR__);
require_once $root_path . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['f'])) {
    $action = $_GET['f'];
    
    if ($action === 'login') {
        // Admin login
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Hash the password with MD5 (as stored in the database)
        $hashed_password = md5($password);
        
        // Check credentials against users table where type = 1 (admin)
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ? AND type = 1");
        $stmt->bind_param("ss", $username, $hashed_password);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Login successful
            $user = $result->fetch_assoc();
            
            // Start session and set user data
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['userdata'] = [
                'id' => $user['id'],
                'firstname' => $user['firstname'],
                'lastname' => $user['lastname'],
                'username' => $user['username'],
                'login_type' => 1, // Admin login
                'last_login' => $user['last_login']
            ];
            
            // Update last login time
            $conn->query("UPDATE users SET last_login = NOW() WHERE id = " . $user['id']);
            
            $response = [
                'status' => 'success',
                'message' => 'Login successful'
            ];
        } else {
            // Login failed
            $response = [
                'status' => 'incorrect',
                'message' => 'Incorrect username or password'
            ];
        }
        
        echo json_encode($response);
        exit;
    } elseif ($action === 'logout') {
        // Logout
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: login.php');
        exit;
    }
}
?>