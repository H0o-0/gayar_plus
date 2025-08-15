<?php
require_once('initialize.php');

echo "<h2>تحديث قاعدة البيانات - إصلاح سريع</h2>";

// 1. إضافة عمود has_colors إذا لم يكن موجود
$check_column = $conn->query("SHOW COLUMNS FROM `products` LIKE 'has_colors'");
if($check_column->num_rows == 0) {
    echo "<p>جاري إضافة عمود has_colors...</p>";
    $add_column = $conn->query("ALTER TABLE `products` ADD COLUMN `has_colors` TINYINT(1) DEFAULT 0 AFTER `status`");
    if($add_column) {
        echo "<p style='color: green;'>✅ تم إضافة عمود has_colors</p>";
    } else {
        echo "<p style='color: red;'>❌ فشل: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ️ عمود has_colors موجود بالفعل</p>";
}

// 2. إنشاء جدول product_colors إذا لم يكن موجود
$check_table = $conn->query("SHOW TABLES LIKE 'product_colors'");
if($check_table->num_rows == 0) {
    echo "<p>جاري إنشاء جدول product_colors...</p>";
    $create_table = $conn->query("CREATE TABLE `product_colors` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `product_id` int(11) NOT NULL,
        `color_name` varchar(100) NOT NULL,
        `color_code` varchar(7) NOT NULL DEFAULT '#007bff',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `product_id` (`product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    if($create_table) {
        echo "<p style='color: green;'>✅ تم إنشاء جدول product_colors</p>";
    } else {
        echo "<p style='color: red;'>❌ فشل: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ️ جدول product_colors موجود بالفعل</p>";

    // فحص إذا كان عمود color_code موجود
    $check_color_code = $conn->query("SHOW COLUMNS FROM `product_colors` LIKE 'color_code'");
    if($check_color_code->num_rows == 0) {
        echo "<p>جاري إضافة عمود color_code...</p>";
        $add_color_code = $conn->query("ALTER TABLE `product_colors` ADD COLUMN `color_code` varchar(7) NOT NULL DEFAULT '#007bff' AFTER `color_name`");
        if($add_color_code) {
            echo "<p style='color: green;'>✅ تم إضافة عمود color_code</p>";
        } else {
            echo "<p style='color: red;'>❌ فشل: " . $conn->error . "</p>";
        }
    }
}

echo "<hr>";
echo "<h3>✅ تم الانتهاء من التحديثات!</h3>";
echo "<p><a href='./' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>الذهاب للواجهة الأمامية</a></p>";
echo "<p><a href='admin/' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>الذهاب للوحة التحكم</a></p>";
?>
