<?php
/**
 * اختبار القارئ الجديد للـ Excel المحسن
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// تعيين الترميز
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

echo '<html>
<head>
    <meta charset="UTF-8">
    <title>اختبار قارئ Excel الجديد</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            direction: rtl; 
            text-align: right;
            padding: 20px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-info {
            color: #31708f;
            background-color: #d9edf7;
            border-color: #bce8f1;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #f2f2f2;
        }
        .test-files {
            margin: 20px 0;
        }
        .test-file {
            background: #f9f9f9;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>';

echo '<h1>🧪 اختبار قارئ Excel المحسن الجديد</h1>';

require_once('admin/warehouse/phpspreadsheet_reader.php');

// قائمة الملفات للاختبار
$testFiles = [
    'test_products.csv',
    'test_arabic_products.csv',
    'fixed_1755134105.xlsx',
    'test_arabic_excel.csv'
];

echo '<div class="test-files">
        <h2>📁 الملفات المتاحة للاختبار:</h2>';

foreach ($testFiles as $file) {
    if (file_exists($file)) {
        $fileSize = number_format(filesize($file));
        echo '<div class="test-file">
                <strong>' . $file . '</strong> 
                (' . $fileSize . ' بايت)
                <a href="?test=' . urlencode($file) . '" style="margin-right: 10px;">اختبار هذا الملف</a>
              </div>';
    }
}

echo '</div>';

// إذا تم اختيار ملف للاختبار
if (isset($_GET['test']) && !empty($_GET['test'])) {
    $testFile = $_GET['test'];
    
    if (file_exists($testFile) && in_array($testFile, $testFiles)) {
        echo '<hr>';
        echo '<h2>🔍 اختبار الملف: ' . $testFile . '</h2>';
        
        // تشخيص الملف أولاً
        echo '<h3>📋 تشخيص الملف:</h3>';
        $diagnosis = diagnose_excel_file($testFile);
        
        echo '<div class="alert alert-info">
                <h4>معلومات الملف:</h4>
                <ul>
                    <li>نوع الملف: ' . $diagnosis['file_type'] . '</li>
                    <li>حجم الملف: ' . number_format($diagnosis['file_size']) . ' بايت</li>
                    <li>الترميزات المكتشفة: ' . implode(', ', $diagnosis['detected_encodings']) . '</li>
                    <li>يحتوي على نصوص عربية: ' . ($diagnosis['has_arabic'] ? 'نعم' : 'لا') . '</li>';
        
        if (!empty($diagnosis['sample_texts'])) {
            echo '<li>عينة النصوص: ' . implode(', ', $diagnosis['sample_texts']) . '</li>';
        }
        
        if (!empty($diagnosis['sample_numbers'])) {
            echo '<li>عينة الأرقام: ' . implode(', ', $diagnosis['sample_numbers']) . '</li>';
        }
        
        echo '    </ul>
              </div>';
        
        // اختبار قراءة الملف
        echo '<h3>📖 نتيجة قراءة الملف:</h3>';
        
        $startTime = microtime(true);
        
        try {
            $data = read_excel_with_phpspreadsheet_arabic($testFile);
            
            $endTime = microtime(true);
            $readTime = round(($endTime - $startTime) * 1000, 2);
            
            if (!empty($data)) {
                echo '<div class="alert alert-success">
                        <h4>✅ تم قراءة الملف بنجاح!</h4>
                        <ul>
                            <li>عدد الصفوف المقروءة: ' . count($data) . '</li>
                            <li>وقت القراءة: ' . $readTime . ' ميللي ثانية</li>
                            <li>أقصى عدد أعمدة: ' . max(array_map('count', $data)) . '</li>
                        </ul>
                      </div>';
                
                // عرض أول 10 صفوف كعينة
                echo '<h3>📝 عينة من البيانات المقروءة (أول 10 صفوف):</h3>';
                echo '<table>';
                echo '<thead>
                        <tr>
                            <th>#</th>
                            <th>العمود 1</th>
                            <th>العمود 2</th>
                            <th>العمود 3</th>
                            <th>العمود 4</th>
                        </tr>
                      </thead>
                      <tbody>';
                
                $sampleRows = array_slice($data, 0, 10);
                foreach ($sampleRows as $index => $row) {
                    echo '<tr>';
                    echo '<td>' . ($index + 1) . '</td>';
                    for ($i = 0; $i < 4; $i++) {
                        $cellValue = isset($row[$i]) ? htmlspecialchars($row[$i]) : '-';
                        // تمييز الأسماء العربية
                        if (preg_match('/[\x{0600}-\x{06FF}]/u', $cellValue)) {
                            $cellValue = '<strong style="color: green;">' . $cellValue . '</strong>';
                        }
                        // تمييز الأرقام
                        if (is_numeric($cellValue) && $cellValue > 0) {
                            $cellValue = '<strong style="color: blue;">' . $cellValue . '</strong>';
                        }
                        echo '<td>' . $cellValue . '</td>';
                    }
                    echo '</tr>';
                }
                
                echo '    </tbody>
                      </table>';
                
                // إحصائيات مفيدة
                echo '<h3>📊 إحصائيات البيانات:</h3>';
                
                $totalCells = 0;
                $emptyCells = 0;
                $arabicCells = 0;
                $numericCells = 0;
                
                foreach ($data as $row) {
                    foreach ($row as $cell) {
                        $totalCells++;
                        
                        if (empty(trim($cell))) {
                            $emptyCells++;
                        } elseif (preg_match('/[\x{0600}-\x{06FF}]/u', $cell)) {
                            $arabicCells++;
                        } elseif (is_numeric($cell) && $cell > 0) {
                            $numericCells++;
                        }
                    }
                }
                
                echo '<div class="alert alert-info">
                        <h4>الإحصائيات:</h4>
                        <ul>
                            <li>إجمالي الخلايا: ' . number_format($totalCells) . '</li>
                            <li>الخلايا الفارغة: ' . number_format($emptyCells) . ' (' . round(($emptyCells/$totalCells)*100, 1) . '%)</li>
                            <li>الخلايا التي تحتوي على عربي: ' . number_format($arabicCells) . ' (' . round(($arabicCells/$totalCells)*100, 1) . '%)</li>
                            <li>الخلايا الرقمية: ' . number_format($numericCells) . ' (' . round(($numericCells/$totalCells)*100, 1) . '%)</li>
                        </ul>
                      </div>';
                
            } else {
                echo '<div class="alert alert-danger">
                        <h4>❌ لم يتم قراءة أي بيانات!</h4>
                        <p>الملف قد يكون فارغاً أو تالفاً أو محمي بكلمة مرور.</p>
                      </div>';
            }
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">
                    <h4>❌ خطأ في قراءة الملف!</h4>
                    <p>رسالة الخطأ: ' . htmlspecialchars($e->getMessage()) . '</p>
                  </div>';
        }
        
    } else {
        echo '<div class="alert alert-danger">
                <h4>❌ الملف غير موجود أو غير مسموح!</h4>
              </div>';
    }
}

// معلومات النظام
echo '<hr>';
echo '<h2>🔧 معلومات النظام:</h2>';
echo '<div class="alert alert-info">
        <h4>البيئة الحالية:</h4>
        <ul>
            <li>إصدار PHP: ' . phpversion() . '</li>
            <li>نظام التشغيل: ' . PHP_OS . '</li>
            <li>الترميز الداخلي: ' . mb_internal_encoding() . '</li>
            <li>ZipArchive متوفر: ' . (class_exists('ZipArchive') ? 'نعم' : 'لا') . '</li>
            <li>COM متوفر: ' . (class_exists('COM') ? 'نعم' : 'لا') . '</li>
            <li>SimpleXMLElement متوفر: ' . (class_exists('SimpleXMLElement') ? 'نعم' : 'لا') . '</li>
        </ul>
      </div>';

echo '</body></html>';
?>
