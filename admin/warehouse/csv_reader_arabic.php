<?php
/**
 * قارئ CSV محسن للنصوص العربية
 * يدعم ترميزات متعددة ويتعامل مع الملفات المصدرة من Excel
 */

/**
 * تشخيص ملف Excel/CSV لاكتشاف الترميز والمحتوى
 */
function diagnose_excel_file($file_path) {
    $info = [
        'file_size' => filesize($file_path),
        'file_type' => 'CSV',
        'detected_encodings' => [],
        'has_arabic' => false,
        'sample_texts' => [],
        'sample_numbers' => []
    ];
    
    // قراءة عينة من الملف
    $sample = file_get_contents($file_path, false, null, 0, 2048);
    
    // اكتشاف الترميز
    $encodings_to_test = ['UTF-8', 'ISO-8859-6'];
    foreach ($encodings_to_test as $encoding) {
        if (mb_check_encoding($sample, $encoding)) {
            $info['detected_encodings'][] = $encoding;
        }
    }
    
    // البحث عن النصوص العربية
    if (preg_match('/[\x{0600}-\x{06FF}]/u', $sample)) {
        $info['has_arabic'] = true;
    }
    
    // استخراج عينة من النصوص والأرقام
    if (preg_match_all('/[^\d\s,;"\'\n\r]+/u', $sample, $matches)) {
        $info['sample_texts'] = array_slice(array_unique($matches[0]), 0, 5);
    }
    
    if (preg_match_all('/\d+(?:\.\d+)?/', $sample, $matches)) {
        $info['sample_numbers'] = array_slice(array_unique($matches[0]), 0, 5);
    }
    
    return $info;
}

/**
 * قراءة ملف CSV مع دعم محسن للعربية
 */
function read_csv_arabic_enhanced($file_path) {
    if (!file_exists($file_path)) {
        return false;
    }
    
    $data = [];
    $encodings = ['UTF-8', 'Windows-1256', 'ISO-8859-6', 'CP1256'];
    
    // محاولة قراءة الملف بترميزات مختلفة
    foreach ($encodings as $encoding) {
        $content = @file_get_contents($file_path);
        if ($content === false) continue;
        
        // تحويل الترميز إلى UTF-8
        if ($encoding !== 'UTF-8') {
            $converted = @iconv($encoding, 'UTF-8//IGNORE', $content);
            if ($converted !== false) {
                $content = $converted;
            }
        }
        
        // تنظيف المحتوى
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $content = trim($content);
        
        if (empty($content)) continue;
        
        // تقسيم إلى أسطر
        $lines = explode("\n", $content);
        $temp_data = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // محاولة تحليل CSV بطرق مختلفة
            $row = parse_csv_line($line);
            if (!empty($row)) {
                $temp_data[] = $row;
            }
        }
        
        // إذا حصلنا على بيانات صالحة، نستخدمها
        if (!empty($temp_data)) {
            $data = $temp_data;
            break;
        }
    }
    
    return $data;
}

/**
 * تحليل سطر CSV واحد مع دعم الفواصل المختلفة
 */
function parse_csv_line($line) {
    $delimiters = [',', ';', '\t', '|'];
    $best_result = [];
    $max_columns = 0;
    
    foreach ($delimiters as $delimiter) {
        if ($delimiter === '\t') {
            $delimiter = "\t";
        }
        
        // استخدام str_getcsv مع الفاصل المحدد
        $result = str_getcsv($line, $delimiter, '"');
        
        // تنظيف البيانات
        $result = array_map('trim', $result);
        $result = array_map(function($cell) {
            // إزالة علامات الاقتباس الزائدة
            $cell = trim($cell, '"\'');
            return $cell;
        }, $result);
        
        // اختيار النتيجة التي تحتوي على أكبر عدد من الأعمدة
        if (count($result) > $max_columns) {
            $max_columns = count($result);
            $best_result = $result;
        }
    }
    
    return $best_result;
}

/**
 * تنظيف وتحويل النص العربي
 */
function clean_arabic_text($text) {
    if (empty($text)) return '';
    
    // إزالة BOM إذا وجد
    $text = str_replace("\xEF\xBB\xBF", '', $text);
    
    // تنظيف المسافات الزائدة
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);
    
    // تحويل الأرقام العربية إلى إنجليزية
    $arabic_numbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $english_numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $text = str_replace($arabic_numbers, $english_numbers, $text);
    
    return $text;
}

/**
 * استخراج الأرقام من النص
 */
function extract_numbers($text) {
    // البحث عن الأرقام (مع الفواصل العشرية)
    preg_match_all('/\d+(?:[.,]\d+)?/', $text, $matches);
    
    if (!empty($matches[0])) {
        // أخذ أول رقم موجود
        $number = $matches[0][0];
        // تحويل الفاصلة إلى نقطة
        $number = str_replace(',', '.', $number);
        return floatval($number);
    }
    
    return 0;
}

/**
 * اكتشاف نوع العمود (نص أم رقم)
 */
function detect_column_type($values) {
    $numeric_count = 0;
    $text_count = 0;
    
    foreach ($values as $value) {
        if (is_numeric($value) || preg_match('/^\d+([.,]\d+)?$/', $value)) {
            $numeric_count++;
        } else {
            $text_count++;
        }
    }
    
    return $numeric_count > $text_count ? 'numeric' : 'text';
}

/**
 * تحليل هيكل ملف CSV
 */
function analyze_csv_structure($data) {
    if (empty($data)) return [];
    
    $analysis = [
        'total_rows' => count($data),
        'total_columns' => 0,
        'column_types' => [],
        'sample_data' => []
    ];
    
    // تحديد عدد الأعمدة
    $max_columns = 0;
    foreach ($data as $row) {
        $max_columns = max($max_columns, count($row));
    }
    $analysis['total_columns'] = $max_columns;
    
    // تحليل نوع كل عمود
    for ($col = 0; $col < $max_columns; $col++) {
        $column_values = [];
        foreach ($data as $row) {
            if (isset($row[$col]) && !empty($row[$col])) {
                $column_values[] = $row[$col];
            }
        }
        
        if (!empty($column_values)) {
            $analysis['column_types'][$col] = detect_column_type($column_values);
            $analysis['sample_data'][$col] = array_slice($column_values, 0, 3);
        }
    }
    
    return $analysis;
}
?>