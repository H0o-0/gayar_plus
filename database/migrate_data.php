<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300); // 5 minutes

require_once('../initialize.php');

echo "<h1>ترحيل البيانات من النظام القديم إلى الهيكلية الجديدة</h1>";

try {
    echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>تحذير مهم:</h3>";
    echo "<p>هذا السكريبت سيقوم بترحيل البيانات من النظام القديم (categories/sub_categories) إلى النظام الجديد (brands/series/models).</p>";
    echo "<p>تأكد من وجود نسخة احتياطية قبل التشغيل!</p>";
    echo "</div>";

    // فحص وجود الجداول الجديدة
    $tables_check = ['brands', 'series', 'models', 'migration_mapping'];
    foreach ($tables_check as $table) {
        $check = $conn->query("SHOW TABLES LIKE '$table'");
        if ($check->num_rows == 0) {
            throw new Exception("الجدول $table غير موجود. يرجى تشغيل سكريبت إنشاء الهيكلية أولاً.");
        }
    }
    
    echo "<h2>الخطوة 1: تحليل البيانات الحالية</h2>";
    
    // تحليل البيانات الحالية
    $categories = [];
    $sub_categories = [];
    $products_count = [];
    
    $cat_result = $conn->query("SELECT * FROM categories WHERE status = 1");
    while ($row = $cat_result->fetch_assoc()) {
        $categories[$row['id']] = $row;
        echo "الفئة الرئيسية: {$row['category']} (ID: {$row['id']})<br>";
    }
    
    $sub_result = $conn->query("SELECT sc.*, c.category as parent_name FROM sub_categories sc LEFT JOIN categories c ON sc.parent_id = c.id WHERE sc.status = 1");
    while ($row = $sub_result->fetch_assoc()) {
        $sub_categories[$row['id']] = $row;
        
        // حساب عدد المنتجات لكل فئة فرعية
        $count_result = $conn->query("SELECT COUNT(*) as count FROM products WHERE sub_category_id = {$row['id']} AND status = 1");
        $count = $count_result->fetch_assoc()['count'];
        $products_count[$row['id']] = $count;
        
        echo "-- الفئة الفرعية: {$row['sub_category']} تحت {$row['parent_name']} ({$count} منتج)<br>";
    }
    
    echo "<h2>الخطوة 2: إنشاء خطة الترحيل الذكية</h2>";
    
    // منطق ذكي لمطابقة البيانات
    $migration_plan = [];
    
    foreach ($categories as $cat_id => $category) {
        $brand_mapping = null;
        $category_name = strtolower($category['category']);
        
        // البحث عن شركة مطابقة في قاعدة البيانات الجديدة
        $brand_search = $conn->query("SELECT * FROM brands WHERE 
            LOWER(name) LIKE '%{$category_name}%' OR 
            LOWER(name_ar) LIKE '%{$category_name}%' OR 
            '{$category_name}' LIKE CONCAT('%', LOWER(name), '%')");
        
        if ($brand_search->num_rows > 0) {
            $brand_mapping = $brand_search->fetch_assoc();
            echo "✓ تم العثور على شركة مطابقة لـ '{$category['category']}': {$brand_mapping['name']}<br>";
        } else {
            // إنشاء شركة جديدة
            $stmt = $conn->prepare("INSERT INTO brands (name, name_ar, description, status) VALUES (?, ?, ?, 1)");
            $name_ar = $category['category'];
            $description = "تم إنشاؤها تلقائياً من الترحيل - " . ($category['description'] ?? '');
            $stmt->bind_param("sss", $category['category'], $name_ar, $description);
            
            if ($stmt->execute()) {
                $brand_id = $conn->insert_id;
                $brand_mapping = [
                    'id' => $brand_id,
                    'name' => $category['category'],
                    'name_ar' => $name_ar
                ];
                echo "✓ تم إنشاء شركة جديدة لـ '{$category['category']}' (ID: $brand_id)<br>";
            } else {
                throw new Exception("فشل في إنشاء الشركة: " . $conn->error);
            }
        }
        
        // معالجة الفئات الفرعية
        foreach ($sub_categories as $sub_id => $sub_category) {
            if ($sub_category['parent_id'] == $cat_id) {
                $series_mapping = null;
                
                // البحث عن فئة مطابقة
                $series_search = $conn->query("SELECT * FROM series WHERE 
                    brand_id = {$brand_mapping['id']} AND 
                    (LOWER(name) LIKE '%{$sub_category['sub_category']}%' OR 
                     name LIKE '%{$sub_category['sub_category']}%')");
                
                if ($series_search->num_rows > 0) {
                    $series_mapping = $series_search->fetch_assoc();
                } else {
                    // إنشاء فئة جديدة
                    $stmt = $conn->prepare("INSERT INTO series (brand_id, name, name_ar, description, status) VALUES (?, ?, ?, ?, 1)");
                    $series_name_ar = $sub_category['sub_category'];
                    $series_desc = "تم إنشاؤها تلقائياً من الترحيل - " . ($sub_category['description'] ?? '');
                    $stmt->bind_param("isss", $brand_mapping['id'], $sub_category['sub_category'], $series_name_ar, $series_desc);
                    
                    if ($stmt->execute()) {
                        $series_id = $conn->insert_id;
                        $series_mapping = [
                            'id' => $series_id,
                            'brand_id' => $brand_mapping['id'],
                            'name' => $sub_category['sub_category'],
                            'name_ar' => $series_name_ar
                        ];
                        echo "-- تم إنشاء فئة جديدة '{$sub_category['sub_category']}' تحت {$brand_mapping['name']} (ID: $series_id)<br>";
                    }
                }
                
                // إنشاء موديل عام للفئة الفرعية
                $model_name = $sub_category['sub_category'] . " - عام";
                $stmt = $conn->prepare("INSERT INTO models (series_id, brand_id, name, name_ar, description, status) VALUES (?, ?, ?, ?, ?, 1)");
                $model_name_ar = $sub_category['sub_category'] . " - عام";
                $model_desc = "موديل عام تم إنشاؤه من الترحيل";
                $stmt->bind_param("iisss", $series_mapping['id'], $brand_mapping['id'], $model_name, $model_name_ar, $model_desc);
                
                if ($stmt->execute()) {
                    $model_id = $conn->insert_id;
                    echo "-- تم إنشاء موديل عام '$model_name' (ID: $model_id)<br>";
                    
                    // حفظ المطابقة
                    $migration_plan[] = [
                        'old_category_id' => $cat_id,
                        'old_sub_category_id' => $sub_id,
                        'new_brand_id' => $brand_mapping['id'],
                        'new_series_id' => $series_mapping['id'],
                        'new_model_id' => $model_id,
                        'products_count' => $products_count[$sub_id] ?? 0
                    ];
                    
                    // حفظ في جدول migration_mapping
                    $stmt = $conn->prepare("INSERT INTO migration_mapping (old_category_id, old_sub_category_id, new_brand_id, new_series_id, new_model_id, notes, status) VALUES (?, ?, ?, ?, ?, ?, 'completed')");
                    $notes = "ترحيل تلقائي - {$products_count[$sub_id]} منتج";
                    $stmt->bind_param("iiiiiis", $cat_id, $sub_id, $brand_mapping['id'], $series_mapping['id'], $model_id, $notes);
                    $stmt->execute();
                }
            }
        }
    }
    
    echo "<h2>الخطوة 3: ترحيل المنتجات</h2>";
    
    $migrated_products = 0;
    $failed_products = 0;
    
    foreach ($migration_plan as $mapping) {
        // تحديث المنتجات
        $update_stmt = $conn->prepare("UPDATE products SET model_id = ? WHERE category_id = ? AND sub_category_id = ?");
        $update_stmt->bind_param("iii", $mapping['new_model_id'], $mapping['old_category_id'], $mapping['old_sub_category_id']);
        
        if ($update_stmt->execute()) {
            $affected = $conn->affected_rows;
            $migrated_products += $affected;
            echo "✓ تم ترحيل $affected منتج من الفئة الفرعية {$mapping['old_sub_category_id']} إلى الموديل {$mapping['new_model_id']}<br>";
        } else {
            $failed_products += $mapping['products_count'];
            echo "✗ فشل في ترحيل المنتجات من الفئة الفرعية {$mapping['old_sub_category_id']}<br>";
        }
    }
    
    echo "<h2>الخطوة 4: إضافة Foreign Key</h2>";
    
    // إضافة Foreign Key للربط
    try {
        $conn->query("ALTER TABLE products ADD FOREIGN KEY (model_id) REFERENCES models(id) ON DELETE SET NULL ON UPDATE CASCADE");
        echo "✓ تم إضافة Foreign Key constraint بين products و models<br>";
    } catch (Exception $e) {
        echo "⚠ تحذير: لم يتم إضافة Foreign Key - " . $e->getMessage() . "<br>";
    }
    
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h2>تم الانتهاء من الترحيل!</h2>";
    echo "<h3>إحصائيات الترحيل:</h3>";
    echo "<ul>";
    echo "<li><strong>الشركات:</strong> " . count(array_unique(array_column($migration_plan, 'new_brand_id'))) . " شركة</li>";
    echo "<li><strong>الفئات:</strong> " . count(array_unique(array_column($migration_plan, 'new_series_id'))) . " فئة</li>";
    echo "<li><strong>الموديلات:</strong> " . count($migration_plan) . " موديل</li>";
    echo "<li><strong>المنتجات المرحلة:</strong> $migrated_products منتج</li>";
    echo "<li><strong>المنتجات الفاشلة:</strong> $failed_products منتج</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h2>التحقق من النتائج:</h2>";
    
    // عرض إحصائيات سريعة
    $brands_count = $conn->query("SELECT COUNT(*) as count FROM brands")->fetch_assoc()['count'];
    $series_count = $conn->query("SELECT COUNT(*) as count FROM series")->fetch_assoc()['count'];
    $models_count = $conn->query("SELECT COUNT(*) as count FROM models")->fetch_assoc()['count'];
    $products_with_model = $conn->query("SELECT COUNT(*) as count FROM products WHERE model_id IS NOT NULL")->fetch_assoc()['count'];
    
    echo "<p>الشركات: $brands_count | الفئات: $series_count | الموديلات: $models_count | المنتجات المرتبطة: $products_with_model</p>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>خطأ في الترحيل:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

$conn->close();
?>
