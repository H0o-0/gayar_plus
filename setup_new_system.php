<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(600); // 10 minutes

require_once('initialize.php');

echo "<!DOCTYPE html>
<html lang='ar' dir='rtl'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>إعداد النظام الجديد - متجر ملحقات الهواتف</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .step-card { margin-bottom: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .step-header { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; }
        .success-msg { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error-msg { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning-msg { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .code-block { background: #f8f9fa; border: 1px solid #e9ecef; font-family: monospace; }
    </style>
</head>
<body>";

echo "<div class='container mt-4'>";
echo "<h1 class='text-center mb-4'><i class='fas fa-cog fa-spin'></i> إعداد النظام الجديد لمتجر ملحقات الهواتف</h1>";

// الخطوة 1: إنشاء الجداول الجديدة
echo "<div class='card step-card'>";
echo "<div class='card-header step-header'>";
echo "<h3><i class='fas fa-database'></i> الخطوة 1: إنشاء هيكل قاعدة البيانات الجديد</h3>";
echo "</div>";
echo "<div class='card-body'>";

try {
    $sql_file = 'database/create_new_structure.sql';
    if (!file_exists($sql_file)) {
        throw new Exception("ملف السكريبت غير موجود");
    }
    
    $sql_content = file_get_contents($sql_file);
    $statements = array_filter(array_map('trim', explode(';', $sql_content)));
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) continue;
        
        try {
            if ($conn->query($statement)) {
                $success_count++;
                echo "<div class='alert success-msg mb-1 py-1'><small>✓ " . substr($statement, 0, 80) . "...</small></div>";
            } else {
                throw new Exception($conn->error);
            }
        } catch (Exception $e) {
            $error_count++;
            echo "<div class='alert error-msg mb-1 py-1'><small>✗ خطأ: " . $e->getMessage() . "</small></div>";
        }
    }
    
    echo "<div class='alert " . ($error_count == 0 ? 'success-msg' : 'warning-msg') . " mt-3'>";
    echo "<h5>النتائج:</h5>";
    echo "<p><strong>نجح:</strong> $success_count | <strong>فشل:</strong> $error_count</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='alert error-msg'><strong>خطأ:</strong> " . $e->getMessage() . "</div>";
}

echo "</div></div>";

// الخطوة 2: ترحيل البيانات
echo "<div class='card step-card'>";
echo "<div class='card-header step-header'>";
echo "<h3><i class='fas fa-exchange-alt'></i> الخطوة 2: ترحيل البيانات</h3>";
echo "</div>";
echo "<div class='card-body'>";

try {
    // فحص وجود البيانات القديمة
    $old_categories = $conn->query("SELECT COUNT(*) as count FROM categories WHERE status = 1")->fetch_assoc()['count'];
    $old_sub_categories = $conn->query("SELECT COUNT(*) as count FROM sub_categories WHERE status = 1")->fetch_assoc()['count'];
    $old_products = $conn->query("SELECT COUNT(*) as count FROM products WHERE status = 1")->fetch_assoc()['count'];
    
    echo "<div class='alert warning-msg'>";
    echo "<h5>البيانات الحالية:</h5>";
    echo "<ul>";
    echo "<li>الفئات الرئيسية: $old_categories</li>";
    echo "<li>الفئات الفرعية: $old_sub_categories</li>";
    echo "<li>المنتجات: $old_products</li>";
    echo "</ul>";
    echo "</div>";
    
    if ($old_categories > 0 && $old_sub_categories > 0) {
        // تنفيذ الترحيل
        echo "<h5>تطبيق الترحيل...</h5>";
        
        $migration_plan = [];
        $categories = [];
        $sub_categories = [];
        
        // جلب البيانات القديمة
        $cat_result = $conn->query("SELECT * FROM categories WHERE status = 1");
        while ($row = $cat_result->fetch_assoc()) {
            $categories[$row['id']] = $row;
        }
        
        $sub_result = $conn->query("SELECT * FROM sub_categories WHERE status = 1");
        while ($row = $sub_result->fetch_assoc()) {
            $sub_categories[$row['id']] = $row;
        }
        
        // ترحيل ذكي
        foreach ($categories as $cat_id => $category) {
            $brand_search = $conn->query("SELECT * FROM brands WHERE LOWER(name) LIKE '%" . strtolower($category['category']) . "%' LIMIT 1");
            
            if ($brand_search->num_rows > 0) {
                $brand = $brand_search->fetch_assoc();
                echo "<div class='alert success-msg mb-1 py-1'><small>✓ شركة موجودة: {$category['category']} → {$brand['name']}</small></div>";
            } else {
                // إنشاء شركة جديدة
                $stmt = $conn->prepare("INSERT INTO brands (name, name_ar, description, status) VALUES (?, ?, ?, 1)");
                $name_ar = $category['category'];
                $description = "تم إنشاؤها من الترحيل";
                $stmt->bind_param("sss", $category['category'], $name_ar, $description);
                
                if ($stmt->execute()) {
                    $brand_id = $conn->insert_id;
                    $brand = ['id' => $brand_id, 'name' => $category['category']];
                    echo "<div class='alert success-msg mb-1 py-1'><small>✓ شركة جديدة: {$category['category']} (ID: $brand_id)</small></div>";
                }
            }
            
            // معالجة الفئات الفرعية
            foreach ($sub_categories as $sub_id => $sub_category) {
                if ($sub_category['parent_id'] == $cat_id) {
                    // إنشاء فئة جديدة
                    $stmt = $conn->prepare("INSERT INTO series (brand_id, name, name_ar, description, status) VALUES (?, ?, ?, ?, 1)");
                    $series_name_ar = $sub_category['sub_category'];
                    $series_desc = "تم إنشاؤها من الترحيل";
                    $stmt->bind_param("isss", $brand['id'], $sub_category['sub_category'], $series_name_ar, $series_desc);
                    
                    if ($stmt->execute()) {
                        $series_id = $conn->insert_id;
                        
                        // إنشاء موديل عام
                        $model_name = $sub_category['sub_category'] . " - عام";
                        $stmt = $conn->prepare("INSERT INTO models (series_id, brand_id, name, name_ar, description, status) VALUES (?, ?, ?, ?, ?, 1)");
                        $model_name_ar = $sub_category['sub_category'] . " - عام";
                        $model_desc = "موديل عام من الترحيل";
                        $stmt->bind_param("iisss", $series_id, $brand['id'], $model_name, $model_name_ar, $model_desc);
                        
                        if ($stmt->execute()) {
                            $model_id = $conn->insert_id;
                            
                            // تحديث المنتجات
                            $update_products = $conn->query("UPDATE products SET model_id = $model_id WHERE category_id = $cat_id AND sub_category_id = $sub_id");
                            $affected = $conn->affected_rows;
                            
                            echo "<div class='alert success-msg mb-1 py-1'><small>✓ تم ترحيل $affected منتج إلى {$sub_category['sub_category']}</small></div>";
                            
                            // حفظ المطابقة
                            $stmt = $conn->prepare("INSERT INTO migration_mapping (old_category_id, old_sub_category_id, new_brand_id, new_series_id, new_model_id, notes, status) VALUES (?, ?, ?, ?, ?, ?, 'completed')");
                            $notes = "ترحيل تلقائي - $affected منتج";
                            $stmt->bind_param("iiiiss", $cat_id, $sub_id, $brand['id'], $series_id, $model_id, $notes);
                            $stmt->execute();
                        }
                    }
                }
            }
        }
        
        echo "<div class='alert success-msg mt-3'>";
        echo "<h5>تم الانتهاء من الترحيل!</h5>";
        echo "</div>";
        
    } else {
        echo "<div class='alert warning-msg'>لا توجد بيانات قديمة للترحيل</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert error-msg'><strong>خطأ في الترحيل:</strong> " . $e->getMessage() . "</div>";
}

echo "</div></div>";

// الخطوة 3: إحصائيات النظام الجديد
echo "<div class='card step-card'>";
echo "<div class='card-header step-header'>";
echo "<h3><i class='fas fa-chart-bar'></i> الخطوة 3: إحصائيات النظام الجديد</h3>";
echo "</div>";
echo "<div class='card-body'>";

try {
    $brands_count = $conn->query("SELECT COUNT(*) as count FROM brands WHERE status = 1")->fetch_assoc()['count'];
    $series_count = $conn->query("SELECT COUNT(*) as count FROM series WHERE status = 1")->fetch_assoc()['count'];
    $models_count = $conn->query("SELECT COUNT(*) as count FROM models WHERE status = 1")->fetch_assoc()['count'];
    $products_with_model = $conn->query("SELECT COUNT(*) as count FROM products WHERE model_id IS NOT NULL")->fetch_assoc()['count'];
    $total_products = $conn->query("SELECT COUNT(*) as count FROM products WHERE status = 1")->fetch_assoc()['count'];
    
    echo "<div class='row text-center'>";
    echo "<div class='col-md-2'><div class='card'><div class='card-body'><h4>$brands_count</h4><p>الشركات</p></div></div></div>";
    echo "<div class='col-md-2'><div class='card'><div class='card-body'><h4>$series_count</h4><p>الفئات</p></div></div></div>";
    echo "<div class='col-md-2'><div class='card'><div class='card-body'><h4>$models_count</h4><p>الموديلات</p></div></div></div>";
    echo "<div class='col-md-3'><div class='card'><div class='card-body'><h4>$products_with_model</h4><p>المنتجات المرتبطة</p></div></div></div>";
    echo "<div class='col-md-3'><div class='card'><div class='card-body'><h4>$total_products</h4><p>إجمالي المنتجات</p></div></div></div>";
    echo "</div>";
    
    // عرض عينة من البيانات
    echo "<h5 class='mt-4'>عينة من البيانات الجديدة:</h5>";
    
    $sample_query = "SELECT b.name as brand_name, s.name as series_name, m.name as model_name, COUNT(p.id) as products_count
                     FROM brands b
                     LEFT JOIN series s ON b.id = s.brand_id
                     LEFT JOIN models m ON s.id = m.series_id
                     LEFT JOIN products p ON m.id = p.model_id
                     WHERE b.status = 1 AND s.status = 1 AND m.status = 1
                     GROUP BY b.id, s.id, m.id
                     ORDER BY b.name, s.name, m.name
                     LIMIT 10";
    
    $sample_result = $conn->query($sample_query);
    
    if ($sample_result && $sample_result->num_rows > 0) {
        echo "<table class='table table-sm table-striped'>";
        echo "<thead><tr><th>الشركة</th><th>الفئة</th><th>الموديل</th><th>عدد المنتجات</th></tr></thead>";
        echo "<tbody>";
        
        while ($row = $sample_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['brand_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['series_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['model_name']) . "</td>";
            echo "<td><span class='badge bg-primary'>" . $row['products_count'] . "</span></td>";
            echo "</tr>";
        }
        
        echo "</tbody></table>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert error-msg'><strong>خطأ:</strong> " . $e->getMessage() . "</div>";
}

echo "</div></div>";

// الخطوة 4: التحقق من سلامة النظام
echo "<div class='card step-card'>";
echo "<div class='card-header step-header'>";
echo "<h3><i class='fas fa-check-double'></i> الخطوة 4: التحقق من سلامة النظام</h3>";
echo "</div>";
echo "<div class='card-body'>";

$checks = [
    'الجداول الجديدة' => [
        'brands' => "SHOW TABLES LIKE 'brands'",
        'series' => "SHOW TABLES LIKE 'series'", 
        'models' => "SHOW TABLES LIKE 'models'",
        'migration_mapping' => "SHOW TABLES LIKE 'migration_mapping'"
    ],
    'الأعمدة الجديدة' => [
        'products.model_id' => "SHOW COLUMNS FROM products LIKE 'model_id'"
    ],
    'البيانات الأساسية' => [
        'brands_data' => "SELECT COUNT(*) as count FROM brands WHERE status = 1",
        'series_data' => "SELECT COUNT(*) as count FROM series WHERE status = 1",
        'models_data' => "SELECT COUNT(*) as count FROM models WHERE status = 1"
    ]
];

foreach ($checks as $check_name => $check_queries) {
    echo "<h6>$check_name:</h6>";
    foreach ($check_queries as $check_desc => $query) {
        try {
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $count = isset($row['count']) ? $row['count'] : $result->num_rows;
                echo "<div class='alert success-msg mb-1 py-1'><small>✓ $check_desc: موجود" . (isset($row['count']) ? " ($count)" : "") . "</small></div>";
            } else {
                echo "<div class='alert error-msg mb-1 py-1'><small>✗ $check_desc: غير موجود</small></div>";
            }
        } catch (Exception $e) {
            echo "<div class='alert error-msg mb-1 py-1'><small>✗ $check_desc: خطأ - " . $e->getMessage() . "</small></div>";
        }
    }
}

echo "</div></div>";

// الخطوة 5: تطبيق الملفات الجديدة
echo "<div class='card step-card'>";
echo "<div class='card-header step-header'>";
echo "<h3><i class='fas fa-file-code'></i> الخطوة 5: تطبيق الملفات الجديدة</h3>";
echo "</div>";
echo "<div class='card-body'>";

$files_to_check = [
    'admin/product/manage_product_new.php' => 'صفحة إدارة المنتجات الجديدة',
    'admin/product/ajax_get_series.php' => 'ملف AJAX للفئات',
    'admin/product/ajax_get_models.php' => 'ملف AJAX للموديلات',
    'inc/topBarNav_new.php' => 'شريط التنقل الجديد',
    'inc/load_mega_menu.php' => 'تحميل القائمة الضخمة'
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        echo "<div class='alert success-msg mb-1 py-1'><small>✓ $description: موجود</small></div>";
    } else {
        echo "<div class='alert error-msg mb-1 py-1'><small>✗ $description: غير موجود ($file)</small></div>";
    }
}

echo "</div></div>";

// الخطوة 6: الخطوات التالية
echo "<div class='card step-card'>";
echo "<div class='card-header step-header'>";
echo "<h3><i class='fas fa-list-check'></i> الخطوات التالية</h3>";
echo "</div>";
echo "<div class='card-body'>";

echo "<div class='alert warning-msg'>";
echo "<h5>المطلوب للإكمال:</h5>";
echo "<ol>";
echo "<li><strong>تحديث شريط التنقل:</strong> استبدال ملف <code>inc/topBarNav.php</code> بالملف الجديد</li>";
echo "<li><strong>تحديث صفحة المنتجات:</strong> استبدال <code>admin/product/manage_product.php</code> بالملف الجديد</li>";
echo "<li><strong>إنشاء صفحات إدارة:</strong> إضافة صفحات إدارة الشركات والفئات والموديلات</li>";
echo "<li><strong>تحديث صفحة عرض المنتجات:</strong> تعديل صفحة المنتجات لتعمل مع النظام الجديد</li>";
echo "<li><strong>اختبار شامل:</strong> اختبار جميع وظائف الموقع</li>";
echo "</ol>";
echo "</div>";

echo "<div class='alert success-msg'>";
echo "<h5>الأدوات المتاحة:</h5>";
echo "<div class='d-grid gap-2 d-md-flex justify-content-md-start'>";
echo "<a href='database/apply_new_structure.php' class='btn btn-primary me-md-2' target='_blank'>تطبيق الهيكلية</a>";
echo "<a href='database/migrate_data.php' class='btn btn-success me-md-2' target='_blank'>ترحيل البيانات</a>";
echo "<a href='check_database.php' class='btn btn-info me-md-2' target='_blank'>فحص قاعدة البيانات</a>";
echo "</div>";
echo "</div>";

echo "</div></div>";

echo "</div>"; // end container

echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>";
echo "</body></html>";

$conn->close();
?>
