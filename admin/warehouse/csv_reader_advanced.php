<?php
/**
 * قارئ CSV متقدم مع دعم كامل للعربية والاختصارات
 * يدعم اكتشاف العلامات التجارية والأجهزة تلقائياً
 * 
 * @version 2.0.0
 * @date 2025-01-16
 */

/**
 * قراءة ملف CSV مع دعم كامل للعربية والرموز
 */
function read_csv_advanced($file_path) {
    $data = [];
    
    try {
        // قراءة محتوى الملف
        $content = file_get_contents($file_path);
        
        if (empty($content)) {
            return [
                'status' => 'error',
                'message' => 'الملف فارغ أو لا يمكن قراءته',
                'data' => []
            ];
        }
        
        // إزالة BOM إذا وُجد
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }
        
        // تحديد أفضل ترميز للملف
        $encoding = detect_best_encoding($content);
        
        if ($encoding !== 'UTF-8') {
            $content = convert_to_utf8($content, $encoding);
        }
        
        // تحديد فاصل الأعمدة
        $delimiter = detect_csv_delimiter($content);
        
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
        
        return [
            'status' => 'success',
            'message' => "تم معالجة $processedRows سطر وقراءة $validRows صف صحيح!",
            'data' => $data,
            'encoding' => $encoding,
            'delimiter' => $delimiter,
            'total_rows' => $validRows
        ];
        
    } catch (Exception $e) {
        error_log("خطأ في قراءة CSV: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => "خطأ في قراءة الملف: " . $e->getMessage(),
            'data' => []
        ];
    }
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
                if (preg_match('/[\x{0600}-\x{06FF}]/u', $content)) {
                    return 'UTF-8';
                }
            }
        } else {
            // للترميزات الأخرى، نجرب التحويل ونفحص النتيجة
            $converted = @iconv($encoding, 'UTF-8//IGNORE', $content);
            if ($converted && preg_match('/[\x{0600}-\x{06FF}]/u', $converted)) {
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
    
    return $cell;
}

/**
 * استخراج الأسعار من النصوص
 */
function extract_price($text) {
    // تنظيف النص
    $text = preg_replace('/[^\d.,]/', '', $text);
    
    // تحويل الفاصلة إلى نقطة إذا كانت موجودة
    $text = str_replace(',', '.', $text);
    
    // إذا كان النص فارغاً، إرجاع 0
    if (empty($text)) {
        return 0;
    }
    
    // تحويل النص إلى رقم
    return floatval($text);
}

/**
 * قاموس الاختصارات والعلامات التجارية
 */
function get_brands_dictionary() {
    return [
        'apple' => [
            'patterns' => ['iph', 'ip', 'iphone', 'ios', 'ايفون', 'ابل', 'apple'],
            'models' => [
                '7' => ['iphone 7', 'ايفون 7', 'ip7'],
                '8' => ['iphone 8', 'ايفون 8', 'ip8'],
                'x' => ['iphone x', 'ايفون x', 'ipx'],
                '11' => ['iphone 11', 'ايفون 11', 'ip11'],
                '12' => ['iphone 12', 'ايفون 12', 'ip12'],
                '13' => ['iphone 13', 'ايفون 13', 'ip13'],
                '14' => ['iphone 14', 'ايفون 14', 'ip14'],
                '15' => ['iphone 15', 'ايفون 15', 'ip15'],
                '16' => ['iphone 16', 'ايفون 16', 'ip16']
            ]
        ],
        'samsung' => [
            'patterns' => ['sam', 'galaxy', 'gal', 'note', 'سام', 'سامسونج', 'samsung'],
            'models' => [
                's20' => ['galaxy s20', 's20', 'سامسونج s20'],
                's21' => ['galaxy s21', 's21', 'سامسونج s21'],
                's22' => ['galaxy s22', 's22', 'سامسونج s22'],
                's23' => ['galaxy s23', 's23', 'سامسونج s23'],
                'note20' => ['note 20', 'note20', 'نوت 20'],
                'a52' => ['galaxy a52', 'a52', 'سامسونج a52'],
                'a53' => ['galaxy a53', 'a53', 'سامسونج a53'],
                'a54' => ['galaxy a54', 'a54', 'سامسونج a54']
            ]
        ],
        'huawei' => [
            'patterns' => ['hua', 'mate', 'p', 'هواوي', 'huawei'],
            'models' => [
                'p30' => ['p30', 'p 30', 'هواوي p30'],
                'p40' => ['p40', 'p 40', 'هواوي p40'],
                'p50' => ['p50', 'p 50', 'هواوي p50'],
                'mate30' => ['mate 30', 'mate30', 'ميت 30'],
                'mate40' => ['mate 40', 'mate40', 'ميت 40'],
                'mate50' => ['mate 50', 'mate50', 'ميت 50']
            ]
        ],
        'xiaomi' => [
            'patterns' => ['xia', 'mi', 'redmi', 'شاومي', 'ريدمي', 'xiaomi'],
            'models' => [
                'mi11' => ['mi 11', 'mi11', 'شاومي 11'],
                'mi12' => ['mi 12', 'mi12', 'شاومي 12'],
                'redmi10' => ['redmi 10', 'redmi10', 'ريدمي 10'],
                'redmi11' => ['redmi 11', 'redmi11', 'ريدمي 11'],
                'poco' => ['poco', 'بوكو']
            ]
        ],
        'oppo' => [
            'patterns' => ['opp', 'reno', 'أوبو', 'اوبو', 'oppo'],
            'models' => [
                'reno5' => ['reno 5', 'reno5', 'رينو 5'],
                'reno6' => ['reno 6', 'reno6', 'رينو 6'],
                'reno7' => ['reno 7', 'reno7', 'رينو 7'],
                'find' => ['find', 'فايند']
            ]
        ],
        'vivo' => [
            'patterns' => ['viv', 'فيفو', 'vivo'],
            'models' => [
                'v20' => ['v20', 'v 20', 'فيفو 20'],
                'v21' => ['v21', 'v 21', 'فيفو 21'],
                'v23' => ['v23', 'v 23', 'فيفو 23'],
                'y20' => ['y20', 'y 20', 'واي 20']
            ]
        ],
        'oneplus' => [
            'patterns' => ['onep', 'ون بلس', 'oneplus'],
            'models' => [
                '9' => ['oneplus 9', 'ون بلس 9'],
                '10' => ['oneplus 10', 'ون بلس 10'],
                '11' => ['oneplus 11', 'ون بلس 11']
            ]
        ],
        'lg' => [
            'patterns' => ['lg'],
            'models' => [
                'g8' => ['g8', 'g 8'],
                'v60' => ['v60', 'v 60']
            ]
        ],
        'sony' => [
            'patterns' => ['son', 'xperia', 'سوني', 'sony'],
            'models' => [
                'xperia1' => ['xperia 1', 'xperia1', 'اكسبيريا 1'],
                'xperia5' => ['xperia 5', 'xperia5', 'اكسبيريا 5'],
                'xperia10' => ['xperia 10', 'xperia10', 'اكسبيريا 10']
            ]
        ]
    ];
}

/**
 * قاموس أنواع الملحقات
 */
function get_accessories_dictionary() {
    return [
        'screen' => [
            'patterns' => ['lcd', 'display', 'scr', 'شاشة', 'screen'],
            'description' => 'شاشات الاستبدال'
        ],
        'battery' => [
            'patterns' => ['batt', 'battery', 'bat', 'بطارية'],
            'description' => 'بطاريات الهواتف'
        ],
        'charger' => [
            'patterns' => ['chg', 'charger', 'شاحن'],
            'description' => 'شواحن وكابلات'
        ],
        'cable' => [
            'patterns' => ['cab', 'cable', 'كابل'],
            'description' => 'كابلات البيانات والشحن'
        ],
        'case' => [
            'patterns' => ['case', 'cover', 'جراب', 'غطاء'],
            'description' => 'جرابات الحماية'
        ],
        'protector' => [
            'patterns' => ['screen', 'protector', 'واقي'],
            'description' => 'واقيات الشاشة'
        ]
    ];
}

/**
 * تحليل اسم المنتج لاكتشاف العلامة التجارية والموديل
 */
function analyze_product_name($product_name) {
    $product_name = mb_strtolower($product_name);
    $brands_dict = get_brands_dictionary();
    $accessories_dict = get_accessories_dictionary();
    
    $result = [
        'brand' => null,
        'model' => null,
        'type' => null
    ];
    
    // البحث عن العلامة التجارية
    foreach ($brands_dict as $brand => $data) {
        foreach ($data['patterns'] as $pattern) {
            if (mb_strpos($product_name, mb_strtolower($pattern)) !== false) {
                $result['brand'] = $brand;
                
                // البحث عن الموديل
                foreach ($data['models'] as $model => $model_patterns) {
                    foreach ($model_patterns as $model_pattern) {
                        if (mb_strpos($product_name, mb_strtolower($model_pattern)) !== false) {
                            $result['model'] = $model;
                            break 2;
                        }
                    }
                }
                
                break 2;
            }
        }
    }
    
    // البحث عن نوع الملحق
    foreach ($accessories_dict as $type => $data) {
        foreach ($data['patterns'] as $pattern) {
            if (mb_strpos($product_name, mb_strtolower($pattern)) !== false) {
                $result['type'] = $type;
                break 2;
            }
        }
    }
    
    return $result;
}

/**
 * تحليل بيانات CSV واكتشاف العلامات التجارية والأنواع
 */
function analyze_csv_data($data, $name_column = 1, $price_column = 2) {
    $results = [];
    $stats = [
        'total' => count($data),
        'classified' => 0,
        'unclassified' => 0,
        'brands' => [],
        'types' => []
    ];
    
    foreach ($data as $row) {
        if (count($row) <= max($name_column, $price_column)) {
            continue;
        }
        
        $product_name = $row[$name_column];
        $price_text = isset($row[$price_column]) ? $row[$price_column] : '';
        
        $price = extract_price($price_text);
        $analysis = analyze_product_name($product_name);
        
        $item = [
            'product_name' => $product_name,
            'original_price' => $price,
            'suggested_brand' => ucfirst($analysis['brand']),
            'suggested_model' => $analysis['model'],
            'suggested_type' => $analysis['type']
        ];
        
        // إحصائيات
        if ($analysis['brand'] || $analysis['type']) {
            $stats['classified']++;
            
            if ($analysis['brand']) {
                if (!isset($stats['brands'][$analysis['brand']])) {
                    $stats['brands'][$analysis['brand']] = 0;
                }
                $stats['brands'][$analysis['brand']]++;
            }
            
            if ($analysis['type']) {
                if (!isset($stats['types'][$analysis['type']])) {
                    $stats['types'][$analysis['type']] = 0;
                }
                $stats['types'][$analysis['type']]++;
            }
        } else {
            $stats['unclassified']++;
        }
        
        $results[] = $item;
    }
    
    return [
        'items' => $results,
        'stats' => $stats
    ];
}

/**
 * استيراد بيانات CSV إلى قاعدة البيانات
 */
function import_csv_to_database($conn, $analyzed_data, $batch_name = '') {
    $imported = 0;
    $errors = 0;
    
    foreach ($analyzed_data['items'] as $item) {
        $product_name = $conn->real_escape_string($item['product_name']);
        $original_price = floatval($item['original_price']);
        $suggested_brand = $conn->real_escape_string($item['suggested_brand'] ?? '');
        $suggested_type = $conn->real_escape_string($item['suggested_type'] ?? '');
        
        $status = ($suggested_brand || $suggested_type) ? 'classified' : 'unclassified';
        
        $sql = "INSERT INTO temp_warehouse 
                (product_name, original_price, suggested_brand, suggested_type, status, batch_name) 
                VALUES 
                ('$product_name', $original_price, '$suggested_brand', '$suggested_type', '$status', '$batch_name')";
        
        if ($conn->query($sql)) {
            $imported++;
        } else {
            $errors++;
            error_log("Error importing product: " . $conn->error . " - SQL: " . $sql);
        }
    }
    
    return [
        'imported' => $imported,
        'errors' => $errors
    ];
}

/**
 * تصنيف تلقائي للمنتجات غير المصنفة
 */
function auto_classify_products($conn) {
    $products = $conn->query("SELECT * FROM temp_warehouse WHERE status = 'unclassified'");
    $classified = 0;
    
    while ($product = $products->fetch_assoc()) {
        $analysis = analyze_product_name($product['product_name']);
        
        if ($analysis['brand'] || $analysis['type']) {
            $suggested_brand = $conn->real_escape_string(ucfirst($analysis['brand']));
            $suggested_type = $conn->real_escape_string($analysis['type']);
            
            $sql = "UPDATE temp_warehouse 
                    SET suggested_brand = '$suggested_brand', 
                        suggested_type = '$suggested_type', 
                        status = 'classified' 
                    WHERE id = {$product['id']}";
            
            if ($conn->query($sql)) {
                $classified++;
            }
        }
    }
    
    return $classified;
}
?>