<?php
require_once '../initialize.php';

header('Content-Type: application/json');

try {
    if ($conn) {
        // Get the first active brand
        $result = $conn->query("SELECT id FROM brands WHERE status = 1 ORDER BY name ASC LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $brand = $result->fetch_assoc();
            echo json_encode(['success' => true, 'brand_id' => $brand['id']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No active brands found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
}
?>