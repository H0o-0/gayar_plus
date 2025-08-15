<?php
// إنشاء ملف Excel بسيط جداً

echo "<h2>إنشاء ملف Excel تجريبي</h2>";

// بيانات تجريبية بسيطة
$products = [
    "Product Name,Price",
    "iPhone 13 Screen,250",
    "Samsung Case,25", 
    "LCD Display,180",
    "Phone Battery,80",
    "Charger Cable,45",
    "Repair Tools,30",
    "Screen Protector,15",
    "Phone Holder,20",
    "Wireless Charger,60"
];

// إنشاء ملف CSV بسيط
$csv_content = implode("\n", $products);
file_put_contents('test_products_simple.csv', $csv_content);

echo "<h3>تم إنشاء ملف CSV بسيط:</h3>";
echo "<pre>" . htmlspecialchars($csv_content) . "</pre>";

echo "<h3>الملفات المتاحة للاختبار:</h3>";
echo "<ul>";
echo "<li><a href='test_products_simple.csv' download>تحميل test_products_simple.csv</a></li>";
echo "<li><a href='debug_excel.php'>أداة تشخيص Excel</a></li>";
echo "<li><a href='admin/index.php?page=warehouse/upload'>صفحة الرفع</a></li>";
echo "</ul>";

echo "<h3>تعليمات:</h3>";
echo "<ol>";
echo "<li>جرب رفع ملف CSV البسيط أولاً للتأكد من عمل النظام</li>";
echo "<li>إذا نجح، جرب ملف Excel الأصلي</li>";
echo "<li>استخدم أداة التشخيص لفهم محتوى ملف Excel</li>";
echo "</ol>";

// إنشاء ملف HTML يحاكي Excel
$html_table = '<table border="1">
<tr><th>Product Name</th><th>Price</th></tr>
<tr><td>iPhone 13 Screen</td><td>250</td></tr>
<tr><td>Samsung Case</td><td>25</td></tr>
<tr><td>LCD Display</td><td>180</td></tr>
<tr><td>Phone Battery</td><td>80</td></tr>
<tr><td>Charger Cable</td><td>45</td></tr>
</table>';

file_put_contents('test_table.html', $html_table);

echo "<h3>جدول تجريبي (HTML):</h3>";
echo $html_table;

echo "<p><strong>نصيحة:</strong> إذا كان ملف Excel معقد، انسخ البيانات والصقها في ملف Excel جديد، ثم احفظه كـ CSV.</p>";
?>
