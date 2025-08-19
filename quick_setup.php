<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('initialize.php');

echo "<h2>إعداد سريع للنظام الجديد</h2>";

try {
    // إنشاء جدول الشركات
    $brands_sql = "CREATE TABLE IF NOT EXISTS `brands` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `name_ar` varchar(100) DEFAULT NULL,
        `description` text DEFAULT NULL,
        `logo` varchar(255) DEFAULT NULL,
        `status` tinyint(1) NOT NULL DEFAULT 1,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `name` (`name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    if ($conn->query($brands_sql)) {
        echo "<p style='color: green;'>✓ تم إنشاء جدول الشركات بنجاح</p>";
    } else {
        echo "<p style='color: red;'>✗ خطأ في إنشاء جدول الشركات: " . $conn->error . "</p>";
    }

    // إنشاء جدول الفئات/السلاسل
    $series_sql = "CREATE TABLE IF NOT EXISTS `series` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `brand_id` int(11) NOT NULL,
        `name` varchar(100) NOT NULL,
        `name_ar` varchar(100) DEFAULT NULL,
        `description` text DEFAULT NULL,
        `image` varchar(255) DEFAULT NULL,
        `status` tinyint(1) NOT NULL DEFAULT 1,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `brand_id` (`brand_id`),
        FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    if ($conn->query($series_sql)) {
        echo "<p style='color: green;'>✓ تم إنشاء جدول الفئات بنجاح</p>";
    } else {
        echo "<p style='color: red;'>✗ خطأ في إنشاء جدول الفئات: " . $conn->error . "</p>";
    }

    // إنشاء جدول الموديلات
    $models_sql = "CREATE TABLE IF NOT EXISTS `models` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `series_id` int(11) NOT NULL,
        `brand_id` int(11) NOT NULL,
        `name` varchar(100) NOT NULL,
        `name_ar` varchar(100) DEFAULT NULL,
        `description` text DEFAULT NULL,
        `image` varchar(255) DEFAULT NULL,
        `specifications` text DEFAULT NULL,
        `status` tinyint(1) NOT NULL DEFAULT 1,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `series_id` (`series_id`),
        KEY `brand_id` (`brand_id`),
        FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE,
        FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    if ($conn->query($models_sql)) {
        echo "<p style='color: green;'>✓ تم إنشاء جدول الموديلات بنجاح</p>";
    } else {
        echo "<p style='color: red;'>✗ خطأ في إنشاء جدول الموديلات: " . $conn->error . "</p>";
    }

    // إنشاء جدول تتبع الترحيل
    $migration_sql = "CREATE TABLE IF NOT EXISTS `migration_mapping` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `old_category_id` int(11) DEFAULT NULL,
        `old_sub_category_id` int(11) DEFAULT NULL,
        `new_brand_id` int(11) DEFAULT NULL,
        `new_series_id` int(11) DEFAULT NULL,
        `new_model_id` int(11) DEFAULT NULL,
        `notes` text DEFAULT NULL,
        `status` enum('pending','completed','failed') DEFAULT 'pending',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `old_category_id` (`old_category_id`),
        KEY `old_sub_category_id` (`old_sub_category_id`),
        KEY `new_brand_id` (`new_brand_id`),
        KEY `new_series_id` (`new_series_id`),
        KEY `new_model_id` (`new_model_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    if ($conn->query($migration_sql)) {
        echo "<p style='color: green;'>✓ تم إنشاء جدول تتبع الترحيل بنجاح</p>";
    } else {
        echo "<p style='color: red;'>✗ خطأ في إنشاء جدول تتبع الترحيل: " . $conn->error . "</p>";
    }

    // إضافة حقل model_id لجدول المنتجات
    $products_column = "ALTER TABLE `products` ADD COLUMN `model_id` int(11) DEFAULT NULL AFTER `sub_category_id`";
    
    if ($conn->query($products_column)) {
        echo "<p style='color: green;'>✓ تم إضافة حقل model_id لجدول المنتجات بنجاح</p>";
    } else {
        // إذا كان الحقل موجود مسبقاً
        if (strpos($conn->error, 'Duplicate column') !== false) {
            echo "<p style='color: orange;'>ℹ حقل model_id موجود مسبقاً في جدول المنتجات</p>";
        } else {
            echo "<p style='color: red;'>✗ خطأ في إضافة حقل model_id: " . $conn->error . "</p>";
        }
    }

    // إضافة بيانات تجريبية
    echo "<h3>إضافة بيانات تجريبية...</h3>";

    // شركات تجريبية
    $brands_data = [
        ['Apple', 'آبل'],
        ['Samsung', 'سامسونج'],
        ['Huawei', 'هواوي'],
        ['Xiaomi', 'شاومي'],
        ['Oppo', 'أوبو'],
        ['OnePlus', 'وان بلس']
    ];

    foreach ($brands_data as $brand) {
        $stmt = $conn->prepare("INSERT IGNORE INTO brands (name, name_ar, description, status) VALUES (?, ?, ?, 1)");
        $desc = "شركة " . $brand[1] . " للهواتف الذكية";
        $stmt->bind_param("sss", $brand[0], $brand[1], $desc);
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✓ تم إضافة شركة: {$brand[1]}</p>";
        }
    }

    // فئات تجريبية
    $series_data = [
        ['iPhone', 'آيفون', 1],
        ['iPad', 'آيباد', 1],
        ['Galaxy S', 'جالاكسي إس', 2],
        ['Galaxy Note', 'جالاكسي نوت', 2],
        ['P Series', 'سلسلة P', 3],
        ['Mate Series', 'سلسلة ميت', 3],
        ['Mi Series', 'سلسلة مي', 4],
        ['Redmi', 'ريدمي', 4]
    ];

    foreach ($series_data as $series) {
        $stmt = $conn->prepare("INSERT IGNORE INTO series (name, name_ar, brand_id, description, status) VALUES (?, ?, ?, ?, 1)");
        $desc = "فئة " . $series[1] . " من الشركة";
        $stmt->bind_param("ssis", $series[0], $series[1], $series[2], $desc);
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✓ تم إضافة فئة: {$series[1]}</p>";
        }
    }

    // موديلات تجريبية
    $models_data = [
        ['iPhone 15', 'آيفون 15', 1, 1],
        ['iPhone 14', 'آيفون 14', 1, 1],
        ['iPad Pro', 'آيباد برو', 2, 1],
        ['Galaxy S24', 'جالاكسي إس 24', 3, 2],
        ['Galaxy S23', 'جالاكسي إس 23', 3, 2],
        ['Galaxy Note 20', 'جالاكسي نوت 20', 4, 2],
        ['P60 Pro', 'P60 برو', 5, 3],
        ['Mate 50', 'ميت 50', 6, 3],
        ['Mi 13', 'مي 13', 7, 4],
        ['Redmi Note 12', 'ريدمي نوت 12', 8, 4]
    ];

    foreach ($models_data as $model) {
        $stmt = $conn->prepare("INSERT IGNORE INTO models (name, name_ar, series_id, brand_id, description, status) VALUES (?, ?, ?, ?, ?, 1)");
        $desc = "موديل " . $model[1];
        $stmt->bind_param("ssiis", $model[0], $model[1], $model[2], $model[3], $desc);
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✓ تم إضافة موديل: {$model[1]}</p>";
        }
    }

    echo "<hr>";
    echo "<h3 style='color: green;'>تم الانتهاء من الإعداد!</h3>";
    echo "<p><a href='admin/product/manage_product_new.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>اختبار صفحة إضافة المنتجات الجديدة</a></p>";
    echo "<p><a href='check_database.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>فحص قاعدة البيانات</a></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>خطأ عام: " . $e->getMessage() . "</p>";
}

$conn->close();
?>
