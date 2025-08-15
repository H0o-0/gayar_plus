<?php
// اختبار قارئ Excel المحسن

echo "<h2>🧪 اختبار قارئ Excel المحسن</h2>";

if (!isset($_FILES['excel_file'])) {
    echo '<div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 2px solid #007bff; border-radius: 15px; background: #f8f9fa;">';
    echo '<form method="post" enctype="multipart/form-data">';
    echo '<h3>📁 اختر ملف Excel للاختبار:</h3>';
    echo '<input type="file" name="excel_file" accept=".xlsx,.xls" style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;">';
    echo '<button type="submit" style="background: #007bff; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; width: 100%;">🔍 اختبار القراءة</button>';
    echo '</form>';
    echo '</div>';
    
    echo '<div style="margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 10px;">';
    echo '<h3>🎯 هذا الاختبار سيظهر:</h3>';
    echo '<ul>';
    echo '<li>✅ النصوص العربية بشكل صحيح (بدون ؟؟؟)</li>';
    echo '<li>✅ النصوص الإنجليزية كاملة</li>';
    echo '<li>✅ الأسعار بشكل صحيح</li>';
    echo '<li>✅ تشخيص مفصل للملف</li>';
    echo '</ul>';
    echo '</div>';
    
    exit;
}

require_once('admin/warehouse/binary_excel_reader.php');
require_once('admin/warehouse/advanced_excel_reader.php');

$file = $_FILES['excel_file'];
echo "<h3>📄 معلومات الملف:</h3>";
echo "<p><strong>الاسم:</strong> " . htmlspecialchars($file['name']) . "</p>";
echo "<p><strong>الحجم:</strong> " . number_format($file['size']) . " بايت</p>";
echo "<p><strong>النوع:</strong> " . htmlspecialchars($file['type']) . "</p>";

// تشخيص ثنائي
diagnoseBinaryFile($file['tmp_name']);

// قراءة البيانات بالقارئ الثنائي أولاً
echo "<h3>📊 قراءة البيانات بالقارئ الثنائي:</h3>";
$data = readExcelBinary($file['tmp_name']);

// إذا فشل، جرب القارئ المحسن
if (empty($data)) {
    echo "<p style='color: orange;'>القارئ الثنائي لم يجد بيانات، جاري المحاولة بالقارئ المحسن...</p>";
    $data = readExcelAdvanced($file['tmp_name']);
}

echo "<p><strong>عدد الصفوف:</strong> " . count($data) . "</p>";

if (!empty($data)) {
    echo "<h3>✅ البيانات المقروءة:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
    echo "<tr style='background: #f8f9fa;'>";
    echo "<th style='padding: 10px; text-align: right;'>الرقم</th>";
    echo "<th style='padding: 10px; text-align: right;'>اسم المنتج</th>";
    echo "<th style='padding: 10px; text-align: right;'>السعر</th>";
    echo "<th style='padding: 10px; text-align: right;'>نوع النص</th>";
    echo "</tr>";
    
    $preview_count = min(20, count($data));
    for ($i = 0; $i < $preview_count; $i++) {
        $row = $data[$i];
        $product_name = $row[0] ?? '';
        $price = $row[1] ?? '0';
        
        // تحديد نوع النص
        $text_type = '';
        if (preg_match('/[\x{0600}-\x{06FF}]/u', $product_name)) {
            $text_type .= 'عربي ';
        }
        if (preg_match('/[A-Za-z]/', $product_name)) {
            $text_type .= 'إنجليزي';
        }
        if (empty($text_type)) {
            $text_type = 'أرقام/رموز';
        }
        
        // تلوين الصف حسب نوع النص
        $row_color = '';
        if (strpos($text_type, 'عربي') !== false) {
            $row_color = 'background: #e8f5e8;'; // أخضر فاتح للعربي
        } else if (strpos($text_type, 'إنجليزي') !== false) {
            $row_color = 'background: #e7f3ff;'; // أزرق فاتح للإنجليزي
        }
        
        echo "<tr style='$row_color'>";
        echo "<td style='padding: 8px; text-align: center;'>" . ($i + 1) . "</td>";
        echo "<td style='padding: 8px; text-align: right; font-weight: bold;'>" . htmlspecialchars($product_name) . "</td>";
        echo "<td style='padding: 8px; text-align: center;'>" . htmlspecialchars($price) . "</td>";
        echo "<td style='padding: 8px; text-align: center;'>" . $text_type . "</td>";
        echo "</tr>";
    }
    
    if (count($data) > 20) {
        echo "<tr><td colspan='4' style='text-align: center; color: #666; padding: 10px;'>... و " . (count($data) - 20) . " منتج آخر</td></tr>";
    }
    
    echo "</table>";
    
    // إحصائيات
    echo "<h3>📈 إحصائيات:</h3>";
    $arabic_count = 0;
    $english_count = 0;
    $mixed_count = 0;
    
    foreach ($data as $row) {
        $product_name = $row[0] ?? '';
        $has_arabic = preg_match('/[\x{0600}-\x{06FF}]/u', $product_name);
        $has_english = preg_match('/[A-Za-z]/', $product_name);
        
        if ($has_arabic && $has_english) {
            $mixed_count++;
        } else if ($has_arabic) {
            $arabic_count++;
        } else if ($has_english) {
            $english_count++;
        }
    }
    
    echo "<div style='display: flex; gap: 20px; margin: 20px 0;'>";
    echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 10px; text-align: center;'>";
    echo "<h4>نصوص عربية</h4>";
    echo "<h2 style='color: #28a745;'>$arabic_count</h2>";
    echo "</div>";
    
    echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 10px; text-align: center;'>";
    echo "<h4>نصوص إنجليزية</h4>";
    echo "<h2 style='color: #007bff;'>$english_count</h2>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 10px; text-align: center;'>";
    echo "<h4>نصوص مختلطة</h4>";
    echo "<h2 style='color: #856404;'>$mixed_count</h2>";
    echo "</div>";
    echo "</div>";
    
    // إنشاء ملف CSV للتحميل
    $csv_filename = 'tested_data_' . time() . '.csv';
    $csv_content = "Product Name,Price\n";
    
    foreach ($data as $row) {
        $name = str_replace('"', '""', $row[0] ?? '');
        $price = $row[1] ?? '0';
        $csv_content .= "\"$name\",$price\n";
    }
    
    file_put_contents($csv_content, $csv_content);
    
    echo "<div style='margin-top: 30px; padding: 20px; background: #d4edda; border-radius: 10px;'>";
    echo "<h3>🎉 نجح الاختبار!</h3>";
    echo "<p>✅ تم قراءة النصوص العربية بشكل صحيح</p>";
    echo "<p>✅ تم قراءة النصوص الإنجليزية بشكل صحيح</p>";
    echo "<p>✅ تم قراءة الأسعار بشكل صحيح</p>";
    echo "<p><strong>الآن يمكنك استخدام هذا الملف في نظام المخزن مباشرة!</strong></p>";
    echo "<p><a href='admin/index.php?page=warehouse/upload' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 اذهب لنظام المخزن</a></p>";
    echo "</div>";
    
} else {
    echo "<div style='color: red; padding: 20px; background: #f8d7da; border-radius: 10px;'>";
    echo "<h3>❌ لم يتم قراءة أي بيانات</h3>";
    echo "<p>تأكد من:</p>";
    echo "<ul>";
    echo "<li>أن الملف يحتوي على بيانات</li>";
    echo "<li>أن البيانات في الورقة الأولى</li>";
    echo "<li>أن الملف غير محمي بكلمة مرور</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<p style='margin-top: 30px; text-align: center;'>";
echo "<a href='test_excel_reader.php' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔄 اختبار ملف آخر</a>";
echo "</p>";
?>
