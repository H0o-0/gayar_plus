<?php
/**
 * أداة تقسيم البيانات الموجودة في المخزن
 * تقوم بتقسيم الملحقات المتعددة الموجودة حالياً في قاعدة البيانات
 */

require_once('../../config.php');
require_once('device_splitter_enhanced.php');

// تعيين الترميز
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

// معالجة طلب التقسيم
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'split_existing') {
    
    // جلب جميع البيانات من temp_warehouse
    $existing_products = $conn->query("SELECT * FROM temp_warehouse ORDER BY id ASC");
    
    $processed_count = 0;
    $split_count = 0;
    $new_products_count = 0;
    $errors = [];
    
    // إنشاء جدول مؤقت للبيانات الجديدة
    $conn->query("CREATE TEMPORARY TABLE temp_split_products LIKE temp_warehouse");
    
    while ($product = $existing_products->fetch_assoc()) {
        $processed_count++;
        
        // تقسيم اسم المنتج
        $split_devices = enhanced_split_multiple_devices($product['product_name']);
        
        if (count($split_devices) > 1) {
            $split_count++;
            
            // إدراج كل جهاز منفصل
            foreach ($split_devices as $device_name) {
                // تصنيف الجهاز الجديد
                $brand = detect_brand_from_name_enhanced(mb_strtolower($device_name));
                $type = detect_accessory_type_enhanced(mb_strtolower($device_name));
                
                // تنظيف البيانات
                $device_name_escaped = $conn->real_escape_string($device_name);
                $suggested_brand_escaped = $conn->real_escape_string($brand ?? '');
                $suggested_type_escaped = $conn->real_escape_string($type ?? '');
                $original_name_escaped = $conn->real_escape_string($product['product_name']);
                $import_batch_escaped = $conn->real_escape_string($product['import_batch'] ?? '');
                $raw_data_escaped = $conn->real_escape_string($product['raw_data'] ?? '');
                
                $status = (!empty($brand) || !empty($type)) ? 'classified' : 'unclassified';
                
                $sql = "INSERT INTO temp_split_products 
                        (product_name, original_price, suggested_brand, suggested_type, status, import_batch, raw_data, confidence, created_at, original_name, is_split)
                        VALUES 
                        ('$device_name_escaped', {$product['original_price']}, '$suggested_brand_escaped', '$suggested_type_escaped', '$status', '$import_batch_escaped', '$raw_data_escaped', 0.8, '{$product['created_at']}', '$original_name_escaped', 1)";
                
                if ($conn->query($sql)) {
                    $new_products_count++;
                } else {
                    $errors[] = "خطأ في إدراج: " . $device_name;
                }
            }
        } else {
            // إدراج المنتج كما هو (لم يتم تقسيمه)
            $product_name_escaped = $conn->real_escape_string($product['product_name']);
            $suggested_brand_escaped = $conn->real_escape_string($product['suggested_brand'] ?? '');
            $suggested_type_escaped = $conn->real_escape_string($product['suggested_type'] ?? '');
            $import_batch_escaped = $conn->real_escape_string($product['import_batch'] ?? '');
            $raw_data_escaped = $conn->real_escape_string($product['raw_data'] ?? '');
            
            $sql = "INSERT INTO temp_split_products 
                    (product_name, original_price, suggested_brand, suggested_type, status, import_batch, raw_data, confidence, created_at, original_name, is_split)
                    VALUES 
                    ('$product_name_escaped', {$product['original_price']}, '$suggested_brand_escaped', '$suggested_type_escaped', '{$product['status']}', '$import_batch_escaped', '$raw_data_escaped', {$product['confidence']}, '{$product['created_at']}', '$product_name_escaped', 0)";
            
            if ($conn->query($sql)) {
                $new_products_count++;
            } else {
                $errors[] = "خطأ في إدراج: " . $product['product_name'];
            }
        }
    }
    
    // إذا كانت العملية ناجحة، استبدل البيانات القديمة بالجديدة
    if (count($errors) == 0) {
        $conn->query("DELETE FROM temp_warehouse");
        $conn->query("INSERT INTO temp_warehouse SELECT * FROM temp_split_products");
        
        $success_message = "تم تقسيم البيانات بنجاح!<br>";
        $success_message .= "المنتجات المعالجة: $processed_count<br>";
        $success_message .= "المنتجات المقسمة: $split_count<br>";
        $success_message .= "إجمالي المنتجات الجديدة: $new_products_count";
    } else {
        $error_message = "حدثت أخطاء أثناء التقسيم:<br>" . implode('<br>', $errors);
    }
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقسيم البيانات الموجودة</title>
    <link rel="stylesheet" href="../../plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header { 
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); 
            color: white; 
            padding: 30px 0; 
            margin-bottom: 30px;
        }
        .preview-item {
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .split-preview {
            background: #e8f5e8;
            padding: 10px;
            margin: 5px 0;
            border-radius: 3px;
            border-left: 3px solid #28a745;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="container">
        <h1 class="text-center">
            <i class="fas fa-cut"></i>
            تقسيم البيانات الموجودة
        </h1>
        <p class="text-center lead">تقسيم الملحقات المتعددة الموجودة حالياً في المخزن</p>
    </div>
</div>

<div class="container">
    
    <?php if (isset($success_message)): ?>
    <div class="alert alert-success">
        <h4><i class="fas fa-check-circle"></i> تمت العملية بنجاح!</h4>
        <?php echo $success_message; ?>
        <div class="mt-3">
            <a href="index.php?page=warehouse" class="btn btn-primary">
                <i class="fas fa-eye"></i> عرض النتائج في المخزن
            </a>
        </div>
    </div>
    <?php elseif (isset($error_message)): ?>
    <div class="alert alert-danger">
        <h4><i class="fas fa-exclamation-triangle"></i> حدثت أخطاء!</h4>
        <?php echo $error_message; ?>
    </div>
    <?php endif; ?>
    
    <?php
    // عرض معاينة للبيانات الحالية
    $current_products = $conn->query("SELECT product_name, COUNT(*) as count FROM temp_warehouse GROUP BY product_name ORDER BY count DESC LIMIT 10");
    $total_current = $conn->query("SELECT COUNT(*) as total FROM temp_warehouse")->fetch_assoc()['total'];
    
    echo '<div class="alert alert-info">';
    echo '<h4><i class="fas fa-info-circle"></i> الوضع الحالي</h4>';
    echo '<p>إجمالي المنتجات في المخزن: <strong>' . $total_current . '</strong></p>';
    echo '</div>';
    
    echo '<div class="card">';
    echo '<div class="card-header">';
    echo '<h5>معاينة البيانات الحالية (أول 10 منتجات)</h5>';
    echo '</div>';
    echo '<div class="card-body">';
    
    while ($product = $current_products->fetch_assoc()) {
        echo '<div class="preview-item">';
        echo '<strong>الاسم الحالي:</strong> ' . htmlspecialchars($product['product_name']) . '<br>';
        echo '<small class="text-muted">العدد: ' . $product['count'] . '</small>';
        
        // عرض معاينة التقسيم
        $split_preview = enhanced_split_multiple_devices($product['product_name']);
        if (count($split_preview) > 1) {
            echo '<div class="mt-2">';
            echo '<strong class="text-success">سيتم تقسيمه إلى ' . count($split_preview) . ' منتجات:</strong>';
            foreach ($split_preview as $split_item) {
                echo '<div class="split-preview">';
                echo '<i class="fas fa-arrow-right text-success"></i> ' . htmlspecialchars($split_item);
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<div class="mt-2">';
            echo '<span class="badge badge-secondary">لن يتم تقسيمه</span>';
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    echo '</div>';
    echo '</div>';
    
    // إحصائيات متوقعة
    $products_to_split = $conn->query("SELECT product_name FROM temp_warehouse");
    $expected_total = 0;
    $expected_split = 0;
    
    while ($product = $products_to_split->fetch_assoc()) {
        $split_count = count(enhanced_split_multiple_devices($product['product_name']));
        $expected_total += $split_count;
        if ($split_count > 1) {
            $expected_split++;
        }
    }
    
    echo '<div class="alert alert-warning">';
    echo '<h4><i class="fas fa-calculator"></i> الإحصائيات المتوقعة</h4>';
    echo '<div class="row">';
    echo '<div class="col-md-3">';
    echo '<div class="text-center">';
    echo '<h3 class="text-primary">' . $total_current . '</h3>';
    echo '<p>المنتجات الحالية</p>';
    echo '</div>';
    echo '</div>';
    echo '<div class="col-md-3">';
    echo '<div class="text-center">';
    echo '<h3 class="text-success">' . $expected_total . '</h3>';
    echo '<p>المنتجات بعد التقسيم</p>';
    echo '</div>';
    echo '</div>';
    echo '<div class="col-md-3">';
    echo '<div class="text-center">';
    echo '<h3 class="text-info">' . $expected_split . '</h3>';
    echo '<p>منتجات ستقسم</p>';
    echo '</div>';
    echo '</div>';
    echo '<div class="col-md-3">';
    echo '<div class="text-center">';
    echo '<h3 class="text-warning">' . ($expected_total - $total_current) . '</h3>';
    echo '<p>منتجات إضافية</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    ?>
    
    <div class="text-center mt-4">
        <form method="POST" style="display: inline;">
            <input type="hidden" name="action" value="split_existing">
            <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('هل أنت متأكد من تقسيم البيانات الموجودة؟ هذا الإجراء سيغير البيانات في المخزن.')">
                <i class="fas fa-cut"></i> تقسيم البيانات الموجودة
            </button>
        </form>
        
        <a href="index.php?page=warehouse" class="btn btn-secondary btn-lg ml-2">
            <i class="fas fa-arrow-left"></i> العودة للمخزن
        </a>
    </div>
    
</div>

<script src="../../plugins/jquery/jquery.min.js"></script>
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
