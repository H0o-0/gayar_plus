<?php
require_once('config.php');

echo "=== تحليل هيكل قاعدة البيانات الحالي ===\n\n";

// فحص الجداول الموجودة
echo "الجداول الموجودة:\n";
$tables = $conn->query("SHOW TABLES");
while($row = $tables->fetch_array()) {
    echo "- " . $row[0] . "\n";
}

echo "\n=== هيكل جدول categories ===\n";
$result = $conn->query("DESCRIBE categories");
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Key'] . "\n";
}

echo "\n=== هيكل جدول sub_categories ===\n";
$result = $conn->query("DESCRIBE sub_categories");
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Key'] . "\n";
}

echo "\n=== هيكل جدول products ===\n";
$result = $conn->query("DESCRIBE products");
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Key'] . "\n";
}

echo "\n=== بيانات categories ===\n";
$result = $conn->query("SELECT * FROM categories");
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . ", Name: " . $row['category'] . "\n";
}

echo "\n=== بيانات sub_categories ===\n";
$result = $conn->query("SELECT sc.*, c.category as parent_name FROM sub_categories sc LEFT JOIN categories c ON sc.parent_id = c.id");
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . ", Name: " . $row['sub_category'] . ", Parent: " . $row['parent_name'] . " (ID: " . $row['parent_id'] . ")\n";
}

// فحص إذا كانت هناك جداول brands أو models موجودة
echo "\n=== فحص الجداول الإضافية ===\n";
$brand_check = $conn->query("SHOW TABLES LIKE 'brands'");
if($brand_check->num_rows > 0) {
    echo "جدول brands موجود\n";
} else {
    echo "جدول brands غير موجود\n";
}

$models_check = $conn->query("SHOW TABLES LIKE 'models'");
if($models_check->num_rows > 0) {
    echo "جدول models موجود\n";
} else {
    echo "جدول models غير موجود\n";
}

$series_check = $conn->query("SHOW TABLES LIKE 'series'");
if($series_check->num_rows > 0) {
    echo "جدول series موجود\n";
} else {
    echo "جدول series غير موجود\n";
}

$conn->close();
?>
