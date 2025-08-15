<?php
require_once('config.php');

echo "<h1>🏗️ إنشاء جدول المخزن المؤقت</h1>";

// إنشاء جدول المخزن المؤقت
$create_warehouse_table = "CREATE TABLE IF NOT EXISTS `temp_warehouse` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_name` varchar(255) NOT NULL,
    `original_price` decimal(10,2) DEFAULT NULL,
    `suggested_brand` varchar(100) DEFAULT NULL,
    `confirmed_brand` varchar(100) DEFAULT NULL,
    `suggested_type` varchar(100) DEFAULT NULL,
    `confirmed_type` varchar(100) DEFAULT NULL,
    `category_id` int(11) DEFAULT NULL,
    `sub_category_id` int(11) DEFAULT NULL,
    `status` enum('unclassified','classified','published') DEFAULT 'unclassified',
    `raw_data` text DEFAULT NULL,
    `import_batch` varchar(50) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `category_id` (`category_id`),
    KEY `sub_category_id` (`sub_category_id`),
    KEY `status` (`status`),
    KEY `suggested_brand` (`suggested_brand`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

echo "<h2>1. إنشاء جدول temp_warehouse</h2>";
if($conn->query($create_warehouse_table)) {
    echo "<p style='color: green;'>✅ تم إنشاء جدول temp_warehouse بنجاح</p>";
} else {
    echo "<p style='color: red;'>❌ فشل في إنشاء الجدول: " . $conn->error . "</p>";
}

// إنشاء جدول إحصائيات المخزن
$create_stats_table = "CREATE TABLE IF NOT EXISTS `warehouse_stats` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `brand` varchar(100) NOT NULL,
    `product_type` varchar(100) DEFAULT NULL,
    `count` int(11) DEFAULT 0,
    `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `brand_type` (`brand`, `product_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

echo "<h2>2. إنشاء جدول warehouse_stats</h2>";
if($conn->query($create_stats_table)) {
    echo "<p style='color: green;'>✅ تم إنشاء جدول warehouse_stats بنجاح</p>";
} else {
    echo "<p style='color: red;'>❌ فشل في إنشاء الجدول: " . $conn->error . "</p>";
}

// إضافة بيانات تجريبية للاختبار
echo "<h2>3. إضافة بيانات تجريبية</h2>";

$test_data = [
    ['شاشة آيفون 13 برو ماكس', 250, 'آيفون', 'شاشات'],
    ['غطاء سامسونج جالاكسي S21 أزرق', 25, 'سامسونج', 'أغطية'],
    ['بطارية هواوي P30 برو', 80, 'هواوي', 'بطاريات'],
    ['شاحن آيفون أصلي', 45, 'آيفون', 'شواحن'],
    ['زجاج خلفي سامسونج نوت 20 أسود', 35, 'سامسونج', 'زجاج خلفي'],
    ['أدوات فك الهواتف مجموعة كاملة', 15, 'أدوات صيانة', 'أدوات'],
    ['شاشة هواوي ميت 40', 180, 'هواوي', 'شاشات'],
    ['غطاء آيفون 12 شفاف', 20, 'آيفون', 'أغطية']
];

foreach($test_data as $item) {
    $name = $conn->real_escape_string($item[0]);
    $price = $item[1];
    $brand = $conn->real_escape_string($item[2]);
    $type = $conn->real_escape_string($item[3]);
    
    $insert_sql = "INSERT INTO `temp_warehouse` 
                   (product_name, original_price, suggested_brand, suggested_type, status, import_batch) 
                   VALUES ('$name', $price, '$brand', '$type', 'unclassified', 'test_batch_1')";
    
    if($conn->query($insert_sql)) {
        echo "<p style='color: green;'>✅ تم إضافة: $name</p>";
    } else {
        echo "<p style='color: red;'>❌ فشل في إضافة: $name</p>";
    }
}

// تحديث الإحصائيات
echo "<h2>4. تحديث إحصائيات المخزن</h2>";

$brands = $conn->query("SELECT suggested_brand, suggested_type, COUNT(*) as count 
                       FROM temp_warehouse 
                       WHERE suggested_brand IS NOT NULL 
                       GROUP BY suggested_brand, suggested_type");

while($row = $brands->fetch_assoc()) {
    $brand = $conn->real_escape_string($row['suggested_brand']);
    $type = $conn->real_escape_string($row['suggested_type']);
    $count = $row['count'];
    
    $update_stats = "INSERT INTO warehouse_stats (brand, product_type, count) 
                     VALUES ('$brand', '$type', $count) 
                     ON DUPLICATE KEY UPDATE count = $count";
    
    if($conn->query($update_stats)) {
        echo "<p style='color: green;'>✅ تم تحديث إحصائيات: $brand - $type ($count منتج)</p>";
    }
}

echo "<hr>";
echo "<h3>✅ تم إنشاء نظام المخزن المؤقت بنجاح!</h3>";

// عرض الإحصائيات
echo "<h3>📊 إحصائيات المخزن الحالية:</h3>";
$stats = $conn->query("SELECT brand, product_type, count FROM warehouse_stats ORDER BY count DESC");

echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
echo "<tr style='background: #f8f9fa;'><th>العلامة التجارية</th><th>نوع المنتج</th><th>العدد</th></tr>";

while($stat = $stats->fetch_assoc()) {
    echo "<tr>";
    echo "<td style='padding: 8px;'>" . $stat['brand'] . "</td>";
    echo "<td style='padding: 8px;'>" . $stat['product_type'] . "</td>";
    echo "<td style='padding: 8px; text-align: center;'>" . $stat['count'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h3>🎯 الخطوات التالية:</h3>";
echo "<ol>";
echo "<li>إنشاء كارد المخزن في الداش بورد</li>";
echo "<li>بناء صفحة رفع Excel</li>";
echo "<li>تطوير نظام التصنيف الذكي</li>";
echo "<li>إنشاء واجهة الفلترة والإدارة</li>";
echo "</ol>";
echo "</div>";

echo "<p>";
echo "<a href='admin/' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>الذهاب للداش بورد</a>";
echo "<a href='./' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>الواجهة الأمامية</a>";
echo "</p>";

$conn->close();
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    background: #f5f5f5;
}

h1, h2, h3 {
    color: #333;
}

p {
    line-height: 1.6;
}

table {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

a:hover {
    opacity: 0.8;
    transform: translateY(-2px);
    transition: all 0.3s ease;
}
</style>
