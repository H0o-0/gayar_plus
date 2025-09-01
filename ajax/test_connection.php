<?php
require_once '../initialize.php';

header('Content-Type: application/json');

try {
    if ($conn) {
        // Test a simple query
        $result = $conn->query("SELECT 1 as test");
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Database connection and query successful']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database query failed: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
}
?>