<?php
require_once('../../config.php');

header('Content-Type: application/json');

// Helper: check if column exists in table
function columnExists($conn, $table, $column){
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $res = $conn->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
    return $res && $res->num_rows > 0;
}

if (!isset($_POST['action']) || !isset($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'بيانات مطلوبة مفقودة']);
    exit;
}

$action = $_POST['action'];
$product_id = intval($_POST['id']);

// الحصول على بيانات المنتج من المخزن المؤقت
$product_query = $conn->query("SELECT * FROM temp_warehouse WHERE id = {$product_id}");
if ($product_query->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'المنتج غير موجود']);
    exit;
}

$temp_product = $product_query->fetch_assoc();

// الحصول على البيانات من النموذج
$product_name = $conn->real_escape_string($_POST['product_name'] ?? '');
$raw_price = $_POST['price'] ?? '0';
// تحويل الأرقام العربية إلى إنجليزية ومعالجة الفواصل وعلامة الكسور
$raw_price = strtr($raw_price, [
    '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9',
    '٫'=>'.','٬'=>'',','=>''
]);
$raw_price = preg_replace('/[^\d\.-]/','',$raw_price);
$price = (float)$raw_price;
$category_id = intval($_POST['category_id'] ?? 0);
$sub_category_id = intval($_POST['sub_category_id'] ?? 0);
$brand = $conn->real_escape_string($_POST['brand'] ?? '');
$quantity = intval($_POST['quantity'] ?? 100);
$description = $conn->real_escape_string($_POST['description'] ?? '');
$size = $conn->real_escape_string($_POST['size'] ?? 'None');
$unit = $conn->real_escape_string($_POST['unit'] ?? 'pcs');
$status = intval($_POST['status'] ?? 1);
$has_colors = isset($_POST['has_colors']) ? 1 : 0;
// اقرأ الحقول الفعلية من النموذج: color_names[] و color_codes[]
$color_names = isset($_POST['color_names']) && is_array($_POST['color_names']) ? $_POST['color_names'] : [];
$color_codes = isset($_POST['color_codes']) && is_array($_POST['color_codes']) ? $_POST['color_codes'] : [];

// التحقق من صحة البيانات
if (empty($product_name) || $price < 0 || $category_id <= 0 || $sub_category_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'يرجى ملء جميع الحقول المطلوبة']);
    exit;
}

if ($action === 'publish') {
    // التحقق من الأعمدة الاختيارية
    $has_has_colors_col = columnExists($conn, 'products', 'has_colors');
    $has_img_path_col = columnExists($conn, 'products', 'img_path');

    // بناء استعلام إدراج المنتج حسب الأعمدة المتوفرة
    if ($has_has_colors_col) {
        $insert_product = "INSERT INTO products (category_id, sub_category_id, product_name, description, status, has_colors) "
                        . "VALUES ({$category_id}, {$sub_category_id}, '{$product_name}', '{$description}', {$status}, {$has_colors})";
    } else {
        $insert_product = "INSERT INTO products (category_id, sub_category_id, product_name, description, status) "
                        . "VALUES ({$category_id}, {$sub_category_id}, '{$product_name}', '{$description}', {$status})";
    }

    if ($conn->query($insert_product)) {
        $new_product_id = $conn->insert_id;

        // إدراج في جدول inventory
        $insert_inventory = "INSERT INTO inventory (product_id, quantity, unit, price, size) "
                           . "VALUES ({$new_product_id}, {$quantity}, '{$unit}', {$price}, '{$size}')";
        
        if ($conn->query($insert_inventory)) {
            // إضافة الألوان إذا كان المنتج يدعم الألوان
            if ($has_colors && !empty($color_names)) {
                foreach ($color_names as $idx => $cname) {
                    $cname = $conn->real_escape_string(trim($cname));
                    if ($cname === '') continue;
                    $ccode = isset($color_codes[$idx]) ? $conn->real_escape_string(trim($color_codes[$idx])) : null;
                    if ($ccode) {
                        $conn->query("INSERT INTO product_colors (product_id, color_name, color_code) VALUES ({$new_product_id}, '{$cname}', '{$ccode}')");
                    } else {
                        $conn->query("INSERT INTO product_colors (product_id, color_name) VALUES ({$new_product_id}, '{$cname}')");
                    }
                }
            }
            
            // معالجة الصورة إذا تم رفعها
            if (isset($_FILES['product_img']) && is_uploaded_file($_FILES['product_img']['tmp_name'])) {
                $dir = '../../uploads/product_' . $new_product_id;
                if (!is_dir($dir)) {
                    @mkdir($dir, 0777, true);
                }
                $safeName = preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($_FILES['product_img']['name']));
                $fname = time() . '_' . $safeName;
                $target = $dir . '/' . $fname;
                
                if (@move_uploaded_file($_FILES['product_img']['tmp_name'], $target)) {
                    $img_path = 'uploads/product_' . $new_product_id . '/' . $fname; // مسار نسبي من جذر الموقع
                    if ($has_img_path_col) {
                        // تحديث مسار الصورة إن كان العمود موجودًا
                        $conn->query("UPDATE products SET img_path = '{$img_path}' WHERE id = {$new_product_id}");
                    }
                }
            }
            
            // تحديث حالة المنتج في المخزن المؤقت
            $update_temp = "UPDATE temp_warehouse SET "
                         . "status = 'published', "
                         . "confirmed_brand = '{$brand}', "
                         . "category_id = {$category_id}, "
                         . "sub_category_id = {$sub_category_id} "
                         . "WHERE id = {$product_id}";
            $conn->query($update_temp);
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'تم نشر المنتج بنجاح',
                'product_id' => $new_product_id
            ]);
        } else {
            // حذف المنتج إذا فشل إدراج المخزون
            $conn->query("DELETE FROM products WHERE id = {$new_product_id}");
            echo json_encode(['status' => 'error', 'message' => 'فشل في إضافة المخزون: ' . $conn->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل في إضافة المنتج: ' . $conn->error]);
    }

} elseif ($action === 'save_draft') {
    // حفظ التعديلات في المخزن المؤقت فقط
    $update_temp = "UPDATE temp_warehouse SET "
                 . "product_name = '{$product_name}', "
                 . "original_price = {$price}, "
                 . "confirmed_brand = '{$brand}', "
                 . "category_id = {$category_id}, "
                 . "sub_category_id = {$sub_category_id}, "
                 . "status = 'classified' "
                 . "WHERE id = {$product_id}";
    
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