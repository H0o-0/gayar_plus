<?php
/**
 * نظام تقسيم الملحقات المحسن - حل مشكلة الأجهزة المتعددة
 * يقوم بتقسيم الملحقات التي تدعم أجهزة متعددة إلى منتجات منفصلة
 * مثال: LCD SAM A125/A127/A022 يصبح 3 منتجات منفصلة
 */

require_once('../../config.php');
require_once('csv_reader_advanced.php');

/**
 * تقسيم الملحقات المتعددة بذكاء - الحل الجديد والمحسن
 */
function enhanced_split_multiple_devices($product_name) {
    if (!$product_name) return [$product_name];
    
    $clean_name = trim($product_name);
    
    // استخراج المعلومات الأساسية
    $base_info = extract_enhanced_base_info($clean_name);
    
    // البحث عن أنماط الأجهزة المتعددة
    $devices = find_device_patterns($clean_name, $base_info);
    
    if (count($devices) <= 1) {
        return [$clean_name];
    }
    
    // إعادة بناء الأسماء للأجهزة المنفصلة
    $rebuilt_devices = [];
    foreach ($devices as $device) {
        $rebuilt_name = rebuild_device_name($device, $base_info);
        if ($rebuilt_name && !in_array($rebuilt_name, $rebuilt_devices)) {
            $rebuilt_devices[] = $rebuilt_name;
        }
    }
    
    return count($rebuilt_devices) > 0 ? $rebuilt_devices : [$clean_name];
}

/**
 * استخراج المعلومات الأساسية المحسنة
 */
function extract_enhanced_base_info($product_name) {
    $name_lc = mb_strtolower(trim($product_name));
    
    // اكتشاف نوع الملحق
    $accessory_type = detect_accessory_type_enhanced($name_lc);
    
    // استخراج البراند
    $brand = detect_brand_from_name_enhanced($name_lc);
    
    // استخراج المعلومات الإضافية (بين الأقواس)
    $additional_info = '';
    if (preg_match('/\(([^)]+)\)\s*$/u', $product_name, $matches)) {
        $additional_info = trim($matches[1]);
    }
    
    // استخراج البادئة (مثل LCD SAM)
    $prefix = extract_prefix($product_name, $accessory_type, $brand);
    
    return [
        'accessory_type' => $accessory_type,
        'brand' => $brand,
        'additional_info' => $additional_info,
        'prefix' => $prefix,
        'original_name' => $product_name
    ];
}

/**
 * استخراج البادئة من اسم المنتج
 */
function extract_prefix($product_name, $accessory_type, $brand) {
    // أنماط البادئات الشائعة
    $patterns = [
        '/^(LCD\s+[A-Z]{2,4})\s+/i',  // LCD SAM, LCD HUA
        '/^(BATTERY\s+[A-Z]{2,4})\s+/i', // BATTERY SAM
        '/^(CHARGER\s+[A-Z]{2,4})\s+/i', // CHARGER SAM
        '/^([A-Z]+\s+[A-Z]{2,4})\s+/i'   // عام
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $product_name, $matches)) {
            return trim($matches[1]);
        }
    }
    
    // إذا لم نجد بادئة، نكون واحدة من نوع الملحق والبراند
    $type_map = [
        'lcd' => 'LCD',
        'battery' => 'BATTERY',
        'charger' => 'CHARGER',
        'cable' => 'CABLE',
        'case' => 'CASE'
    ];
    
    $brand_map = [
        'samsung' => 'SAM',
        'apple' => 'APPLE',
        'huawei' => 'HUA',
        'xiaomi' => 'XIA',
        'oppo' => 'OPPO',
        'vivo' => 'VIVO',
        'realme' => 'REAL',
        'infinix' => 'INF',
        'tecno' => 'TEC'
    ];
    
    $type_prefix = $type_map[$accessory_type] ?? strtoupper($accessory_type);
    $brand_prefix = $brand_map[$brand] ?? strtoupper($brand);
    
    return $type_prefix . ' ' . $brand_prefix;
}

/**
 * البحث عن أنماط الأجهزة المتعددة
 */
function find_device_patterns($product_name, $base_info) {
    // إزالة المعلومات الإضافية والبادئة للتركيز على الأجهزة
    $clean_name = $product_name;
    
    // إزالة المعلومات بين الأقواس
    $clean_name = preg_replace('/\([^)]+\)\s*$/u', '', $clean_name);
    
    // إزالة البادئة
    if (!empty($base_info['prefix'])) {
        $prefix_pattern = preg_quote($base_info['prefix'], '/');
        $clean_name = preg_replace('/^' . $prefix_pattern . '\s*/i', '', $clean_name);
    }
    
    $clean_name = trim($clean_name);
    
    // أنماط الفصل المختلفة
    $separators = ['/', '\\', '|', ' / ', ' \\ ', ' | '];
    
    $devices = [];
    $found_separator = false;
    
    foreach ($separators as $separator) {
        if (mb_strpos($clean_name, $separator) !== false) {
            $parts = array_map('trim', explode($separator, $clean_name));
            $parts = array_filter($parts, function($part) {
                return !empty($part) && mb_strlen($part) > 1;
            });
            
            if (count($parts) > 1) {
                $devices = array_merge($devices, $parts);
                $found_separator = true;
            }
        }
    }
    
    // إذا لم نجد فواصل، نبحث عن أنماط أخرى
    if (!$found_separator) {
        // البحث عن أنماط مثل A125 A127 A022 (مفصولة بمسافات)
        if (preg_match_all('/[A-Z]\d{2,4}[A-Z]?/u', $clean_name, $matches)) {
            if (count($matches[0]) > 1) {
                $devices = $matches[0];
            }
        }
        
        // البحث عن أنماط مثل 6/7/6i/6s (أرقام مع حروف)
        if (empty($devices) && preg_match_all('/\d+[a-z]*(?:\s*\/\s*|\s+)/u', $clean_name, $matches)) {
            $devices = array_map('trim', explode('/', str_replace(' ', '/', $clean_name)));
            $devices = array_filter($devices);
        }
    }
    
    // تنظيف الأجهزة
    $devices = array_unique(array_map('trim', $devices));
    $devices = array_filter($devices, function($device) {
        return !empty($device) && mb_strlen($device) > 1;
    });
    
    return array_values($devices);
}

/**
 * إعادة بناء اسم الجهاز
 */
function rebuild_device_name($device, $base_info) {
    $device = trim($device);
    if (empty($device)) return null;
    
    // بناء الاسم الجديد
    $new_name = '';
    
    // إضافة البادئة
    if (!empty($base_info['prefix'])) {
        $new_name = $base_info['prefix'] . ' ';
    }
    
    // إضافة اسم الجهاز
    $new_name .= $device;
    
    // إضافة المعلومات الإضافية
    if (!empty($base_info['additional_info'])) {
        $new_name .= ' (' . $base_info['additional_info'] . ')';
    }
    
    return trim($new_name);
}

/**
 * اكتشاف البراند المحسن
 */
function detect_brand_from_name_enhanced($name_lc) {
    $brand_patterns = [
        'samsung' => ['sam', 'samsung', 'galaxy', 'سام', 'سامسونج'],
        'apple' => ['iph', 'iphone', 'apple', 'ابل', 'ايفون'],
        'huawei' => ['hua', 'huawei', 'هواوي'],
        'xiaomi' => ['xia', 'xiaomi', 'mi', 'redmi', 'شاومي', 'ريدمي'],
        'oppo' => ['opp', 'oppo', 'أوبو', 'اوبو'],
        'vivo' => ['viv', 'vivo', 'فيفو'],
        'realme' => ['real', 'realme', 'ريلمي'],
        'infinix' => ['inf', 'infinix', 'انفنكس'],
        'tecno' => ['tec', 'tecno', 'تكنو'],
        'oneplus' => ['onep', 'oneplus', 'ون بلس'],
        'nokia' => ['nokia', 'نوكيا'],
        'lg' => ['lg'],
        'sony' => ['sony', 'سوني']
    ];
    
    foreach ($brand_patterns as $brand => $patterns) {
        foreach ($patterns as $pattern) {
            if (mb_strpos($name_lc, $pattern) !== false) {
                return $brand;
            }
        }
    }
    
    return null;
}

/**
 * معالجة ملف CSV وتقسيم الأجهزة المتعددة
 */
function process_csv_with_device_splitting($file_path, $name_column = 1, $price_column = 2, $start_row = 2) {
    // قراءة ملف CSV
    $csv_data = read_csv_advanced($file_path);
    
    if (!$csv_data || $csv_data['status'] !== 'success') {
        return ['status' => 'error', 'message' => 'فشل في قراءة ملف CSV'];
    }
    
    $data = $csv_data['data'];
    $processed_items = [];
    $stats = [
        'original_count' => 0,
        'split_count' => 0,
        'total_devices' => 0
    ];
    
    foreach ($data as $index => $row) {
        if ($index + 1 < $start_row) continue;
        
        if (count($row) <= max($name_column, $price_column)) continue;
        
        $product_name = isset($row[$name_column]) ? trim($row[$name_column]) : '';
        $price = isset($row[$price_column]) ? trim($row[$price_column]) : '';
        
        if (empty($product_name)) continue;
        
        $stats['original_count']++;
        
        // تقسيم الأجهزة المتعددة
        $split_devices = enhanced_split_multiple_devices($product_name);
        
        if (count($split_devices) > 1) {
            $stats['split_count']++;
        }
        
        foreach ($split_devices as $device_name) {
            $processed_items[] = [
                'product_name' => $device_name,
                'original_price' => extract_price($price),
                'original_name' => $product_name,
                'is_split' => count($split_devices) > 1
            ];
            $stats['total_devices']++;
        }
    }
    
    return [
        'status' => 'success',
        'items' => $processed_items,
        'stats' => $stats
    ];
}

/**
 * حفظ البيانات المقسمة في قاعدة البيانات
 */
function save_split_devices_to_database($conn, $processed_data, $import_batch = '') {
    if ($processed_data['status'] !== 'success') {
        return ['status' => 'error', 'message' => 'بيانات غير صحيحة'];
    }
    
    $imported = 0;
    $errors = 0;
    $classification_stats = [];
    
    foreach ($processed_data['items'] as $item) {
        // تصنيف المنتج
        $analysis = smart_device_classification($item['product_name']);
        
        $suggested_brand = '';
        $suggested_type = '';
        $confidence = 0;
        $status = 'unclassified';
        
        if (!empty($analysis)) {
            $first_analysis = $analysis[0];
            $suggested_brand = $first_analysis['brand'] ?? '';
            $suggested_type = $first_analysis['type'] ?? '';
            $confidence = $first_analysis['confidence'] ?? 0;
            
            if (!empty($suggested_brand) || !empty($suggested_type)) {
                $status = 'classified';
            }
        }
        
        // إحصائيات التصنيف
        $brand_key = !empty($suggested_brand) ? ucfirst($suggested_brand) : 'غير مصنف';
        if (!isset($classification_stats[$brand_key])) {
            $classification_stats[$brand_key] = 0;
        }
        $classification_stats[$brand_key]++;
        
        // تنظيف البيانات للإدراج
        $product_name_escaped = $conn->real_escape_string($item['product_name']);
        $original_price = floatval($item['original_price']);
        $suggested_brand_escaped = $conn->real_escape_string($suggested_brand);
        $suggested_type_escaped = $conn->real_escape_string($suggested_type);
        $import_batch_escaped = $conn->real_escape_string($import_batch);
        $original_name_escaped = $conn->real_escape_string($item['original_name']);
        $is_split = $item['is_split'] ? 1 : 0;
        
        // إدراج في قاعدة البيانات
        $sql = "INSERT INTO temp_warehouse 
                (product_name, original_price, suggested_brand, suggested_type, status, import_batch, raw_data, confidence, original_name, is_split)
                VALUES 
                ('$product_name_escaped', $original_price, '$suggested_brand_escaped', '$suggested_type_escaped', '$status', '$import_batch_escaped', '$original_name_escaped', $confidence, '$original_name_escaped', $is_split)";
        
        if ($conn->query($sql)) {
            $imported++;
        } else {
            $errors++;
            error_log("خطأ في إدراج المنتج: " . $conn->error);
        }
    }
    
    return [
        'status' => 'success',
        'imported' => $imported,
        'errors' => $errors,
        'stats' => $processed_data['stats'],
        'classification_stats' => $classification_stats
    ];
}

/**
 * اختبار تقسيم الأجهزة
 */
function test_device_splitting() {
    $test_cases = [
        'LCD SAM A125/A127/A022/A326 / M127 (MCN -EDITION)',
        'LCD REALME6 / 7 / 6i / 6s / NARZO 30 OR NEW 100%',
        'LCD REALME7 PRO / OPPO A94 / A94 5G / F19 PRO 4G',
        'BATTERY SAM S20/S21/S22 (ORIGINAL)',
        'CHARGER APPLE IPHONE 12/13/14/15 (FAST CHARGE)'
    ];
    
    echo "<h3>اختبار تقسيم الأجهزة المتعددة:</h3>";
    
    foreach ($test_cases as $test_case) {
        echo "<div style='margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
        echo "<strong>الاسم الأصلي:</strong> " . htmlspecialchars($test_case) . "<br>";
        
        $split_devices = enhanced_split_multiple_devices($test_case);
        
        echo "<strong>النتيجة (" . count($split_devices) . " جهاز):</strong><br>";
        echo "<ul>";
        foreach ($split_devices as $device) {
            echo "<li>" . htmlspecialchars($device) . "</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
}

// إذا تم استدعاء الملف مباشرة، قم بتشغيل الاختبار
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    test_device_splitting();
}
?>
