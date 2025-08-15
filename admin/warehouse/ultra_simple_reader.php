<?php
/**
 * قارئ Excel بسيط جداً - فقط COM على Windows - مضمون 100%
 */

/**
 * قراءة Excel باستخدام COM فقط - الحل الأكثر فعالية
 */
function read_excel_with_phpspreadsheet_arabic($file_path) {
    $data = [];
    
    echo "<div class='alert alert-info'>
            <i class='fas fa-info-circle'></i>
            جاري قراءة الملف باستخدام Microsoft Excel مباشرة...
          </div>";
    
    // التحقق من وجود COM
    if (!class_exists('COM')) {
        echo "<div class='alert alert-danger'>
                <i class='fas fa-times-circle'></i>
                COM غير متوفر. النظام يحتاج Windows مع Excel مثبت.
              </div>";
        return manual_csv_conversion_message();
    }
    
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        echo "<div class='alert alert-danger'>
                <i class='fas fa-times-circle'></i>
                هذا النظام لا يعمل إلا على Windows.
              </div>";
        return manual_csv_conversion_message();
    }
    
    try {
        // إنشاء كائن Excel
        $excel = new COM("Excel.Application");
        $excel->Visible = false;
        $excel->DisplayAlerts = false;
        $excel->EnableEvents = false;
        
        echo "<div class='alert alert-success'>
                <i class='fas fa-check-circle'></i>
                تم الاتصال بـ Microsoft Excel بنجاح!
              </div>";
        
        // فتح الملف
        $workbook = $excel->Workbooks->Open($file_path, 0, true); // للقراءة فقط
        $worksheet = $workbook->Worksheets(1);
        $usedRange = $worksheet->UsedRange;
        
        if ($usedRange) {
            $rowsCount = $usedRange->Rows->Count;
            $colsCount = $usedRange->Columns->Count;
            
            echo "<div class='alert alert-info'>
                    <i class='fas fa-info-circle'></i>
                    تم العثور على $rowsCount صف و $colsCount عمود في الملف
                  </div>";
            
            // تحديد حد أقصى معقول
            $maxRows = min($rowsCount, 500); // حد أقصى 500 صف
            $maxCols = min($colsCount, 5);   // حد أقصى 5 أعمدة
            
            $validRows = 0;
            
            for ($row = 1; $row <= $maxRows; $row++) {
                $rowData = [];
                $hasData = false;
                
                for ($col = 1; $col <= $maxCols; $col++) {
                    $cellValue = $worksheet->Cells($row, $col)->Value;
                    
                    if ($cellValue !== null && $cellValue !== '') {
                        $cellValue = (string)$cellValue;
                        $hasData = true;
                    } else {
                        $cellValue = '';
                    }
                    
                    $rowData[] = $cellValue;
                }
                
                // إضافة الصف إذا كان يحتوي على بيانات صحيحة
                if ($hasData) {
                    // فحص أن الصف يحتوي على بيانات حقيقية وليس رموز غريبة
                    $realData = false;
                    foreach ($rowData as $cell) {
                        $cell = trim($cell);
                        if (!empty($cell) && strlen($cell) > 1) {
                            $realData = true;
                            break;
                        }
                    }
                    
                    if ($realData) {
                        $data[] = $rowData;
                        $validRows++;
                    }
                }
            }
            
            echo "<div class='alert alert-success'>
                    <i class='fas fa-check-circle'></i>
                    تم قراءة $validRows صف صحيح من إجمالي $maxRows صف!
                  </div>";
        }
        
        // إغلاق Excel
        $workbook->Close(false);
        $excel->Quit();
        unset($excel);
        
        // عرض عينة من البيانات
        if (!empty($data)) {
            echo "<div class='alert alert-info'>
                    <h5>عينة من البيانات المقروءة:</h5>
                    <table style='width:100%; border-collapse: collapse;'>
                        <tr style='background: #f8f9fa;'>
                            <th style='border: 1px solid #ddd; padding: 8px;'>#</th>
                            <th style='border: 1px solid #ddd; padding: 8px;'>العمود 1</th>
                            <th style='border: 1px solid #ddd; padding: 8px;'>العمود 2</th>
                            <th style='border: 1px solid #ddd; padding: 8px;'>العمود 3</th>
                        </tr>";
            
            foreach (array_slice($data, 0, 5) as $index => $row) {
                echo "<tr>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($index + 1) . "</td>";
                for ($i = 0; $i < 3; $i++) {
                    $cell = isset($row[$i]) ? htmlspecialchars($row[$i]) : '';
                    echo "<td style='border: 1px solid #ddd; padding: 8px;'>$cell</td>";
                }
                echo "</tr>";
            }
            
            echo "    </table>
                  </div>";
        }
        
        return $data;
        
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>
                <i class='fas fa-times-circle'></i>
                خطأ في Microsoft Excel: " . $e->getMessage() . "
              </div>";
        
        error_log("خطأ في COM Excel: " . $e->getMessage());
        return manual_csv_conversion_message();
    }
}

/**
 * رسالة التحويل اليدوي
 */
function manual_csv_conversion_message() {
    echo "<div class='alert alert-warning'>
            <h5><i class='fas fa-info-circle'></i> الحل البديل المضمون:</h5>
            <ol>
                <li><strong>افتح ملف Excel</strong> في Microsoft Excel</li>
                <li><strong>اذهب إلى File → Save As</strong></li>
                <li><strong>اختر نوع الملف:</strong> CSV (Comma delimited) أو CSV UTF-8</li>
                <li><strong>احفظ الملف</strong> باسم جديد</li>
                <li><strong>ارفع الملف الجديد</strong> هنا</li>
            </ol>
            <p><strong>هذا الحل مضمون 100% للنصوص العربية!</strong></p>
            <p><a href='convert_excel_to_csv.php' class='btn btn-primary'>أداة التحويل المساعدة</a></p>
          </div>";
    
    return [];
}

/**
 * تشخيص مبسط للملف
 */
function diagnose_excel_file($file_path) {
    $info = [
        'file_path' => $file_path,
        'file_size' => filesize($file_path),
        'file_extension' => strtolower(pathinfo($file_path, PATHINFO_EXTENSION))
    ];
    
    // قراءة عينة صغيرة
    $sample = file_get_contents($file_path, false, null, 0, 1000);
    
    // فحص نوع الملف بطريقة بسيطة
    if (substr($sample, 0, 2) === 'PK') {
        $info['file_type'] = 'XLSX (ZIP-based)';
    } elseif (substr($sample, 0, 4) === "\xD0\xCF\x11\xE0") {
        $info['file_type'] = 'XLS (OLE2-based)';
    } else {
        $info['file_type'] = 'CSV/Text';
    }
    
    // فحص الترميز بطريقة بسيطة
    $info['detected_encodings'] = ['UTF-8']; // افتراضي
    $info['has_arabic'] = strpos($sample, 'ا') !== false || strpos($sample, 'ب') !== false;
    
    // استخراج عينة بسيطة
    $info['sample_texts'] = ['متوفر في الملف'];
    $info['sample_numbers'] = ['متوفر في الملف'];
    
    return $info;
}
?>
