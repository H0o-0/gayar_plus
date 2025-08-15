<?php
/**
 * قارئ CSV محسن للنصوص العربية والرموز
 * يدعم جميع الأحرف العربية والرموز مثل الأقواس والنقاط والشرطات
 */

/**
 * قراءة ملف CSV مع دعم كامل للعربية والرموز
 */
function read_csv_arabic_enhanced($file_path) {
    $data = [];
    
    try {
        echo "<div class='alert alert-info'>
                <i class='fas fa-file-csv'></i>
                جاري قراءة ملف CSV مع دعم العربية والرموز...
              </div>";
        
        // قراءة محتوى الملف
        $content = file_get_contents($file_path);
        
        if (empty($content)) {
            echo "<div class='alert alert-danger'>
                    <i class='fas fa-times-circle'></i>
                    الملف فارغ أو لا يمكن قراءته
                  </div>";
            return [];
        }
        
        // إزالة BOM إذا وُجد
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
            echo "<div class='alert alert-info'>
                    <i class='fas fa-info-circle'></i>
                    تم إزالة UTF-8 BOM من الملف
                  </div>";
        }
        
        // تحديد أفضل ترميز للملف
        $originalContent = $content;
        $encoding = detect_best_encoding($content);
        
        if ($encoding !== 'UTF-8') {
            echo "<div class='alert alert-info'>
                    <i class='fas fa-info-circle'></i>
                    تم اكتشاف الترميز: $encoding - جاري التحويل لـ UTF-8
                  </div>";
            
            $content = convert_to_utf8($content, $encoding);
        }
        
        // تحديد فاصل الأعمدة
        $delimiter = detect_csv_delimiter($content);
        $delimiterName = get_delimiter_name($delimiter);
        
        echo "<div class='alert alert-info'>
                <i class='fas fa-info-circle'></i>
                تم اكتشاف فاصل الأعمدة: $delimiterName
              </div>";
        
        // تقسيم المحتوى إلى أسطر
        $lines = preg_split('/\r\n|\r|\n/', $content);
        
        $processedRows = 0;
        $validRows = 0;
        
        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $processedRows++;
            
            // تحليل السطر مع الحفاظ على الرموز
            $row = parse_csv_line_safe($line, $delimiter);
            
            // تنظيف البيانات مع الحفاظ على الرموز المفيدة
            $cleanRow = [];
            $hasValidData = false;
            
            foreach ($row as $cell) {
                $cell = clean_csv_cell($cell);
                $cleanRow[] = $cell;
                
                // فحص وجود بيانات صحيحة
                if (!empty(trim($cell)) && strlen(trim($cell)) > 1) {
                    $hasValidData = true;
                }
            }
            
            // إضافة الصف إذا كان يحتوي على بيانات صحيحة
            if ($hasValidData) {
                $data[] = $cleanRow;
                $validRows++;
            }
        }
        
        echo "<div class='alert alert-success'>
                <i class='fas fa-check-circle'></i>
                تم معالجة $processedRows سطر وقراءة $validRows صف صحيح!
              </div>";
        
        // عرض عينة من البيانات
        if (!empty($data)) {
            show_data_sample($data);
        }
        
    } catch (Exception $e) {
        error_log("خطأ في قراءة CSV: " . $e->getMessage());
        echo "<div class='alert alert-danger'>
                <i class='fas fa-times-circle'></i>
                خطأ في قراءة الملف: " . $e->getMessage() . "
              </div>";
    }
    
    return $data;
}

/**
 * اكتشاف أفضل ترميز للملف
 */
function detect_best_encoding($content) {
    // قائمة الترميزات مرتبة حسب الأولوية للعربية
    $encodings = ['UTF-8', 'Windows-1256', 'ISO-8859-6', 'CP1256'];
    
    foreach ($encodings as $encoding) {
        if ($encoding === 'UTF-8') {
            if (mb_check_encoding($content, 'UTF-8')) {
                // فحص وجود أحرف عربية
                if (strpos($content, 'ا') !== false || strpos($content, 'ب') !== false || strpos($content, 'ت') !== false) {
                    return 'UTF-8';
                }
            }
        } else {
            // للترميزات الأخرى، نجرب التحويل ونفحص النتيجة
            $converted = @iconv($encoding, 'UTF-8//IGNORE', $content);
            if ($converted && (strpos($converted, 'ا') !== false || strpos($converted, 'ب') !== false)) {
                return $encoding;
            }
        }
    }
    
    return 'UTF-8'; // افتراضي
}

/**
 * تحويل المحتوى إلى UTF-8
 */
function convert_to_utf8($content, $encoding) {
    // محاولة iconv أولاً
    $converted = @iconv($encoding, 'UTF-8//IGNORE', $content);
    if ($converted) {
        return $converted;
    }
    
    // محاولة mb_convert_encoding
    $converted = @mb_convert_encoding($content, 'UTF-8', $encoding);
    if ($converted) {
        return $converted;
    }
    
    return $content; // إرجاع الأصلي إذا فشل التحويل
}

/**
 * تحديد فاصل CSV بذكاء
 */
function detect_csv_delimiter($content) {
    $delimiters = [',', ';', "\t", '|'];
    $bestDelimiter = ',';
    $bestScore = 0;
    
    // أخذ عينة من أول 10 أسطر
    $lines = array_slice(preg_split('/\r\n|\r|\n/', $content), 0, 10);
    
    foreach ($delimiters as $delimiter) {
        $counts = [];
        $validLines = 0;
        
        foreach ($lines as $line) {
            if (!empty(trim($line))) {
                $count = substr_count($line, $delimiter);
                if ($count > 0) {
                    $counts[] = $count;
                    $validLines++;
                }
            }
        }
        
        if ($validLines > 0 && !empty($counts)) {
            // حساب الاتساق (أن عدد الفواصل ثابت في معظم الأسطر)
            $avgCount = array_sum($counts) / count($counts);
            $maxCount = max($counts);
            $minCount = min($counts);
            $consistency = $validLines > 1 ? 1 - (($maxCount - $minCount) / max($maxCount, 1)) : 1;
            
            $score = $avgCount * $consistency * $validLines;
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestDelimiter = $delimiter;
            }
        }
    }
    
    return $bestDelimiter;
}

/**
 * تحليل سطر CSV بطريقة آمنة مع الحفاظ على الرموز
 */
function parse_csv_line_safe($line, $delimiter) {
    // استخدام str_getcsv مع معالجة خاصة للرموز
    $row = str_getcsv($line, $delimiter, '"', '\\');
    
    // إذا فشلت str_getcsv، نستخدم التقسيم اليدوي
    if (empty($row) || (count($row) == 1 && empty(trim($row[0])))) {
        $row = explode($delimiter, $line);
    }
    
    return $row;
}

/**
 * تنظيف خلية CSV مع الحفاظ على الرموز المفيدة
 */
function clean_csv_cell($cell) {
    // إزالة المسافات من البداية والنهاية
    $cell = trim($cell);
    
    // إزالة علامات التنصيص الزائدة
    $cell = trim($cell, '"\'');
    
    // إزالة BOM إذا وُجد في الخلية
    $cell = preg_replace('/^\xEF\xBB\xBF/', '', $cell);
    
    // إزالة الرموز الضارة فقط (control characters) مع الحفاظ على الرموز المفيدة
    $cell = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $cell);
    
    // الحفاظ على الرموز المفيدة: الأقواس، النقاط، الشرطات، إلخ
    // لا نحذف: ( ) [ ] { } . , - _ + = / \ @ # $ % & * ! ? : ;
    
    return $cell;
}

/**
 * عرض عينة من البيانات
 */
function show_data_sample($data) {
    echo "<div class='alert alert-info'>
            <h5><i class='fas fa-table'></i> عينة من البيانات المقروءة (أول 5 صفوف):</h5>
            <div style='overflow-x: auto;'>
                <table style='width: 100%; border-collapse: collapse; margin: 10px 0;'>
                    <thead>
                        <tr style='background: #f8f9fa;'>
                            <th style='border: 1px solid #ddd; padding: 8px; text-align: center;'>#</th>
                            <th style='border: 1px solid #ddd; padding: 8px;'>العمود 1</th>
                            <th style='border: 1px solid #ddd; padding: 8px;'>العمود 2</th>
                            <th style='border: 1px solid #ddd; padding: 8px;'>العمود 3</th>
                        </tr>
                    </thead>
                    <tbody>";
    
    foreach (array_slice($data, 0, 5) as $index => $row) {
        echo "<tr>";
        echo "<td style='border: 1px solid #ddd; padding: 8px; text-align: center;'>" . ($index + 1) . "</td>";
        
        for ($i = 0; $i < 3; $i++) {
            $cell = isset($row[$i]) ? htmlspecialchars($row[$i]) : '';
            
            // تمييز النصوص العربية بلون أخضر
            if (!empty($cell) && preg_match('/[\x{0600}-\x{06FF}]/u', $cell)) {
                $cell = '<strong style="color: #28a745;">' . $cell . '</strong>';
            }
            // تمييز الأرقام بلون أزرق
            elseif (!empty($cell) && is_numeric($cell)) {
                $cell = '<strong style="color: #007bff;">' . $cell . '</strong>';
            }
            
            echo "<td style='border: 1px solid #ddd; padding: 8px;'>$cell</td>";
        }
        echo "</tr>";
    }
    
    echo "      </tbody>
                </table>
            </div>
          </div>";
    
    // إحصائيات مفيدة
    $stats = analyze_csv_data($data);
    show_csv_statistics($stats);
}

/**
 * تحليل بيانات CSV
 */
function analyze_csv_data($data) {
    $stats = [
        'total_rows' => count($data),
        'total_cells' => 0,
        'arabic_cells' => 0,
        'numeric_cells' => 0,
        'symbol_cells' => 0,
        'empty_cells' => 0,
        'max_columns' => 0
    ];
    
    foreach ($data as $row) {
        $stats['max_columns'] = max($stats['max_columns'], count($row));
        
        foreach ($row as $cell) {
            $stats['total_cells']++;
            $cell = trim($cell);
            
            if (empty($cell)) {
                $stats['empty_cells']++;
            } elseif (preg_match('/[\x{0600}-\x{06FF}]/u', $cell)) {
                $stats['arabic_cells']++;
            } elseif (is_numeric($cell)) {
                $stats['numeric_cells']++;
            } elseif (preg_match('/[()[\]{}.,-_+=\/\\@#$%&*!?:;]/', $cell)) {
                $stats['symbol_cells']++;
            }
        }
    }
    
    return $stats;
}

/**
 * عرض إحصائيات CSV
 */
function show_csv_statistics($stats) {
    $totalCells = $stats['total_cells'];
    
    echo "<div class='alert alert-secondary'>
            <h5><i class='fas fa-chart-bar'></i> إحصائيات البيانات:</h5>
            <div class='row'>
                <div class='col-md-6'>
                    <ul style='margin: 0;'>
                        <li><strong>إجمالي الصفوف:</strong> " . number_format($stats['total_rows']) . "</li>
                        <li><strong>إجمالي الخلايا:</strong> " . number_format($totalCells) . "</li>
                        <li><strong>أقصى عدد أعمدة:</strong> " . $stats['max_columns'] . "</li>
                    </ul>
                </div>
                <div class='col-md-6'>
                    <ul style='margin: 0;'>
                        <li><strong>خلايا عربية:</strong> " . number_format($stats['arabic_cells']) . " (" . round(($stats['arabic_cells']/$totalCells)*100, 1) . "%)</li>
                        <li><strong>خلايا رقمية:</strong> " . number_format($stats['numeric_cells']) . " (" . round(($stats['numeric_cells']/$totalCells)*100, 1) . "%)</li>
                        <li><strong>خلايا بها رموز:</strong> " . number_format($stats['symbol_cells']) . " (" . round(($stats['symbol_cells']/$totalCells)*100, 1) . "%)</li>
                    </ul>
                </div>
            </div>
          </div>";
}

/**
 * الحصول على اسم الفاصل
 */
function get_delimiter_name($delimiter) {
    switch ($delimiter) {
        case ',': return 'فاصلة (,)';
        case ';': return 'فاصلة منقوطة (;)';
        case "\t": return 'Tab';
        case '|': return 'خط عمودي (|)';
        default: return 'مخصص (' . $delimiter . ')';
    }
}

/**
 * تشخيص ملف CSV
 */
function diagnose_excel_file($file_path) {
    $info = [
        'file_path' => $file_path,
        'file_size' => filesize($file_path),
        'file_extension' => strtolower(pathinfo($file_path, PATHINFO_EXTENSION))
    ];
    
    // قراءة عينة من الملف
    $sample = file_get_contents($file_path, false, null, 0, 2000);
    
    // تحديد نوع الملف
    if ($info['file_extension'] === 'csv' || strpos($sample, ',') !== false || strpos($sample, ';') !== false) {
        $info['file_type'] = 'CSV';
    } else {
        $info['file_type'] = 'نص عادي';
    }
    
    // اكتشاف الترميز
    $encoding = detect_best_encoding($sample);
    $info['detected_encodings'] = [$encoding];
    $info['has_arabic'] = strpos($sample, 'ا') !== false || strpos($sample, 'ب') !== false || strpos($sample, 'ت') !== false;
    
    // استخراج عينة من النصوص
    $bestContent = $encoding !== 'UTF-8' ? convert_to_utf8($sample, $encoding) : $sample;
    
    // البحث عن كلمات عربية
    $arabicTexts = [];
    if (preg_match_all('/[\x{0600}-\x{06FF}]+[\x{0600}-\x{06FF}\s\w()[\]{}.,-_+=\/\\@#$%&*!?:;]*[\x{0600}-\x{06FF}]+/u', $bestContent, $matches)) {
        $arabicTexts = array_slice(array_unique($matches[0]), 0, 3);
    }
    
    $info['sample_texts'] = $arabicTexts;
    
    // البحث عن أرقام
    $numbers = [];
    if (preg_match_all('/\b\d{1,10}(?:\.\d{1,2})?\b/', $bestContent, $matches)) {
        $numbers = array_slice(array_unique($matches[0]), 0, 5);
    }
    
    $info['sample_numbers'] = $numbers;
    
    return $info;
}
?>
