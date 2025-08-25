<?php
require_once '../initialize.php';

header('Content-Type: application/json');

try {
    if ($conn) {
        // Get the first active category/series
        $result = $conn->query("SELECT id FROM series WHERE status = 1 ORDER BY name ASC LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $category = $result->fetch_assoc();
            echo json_encode(['success' => true, 'category_id' => $category['id']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No active categories found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
}
?>