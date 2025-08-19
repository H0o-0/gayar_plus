<?php
// ملف AJAX منفصل وبسيط جداً
error_reporting(0);
ini_set('display_errors', 0);

// منع أي output قبل JSON
ob_start();

try {
    require_once('../config.php');
    
    // تنظيف أي output سابق
    ob_clean();
    
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    
    if(!isset($_POST['action'])) {
        echo json_encode(['status' => 'error', 'message' => 'لم يتم تحديد العملية']);
        exit;
    }
    
    $action = $_POST['action'];
    
    switch($action) {
        case 'delete_product':
            $id = intval($_POST['id'] ?? 0);
            if($id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'معرف غير صحيح']);
                exit;
            }
            
            $sql = "DELETE FROM temp_warehouse WHERE id = $id";
            $result = $conn->query($sql);
            
            if($result) {
                if($conn->affected_rows > 0) {
                    echo json_encode(['status' => 'success', 'message' => 'تم ح��ف المنتج بنجاح']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'المنتج غير موجود']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'فشل في الحذف']);
            }
            break;
            
        case 'bulk_delete':
            $ids = $_POST['ids'] ?? [];
            if(empty($ids)) {
                echo json_encode(['status' => 'error', 'message' => 'لم يتم تحديد منتجات']);
                exit;
            }
            
            // تنظيف المعرفات
            $clean_ids = array_map('intval', $ids);
            $clean_ids = array_filter($clean_ids, function($id) { return $id > 0; });
            
            if(empty($clean_ids)) {
                echo json_encode(['status' => 'error', 'message' => 'معرفات غير صحيحة']);
                exit;
            }
            
            $ids_str = implode(',', $clean_ids);
            $sql = "DELETE FROM temp_warehouse WHERE id IN ($ids_str)";
            $result = $conn->query($sql);
            
            if($result) {
                $affected = $conn->affected_rows;
                echo json_encode(['status' => 'success', 'message' => "تم حذف $affected منتج بنجاح"]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'فشل في الحذف المجمع']);
            }
            break;
            
        case 'delete_unclassified':
            $sql = "DELETE FROM temp_warehouse WHERE status = 'unclassified'";
            $result = $conn->query($sql);
            
            if($result) {
                $affected = $conn->affected_rows;
                echo json_encode(['status' => 'success', 'message' => "تم حذف $affected منتج غير مصنف"]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'فشل في حذف غير المصنف']);
            }
            break;
            
        case 'auto_classify':
            $sql = "SELECT id, product_name FROM temp_warehouse WHERE status = 'unclassified' OR suggested_brand IS NULL LIMIT 50";
            $result = $conn->query($sql);
            
            if(!$result) {
                echo json_encode(['status' => 'error', 'message' => 'خطأ في الاستعلام']);
                exit;
            }
            
            $fixed_count = 0;
            $brands = [
                'Apple' => ['iphone', 'apple', 'ايفون', 'ابل'],
                'Samsung' => ['samsung', 'galaxy', 'سامسونج'],
                'Huawei' => ['huawei', 'هواوي'],
                'Xiaomi' => ['xiaomi', 'شاومي'],
                'Oppo' => ['oppo', 'اوبو'],
                'Vivo' => ['vivo', 'فيفو'],
                'LG' => ['lg']
            ];
            
            while($row = $result->fetch_assoc()) {
                $name = strtolower($row['product_name']);
                $brand = null;
                
                foreach($brands as $b => $keywords) {
                    foreach($keywords as $keyword) {
                        if(strpos($name, $keyword) !== false) {
                            $brand = $b;
                            break 2;
                        }
                    }
                }
                
                if($brand) {
                    $update_sql = "UPDATE temp_warehouse SET suggested_brand = '$brand', status = 'classified' WHERE id = " . $row['id'];
                    if($conn->query($update_sql)) {
                        $fixed_count++;
                    }
                }
            }
            
            echo json_encode(['status' => 'success', 'message' => "تم تصنيف $fixed_count منتج", 'fixed_count' => $fixed_count]);
            break;
            
        case 'quick_publish':
            $id = intval($_POST['id'] ?? 0);
            if($id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'معرف غير صحيح']);
                exit;
            }
            
            $check = $conn->query("SELECT id FROM temp_warehouse WHERE id = $id");
            if(!$check || $check->num_rows == 0) {
                echo json_encode(['status' => 'error', 'message' => 'المنتج غير موجود']);
                exit;
            }
            
            echo json_encode([
                'status' => 'success',
                'message' => 'جاري التحويل',
                'redirect' => 'index.php?page=warehouse/edit_product&id=' . $id
            ]);
            break;
            
        default:
            echo json_encode(['status' => 'error', 'message' => 'عملية غير مدعومة']);
    }
    
} catch(Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'خطأ: ' . $e->getMessage()]);
} catch(Error $e) {
    echo json_encode(['status' => 'error', 'message' => 'خطأ نظام: ' . $e->getMessage()]);
}

exit;
?>