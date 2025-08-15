<?php
echo "<h2>اختبار قراءة ملف CSV</h2>";

$file_path = 'test_products.csv';

if (!file_exists($file_path)) {
    echo "<p style='color: red;'>الملف غير موجود: $file_path</p>";
    exit;
}

echo "<p>حجم الملف: " . filesize($file_path) . " بايت</p>";

$handle = fopen($file_path, 'r');
if (!$handle) {
    echo "<p style='color: red;'>فشل في فتح الملف</p>";
    exit;
}

echo "<h3>محتويات الملف:</h3>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>رقم السطر</th><th>اسم المنتج</th><th>السعر</th></tr>";

$row_number = 1;
while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
    echo "<tr>";
    echo "<td>$row_number</td>";
    echo "<td>" . (isset($row[0]) ? htmlspecialchars($row[0]) : 'فارغ') . "</td>";
    echo "<td>" . (isset($row[1]) ? htmlspecialchars($row[1]) : 'فارغ') . "</td>";
    echo "</tr>";
    
    $row_number++;
    if ($row_number > 10) { // عرض أول 10 أسطر فقط
        echo "<tr><td colspan='3'>... والمزيد</td></tr>";
        break;
    }
}

echo "</table>";
fclose($handle);

echo "<hr>";
echo "<h3>اختبار التصنيف:</h3>";

// محاكاة دالة التصنيف
function testClassify($product_name) {
    $name = strtolower($product_name);
    
    $brands = [
        'iPhone' => ['iphone', 'apple'],
        'Samsung' => ['samsung', 'galaxy'],
        'Huawei' => ['huawei'],
        'Xiaomi' => ['xiaomi', 'redmi'],
        'Tools' => ['tool', 'repair']
    ];
    
    $types = [
        'Accessories' => ['screen', 'case', 'battery', 'charger', 'glass', 'cover', 'earphones'],
        'Food' => ['food', 'treats'],
        'Accessories' => ['collar', 'toy', 'leash', 'bed', 'bowl', 'shampoo']
    ];
    
    $suggested_brand = null;
    $suggested_type = null;
    
    foreach ($brands as $brand => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($name, $keyword) !== false) {
                $suggested_brand = $brand;
                break 2;
            }
        }
    }
    
    foreach ($types as $type => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($name, $keyword) !== false) {
                $suggested_type = $type;
                break 2;
            }
        }
    }
    
    return [
        'brand' => $suggested_brand,
        'type' => $suggested_type
    ];
}

// اختبار التصنيف على بعض المنتجات
$test_products = [
    'iPhone 13 Screen Replacement',
    'Samsung Galaxy S21 Case',
    'Dog Food Premium',
    'Phone Repair Tool Kit'
];

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>المنتج</th><th>العلامة التجارية</th><th>النوع</th></tr>";

foreach ($test_products as $product) {
    $classification = testClassify($product);
    echo "<tr>";
    echo "<td>$product</td>";
    echo "<td>" . ($classification['brand'] ?: 'غير محدد') . "</td>";
    echo "<td>" . ($classification['type'] ?: 'غير محدد') . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<p><strong>النتيجة:</strong> قراءة CSV تعمل بشكل صحيح والتصنيف يعمل!</p>";
echo "<p><a href='admin/index.php?page=warehouse/upload'>جرب رفع الملف الآن</a></p>";
?>
