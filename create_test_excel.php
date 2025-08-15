<?php
// إنشاء ملف Excel بسيط للاختبار

echo "<h2>إنشاء ملف Excel تجريبي</h2>";

// بيانات تجريبية
$products = [
    ['Product Name', 'Price'],
    ['iPhone 13 Screen', '250'],
    ['Samsung Case', '25'],
    ['LCD Display', '180'],
    ['Phone Battery', '80'],
    ['Charger Cable', '45']
];

// إنشاء محتوى XML لـ Excel
$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetData>';

$rowNum = 1;
foreach ($products as $product) {
    $xml .= '<row r="' . $rowNum . '">';
    $colNum = 1;
    foreach ($product as $cell) {
        $colLetter = chr(64 + $colNum); // A, B, C...
        $xml .= '<c r="' . $colLetter . $rowNum . '" t="inlineStr">';
        $xml .= '<is><t>' . htmlspecialchars($cell) . '</t></is>';
        $xml .= '</c>';
        $colNum++;
    }
    $xml .= '</row>';
    $rowNum++;
}

$xml .= '</sheetData></worksheet>';

// حفظ كملف XML مؤقت
file_put_contents('test_excel_data.xml', $xml);

echo "<p>تم إنشاء ملف XML تجريبي: test_excel_data.xml</p>";

// إنشاء ملف CSV بسيط
$csv_content = '';
foreach ($products as $product) {
    $csv_content .= implode(',', $product) . "\n";
}

file_put_contents('test_simple.csv', $csv_content);

echo "<p>تم إنشاء ملف CSV بسيط: test_simple.csv</p>";

echo "<h3>محتوى الملف:</h3>";
echo "<pre>" . htmlspecialchars($csv_content) . "</pre>";

echo "<p><a href='admin/index.php?page=warehouse/upload'>جرب رفع test_simple.csv</a></p>";
?>
