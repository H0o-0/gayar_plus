<?php
require_once('../config.php');

header('Content-Type: application/json');

if (!isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'لم يتم تحديد العملية']);
    exit;
}

$action = $_POST['action'];

switch ($action) {
    case 'delete_product':
        deleteProduct();
        break;
    case 'publish_product':
        publishProduct();
        break;
    case 'quick_publish':
        quickPublish();
        break;
    case 'republish_product':
        republishProduct();
        break;
    case 'bulk_delete':
        bulkDelete();
        break;
    case 'bulk_publish':
        bulkPublish();
        break;
    case 'delete_unclassified':
        deleteUnclassified();
        break;
    case 'get_subcategories':
        getSubcategories();
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'عملية غير مدعومة']);
}

function deleteProduct() {
    global $conn;
    
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'معرف غير صحيح']);
        return;
    }
    
    $sql = "DELETE FROM temp_warehouse WHERE id = $id";
    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success', 'message' => 'تم حذف المنتج بنجاح']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل في حذف المنتج: ' . $conn->error]);
    }
}

function publishProduct() {
    global $conn;
    
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'معرف غير صحيح']);
        return;
    }
    
    // الحصول على بيانات المنتج من المخزن المؤقت
    $product_query = $conn->query("SELECT * FROM temp_warehouse WHERE id = $id");
    if ($product_query->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'المنتج غير موجود']);
        return;
    }
    
    $product = $product_query->fetch_assoc();
    
    // التحقق من وجود التصنيفات
    if (!$product['category_id'] || !$product['sub_category_id']) {
        echo json_encode(['status' => 'error', 'message' => 'يجب تصنيف المنتج أولاً']);
        return;
    }
    
    // إضافة المنتج للنظام الأساسي
    $product_name = $conn->real_escape_string($product['product_name']);
    $category_id = $product['category_id'];
    $sub_category_id = $product['sub_category_id'];
    
    // إدراج في جدول products
    $insert_product = "INSERT INTO products (category_id, sub_category_id, product_name, description, status) 
                       VALUES ($category_id, $sub_category_id, '$product_name', 'منتج مستورد من المخزن المؤقت', 1)";
    
    if ($conn->query($insert_product)) {
        $new_product_id = $conn->insert_id;
        
        // إدراج في جدول inventory
        $price = $product['original_price'];
        $insert_inventory = "INSERT INTO inventory (product_id, quantity, unit, price, size) 
                            VALUES ($new_product_id, 100, 'pcs', $price, 'None')";
        
        if ($conn->query($insert_inventory)) {
            // تحديث حالة المنتج في المخزن المؤقت
            $conn->query("UPDATE temp_warehouse SET status = 'published' WHERE id = $id");
            
            echo json_encode(['status' => 'success', 'message' => 'تم نشر المنتج بنجاح']);
        } else {
            // حذف المنتج إذا فشل إدراج المخزون
            $conn->query("DELETE FROM products WHERE id = $new_product_id");
            echo json_encode(['status' => 'error', 'message' => 'فشل في إضافة المخزون: ' . $conn->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل في إضافة المنتج: ' . $conn->error]);
    }
}

/**
 * النشر السريع - تحويل المستخدم إلى صفحة التحرير بدلاً من النشر المباشر
 */
function quickPublish() {
    global $conn;
    
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'معرف غير صحيح']);
        return;
    }
    
    // التحقق من وجود المنتج
    $product_query = $conn->query("SELECT * FROM temp_warehouse WHERE id = $id");
    if ($product_query->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'المنتج غير موجود']);
        return;
    }
    
    // إرجاع رابط صفحة التحرير
    echo json_encode([
        'status' => 'success',
        'message' => 'جاري التحويل إلى صفحة التحرير',
        'redirect' => 'index.php?page=warehouse/edit_product&id=' . $id
    ]);
}

/**
 * إعادة نشر منتج تم نشره سابقاً
 */
function republishProduct() {
    global $conn;
    
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'معرف غير صحيح']);
        return;
    }
    
    // التحقق من وجود المنتج
    $product_query = $conn->query("SELECT * FROM temp_warehouse WHERE id = $id");
    if ($product_query->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'المنتج غير موجود']);
        return;
    }
    
    $product = $product_query->fetch_assoc();
    
    // التحقق من أن المنتج منشور بالفعل
    if ($product['status'] != 'published') {
        echo json_encode(['status' => 'error', 'message' => 'هذا المنتج غير منشور بعد']);
        return;
    }
    
    // تحويل المستخدم إلى صفحة التحرير
    echo json_encode([
        'status' => 'success',
        'message' => 'جاري التحويل إلى صفحة التحرير',
        'redirect' => 'index.php?page=warehouse/edit_product&id=' . $id
    ]);
}

/**
 * الحصول على الفئات الفرعية بناءً على الفئة الرئيسية
 */
function getSubcategories() {
    global $conn;
    
    $category_id = intval($_POST['category_id'] ?? 0);
    if ($category_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'معرف الفئة غير صحيح']);
        return;
    }
    
    $subcategories = [];
    $query = $conn->query("SELECT id, sub_category FROM sub_categories WHERE parent_id = $category_id AND status = 1");
    
    while ($row = $query->fetch_assoc()) {
        $subcategories[$row['id']] = $row['sub_category'];
    }
    
    echo json_encode([
        'status' => 'success',
        'subcategories' => $subcategories
    ]);
}

function bulkDelete() {
    global $conn;

    $ids = $_POST['ids'] ?? [];
    if (empty($ids) || !is_array($ids)) {
        echo json_encode(['status' => 'error', 'message' => 'لم يتم تحديد منتجات للحذف']);
        return;
    }
    
    // تسجيل العملية للتشخيص
    error_log("bulkDelete called with IDs: " . json_encode($ids));

    // تنظيف وفلترة المعرفات
    $ids = array_filter(array_map('intval', $ids), function($id) {
        return $id > 0;
    });

    if (empty($ids)) {
        echo json_encode(['status' => 'error', 'message' => 'معرفات غير صحيحة']);
        return;
    }

    $ids_str = implode(',', $ids);

    // التحقق من وجود المنتجات أولاً
    $check_sql = "SELECT COUNT(*) as count FROM temp_warehouse WHERE id IN ($ids_str)";
    $check_result = $conn->query($check_sql);
    $existing_count = $check_result->fetch_assoc()['count'];

    if ($existing_count == 0) {
        echo json_encode(['status' => 'error', 'message' => 'لم يتم العثور على المنتجات المحددة']);
        return;
    }

    // تنفيذ الحذف
    $sql = "DELETE FROM temp_warehouse WHERE id IN ($ids_str)";
    if ($conn->query($sql)) {
        $affected = $conn->affected_rows;

        // تحديث الإحصائيات
        updateWarehouseStats($conn);

        echo json_encode([
            'status' => 'success',
            'message' => "تم حذف $affected منتج بنجاح من أصل " . count($ids) . " محدد"
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل في الحذف: ' . $conn->error]);
    }
}

// دالة مساعدة لتحديث الإحصائيات
function updateWarehouseStats($conn) {
    try {
        // التحقق من وجود الجدول
        $table_check = $conn->query("SHOW TABLES LIKE 'warehouse_stats'");
        if ($table_check->num_rows == 0) {
            error_log("warehouse_stats table does not exist");
            return; // لا نحاول تحديث جدول غير موجود
        }
        
        // حذف الإحصائيات القديمة
        $conn->query("DELETE FROM warehouse_stats");

        // إعادة حساب الإحصائيات
        $stats_query = "SELECT suggested_brand, COUNT(*) as count
                        FROM temp_warehouse
                        WHERE suggested_brand IS NOT NULL
                        GROUP BY suggested_brand";

        $stats_result = $conn->query($stats_query);
        if ($stats_result) {
            while ($row = $stats_result->fetch_assoc()) {
                $brand = $conn->real_escape_string($row['suggested_brand']);
                $count = $row['count'];

                $insert_stats = "INSERT INTO warehouse_stats (brand, product_type, count)
                                VALUES ('$brand', 'General', $count)";
                $conn->query($insert_stats);
            }
        }
    } catch (Exception $e) {
        error_log("Error updating warehouse stats: " . $e->getMessage());
    }
}

function bulkPublish() {
    global $conn;
    
    $ids = $_POST['ids'] ?? [];
    if (empty($ids) || !is_array($ids)) {
        echo json_encode(['status' => 'error', 'message' => 'لم يتم تحديد منتجات للنشر']);
        return;
    }
    
    $ids = array_map('intval', $ids);
    $success_count = 0;
    $error_count = 0;
    
    // تسجيل العملية للتشخيص
    error_log("bulkPublish called with IDs: " . json_encode($ids));
    
    foreach ($ids as $id) {
        try {
            // محاكاة نشر كل منتج
            $_POST['id'] = $id;
            ob_start();
            publishProduct();
            $result = ob_get_clean();
            $response = json_decode($result, true);
            
            if ($response && $response['status'] == 'success') {
                $success_count++;
            } else {
                $error_count++;
                error_log("Failed to publish product ID $id: " . ($result ?: 'No response'));
            }
        } catch (Exception $e) {
            $error_count++;
            error_log("Exception publishing product ID $id: " . $e->getMessage());
        }
    }
    
    if ($success_count > 0) {
        echo json_encode(['status' => 'success', 'message' => "تم نشر $success_count منتج بنجاح" . ($error_count > 0 ? " و فشل $error_count منتج" : "")]);
    } else {
        echo json_encode(['status' => 'error', 'message' => "فشل في نشر جميع المنتجات"]);
    }
}

function deleteUnclassified() {
    global $conn;

    // حذف جميع المنتجات غير المصنفة
    $sql = "DELETE FROM temp_warehouse WHERE status = 'unclassified'";

    if ($conn->query($sql)) {
        $affected = $conn->affected_rows;

        // تحديث الإحصائيات
        updateWarehouseStats($conn);

        echo json_encode([
            'status' => 'success',
            'message' => "تم حذف $affected منتج غير مصنف بنجاح"
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل في الحذف: ' . $conn->error]);
    }
}
?>
