<?php
/**
 * جلب جميع بيانات القائمة المتدرجة دفعة واحدة
 * Optimized single request for all mega menu data
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

try {
    // التحقق من توفر اتصال قاعدة البيانات
    if (!isset($conn) || !$conn) {
        throw new Exception('Database connection not available');
    }
    
    // اختبار الاتصال
    if (!$conn->ping()) {
        throw new Exception('Database connection is not active');
    }
    
    // تسجيل بداية عملية جلب البيانات
    $start_time = microtime(true);
    error_log("Starting mega menu data fetch");
    
    // جلب العلامات التجارية مع عدد الفئات
    $brands_query = "
        SELECT 
            b.id,
            b.name,
            b.name_ar,
            b.description,
            b.sort_order,
            COUNT(DISTINCT s.id) as categories_count,
            COUNT(DISTINCT m.id) as models_count,
            COUNT(DISTINCT p.id) as products_count
        FROM brands b 
        LEFT JOIN series s ON b.id = s.brand_id AND s.status = 1
        LEFT JOIN models m ON s.id = m.series_id AND m.status = 1
        LEFT JOIN products p ON m.id = p.model_id AND p.status = 1
        WHERE b.status = 1 
        GROUP BY b.id, b.name, b.name_ar, b.description, b.sort_order
        ORDER BY b.sort_order ASC, b.name ASC
    ";
    
    $brands_result = $conn->query($brands_query);
    $brands_data = [];
    
    if ($brands_result && $brands_result->num_rows > 0) {
        while ($brand = $brands_result->fetch_assoc()) {
            $brand['display_name'] = !empty($brand['name_ar']) ? $brand['name_ar'] : $brand['name'];
            $brands_data[] = $brand;
        }
    }
    
    // جلب جميع الفئات مع تجميعها حسب العلامة التجارية
    $categories_query = "
        SELECT 
            s.id,
            s.name,
            s.name_ar,
            s.brand_id,
            s.sort_order,
            COUNT(DISTINCT m.id) as models_count,
            COUNT(DISTINCT p.id) as products_count
        FROM series s 
        LEFT JOIN models m ON s.id = m.series_id AND m.status = 1
        LEFT JOIN products p ON m.id = p.model_id AND p.status = 1
        WHERE s.status = 1 
        GROUP BY s.id, s.name, s.name_ar, s.brand_id, s.sort_order
        ORDER BY s.sort_order ASC, s.name ASC
    ";
    
    $categories_result = $conn->query($categories_query);
    $categories_data = [];
    
    if ($categories_result && $categories_result->num_rows > 0) {
        while ($category = $categories_result->fetch_assoc()) {
            $category['display_name'] = !empty($category['name_ar']) ? $category['name_ar'] : $category['name'];
            
            // تجميع الفئات حسب العلامة التجارية
            if (!isset($categories_data[$category['brand_id']])) {
                $categories_data[$category['brand_id']] = [];
            }
            $categories_data[$category['brand_id']][] = $category;
        }
    }
    
    // جلب جميع الموديلات مع تجميعها حسب الفئة
    $models_query = "
        SELECT 
            m.id,
            m.name,
            m.name_ar,
            m.series_id,
            m.sort_order,
            COUNT(DISTINCT p.id) as products_count,
            MIN(i.price) as min_price,
            MAX(i.price) as max_price
        FROM models m 
        LEFT JOIN products p ON m.id = p.model_id AND p.status = 1
        LEFT JOIN inventory i ON p.id = i.product_id
        WHERE m.status = 1 
        GROUP BY m.id, m.name, m.name_ar, m.series_id, m.sort_order
        ORDER BY m.sort_order ASC, m.name ASC
    ";
    
    $models_result = $conn->query($models_query);
    $models_data = [];
    
    if ($models_result && $models_result->num_rows > 0) {
        while ($model = $models_result->fetch_assoc()) {
            $model['display_name'] = !empty($model['name_ar']) ? $model['name_ar'] : $model['name'];
            
            // تنسيق نطاق الأسعار
            if ($model['min_price'] && $model['max_price']) {
                if ($model['min_price'] == $model['max_price']) {
                    $model['price_range'] = number_format($model['min_price']) . ' IQD';
                } else {
                    $model['price_range'] = number_format($model['min_price']) . ' - ' . number_format($model['max_price']) . ' IQD';
                }
            } else {
                $model['price_range'] = 'السعر غير محدد';
            }
            
            // تجميع الموديلات حسب الفئة
            if (!isset($models_data[$model['series_id']])) {
                $models_data[$model['series_id']] = [];
            }
            $models_data[$model['series_id']][] = $model;
        }
    }
    
    // حساب الوقت المستغرق
    $end_time = microtime(true);
    $execution_time = round(($end_time - $start_time) * 1000, 2);
    
    // إحصائيات النتائج
    $stats = [
        'total_brands' => count($brands_data),
        'total_categories' => array_sum(array_map('count', $categories_data)),
        'total_models' => array_sum(array_map('count', $models_data)),
        'execution_time_ms' => $execution_time
    ];
    
    // تسجيل النتائج والأداء
    error_log("Mega menu data loaded successfully: " . json_encode($stats));
    
    echo json_encode([
        'success' => true,
        'data' => [
            'brands' => $brands_data,
            'categories' => $categories_data,
            'models' => $models_data
        ],
        'stats' => $stats,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch(Exception $e) {
    error_log("Database error in get_mega_menu_data.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
