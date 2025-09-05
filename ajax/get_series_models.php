<?php
require_once '../initialize.php';

header('Content-Type: application/json');

$response = ['success' => false, 'models' => []];

if (isset($_GET['series_id']) && is_numeric($_GET['series_id'])) {
    $series_id = intval($_GET['series_id']);
    
    try {
        // البحث في جدول models أولاً
        $query = "SELECT id, name FROM models WHERE series_id = ? AND status = 1 ORDER BY name ASC";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("i", $series_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $models = [];
            while ($row = $result->fetch_assoc()) {
                $models[] = [
                    'id' => $row['id'],
                    'name' => $row['name']
                ];
            }
            
            // إذا لم نجد نتائج، جرب جدول phone_models كـ fallback
            if (empty($models)) {
                $stmt->close();
                
                $query_fallback = "SELECT id, model_name as name FROM phone_models WHERE sub_category_id = ? AND status = 1 ORDER BY model_name ASC";
                $stmt_fallback = $conn->prepare($query_fallback);
                
                if ($stmt_fallback) {
                    $stmt_fallback->bind_param("i", $series_id);
                    $stmt_fallback->execute();
                    $result_fallback = $stmt_fallback->get_result();
                    
                    while ($row = $result_fallback->fetch_assoc()) {
                        $models[] = [
                            'id' => $row['id'],
                            'name' => $row['name']
                        ];
                    }
                    $stmt_fallback->close();
                }
            } else {
                $stmt->close();
            }
            
            $response['success'] = true;
            $response['models'] = $models;
        }
    } catch (Exception $e) {
        $response['error'] = 'خطأ في قاعدة البيانات';
        error_log("Get series models error: " . $e->getMessage());
    }
}

echo json_encode($response);
?>
