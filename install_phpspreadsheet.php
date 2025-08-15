<?php
// تثبيت PhpSpreadsheet لقراءة Excel مباشرة

echo "<h2>🔧 تثبيت مكتبة قراءة Excel</h2>";

// إنشاء مجلد vendor إذا لم يكن موجود
if (!file_exists('vendor')) {
    mkdir('vendor', 0755, true);
    echo "<p>✅ تم إنشاء مجلد vendor</p>";
}

// تحميل PhpSpreadsheet مبسط
$phpspreadsheet_url = "https://github.com/PHPOffice/PhpSpreadsheet/archive/refs/heads/master.zip";

echo "<h3>📥 تحميل مكتبة PhpSpreadsheet...</h3>";

// محاولة تحميل المكتبة
$zip_file = 'phpspreadsheet.zip';

// استخدام cURL لتحميل الملف
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $phpspreadsheet_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$zip_content = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200 && $zip_content) {
    file_put_contents($zip_file, $zip_content);
    echo "<p>✅ تم تحميل المكتبة بنجاح</p>";
    
    // استخراج الملف
    $zip = new ZipArchive();
    if ($zip->open($zip_file) === TRUE) {
        $zip->extractTo('vendor/');
        $zip->close();
        echo "<p>✅ تم استخراج المكتبة</p>";
        
        // حذف ملف ZIP
        unlink($zip_file);
        
        echo "<p>✅ تم التثبيت بنجاح!</p>";
        
    } else {
        echo "<p style='color: red;'>❌ فشل في استخراج المكتبة</p>";
    }
} else {
    echo "<p style='color: red;'>❌ فشل في تحميل المكتبة</p>";
    echo "<p>كود الخطأ: $http_code</p>";
}

// إنشاء قارئ Excel مبسط بدون مكتبات خارجية
echo "<h3>🔧 إنشاء قارئ Excel مبسط...</h3>";

$simple_excel_reader = '<?php
// قارئ Excel مبسط يدعم العربية والإنجليزية

class SimpleExcelReader {
    
    public static function readFile($file_path) {
        $data = [];
        
        try {
            // قراءة الملف كبيانات خام
            $content = file_get_contents($file_path);
            
            if (empty($content)) {
                return [];
            }
            
            // تحديد نوع الملف
            $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
            
            if ($file_extension === "xlsx") {
                return self::readXLSX($content);
            } else if ($file_extension === "xls") {
                return self::readXLS($content);
            }
            
        } catch (Exception $e) {
            error_log("Excel Reader Error: " . $e->getMessage());
        }
        
        return [];
    }
    
    private static function readXLSX($content) {
        $data = [];
        
        try {
            // إنشاء ملف مؤقت
            $temp_file = tempnam(sys_get_temp_dir(), "xlsx_");
            file_put_contents($temp_file, $content);
            
            $zip = new ZipArchive();
            if ($zip->open($temp_file) === TRUE) {
                
                // قراءة النصوص المشتركة
                $sharedStrings = [];
                $sharedStringsXML = $zip->getFromName("xl/sharedStrings.xml");
                
                if ($sharedStringsXML) {
                    $dom = new DOMDocument();
                    $dom->loadXML($sharedStringsXML);
                    $xpath = new DOMXPath($dom);
                    
                    $textNodes = $xpath->query("//t");
                    foreach ($textNodes as $textNode) {
                        $sharedStrings[] = $textNode->nodeValue;
                    }
                }
                
                // قراءة بيانات الورقة
                $worksheetXML = $zip->getFromName("xl/worksheets/sheet1.xml");
                
                if ($worksheetXML) {
                    $dom = new DOMDocument();
                    $dom->loadXML($worksheetXML);
                    $xpath = new DOMXPath($dom);
                    
                    $rows = $xpath->query("//row");
                    
                    foreach ($rows as $row) {
                        $rowData = [];
                        $cells = $xpath->query(".//c", $row);
                        
                        foreach ($cells as $cell) {
                            $cellType = $cell->getAttribute("t");
                            $valueNode = $xpath->query(".//v", $cell)->item(0);
                            
                            $cellValue = "";
                            if ($valueNode) {
                                if ($cellType == "s" && isset($sharedStrings[(int)$valueNode->nodeValue])) {
                                    $cellValue = $sharedStrings[(int)$valueNode->nodeValue];
                                } else {
                                    $cellValue = $valueNode->nodeValue;
                                }
                            }
                            
                            $rowData[] = $cellValue;
                        }
                        
                        if (!empty(array_filter($rowData))) {
                            $data[] = $rowData;
                        }
                    }
                }
                
                $zip->close();
            }
            
            // حذف الملف المؤقت
            unlink($temp_file);
            
        } catch (Exception $e) {
            error_log("XLSX Reader Error: " . $e->getMessage());
        }
        
        return $data;
    }
    
    private static function readXLS($content) {
        // قراءة XLS بطريقة مبسطة
        $data = [];
        
        // استخراج النصوص العربية والإنجليزية
        preg_match_all("/[\x{0600}-\x{06FF}A-Za-z][\x{0600}-\x{06FF}A-Za-z\s\d\-\.]{2,}/u", $content, $text_matches);
        
        // استخراج الأرقام
        preg_match_all("/\b\d{2,}\b/", $content, $number_matches);
        
        $texts = array_unique($text_matches[0]);
        $numbers = array_unique($number_matches[0]);
        
        // ربط النصوص بالأرقام
        $text_index = 0;
        $number_index = 0;
        
        while ($text_index < count($texts) && $number_index < count($numbers)) {
            $text = trim($texts[$text_index]);
            $number = trim($numbers[$number_index]);
            
            if (strlen($text) > 3 && intval($number) > 0) {
                $data[] = [$text, $number];
            }
            
            $text_index++;
            $number_index++;
        }
        
        return $data;
    }
}
?>';

file_put_contents('admin/warehouse/simple_excel_reader.php', $simple_excel_reader);
echo "<p>✅ تم إنشاء قارئ Excel مبسط</p>";

echo "<h3>🎯 الخطوة التالية:</h3>";
echo "<p><a href='test_excel_reader.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>اختبار قارئ Excel الجديد</a></p>";

?>
