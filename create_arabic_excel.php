<?php
// إنشاء ملف Excel تجريبي بالعربية لاختبار النظام

header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

echo '<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إنشاء ملف Excel تجريبي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h3>📄 إنشاء ملف Excel تجريبي بالعربية</h3>
        </div>
        <div class="card-body">';

// البيانات التجريبية العربية
$products = [
    ['اسم المنتج', 'السعر'],
    ['شاشة آيفون 13', '250000'],
    ['غطاء سامسونج جالاكسي', '25000'],
    ['بطارية هواوي P30', '80000'],
    ['كابل شحن آيفون', '45000'],
    ['زجاج خلفي سامسونج نوت', '35000'],
    ['أدوات إصلاح الهواتف', '15000'],
    ['شاشة شاومي ريدمي', '180000'],
    ['غطاء آيفون 12', '20000'],
    ['شاحن ون بلس', '30000'],
    ['سماعات سامسونج جالاكسي', '50000'],
    ['LCD A04S - A13 5G HONG KONG وكة', '1000'],
    ['LCD A13 4G - M33 5G فيتنام خرطة', '11000'],
    ['LCD OPPO RENO7 4G REALME', '52000'],
    ['LCD IIPRO MAX OLED FLY LCD', '41000'],
    ['LCD I2 PRO MAX FLY OLED', '246000'],
    ['LCD I7 PLUS FLY LCD BLACK', '13250'],
    ['LCD I7 PLUS FLY LCD WHITE', '13500'],
    ['LCD 8 PLUS FLY LCD BLACK', '13500'],
    ['LCD A02S - A03S HONG KONG', '9500']
];

// إنشاء ملف CSV بترميز UTF-8 مع BOM
$csv_content = "\xEF\xBB\xBF"; // UTF-8 BOM
foreach ($products as $row) {
    $csv_content .= '"' . implode('","', $row) . '"' . "\n";
}

// حفظ الملف
$filename = 'test_arabic_products_' . date('Y_m_d_H_i_s') . '.csv';
file_put_contents($filename, $csv_content);

echo '<div class="alert alert-success">
        <h5>✅ تم إنشاء الملف بنجاح!</h5>
        <p><strong>اسم الملف:</strong> ' . $filename . '</p>
        <p><strong>عدد المنتجات:</strong> ' . (count($products) - 1) . '</p>
        <p><strong>الترميز:</strong> UTF-8 مع BOM</p>
      </div>';

echo '<div class="alert alert-info">
        <h5>📋 محتوى الملف:</h5>
        <div style="max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px;">';

foreach ($products as $index => $row) {
    $style = $index == 0 ? 'font-weight: bold; color: #0066cc;' : '';
    echo '<div style="' . $style . '">' . ($index + 1) . '. ' . implode(' | ', $row) . '</div>';
}

echo '</div></div>';

echo '<div class="mt-3">
        <a href="' . $filename . '" class="btn btn-primary" download>📥 تحميل الملف</a>
        <a href="test_arabic_upload.php" class="btn btn-success">🧪 اختبار الرفع</a>
        <a href="admin/warehouse/upload.php" class="btn btn-warning">📤 رفع في النظام</a>
      </div>';

// إنشاء ملف Excel حقيقي أيضاً (بصيغة XML)
$excel_content = '<?xml version="1.0" encoding="UTF-8"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Worksheet ss:Name="Sheet1">
  <Table>';

foreach ($products as $row) {
    $excel_content .= '<Row>';
    foreach ($row as $cell) {
        $excel_content .= '<Cell><Data ss:Type="String">' . htmlspecialchars($cell, ENT_XML1, 'UTF-8') . '</Data></Cell>';
    }
    $excel_content .= '</Row>';
}

$excel_content .= '</Table>
 </Worksheet>
</Workbook>';

$excel_filename = 'test_arabic_products_' . date('Y_m_d_H_i_s') . '.xls';
file_put_contents($excel_filename, $excel_content);

echo '<div class="alert alert-warning mt-3">
        <h5>📊 تم إنشاء ملف Excel أيضاً!</h5>
        <p><strong>اسم الملف:</strong> ' . $excel_filename . '</p>
        <p><strong>النوع:</strong> Excel XML</p>
        <a href="' . $excel_filename . '" class="btn btn-outline-primary" download>📥 تحميل Excel</a>
      </div>';

echo '</div>
    </div>
</div>
</body>
</html>';
?>
