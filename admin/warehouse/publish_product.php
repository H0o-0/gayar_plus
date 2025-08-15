<?php
require_once('../config.php');

header('Content-Type: application/json');

if (!isset($_POST['action']) || !isset($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'بيانات مطلوبة مفقودة']);
    exit;
}

$action = $_POST['action'];
$product_id = intval($_POST['id']);

// الحصول على بيانات المنتج من المخزن المؤقت
$product_query = $conn->query("SELECT * FROM temp_warehouse WHERE id = $product_id");
if ($product_query->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'المنتج غير موجود']);
    exit;
}

$temp_product = $product_query->fetch_assoc();

// الحصول على البيانات من النموذج
$product_name = $conn->real_escape_string($_POST['product_name']);
$price = floatval($_POST['price']);
$category_id = intval($_POST['category_id']);
$sub_category_id = intval($_POST['sub_category_id']);
$brand = $conn->real_escape_string($_POST['brand'] ?? '');
$quantity = intval($_POST['quantity'] ?? 100);
$description = $conn->real_escape_string($_POST['description'] ?? '');
$size = $conn->real_escape_string($_POST['size'] ?? 'None');
$unit = $conn->real_escape_string($_POST['unit'] ?? 'pcs');
$status = intval($_POST['status'] ?? 1);
$has_colors = isset($_POST['has_colors']) ? 1 : 0;
$colors = isset($_POST['colors']) ? $_POST['colors'] : [];

// التحقق من صحة البيانات
if (empty($product_name) || $price < 0 || $category_id <= 0 || $sub_category_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'يرجى ملء جميع الحقول المطلوبة']);
    exit;
}

if ($action == 'publish') {
    // نشر المنتج للمتجر الفعلي
    
    // إدراج في جدول products
    $insert_product = "INSERT INTO products (category_id, sub_category_id, product_name, description, status, has_colors) 
                       VALUES ($category_id, $sub_category_id, '$product_name', '$description', $status, $has_colors)";
    
    if ($conn->query($insert_product)) {
        $new_product_id = $conn->insert_id;
        
        // إدراج في جدول inventory
        $insert_inventory = "INSERT INTO inventory (product_id, quantity, unit, price, size) 
                            VALUES ($new_product_id, $quantity, '$unit', $price, '$size')";
        
        if ($conn->query($insert_inventory)) {
            // إضافة الألوان إذا كان المنتج يدعم الألوان
            if ($has_colors && !empty($colors)) {
                foreach ($colors as $color) {
                    $color_name = $conn->real_escape_string($color);
                    $conn->query("INSERT INTO product_colors (product_id, color_name) VALUES ($new_product_id, '$color_name')");
                }
            }
            
            // معالجة الصورة إذا تم رفعها
            $img_path = '';
            if (isset($_FILES['product_img']) && $_FILES['product_img']['tmp_name'] != '') {
                $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['product_img']['name'];
                $move = move_uploaded_file($_FILES['product_img']['tmp_name'], '../../uploads/product_'.$new_product_id.'/'.$fname);
                
                if ($move) {
                    $img_path = 'uploads/product_'.$new_product_id.'/'.$fname;
                    // تحديث مسار الصورة في جدول المنتجات
                    $conn->query("UPDATE products SET img_path = '$img_path' WHERE id = $new_product_id");
                }
            }
            
            // تحديث حالة المنتج في المخزن المؤقت
            $update_temp = "UPDATE temp_warehouse SET 
                           status = 'published',
                           confirmed_brand = '$brand',
                           category_id = $category_id,
                           sub_category_id = $sub_category_id
                           WHERE id = $product_id";
            $conn->query($update_temp);
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'تم نشر المنتج بنجاح',
                'product_id' => $new_product_id
            ]);
        } else {
            // حذف المنتج إذا فشل إدراج المخزون
            $conn->query("DELETE FROM products WHERE id = $new_product_id");
            echo json_encode(['status' => 'error', 'message' => 'فشل في إضافة المخزون: ' . $conn->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل في إضافة المنتج: ' . $conn->error]);
    }
    
} else if ($action == 'save_draft') {
    // حفظ التعديلات في المخزن المؤقت فقط
    
    $update_temp = "UPDATE temp_warehouse SET 
                   product_name = '$product_name',
                   original_price = $price,
                   confirmed_brand = '$brand',
                   category_id = $category_id,
                   sub_category_id = $sub_category_id,
                   status = 'classified'
                   WHERE id = $product_id";
    
    if ($conn->query($update_temp)) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'تم حفظ التعديلات بنجاح'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل في حفظ التعديلات: ' . $conn->error]);
    }
    
} else {
    echo json_encode(['status' => 'error', 'message' => 'إجراء غير مدعوم']);
}
?>
