<?php
/**
 * جلب موديلات الفئة مع معلومات إضافية
 * Enhanced version with more detailed model information
 */

// استخدام مسار قوي لحل المسار
$root_path = dirname(__DIR__);

// التحقق من وجود initialize.php
if (!file_exists($root_path . '/initialize.php')) {
    // جرب مسارات بديلة
    if (file_exists('./initialize.php')) {
        require_once './initialize.php';
    } else if (file_exists('../initialize.php')) {
        require_once '../initialize.php';
    } else {
        // العودة إلى config.php إذا لم يوجد initialize.php
        require_once $root_path . '/config.php';
    }
} else {
    require_once $root_path . '/initialize.php';
}

header('Content-Type: application/json');

// تفعيل تقارير الأخطاء للتصحيح
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_POST['category_id'])) {
    echo json_encode(['success' => false, 'message' => 'Category ID required']);
    exit;
}

$category_id = intval($_POST['category_id']);
$brand_id = isset($_POST['brand_id']) ? intval($_POST['brand_id']) : null;

try {
    // التحقق من توفر اتصال قاعدة البيانات
    if (!isset($conn) || !$conn) {
        throw new Exception('Database connection not available');
    }
    
    // اختبار الاتصال
    if (!$conn->ping()) {
        throw new Exception('Database connection is not active');
    }
    
    // تسجيل الطلب للتصحيح
    error_log("Fetching enhanced models for category_id: " . $category_id . ", brand_id: " . $brand_id);
    
    $models_query = "
        SELECT 
            m.id,
            m.name,
            m.name_ar,
            m.description,
            m.sort_order,
            COUNT(p.id) as products_count,
            MIN(i.price) as min_price,
            MAX(i.price) as max_price
        FROM models m 
        LEFT JOIN products p ON m.id = p.model_id AND p.status = 1
        LEFT JOIN inventory i ON p.id = i.product_id
        WHERE m.series_id = $category_id AND m.status = 1 
    ";
    
    // إضافة فلتر العلامة التجارية إذا كان متوفراً
    if ($brand_id) {
        $models_query .= " AND m.brand_id = $brand_id ";
    }
    
    $models_query .= "
        GROUP BY m.id, m.name, m.name_ar, m.description, m.sort_order
        ORDER BY m.sort_order ASC, m.name ASC
    ";
    
    $models_result = $conn->query($models_query);
    
    // تسجيل عدد النتائج
    error_log("Number of enhanced models found: " . ($models_result ? $models_result->num_rows : 0));
    
    $result = [];
    if($models_result && $models_result->num_rows > 0) {
        while($model = $models_result->fetch_assoc()) {
            // استخدام الاسم العربي إذا كان متاحاً
            $model['display_name'] = !empty($model['name_ar']) ? $model['name_ar'] : $model['name'];
            
            // تنسيق معلومات السعر
            if ($model['min_price'] && $model['max_price']) {
                if ($model['min_price'] == $model['max_price']) {
                    $model['price_range'] = number_format($model['min_price']) . ' IQD';
                } else {
                    $model['price_range'] = number_format($model['min_price']) . ' - ' . number_format($model['max_price']) . ' IQD';
                }
            } else {
                $model['price_range'] = 'السعر غير محدد';
            }
            
            $result[] = $model;
        }
    }
    
    // جلب معلومات الفئة أيضاً
    $category_query = "
        SELECT 
            s.name,
            s.name_ar,
            b.name as brand_name,
            b.name_ar as brand_name_ar
        FROM series s
        LEFT JOIN brands b ON s.brand_id = b.id
        WHERE s.id = $category_id
    ";
    
    $category_result = $conn->query($category_query);
    $category_info = null;
    
    if ($category_result && $category_result->num_rows > 0) {
        $category_data = $category_result->fetch_assoc();
        $category_info = [
            'name' => !empty($category_data['name_ar']) ? $category_data['name_ar'] : $category_data['name'],
            'brand_name' => !empty($category_data['brand_name_ar']) ? $category_data['brand_name_ar'] : $category_data['brand_name']
        ];
    }
    
    // تسجيل النتيجة
    error_log("Enhanced models result: " . json_encode([
        'models_count' => count($result),
        'category_info' => $category_info
    ]));
    
    echo json_encode([
        'success' => true, 
        'models' => $result,
        'category_info' => $category_info,
        'total_count' => count($result)
    ]);
    
} catch(Exception $e) {
    error_log("Database error in get_models_enhanced.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
