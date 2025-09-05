<?php
require_once '../initialize.php';

header('Content-Type: application/json');

$response = ['success' => false, 'series' => []];

if (isset($_GET['brand_id']) && is_numeric($_GET['brand_id'])) {
    $brand_id = intval($_GET['brand_id']);
    
    try {
        $query = "SELECT id, name FROM series WHERE brand_id = ? AND status = 1 ORDER BY name ASC";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("i", $brand_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $series = [];
            while ($row = $result->fetch_assoc()) {
                $series[] = [
                    'id' => $row['id'],
                    'name' => $row['name']
                ];
            }
            
            $response['success'] = true;
            $response['series'] = $series;
            $stmt->close();
        }
    } catch (Exception $e) {
        $response['error'] = 'خطأ في قاعدة البيانات';
        error_log("Get brand series error: " . $e->getMessage());
    }
}

echo json_encode($response);
?>
