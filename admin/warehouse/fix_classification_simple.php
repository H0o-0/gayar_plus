<?php
// ملف التصنيف التلقائي المبسط
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once('../config.php');

header('Content-Type: application/json');

try {
    // البحث عن المنتجات غير المصنفة
    $unclassified_query = $conn->query("
        SELECT id, product_name 
        FROM temp_warehouse 
        WHERE status = 'unclassified' OR suggested_brand IS NULL
        LIMIT 100
    ");
    
    if (!$unclassified_query) {
        throw new Exception('خطأ في الاستعلام: ' . $conn->error);
    }
    
    $fixed_count = 0;
    $total_processed = 0;
    
    // قواعد التصنيف البسيطة
    $brand_keywords = [
        'Apple' => ['iphone', 'apple', 'ios', 'ايفون', 'آيفون', 'أيفون', 'ابل'],
        'Samsung' => ['samsung', 'galaxy', 'note', 'سامسونج', 'جالاكسي', 'سامسونغ'],
        'Huawei' => ['huawei', 'mate', 'p30', 'p40', 'هواوي'],
        'Xiaomi' => ['xiaomi', 'redmi', 'mi', 'شاومي', 'ريدمي'],
        'Oppo' => ['oppo', 'find', 'reno', 'أو��و', 'اوبو'],
        'Vivo' => ['vivo', 'nex', 'فيفو'],
        'LG' => ['lg', 'ال جي']
    ];
    
    while ($row = $unclassified_query->fetch_assoc()) {
        $total_processed++;
        $product_name = strtolower($row['product_name']);
        $suggested_brand = null;
        
        // البحث عن العلامة التجارية
        foreach ($brand_keywords as $brand => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($product_name, $keyword) !== false) {
                    $suggested_brand = $brand;
                    break 2;
                }
            }
        }
        
        // تحديث المنتج إذا تم العثور على علامة تجارية
        if ($suggested_brand) {
            $stmt = $conn->prepare("UPDATE temp_warehouse SET suggested_brand = ?, status = 'classified' WHERE id = ?");
            $stmt->bind_param("si", $suggested_brand, $row['id']);
            
            if ($stmt->execute()) {
                $fixed_count++;
            }
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => "تم تصنيف $fixed_count منتج من أصل $total_processed منتج",
        'fixed_count' => $fixed_count,
        'total_processed' => $total_processed
    ]);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} catch (Error $e) {
    echo json_encode(['status' => 'error', 'message' => 'خطأ في النظام: ' . $e->getMessage()]);
}
?>