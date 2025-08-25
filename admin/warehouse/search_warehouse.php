<?php
require_once '../../classes/DBConnection.php';

header('Content-Type: application/json');

try {
    $conn = new DBConnection();
    $db = $conn->getConnection();
    
    if (!$db) {
        throw new Exception('فشل في الاتصال بقاعدة البيانات');
    }
    
    // التحقق من وجود نص البحث
    if (!isset($_POST['search']) || empty(trim($_POST['search']))) {
        throw new Exception('نص البحث مطلوب');
    }
    
    $search_term = trim($_POST['search']);
    
    // البحث في المخزن
    $sql = "SELECT 
                tw.id,
                tw.product_name,
                tw.suggested_brand,
                tw.suggested_type,
                tw.original_price,
                tw.status,
                c.category as brand_name
            FROM temp_warehouse tw
            LEFT JOIN categories c ON tw.category_id = c.id
            WHERE (
                LOWER(tw.product_name) LIKE LOWER(?) OR
                LOWER(tw.suggested_brand) LIKE LOWER(?) OR
                LOWER(tw.suggested_type) LIKE LOWER(?)
            )
            AND tw.status IN ('classified', 'unclassified')
            ORDER BY 
                CASE 
                    WHEN LOWER(tw.product_name) LIKE LOWER(?) THEN 1
                    WHEN LOWER(tw.suggested_brand) LIKE LOWER(?) THEN 2
                    ELSE 3
                END,
                tw.product_name ASC
            LIMIT 20";
    
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        throw new Exception('فشل في إعداد الاستعلام: ' . $db->error);
    }
    
    $search_pattern = '%' . $search_term . '%';
    $stmt->bind_param('sssss', 
        $search_pattern, 
        $search_pattern, 
        $search_pattern,
        $search_pattern,
        $search_pattern
    );
    
    if (!$stmt->execute()) {
        throw new Exception('فشل في تنفيذ الاستعلام: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $items = [];
    
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'id' => $row['id'],
            'product_name' => htmlspecialchars($row['product_name']),
            'suggested_brand' => htmlspecialchars($row['suggested_brand']),
            'suggested_type' => htmlspecialchars($row['suggested_type']),
            'original_price' => $row['original_price'],
            'status' => $row['status'],
            'brand_name' => htmlspecialchars($row['brand_name'] ?? '')
        ];
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'data' => $items,
        'count' => count($items),
        'search_term' => $search_term
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($db)) {
        $db->close();
    }
}
?>
