<?php
require_once('../../config.php');
require_once('device_splitter_enhanced.php');

// تعيين الترميز
header('Content-Type: application/json; charset=utf-8');
mb_internal_encoding('UTF-8');

// معالجة طلبات AJAX
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $response = ['status' => 'error', 'message' => 'إجراء غير معروف'];
    
    switch ($action) {
        case 'split_product':
            // تقسيم منتج واحد
            if (isset($_POST['product_id'])) {
                $product_id = intval($_POST['product_id']);
                $response = split_single_product($conn, $product_id);
            } else {
                $response = ['status' => 'error', 'message' => 'معرف المنتج مطلوب'];
            }
            break;
            
        case 'bulk_split':
            // تقسيم مجموعة منتجات
            if (isset($_POST['product_ids']) && is_array($_POST['product_ids'])) {
                $product_ids = array_map('intval', $_POST['product_ids']);
                $response = bulk_split_products($conn, $product_ids);
            } else {
                $response = ['status' => 'error', 'message' => 'معرفات المنتجات مطلوبة'];
            }
            break;
            
        case 'quick_publish':
            // نشر سريع
            if (isset($_POST['product_id'])) {
                $product_id = intval($_POST['product_id']);
                $response = quick_publish_product($conn, $product_id);
            } else {
                $response = ['status' => 'error', 'message' => 'معرف المنتج مطلوب'];
            }
            break;
            
        case 'bulk_quick_publish':
            // نشر سريع لمجموعة منتجات
            if (isset($_POST['product_ids']) && is_array($_POST['product_ids'])) {
                $product_ids = array_map('intval', $_POST['product_ids']);
                $response = bulk_quick_publish($conn, $product_ids);
            } else {
                $response = ['status' => 'error', 'message' => 'معرفات المنتجات مطلوبة'];
            }
            break;
            
        case 'delete_product':
            // حذف منتج
            if (isset($_POST['product_id'])) {
                $product_id = intval($_POST['product_id']);
                $response = delete_product($conn, $product_id);
            } else {
                $response = ['status' => 'error', 'message' => 'معرف المنتج مطلوب'];
            }
            break;
            
        case 'bulk_delete':
            // حذف مجموعة منتجات
            if (isset($_POST['product_ids']) && is_array($_POST['product_ids'])) {
                $product_ids = array_map('intval', $_POST['product_ids']);
                $response = bulk_delete_products($conn, $product_ids);
            } else {
                $response = ['status' => 'error', 'message' => 'معرفات المنتجات مطلوبة'];
            }
            break;
            
        case 'delete_all_unclassified':
            // حذف كل المنتجات غير المصنفة
            $response = delete_all_unclassified($conn);
            break;
            
        case 'auto_classify':
            // تصنيف تلقائي
            $response = auto_classify_products($conn);
            break;
    }
    
    echo json_encode($response);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'get_brand_stats') {
    $stats_query = "
        SELECT c.category, c.emoji, COUNT(tw.id) as count 
        FROM categories c 
        LEFT JOIN temp_warehouse tw ON c.id = tw.category_id 
        WHERE c.category_type = 'devices' AND c.status = 1
        GROUP BY c.id, c.category, c.emoji 
        HAVING count > 0
        ORDER BY count DESC, c.category
    ";
    
    $result = $conn->query($stats_query);
    $stats = [];
    
    while ($row = $result->fetch_assoc()) {
        $stats[] = [
            'category' => $row['category'],
            'emoji' => $row['emoji'],
            'count' => intval($row['count'])
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($stats);
    exit;
}

/**
 * تقسيم منتج واحد
 */
function split_single_product($conn, $product_id) {
    // جلب بيانات المنتج
    $product_query = $conn->query("SELECT * FROM temp_warehouse WHERE id = $product_id");
    
    if ($product_query->num_rows == 0) {
        return ['status' => 'error', 'message' => 'المنتج غير موجود'];
    }
    
    $product = $product_query->fetch_assoc();
    
    // تقسيم اسم المنتج
    $split_devices = enhanced_split_multiple_devices($product['product_name']);
    
    if (count($split_devices) <= 1) {
        return ['status' => 'error', 'message' => 'هذا المنتج لا يمكن تقسيمه'];
    }
    
    // إدراج كل جهاز منفصل
    $new_products = [];
    foreach ($split_devices as $device_name) {
        // تصنيف الجهاز الجديد
        $analysis = smart_device_classification($device_name);
        
        $suggested_brand = '';
        $suggested_type = '';
        $confidence = 0;
        
        if (!empty($analysis) && is_array($analysis) && count($analysis) > 0) {
            $first_analysis = $analysis[0];
            $suggested_brand = $first_analysis['brand'] ?? '';
            $suggested_type = $first_analysis['type'] ?? '';
            $confidence = $first_analysis['confidence'] ?? 0;
        } else {
            // إذا فشل التصنيف، استخدم الوظائف الأساسية
            $suggested_brand = detect_brand_from_name_enhanced(mb_strtolower($device_name));
            $suggested_type = detect_accessory_type_enhanced(mb_strtolower($device_name));
            $confidence = 0.5;
        }
        
        // تنظيف البيانات للإدراج
        $device_name_escaped = $conn->real_escape_string($device_name);
        $suggested_brand_escaped = $conn->real_escape_string($suggested_brand);
        $suggested_type_escaped = $conn->real_escape_string($suggested_type);
        
        // نسخ البيانات الأخرى من المنتج الأصلي
        $sql = "INSERT INTO temp_warehouse 
                (product_name, original_price, suggested_brand, suggested_type, 
                status, import_batch, category_id, sub_category_id, confidence, 
                created_at, updated_at, raw_data, original_name, is_split)
                VALUES 
                ('$device_name_escaped', {$product['original_price']}, 
                '$suggested_brand_escaped', '$suggested_type_escaped', 
                'classified', '{$product['import_batch']}', 
                {$product['category_id'] ?: 'NULL'}, 
                {$product['sub_category_id'] ?: 'NULL'}, 
                $confidence, 
                NOW(), NOW(), 
                '{$conn->real_escape_string($product['raw_data'])}',
                '{$conn->real_escape_string($product['product_name'])}',
                1)";
        
        if ($conn->query($sql)) {
            $new_products[] = [
                'id' => $conn->insert_id,
                'name' => $device_name
            ];
        }
    }
    
    // حذف المنتج الأصلي
    $conn->query("DELETE FROM temp_warehouse WHERE id = $product_id");
    
    return [
        'status' => 'success',
        'message' => 'تم تقسيم المنتج بنجاح',
        'original_product' => $product['product_name'],
        'new_products' => $new_products,
        'count' => count($new_products)
    ];
}

/**
 * تقسيم مجموعة منتجات
 */
function bulk_split_products($conn, $product_ids) {
    if (empty($product_ids)) {
        return ['status' => 'error', 'message' => 'لم يتم تحديد أي منتجات'];
    }
    
    $ids_str = implode(',', $product_ids);
    $products_query = $conn->query("SELECT * FROM temp_warehouse WHERE id IN ($ids_str)");
    
    $processed = 0;
    $split_count = 0;
    $new_products_count = 0;
    $errors = [];
    
    while ($product = $products_query->fetch_assoc()) {
        $processed++;
        
        // تقسيم اسم المنتج
        $split_devices = enhanced_split_multiple_devices($product['product_name']);
        
        if (count($split_devices) > 1) {
            $split_count++;
            
            // إدراج كل جهاز منفصل
            foreach ($split_devices as $device_name) {
                // تصنيف الجهاز الجديد
                $analysis = smart_device_classification($device_name);
                
                $suggested_brand = '';
                $suggested_type = '';
                $confidence = 0;
                
                if (!empty($analysis) && is_array($analysis) && count($analysis) > 0) {
                    $first_analysis = $analysis[0];
                    $suggested_brand = $first_analysis['brand'] ?? '';
                    $suggested_type = $first_analysis['type'] ?? '';
                    $confidence = $first_analysis['confidence'] ?? 0;
                } else {
                    // إذا فشل التصنيف، استخدم الوظائف الأساسية
                    $suggested_brand = detect_brand_from_name_enhanced(mb_strtolower($device_name));
                    $suggested_type = detect_accessory_type_enhanced(mb_strtolower($device_name));
                    $confidence = 0.5;
                }
                
                // تنظيف البيانات للإدراج
                $device_name_escaped = $conn->real_escape_string($device_name);
                $suggested_brand_escaped = $conn->real_escape_string($suggested_brand);
                $suggested_type_escaped = $conn->real_escape_string($suggested_type);
                
                // نسخ البيانات الأخرى من المنتج الأصلي
                $sql = "INSERT INTO temp_warehouse 
                        (product_name, original_price, suggested_brand, suggested_type, 
                        status, import_batch, category_id, sub_category_id, confidence, 
                        created_at, updated_at, raw_data, original_name, is_split)
                        VALUES 
                        ('$device_name_escaped', {$product['original_price']}, 
                        '$suggested_brand_escaped', '$suggested_type_escaped', 
                        'classified', '{$product['import_batch']}', 
                        {$product['category_id'] ?: 'NULL'}, 
                        {$product['sub_category_id'] ?: 'NULL'}, 
                        $confidence, 
                        NOW(), NOW(), 
                        '{$conn->real_escape_string($product['raw_data'])}',
                        '{$conn->real_escape_string($product['product_name'])}',
                        1)";
                
                if ($conn->query($sql)) {
                    $new_products_count++;
                } else {
                    $errors[] = "خطأ في إدراج المنتج: {$device_name} - {$conn->error}";
                }
            }
            
            // حذف المنتج الأصلي
            $conn->query("DELETE FROM temp_warehouse WHERE id = {$product['id']}");
        }
    }
    
    return [
        'status' => 'success',
        'message' => 'تمت معالجة المنتجات المحددة',
        'processed' => $processed,
        'split_count' => $split_count,
        'new_products_count' => $new_products_count,
        'errors' => $errors
    ];
}

/**
 * نشر سريع لمنتج
 */
function quick_publish_product($conn, $product_id) {
    // تحديث حالة المنتج إلى منشور
    $sql = "UPDATE temp_warehouse SET status = 'published', updated_at = NOW() WHERE id = $product_id";
    
    if ($conn->query($sql)) {
        return [
            'status' => 'success',
            'message' => 'تم نشر المنتج بنجاح',
            'product_id' => $product_id
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'فشل في نشر المنتج: ' . $conn->error
        ];
    }
}

/**
 * نشر سريع لمجموعة منتجات
 */
function bulk_quick_publish($conn, $product_ids) {
    if (empty($product_ids)) {
        return ['status' => 'error', 'message' => 'لم يتم تحديد أي منتجات'];
    }
    
    $ids_str = implode(',', $product_ids);
    $sql = "UPDATE temp_warehouse SET status = 'published', updated_at = NOW() WHERE id IN ($ids_str)";
    
    if ($conn->query($sql)) {
        return [
            'status' => 'success',
            'message' => 'تم نشر المنتجات المحددة بنجاح',
            'count' => $conn->affected_rows
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'فشل في نشر المنتجات: ' . $conn->error
        ];
    }
}

/**
 * حذف منتج
 */
function delete_product($conn, $product_id) {
    $sql = "DELETE FROM temp_warehouse WHERE id = $product_id";
    
    if ($conn->query($sql)) {
        return [
            'status' => 'success',
            'message' => 'تم حذف المنتج بنجاح',
            'product_id' => $product_id
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'فشل في حذف المنتج: ' . $conn->error
        ];
    }
}

/**
 * حذف مجموعة منتجات
 */
function bulk_delete_products($conn, $product_ids) {
    if (empty($product_ids)) {
        return ['status' => 'error', 'message' => 'لم يتم تحديد أي منتجات'];
    }
    
    $ids_str = implode(',', $product_ids);
    $sql = "DELETE FROM temp_warehouse WHERE id IN ($ids_str)";
    
    if ($conn->query($sql)) {
        return [
            'status' => 'success',
            'message' => 'تم حذف المنتجات المحددة بنجاح',
            'count' => $conn->affected_rows
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'فشل في حذف المنتجات: ' . $conn->error
        ];
    }
}

/**
 * حذف كل المنتجات غير المصنفة
 */
function delete_all_unclassified($conn) {
    $sql = "DELETE FROM temp_warehouse WHERE status = 'unclassified'";
    
    if ($conn->query($sql)) {
        return [
            'status' => 'success',
            'message' => 'تم حذف جميع المنتجات غير المصنفة بنجاح',
            'count' => $conn->affected_rows
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'فشل في حذف المنتجات غير المصنفة: ' . $conn->error
        ];
    }
}

/**
 * تصنيف تلقائي للمنتجات
 */
function auto_classify_products($conn) {
    // تحديث المنتجات غير المصنفة التي لديها اقتراحات للعلامة التجارية أو النوع
    $sql = "UPDATE temp_warehouse 
            SET status = 'classified' 
            WHERE status = 'unclassified' 
            AND (suggested_brand IS NOT NULL OR suggested_type IS NOT NULL)";
    
    $conn->query($sql);
    $updated_count = $conn->affected_rows;
    
    // محاولة تصنيف المنتجات المتبقية غير المصنفة
    $unclassified_query = $conn->query("SELECT id, product_name FROM temp_warehouse WHERE status = 'unclassified'");
    
    $classified_count = 0;
    while ($product = $unclassified_query->fetch_assoc()) {
        $analysis = smart_device_classification($product['product_name']);
        
        if (!empty($analysis) && is_array($analysis) && count($analysis) > 0) {
            $first_analysis = $analysis[0];
            $suggested_brand = $first_analysis['brand'] ?? '';
            $suggested_type = $first_analysis['type'] ?? '';
            $confidence = $first_analysis['confidence'] ?? 0;
            
            if (!empty($suggested_brand) || !empty($suggested_type)) {
                $suggested_brand_escaped = $conn->real_escape_string($suggested_brand);
                $suggested_type_escaped = $conn->real_escape_string($suggested_type);
                
                $update_sql = "UPDATE temp_warehouse 
                                SET suggested_brand = '$suggested_brand_escaped', 
                                    suggested_type = '$suggested_type_escaped', 
                                    confidence = $confidence, 
                                    status = 'classified', 
                                    updated_at = NOW() 
                                WHERE id = {$product['id']}";
                
                if ($conn->query($update_sql)) {
                    $classified_count++;
                }
            }
        }
    }
    
    return [
        'status' => 'success',
        'message' => 'تم تصنيف المنتجات بنجاح',
        'updated_count' => $updated_count,
        'newly_classified' => $classified_count
    ];
}
?>