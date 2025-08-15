<?php
// أداة إصلاح ترميز ملفات Excel

echo "<h2>🔧 أداة إصلاح ترميز Excel</h2>";

if (!isset($_FILES['excel_file'])) {
    echo '<div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 2px solid #dc3545; border-radius: 15px; background: #f8f9fa;">';
    echo '<h3>🚨 هذه الأداة لإصلاح ملفات Excel المعطوبة</h3>';
    echo '<form method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="excel_file" accept=".xlsx,.xls" style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;">';
    echo '<button type="submit" style="background: #dc3545; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; width: 100%;">🔧 إصلاح الملف</button>';
    echo '</form>';
    echo '</div>';
    
    echo '<div style="margin-top: 30px; padding: 20px; background: #fff3cd; border-radius: 10px;">';
    echo '<h3>⚠️ متى تستخدم هذه الأداة؟</h3>';
    echo '<ul>';
    echo '<li>عندما يظهر اسم الملف رموز غريبة مثل ��</li>';
    echo '<li>عندما تظهر النصوص العربية كـ ؟؟؟</li>';
    echo '<li>عندما لا يقرأ النظام الملف نهائياً</li>';
    echo '<li>عندما تكون البيانات مختلطة عربي/إنجليزي</li>';
    echo '</ul>';
    echo '</div>';
    
    exit;
}

$file = $_FILES['excel_file'];

echo "<h3>📄 معلومات الملف الأصلي:</h3>";
echo "<p>الاسم: " . htmlspecialchars($file['name']) . "</p>";
echo "<p>الحجم: " . number_format($file['size']) . " بايت</p>";

// قراءة المحتوى الخام
$content = file_get_contents($file['tmp_name']);
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

echo "<h3>🔍 تحليل المشكلة:</h3>";

// فحص التوقيع
$signature = bin2hex(substr($content, 0, 8));
echo "<p>التوقيع الثنائي: $signature</p>";

if ($file_extension === 'xlsx') {
    echo "<p>نوع الملف: Excel حديث (XLSX)</p>";
    
    // محاولة إصلاح XLSX
    $fixed_content = fixXLSXEncoding($content);
    
} else {
    echo "<p>نوع الملف: Excel قديم (XLS)</p>";
    
    // محاولة إصلاح XLS
    $fixed_content = fixXLSEncoding($content);
}

if ($fixed_content !== false) {
    // حفظ الملف المصلح
    $fixed_filename = 'fixed_' . time() . '.' . $file_extension;
    file_put_contents($fixed_filename, $fixed_content);
    
    echo "<h3>✅ تم إصلاح الملف!</h3>";
    echo "<p><a href='$fixed_filename' download style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📥 تحميل الملف المصلح</a></p>";
    
    // اختبار الملف المصلح
    echo "<h3>🧪 اختبار الملف المصلح:</h3>";
    
    require_once('admin/warehouse/binary_excel_reader.php');
    $test_data = readExcelBinary($fixed_filename);
    
    if (!empty($test_data)) {
        echo "<p style='color: green;'>✅ نجح الإصلاح! تم قراءة " . count($test_data) . " صف</p>";
        
        echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
        echo "<tr><th>اسم المنتج</th><th>السعر</th></tr>";
        
        for ($i = 0; $i < min(5, count($test_data)); $i++) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($test_data[$i][0] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($test_data[$i][1] ?? '') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p><a href='admin/index.php?page=warehouse/upload' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 استخدم الملف في النظام</a></p>";
        
    } else {
        echo "<p style='color: red;'>❌ الإصلاح لم ينجح تماماً</p>";
    }
    
} else {
    echo "<h3>❌ فشل في إصلاح الملف</h3>";
    echo "<p>جرب:</p>";
    echo "<ul>";
    echo "<li>فتح الملف في Excel وحفظه مرة أخرى</li>";
    echo "<li>نسخ البيانات ولصقها في ملف جديد</li>";
    echo "<li>تصدير البيانات كـ CSV UTF-8</li>";
    echo "</ul>";
}

function fixXLSXEncoding($content) {
    try {
        // إنشاء ملف مؤقت
        $temp_file = tempnam(sys_get_temp_dir(), 'xlsx_fix_');
        file_put_contents($temp_file, $content);
        
        $zip = new ZipArchive();
        if ($zip->open($temp_file) === TRUE) {
            
            // إصلاح ملف النصوص المشتركة
            $sharedStringsXML = $zip->getFromName('xl/sharedStrings.xml');
            
            if ($sharedStringsXML) {
                // تجربة ترميزات مختلفة
                $encodings = ['UTF-8', 'UTF-16LE', 'UTF-16BE', 'Windows-1256', 'Windows-1252'];
                $fixed_xml = $sharedStringsXML;
                
                foreach ($encodings as $encoding) {
                    try {
                        $test_conversion = @mb_convert_encoding($sharedStringsXML, 'UTF-8', $encoding);
                        if ($test_conversion && preg_match('/[\x{0600}-\x{06FF}]/u', $test_conversion)) {
                            $fixed_xml = $test_conversion;
                            break;
                        }
                    } catch (Exception $e) {
                        continue;
                    }
                }
                
                // استبدال الملف في الأرشيف
                $zip->deleteName('xl/sharedStrings.xml');
                $zip->addFromString('xl/sharedStrings.xml', $fixed_xml);
            }
            
            $zip->close();
            
            // قراءة المحتوى المصلح
            $fixed_content = file_get_contents($temp_file);
            unlink($temp_file);
            
            return $fixed_content;
        }
        
        unlink($temp_file);
        
    } catch (Exception $e) {
        error_log('XLSX Fix Error: ' . $e->getMessage());
    }
    
    return false;
}

function fixXLSEncoding($content) {
    try {
        // تجربة ترميزات مختلفة للملف كاملاً
        $encodings = ['UTF-8', 'UTF-16LE', 'UTF-16BE', 'Windows-1256', 'Windows-1252', 'ISO-8859-1'];
        
        foreach ($encodings as $encoding) {
            try {
                $converted = @mb_convert_encoding($content, 'UTF-8', $encoding);
                if ($converted && preg_match('/[\x{0600}-\x{06FF}]/u', $converted)) {
                    return $converted;
                }
            } catch (Exception $e) {
                continue;
            }
        }
        
        // إذا فشلت كل المحاولات، أرجع المحتوى الأصلي
        return $content;
        
    } catch (Exception $e) {
        error_log('XLS Fix Error: ' . $e->getMessage());
    }
    
    return false;
}

echo "<p style='margin-top: 30px;'><a href='fix_excel_encoding.php'>🔄 إصلاح ملف آخر</a></p>";
?>
