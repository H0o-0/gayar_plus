<?php
// أداة تحويل Excel إلى CSV بسيطة

echo "<h2>🔄 محول Excel إلى CSV</h2>";

if (!isset($_FILES['excel_file'])) {
    echo '<div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">';
    echo '<form method="post" enctype="multipart/form-data">';
    echo '<div style="margin-bottom: 20px;">';
    echo '<label for="excel_file" style="display: block; margin-bottom: 10px; font-weight: bold;">اختر ملف Excel:</label>';
    echo '<input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">';
    echo '</div>';
    echo '<button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">تحويل إلى CSV</button>';
    echo '</form>';
    echo '</div>';
    
    echo '<div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px;">';
    echo '<h3>كيفية الاستخدام:</h3>';
    echo '<ol>';
    echo '<li>ارفع ملف Excel هنا</li>';
    echo '<li>سيتم تحويله إلى CSV تلقائياً</li>';
    echo '<li>حمل ملف CSV الناتج</li>';
    echo '<li>ارفعه في نظام المخزن</li>';
    echo '</ol>';
    echo '</div>';
    
    exit;
}

$file = $_FILES['excel_file'];
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

echo "<h3>معلومات الملف:</h3>";
echo "<p>الاسم: " . htmlspecialchars($file['name']) . "</p>";
echo "<p>الحجم: " . number_format($file['size']) . " بايت</p>";
echo "<p>النوع: " . htmlspecialchars($file['type']) . "</p>";

// قراءة الملف
$data = [];

if ($file_extension === 'xlsx') {
    require_once('admin/warehouse/excel_reader.php');
    $data = readExcelFile($file['tmp_name']);
    echo "<p>تم استخدام قارئ XLSX</p>";
} else if ($file_extension === 'xls') {
    require_once('admin/warehouse/xls_reader.php');
    $data = readXLSFile($file['tmp_name']);
    echo "<p>تم استخدام قارئ XLS</p>";
} else {
    echo "<p style='color: red;'>نوع ملف غير مدعوم</p>";
    exit;
}

echo "<h3>نتائج القراءة:</h3>";
echo "<p>عدد الصفوف: " . count($data) . "</p>";

if (!empty($data)) {
    // إنشاء ملف CSV
    $csv_filename = 'converted_' . time() . '.csv';
    $csv_content = '';
    
    // إضافة رأس الجدول
    $csv_content .= "Product Name,Price\n";
    
    // إضافة البيانات مع حفظ الأسماء كاملة
    foreach ($data as $row) {
        if (count($row) >= 2) {
            // حفظ الاسم كاملاً مع معالجة الفواصل
            $name = trim($row[0]);
            $name = str_replace('"', '""', $name); // escape quotes
            $price = is_numeric($row[1]) ? $row[1] : '0';
            $csv_content .= "\"$name\",$price\n";
        } else if (count($row) == 1) {
            $name = trim($row[0]);
            $name = str_replace('"', '""', $name);
            $csv_content .= "\"$name\",0\n";
        }
    }
    
    // حفظ الملف
    file_put_contents($csv_filename, $csv_content);
    
    echo "<h3>✅ تم التحويل بنجاح!</h3>";
    echo "<p><a href='$csv_filename' download style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📥 تحميل ملف CSV</a></p>";
    
    echo "<h3>معاينة البيانات:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
    echo "<tr style='background: #f8f9fa;'><th>اسم المنتج</th><th>السعر</th></tr>";
    
    $preview_count = min(10, count($data));
    for ($i = 0; $i < $preview_count; $i++) {
        $row = $data[$i];
        echo "<tr>";
        echo "<td style='padding: 8px;'>" . htmlspecialchars($row[0] ?? '') . "</td>";
        echo "<td style='padding: 8px;'>" . htmlspecialchars($row[1] ?? '0') . "</td>";
        echo "</tr>";
    }
    
    if (count($data) > 10) {
        echo "<tr><td colspan='2' style='text-align: center; color: #666;'>... و " . (count($data) - 10) . " منتج آخر</td></tr>";
    }
    
    echo "</table>";
    
    echo "<div style='margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 10px;'>";
    echo "<h3>الخطوة التالية:</h3>";
    echo "<ol>";
    echo "<li>حمل ملف CSV من الرابط أعلاه</li>";
    echo "<li><a href='admin/index.php?page=warehouse/upload'>اذهب لصفحة رفع المخزن</a></li>";
    echo "<li>ارفع ملف CSV الجديد</li>";
    echo "</ol>";
    echo "</div>";
    
} else {
    echo "<p style='color: red;'>❌ فشل في قراءة البيانات من الملف</p>";
    
    echo "<div style='margin-top: 20px; padding: 20px; background: #fff3cd; border-radius: 10px;'>";
    echo "<h3>جرب هذا:</h3>";
    echo "<ol>";
    echo "<li>افتح ملف Excel</li>";
    echo "<li>حدد البيانات فقط (بدون تنسيق)</li>";
    echo "<li>انسخها والصقها في ملف نصي جديد</li>";
    echo "<li>احفظ الملف باسم 'products.csv'</li>";
    echo "<li>ارفعه في نظام المخزن</li>";
    echo "</ol>";
    echo "</div>";
}

echo "<p style='margin-top: 30px;'><a href='excel_to_csv_converter.php'>🔄 تحويل ملف آخر</a></p>";
?>
