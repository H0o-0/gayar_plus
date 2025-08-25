<?php
require_once '../../classes/DBConnection.php';

header('Content-Type: application/json');

try {
    $conn = new DBConnection();
    $db = $conn->getConnection();
    
    if (!$db) {
        throw new Exception('فشل في الاتصال بقاعدة البيانات');
    }
    
    // التحقق من وجود البيانات المطلوبة
    if (!isset($_POST['brand_id']) || !isset($_POST['series_id']) || !isset($_POST['model_id'])) {
        throw new Exception('جميع البيانات مطلوبة: brand_id, series_id, model_id');
    }
    
    $brand_id = (int)$_POST['brand_id'];
    $series_id = (int)$_POST['series_id'];
    $model_id = (int)$_POST['model_id'];
    
    // الحصول على معلومات البراند والسيريس والموديل
    $brand_query = $db->prepare("SELECT name FROM brands WHERE id = ?");
    $brand_query->bind_param('i', $brand_id);
    $brand_query->execute();
    $brand_result = $brand_query->get_result();
    
    if ($brand_result->num_rows === 0) {
        throw new Exception('البراند غير موجود');
    }
    
    $brand_name = $brand_result->fetch_assoc()['name'];
    $brand_lc = strtolower($brand_name);
    
    // البحث عن الملحقات المتصلة في المخزن
    $sql = "SELECT 
                tw.id,
                tw.product_name,
                tw.suggested_brand,
                tw.suggested_type,
                tw.original_price,
                tw.status,
                tw.confidence
            FROM temp_warehouse tw
            WHERE (
                LOWER(tw.suggested_brand) = LOWER(?) OR
                LOWER(tw.suggested_brand) LIKE LOWER(?)
            )
            AND tw.status IN ('classified', 'unclassified')
            AND tw.confidence >= 0.7
            ORDER BY tw.confidence DESC, tw.product_name ASC
            LIMIT 50";
    
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        throw new Exception('فشل في إعداد الاستعلام: ' . $db->error);
    }
    
    $brand_pattern = '%' . $brand_lc . '%';
    $stmt->bind_param('ss', $brand_lc, $brand_pattern);
    
    if (!$stmt->execute()) {
        throw new Exception('فشل في تنفيذ الاستعلام: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $accessories = [];
    
    while ($row = $result->fetch_assoc()) {
        // تحقق إضافي من مطابقة البراند
        if (is_brand_match($row['suggested_brand'], $brand_name)) {
            $accessories[] = [
                'id' => $row['id'],
                'product_name' => htmlspecialchars($row['product_name']),
                'suggested_brand' => htmlspecialchars($row['suggested_brand']),
                'suggested_type' => htmlspecialchars($row['suggested_type']),
                'original_price' => $row['original_price'],
                'status' => $row['status'],
                'confidence' => $row['confidence']
            ];
        }
    }
    
    $stmt->close();
    $brand_query->close();
    
    echo json_encode([
        'success' => true,
        'data' => $accessories,
        'count' => count($accessories),
        'brand_name' => $brand_name,
        'brand_id' => $brand_id,
        'series_id' => $series_id,
        'model_id' => $model_id
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

/**
 * التحقق من مطابقة البراند
 */
function is_brand_match($suggested_brand, $actual_brand) {
    if (!$suggested_brand || !$actual_brand) return false;
    
    $suggested_lc = strtolower($suggested_brand);
    $actual_lc = strtolower($actual_brand);
    
    // مطابقة كاملة
    if ($suggested_lc === $actual_lc) return true;
    
    // مطابقة جزئية
    if (strpos($suggested_lc, $actual_lc) !== false) return true;
    if (strpos($actual_lc, $suggested_lc) !== false) return true;
    
    // مطابقة الاختصارات
    $brand_aliases = [
        'samsung' => ['sam', 'galaxy'],
        'apple' => ['iph', 'iphone'],
        'huawei' => ['hw', 'huawei'],
        'xiaomi' => ['xia', 'mi', 'redmi'],
        'oppo' => ['op', 'oppo'],
        'realme' => ['real', 'realme'],
        'infinix' => ['inf', 'infinix'],
        'tecno' => ['tec', 'tecno'],
        'itel' => ['itel'],
        'oneplus' => ['onep', 'oneplus'],
        'vivo' => ['viv', 'vivo'],
        'poco' => ['poco']
    ];
    
    foreach ($brand_aliases as $canonical => $aliases) {
        if (strtolower($canonical) === $actual_lc) {
            foreach ($aliases as $alias) {
                if (strtolower($alias) === $suggested_lc) {
                    return true;
                }
            }
        }
    }
    
    return false;
}
?>
