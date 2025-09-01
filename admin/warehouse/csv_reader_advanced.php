<?php
/**
 * قارئ CSV محسن مع دعم كامل للغة العربية والتصنيف الذكي
 * يدعم ترميزات متعددة ويتعامل مع الملفات المصدرة من Excel
 */

// ضبط الترميز للغة العربية
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

/**
 * قراءة ملف CSV مع دعم محسن للعربية والتصنيف الذكي
 */
function read_csv_advanced($file_path) {
    $data = [];
    
    try {
        // قراءة الملف مع دعم الترميزات العربية
        $content = read_file_with_arabic_support($file_path);
        if ($content === false || $content === null) {
            return ['status' => 'error', 'message' => 'تعذر فتح الملف أو الترميز غير مدعوم'];
        }
        
        // تقسيم المحتوى إلى أسطر
        $lines = preg_split('/\r\n|\r|\n/', $content);
        if (!is_array($lines) || count($lines) === 0) {
            return ['status' => 'error', 'message' => 'الملف فارغ'];
        }
        
        // اكتشاف الفاصل الأنسب تلقائياً من عينة الأسطر
        $delimiter = detect_best_delimiter($lines);
        
        $processedRows = 0;
        $validRows = 0;
        
        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);
            if ($line === '') continue;
            $processedRows++;
            
            // تحليل السطر بطريقة آمنة (يدعم WHITESPACE والفواصل الأخرى)
            $row = parse_csv_line_safe($line, $delimiter);
            
            // تنظيف كل خلية
            $row = array_map(function($cell){
                return clean_arabic_text(clean_csv_cell($cell));
            }, $row);
            
            if (!empty($row) && count($row) > 1) {
                $data[] = $row;
                $validRows++;
            }
        }
        
        if ($validRows === 0) {
            return [
                'status' => 'error',
                'message' => 'لم يتمكن القارئ من فصل الأعمدة. تحقق من الفاصل والترميز.'
            ];
        }
        
        return [
            'status' => 'success',
            'data' => $data,
            'total_rows' => count($lines),
            'processed_rows' => $processedRows,
            'valid_rows' => $validRows,
            'delimiter' => $delimiter
        ];
        
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'خطأ في قراءة الملف: ' . $e->getMessage()];
    }
}

/**
 * قراءة الملف مع دعم الترميزات العربية
 */
function read_file_with_arabic_support($file_path) {
    $encodings = ['UTF-8', 'Windows-1256', 'ISO-8859-6', 'CP1256', 'UTF-8-BOM'];
    
    foreach ($encodings as $encoding) {
        $content = @file_get_contents($file_path);
        if ($content === false) continue;
        
        // إزالة BOM إذا وجد
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }
        
        // تحويل الترميز إلى UTF-8
        if ($encoding !== 'UTF-8') {
            $converted = @iconv($encoding, 'UTF-8//IGNORE', $content);
            if ($converted !== false) {
                $content = $converted;
            }
        }
        
        // التحقق من صحة الترميز
        if (mb_check_encoding($content, 'UTF-8')) {
            return $content;
        }
    }
    
    return false;
}

/**
 * تنظيف النص العربي من الأحرف غير المرغوبة
 */
function clean_arabic_text($text) {
    if (!$text) return '';
    
    // إزالة الأحرف غير المرئية
    $text = preg_replace('/[\x00-\x1F\x7F]/', '', $text);
    
    // إزالة المسافات الزائدة
    $text = preg_replace('/\s+/', ' ', $text);
    
    // تنظيف النص العربي
    $text = trim($text);
    
    return $text;
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
 * اكتشاف أفضل فاصل للملف من عينة الأسطر
 */
function detect_best_delimiter($lines) {
    // مرشّحات محتملة: فاصلة، فاصلة منقوطة، تبويب، بايب
    $candidates = [',', ';', "\t", '|'];
    $scores = [];
    $sampled = 0;
    foreach ($candidates as $c) {
        $scores[$c] = [
            'lines' => 0,           // عدد الأسطر المقروءة بالعينة
            'with2plus' => 0,       // أسطر أنتجت عمودين فأكثر
            'avgFields' => 0.0,     // متوسط الأعمدة
            'fieldCounts' => []     // توزيع عدد الأعمدة
        ];
    }
    
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;
        $sampleedFields = [];
        foreach ($candidates as $c) {
            $fields = @str_getcsv($line, $c);
            $count = is_array($fields) ? count($fields) : 1;
            $scores[$c]['lines']++;
            if ($count >= 2) $scores[$c]['with2plus']++;
            $scores[$c]['avgFields'] += $count;
            if (!isset($scores[$c]['fieldCounts'][$count])) $scores[$c]['fieldCounts'][$count] = 0;
            $scores[$c]['fieldCounts'][$count]++;
        }
        $sampled++;
        if ($sampled >= 20) break; // نكتفي بـ 20 سطراً للعينة
    }
    
    // اختيار الفاصل وفق: أعلى أسطر مع 2+ حقل، ثم أعلى متوسط أعمدة، ثم استقرار (المود)
    $best = null;
    $bestKey = null;
    foreach ($scores as $c => $s) {
        if ($s['lines'] === 0) continue;
        $s['avgFields'] = $s['avgFields'] / max(1, $s['lines']);
        // احسب المود
        $modeCount = 0;
        foreach ($s['fieldCounts'] as $k => $v) {
            if ($v > $modeCount) $modeCount = $v;
        }
        $scoreTuple = [$s['with2plus'], $s['avgFields'], $modeCount];
        if ($best === null || $scoreTuple > $best) {
            $best = $scoreTuple;
            $bestKey = $c;
        }
    }
    
    // إذا لم يحقق أي مرشح نتيحة جيدة، جرّب الفصل بالمسافات المتعددة/التبويب كخيار عام
    if ($best === null || $scores[$bestKey]['with2plus'] === 0) {
        return 'WHITESPACE';
    }
    
    return $bestKey;
}

/**
 * تحليل سطر CSV بطريقة آمنة مع الحفاظ على الرموز
 */
function parse_csv_line_safe($line, $delimiter) {
    // في وضع الفصل بالمسافات/التبويب
    if ($delimiter === 'WHITESPACE') {
        // افصل على تبويبات أو مسافات متتالية (2+) وتجاهل الفراغات الفارغة
        $parts = preg_split('/\t+|\s{2,}/u', $line, -1, PREG_SPLIT_NO_EMPTY);
        return is_array($parts) ? $parts : [$line];
    }
    
    // استخدم str_getcsv لأنه يدعم الحقول المقتبسة بشكل صحيح
    $parsed = @str_getcsv($line, $delimiter);
    if (is_array($parsed) && count($parsed) > 0) {
        return $parsed;
    }
    
    // رجوع احتياطي بسيط
    return explode($delimiter, $line);
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
    if (!$text) return 0;

    // 1) توحيد الأرقام العربية إلى إنجليزية وإزالة مسافات غريبة
    $text = normalize_arabic_digits($text);
    $text = str_replace(["\xC2\xA0", ' '], '', $text); // إزالة NBSP والمسافات

    // 2) إزالة رموز العملة وأي أحرف غير الأرقام والفواصل والنقاط
    $text = preg_replace('/[^0-9.,]/u', '', $text);

    if ($text === '') return 0;

    // 3) معالجة أنماط الأرقام الشائعة
    // نمط آلاف بفواصل فقط: 12,345 أو 1,234,567
    if (preg_match('/^\d{1,3}(,\d{3})+$/', $text)) {
        $text = str_replace(',', '', $text);
        return (float) $text;
    }

    // نمط عشري بفاصلة: 12345,67 => 12345.67
    if (preg_match('/^\d+(,\d{1,2})$/', $text)) {
    $text = str_replace(',', '.', $text);
        return (float) $text;
    }

    // نمط عشري بنقطة: 12345.67
    if (preg_match('/^\d+(\.\d{1,2})$/', $text)) {
        return (float) $text;
    }

    // في حال وجود كلا الرمزين، اعتبر آخر ظهور كفاصل عشري والباقي آلاف
    $lastDot = strrpos($text, '.');
    $lastComma = strrpos($text, ',');
    if ($lastDot !== false || $lastComma !== false) {
        if ($lastDot !== false && ($lastComma === false || $lastDot > $lastComma)) {
            // النقطة آخر فاصل: أزل جميع الفواصل واحتفظ بالنقطة الأخيرة
            $text = str_replace(',', '', $text);
        } else {
            // الفاصلة آخر فاصل: أزل جميع النقاط وحوّل الفاصلة الأخيرة إلى نقطة
            $text = str_replace('.', '', $text);
            $pos = strrpos($text, ',');
            if ($pos !== false) {
                $text[$pos] = '.';
            }
        }
        return (float) $text;
    }

    // كخيار أخير، أزل جميع الفواصل والنقاط (أسعار الدائنار عادة أعداد صحيحة)
    $text = str_replace([',', '.'], '', $text);
    return (float) $text;
}

/**
 * تحويل الأرقام العربية/الهندية إلى إنجليزية
 */
function normalize_arabic_digits($text) {
    $arabic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩', '٬', '٫']; // ٬ آلاف ، ٫ عشري
    $latin  = ['0','1','2','3','4','5','6','7','8','9', ',', '.'];
    return str_replace($arabic, $latin, $text);
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
 * تحليل اسم المنتج وفصل الأجهزة المتعددة وتصنيف كل جهاز تحت برانده الصحيح
 */
function analyze_product_name($product_name) {
    if (!$product_name) return ['brand' => null, 'model' => null, 'type' => null];
    
    $name_lc = mb_strtolower(trim($product_name));
    
    // 1) اكتشاف نوع الملحق (LCD, Battery, Charger, etc.)
    $accessory_type = detect_accessory_type($name_lc);
    
    // 2) فصل الأجهزة المتعددة (مفصولة بـ / أو - أو |)
    $devices = split_multiple_devices($product_name);
    
    $results = [];
    
    foreach ($devices as $device_name) {
        $device_analysis = analyze_single_device($device_name, $accessory_type);
        if ($device_analysis['brand']) {
            $results[] = $device_analysis;
        }
    }
    
    // إذا لم نجد أجهزة منفصلة، نحلل الاسم كجهاز واحد
    if (empty($results)) {
        $results[] = analyze_single_device($product_name, $accessory_type);
    }
    
    return $results;
}

/**
 * اكتشاف نوع الملحق
 */
function detect_accessory_type($name_lc) {
    $accessory_patterns = [
        'lcd' => ['lcd', 'شاشة', 'display', 'screen'],
        'battery' => ['battery', 'بطارية', 'bat'],
        'charger' => ['charger', 'شاحن', 'adapter'],
        'cable' => ['cable', 'كابل', 'wire', 'cord'],
        'case' => ['case', 'غلاف', 'cover', 'protector'],
        'headphone' => ['headphone', 'سماعة', 'earphone'],
        'camera' => ['camera', 'كاميرا', 'lens']
    ];
    
    foreach ($accessory_patterns as $type => $patterns) {
        foreach ($patterns as $pattern) {
            if (mb_strpos($name_lc, $pattern) !== false) {
                return $type;
            }
        }
    }
    
    return 'other';
}

/**
 * فصل الأجهزة المتعددة من اسم واحد
 */
function split_multiple_devices($product_name) {
    // تنظيف الاسم
    $clean_name = trim($product_name);
    
    // البحث عن فواصل الأجهزة المتعددة
    $separators = ['/', '-', '|', '\\', '،', '،'];
    
    foreach ($separators as $separator) {
        if (mb_strpos($clean_name, $separator) !== false) {
            $devices = array_map('trim', mb_split($separator, $clean_name));
            $devices = array_filter($devices, function($device) {
                return !empty($device) && mb_strlen($device) > 2;
            });
            
            if (count($devices) > 1) {
                return array_values($devices);
            }
        }
    }
    
    // إذا لم نجد فواصل، نعيد الاسم كما هو
    return [$clean_name];
}

/**
 * تحليل جهاز واحد لاكتشاف البراند والموديل
 */
function analyze_single_device($device_name, $accessory_type) {
    $device_lc = mb_strtolower(trim($device_name));
    
    // 1) البحث عن البراند باستخدام الاختصارات والأسماء الكاملة
    $brand = detect_brand_from_name($device_lc);
    
    // 2) استخراج الموديل
    $model = extract_model_from_name($device_name, $brand);
    
    // 3) تحديد النوع
    $type = $accessory_type;
    
    return [
        'brand' => $brand,
        'model' => $model,
        'type' => $type,
        'original_name' => $device_name
    ];
}

/**
 * اكتشاف البراند من اسم الجهاز
 */
function detect_brand_from_name($name_lc) {
    // اختصارات البراندات
    $brand_aliases = [
        'samsung' => ['sam', 'samsung', 'galaxy', 'galaxy s', 'galaxy note', 'galaxy a', 'galaxy m'],
        'apple' => ['iph', 'iphone', 'apple', 'ipad', 'mac'],
        'huawei' => ['hw', 'huawei', 'honor', 'nova', 'p series', 'mate'],
        'xiaomi' => ['xia', 'xiaomi', 'redmi', 'mi', 'poco'],
        'poco' => ['poco', 'poco x', 'poco f'],
        'oneplus' => ['onep', 'oneplus', 'one plus', 'nord'],
        'oppo' => ['op', 'oppo', 'reno', 'find x', 'a series'],
        'realme' => ['real', 'realme', 'gt', 'gt neo', 'q series'],
        'infinix' => ['inf', 'infinix', 'zero', 'hot', 'spark', 'camon'],
        'tecno' => ['tec', 'tecno', 'camon', 'spark', 'phantom'],
        'itel' => ['itel', 'vision', 's series', 'p series']
    ];
    
    // البحث عن البراند
    foreach ($brand_aliases as $canonical_brand => $aliases) {
        foreach ($aliases as $alias) {
            if (mb_strpos($name_lc, $alias) !== false) {
                return $canonical_brand;
            }
        }
    }
    
    return null;
}

/**
 * استخراج موديل الجهاز من الاسم
 */
function extract_model_from_name($device_name, $brand) {
    if (!$brand) return null;
    
    $name_lc = mb_strtolower($device_name);
    
    // أنماط الموديلات الشائعة
    $model_patterns = [
        'samsung' => ['galaxy s\d+', 'galaxy note\d+', 'galaxy a\d+', 'galaxy m\d+', 'galaxy z'],
        'apple' => ['iphone \d+', 'iphone \d+ pro', 'iphone \d+ plus', 'iphone se'],
        'huawei' => ['nova \d+', 'p\d+', 'mate \d+', 'honor \d+'],
        'xiaomi' => ['redmi \d+', 'mi \d+', 'poco \w+'],
        'oppo' => ['reno \d+', 'find x\d+', 'a\d+'],
        'realme' => ['gt \w+', 'gt neo', 'q\d+', 'x\d+'],
        'infinix' => ['zero \w+', 'hot \d+', 'spark \d+', 'camon \d+'],
        'tecno' => ['camon \d+', 'spark \d+', 'phantom \w+'],
        'itel' => ['vision \d+', 's\d+', 'p\d+']
    ];
    
    if (isset($model_patterns[$brand])) {
        foreach ($model_patterns[$brand] as $pattern) {
            if (preg_match('/' . $pattern . '/i', $device_name, $matches)) {
                return $matches[0];
            }
        }
    }
    
    return null;
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
                 (product_name, original_price, suggested_brand, suggested_type, status, import_batch) 
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

/**
 * قاعدة بيانات شاملة لجميع شركات الهواتف وأجهزتها
 */
function get_comprehensive_brands_database() {
    return [
        'samsung' => [
            'aliases' => ['sam', 'samsung', 'galaxy', 'gal', 'سام', 'سامسونج'],
            'devices' => [
                'galaxy s' => ['s20', 's21', 's22', 's23', 's24', 's10', 's9', 's8', 's7', 's6'],
                'galaxy note' => ['note20', 'note10', 'note9', 'note8', 'note7', 'note5', 'note4'],
                'galaxy a' => ['a04s', 'a13', 'a23', 'a33', 'a53', 'a73', 'a54', 'a34', 'a14', 'a24'],
                'galaxy m' => ['m33', 'm53', 'm13', 'm23', 'm43', 'm63', 'm34', 'm44'],
                'galaxy z' => ['z flip', 'z fold', 'z flip3', 'z fold3', 'z flip4', 'z fold4'],
                'galaxy j' => ['j1', 'j2', 'j3', 'j4', 'j5', 'j6', 'j7', 'j8'],
                'galaxy on' => ['on5', 'on6', 'on7', 'on8'],
                'galaxy core' => ['core prime', 'core 2', 'core lite'],
                'galaxy grand' => ['grand prime', 'grand 2', 'grand neo'],
                'galaxy win' => ['win', 'win 2', 'win duos'],
                'galaxy y' => ['y', 'y pro', 'y duos', 'y young'],
                'galaxy ace' => ['ace', 'ace 2', 'ace 3', 'ace 4'],
                'galaxy fit' => ['fit', 'fit 2', 'fit e'],
                'galaxy tab' => ['tab a', 'tab s', 'tab e', 'tab active'],
                'galaxy watch' => ['watch', 'watch active', 'watch 3', 'watch 4']
            ]
        ],
        'huawei' => [
            'aliases' => ['hw', 'huawei', 'هواوي', 'هواوي'],
            'devices' => [
                'p series' => ['p30', 'p40', 'p50', 'p60', 'p20', 'p10', 'p9', 'p8'],
                'mate series' => ['mate 30', 'mate 40', 'mate 50', 'mate 20', 'mate 10', 'mate 9', 'mate 8'],
                'nova series' => ['nova 3', 'nova 4', 'nova 5', 'nova 6', 'nova 7', 'nova 8', 'nova 9', 'nova 10'],
                'y series' => ['y5', 'y6', 'y7', 'y8', 'y9', 'y6p', 'y7p', 'y8p', 'y9s'],
                'honor' => ['honor 8x', 'honor 9x', 'honor 10x', 'honor 20', 'honor 30', 'honor 50', 'honor 60', 'honor 70'],
                'g series' => ['g7', 'g8', 'g9', 'g10', 'g20', 'g30', 'g40', 'g50'],
                'enjoy' => ['enjoy 5', 'enjoy 6', 'enjoy 7', 'enjoy 8', 'enjoy 9', 'enjoy 10'],
                'ascend' => ['ascend p', 'ascend mate', 'ascend g', 'ascend y'],
                'mediapad' => ['mediapad t1', 'mediapad t2', 'mediapad t3', 'mediapad t5', 'mediapad t8', 'mediapad t10']
            ]
        ],
        'xiaomi' => [
            'aliases' => ['xia', 'xiaomi', 'mi', 'redmi', 'شاومي', 'ريدمي'],
            'devices' => [
                'mi series' => ['mi 10', 'mi 11', 'mi 12', 'mi 13', 'mi 14', 'mi 9', 'mi 8', 'mi 6', 'mi 5', 'mi 4'],
                'redmi series' => ['redmi 10', 'redmi 11', 'redmi 12', 'redmi 13', 'redmi 9', 'redmi 8', 'redmi 7', 'redmi 6', 'redmi 5', 'redmi 4'],
                'poco series' => ['poco x3', 'poco x4', 'poco x5', 'poco f3', 'poco f4', 'poco f5', 'poco m3', 'poco m4', 'poco m5'],
                'note series' => ['note 10', 'note 11', 'note 12', 'note 13', 'note 9', 'note 8', 'note 7', 'note 6', 'note 5', 'note 4'],
                'max series' => ['max 2', 'max 3', 'max 4', 'max 5', 'max 6', 'max 7', 'max 8', 'max 9', 'max 10'],
                'a series' => ['a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10'],
                'c series' => ['c1', 'c2', 'c3', 'c4', 'c5', 'c6', 'c7', 'c8', 'c9', 'c10'],
                'y series' => ['y1', 'y2', 'y3', 'y4', 'y5', 'y6', 'y7', 'y8', 'y9', 'y10'],
                'mix series' => ['mix 2', 'mix 3', 'mix 4', 'mix 5', 'mix 6', 'mix 7', 'mix 8', 'mix 9', 'mix 10']
            ]
        ],
        'oppo' => [
            'aliases' => ['op', 'oppo', 'أوبو', 'اوبو'],
            'devices' => [
                'reno series' => ['reno 5', 'reno 6', 'reno 7', 'reno 8', 'reno 9', 'reno 10', 'reno 11', 'reno 12'],
                'find series' => ['find x', 'find x2', 'find x3', 'find x5', 'find x6', 'find x7', 'find x8', 'find x9'],
                'a series' => ['a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12', 'a13', 'a14', 'a15', 'a16', 'a17', 'a18', 'a19', 'a20'],
                'k series' => ['k1', 'k2', 'k3', 'k4', 'k5', 'k6', 'k7', 'k8', 'k9', 'k10', 'k11', 'k12'],
                'f series' => ['f1', 'f2', 'f3', 'f4', 'f5', 'f6', 'f7', 'f8', 'f9', 'f10', 'f11', 'f12', 'f13', 'f14', 'f15', 'f16', 'f17', 'f18', 'f19', 'f20'],
                'r series' => ['r1', 'r2', 'r3', 'r4', 'r5', 'r6', 'r7', 'r8', 'r9', 'r10', 'r11', 'r12', 'r13', 'r14', 'r15', 'r16', 'r17', 'r18', 'r19', 'r20']
            ]
        ],
        'realme' => [
            'aliases' => ['real', 'realme', 'ريلمي', 'ريالمي'],
            'devices' => [
                'gt series' => ['gt', 'gt neo', 'gt neo 2', 'gt neo 3', 'gt neo 4', 'gt neo 5', 'gt neo 6', 'gt neo 7', 'gt neo 8', 'gt neo 9'],
                'q series' => ['q', 'q2', 'q3', 'q3 pro', 'q3t', 'q5', 'q5 pro', 'q5i', 'q6', 'q6 pro', 'q7', 'q7 pro', 'q8', 'q8 pro', 'q9', 'q9 pro'],
                'x series' => ['x', 'x2', 'x3', 'x7', 'x7 max', 'x8', 'x8 pro', 'x9', 'x9 pro', 'x10', 'x10 pro', 'x11', 'x11 pro', 'x12', 'x12 pro'],
                'c series' => ['c1', 'c2', 'c3', 'c4', 'c5', 'c6', 'c7', 'c8', 'c9', 'c10', 'c11', 'c12', 'c13', 'c14', 'c15', 'c16', 'c17', 'c18', 'c19', 'c20'],
                'v series' => ['v1', 'v2', 'v3', 'v4', 'v5', 'v6', 'v7', 'v8', 'v9', 'v10', 'v11', 'v12', 'v13', 'v14', 'v15', 'v16', 'v17', 'v18', 'v19', 'v20'],
                'narzo series' => ['narzo 10', 'narzo 20', 'narzo 30', 'narzo 40', 'narzo 50', 'narzo 60', 'narzo 70', 'narzo 80', 'narzo 90']
            ]
        ],
        'infinix' => [
            'aliases' => ['inf', 'infinix', 'انفنكس', 'انفنيكس'],
            'devices' => [
                'zero series' => ['zero', 'zero x', 'zero x pro', 'zero 2', 'zero 3', 'zero 4', 'zero 5', 'zero 6', 'zero 7', 'zero 8', 'zero 9', 'zero 10'],
                'hot series' => ['hot', 'hot 2', 'hot 3', 'hot 4', 'hot 5', 'hot 6', 'hot 7', 'hot 8', 'hot 9', 'hot 10', 'hot 11', 'hot 12', 'hot 13', 'hot 14', 'hot 15', 'hot 16', 'hot 17', 'hot 18', 'hot 19', 'hot 20'],
                'spark series' => ['spark', 'spark 2', 'spark 3', 'spark 4', 'spark 5', 'spark 6', 'spark 7', 'spark 8', 'spark 9', 'spark 10', 'spark 11', 'spark 12', 'spark 13', 'spark 14', 'spark 15', 'spark 16', 'spark 17', 'spark 18', 'spark 19', 'spark 20'],
                'camon series' => ['camon', 'camon 2', 'camon 3', 'camon 4', 'camon 5', 'camon 6', 'camon 7', 'camon 8', 'camon 9', 'camon 10', 'camon 11', 'camon 12', 'camon 13', 'camon 14', 'camon 15', 'camon 16', 'camon 17', 'camon 18', 'camon 19', 'camon 20'],
                'note series' => ['note', 'note 2', 'note 3', 'note 4', 'note 5', 'note 6', 'note 7', 'note 8', 'note 9', 'note 10', 'note 11', 'note 12', 'note 13', 'note 14', 'note 15', 'note 16', 'note 17', 'note 18', 'note 19', 'note 20'],
                's series' => ['s1', 's2', 's3', 's4', 's5', 's6', 's7', 's8', 's9', 's10', 's11', 's12', 's13', 's14', 's15', 's16', 's17', 's18', 's19', 's20'],
                'x series' => ['x1', 'x2', 'x3', 'x4', 'x5', 'x6', 'x7', 'x8', 'x9', 'x10', 'x11', 'x12', 'x13', 'x14', 'x15', 'x16', 'x17', 'x18', 'x19', 'x20']
            ]
        ],
        'tecno' => [
            'aliases' => ['tec', 'tecno', 'تكنو'],
            'devices' => [
                'camon series' => ['camon', 'camon 1', 'camon 2', 'camon 3', 'camon 4', 'camon 5', 'camon 6', 'camon 7', 'camon 8', 'camon 9', 'camon 10', 'camon 11', 'camon 12', 'camon 13', 'camon 14', 'camon 15', 'camon 16', 'camon 17', 'camon 18', 'camon 19', 'camon 20'],
                'spark series' => ['spark', 'spark 2', 'spark 3', 'spark 4', 'spark 5', 'spark 6', 'spark 7', 'spark 8', 'spark 9', 'spark 10', 'spark 11', 'spark 12', 'spark 13', 'spark 14', 'spark 15', 'spark 16', 'spark 17', 'spark 18', 'spark 19', 'spark 20'],
                'phantom series' => ['phantom', 'phantom 2', 'phantom 3', 'phantom 4', 'phantom 5', 'phantom 6', 'phantom 7', 'phantom 8', 'phantom 9', 'phantom 10', 'phantom 11', 'phantom 12', 'phantom 13', 'phantom 14', 'phantom 15', 'phantom 16', 'phantom 17', 'phantom 18', 'phantom 19', 'phantom 20'],
                'pop series' => ['pop', 'pop 2', 'pop 3', 'pop 4', 'pop 5', 'pop 6', 'pop 7', 'pop 8', 'pop 9', 'pop 10', 'pop 11', 'pop 12', 'pop 13', 'pop 14', 'pop 15', 'pop 16', 'pop 17', 'pop 18', 'pop 19', 'pop 20'],
                'f series' => ['f1', 'f2', 'f3', 'f4', 'f5', 'f6', 'f7', 'f8', 'f9', 'f10', 'f11', 'f12', 'f13', 'f14', 'f15', 'f16', 'f17', 'f18', 'f19', 'f20'],
                'p series' => ['p1', 'p2', 'p3', 'p4', 'p5', 'p6', 'p7', 'p8', 'p9', 'p10', 'p11', 'p12', 'p13', 'p14', 'p15', 'p16', 'p17', 'p18', 'p19', 'p20']
            ]
        ],
        'itel' => [
            'aliases' => ['itel', 'اتيل'],
            'devices' => [
                'vision series' => ['vision', 'vision 1', 'vision 1 pro', 'vision 1 pro 2', 'vision 1 pro 3', 'vision 2', 'vision 3', 'vision 4', 'vision 5', 'vision 6', 'vision 7', 'vision 8', 'vision 9', 'vision 10'],
                's series' => ['s11', 's12', 's13', 's14', 's15', 's16', 's17', 's18', 's19', 's20', 's21', 's22', 's23', 's24', 's25', 's26', 's27', 's28', 's29', 's30'],
                'p series' => ['p11', 'p12', 'p13', 'p14', 'p15', 'p16', 'p17', 'p18', 'p19', 'p20', 'p21', 'p22', 'p23', 'p24', 'p25', 'p26', 'p27', 'p28', 'p29', 'p30'],
                'a series' => ['a11', 'a12', 'a13', 'a14', 'a15', 'a16', 'a17', 'a18', 'a19', 'a20', 'a21', 'a22', 'a23', 'a24', 'a25', 'a26', 'a27', 'a28', 'a29', 'a30'],
                'c series' => ['c11', 'c12', 'c13', 'c14', 'c15', 'c16', 'c17', 'c18', 'c19', 'c20', 'c21', 'c22', 'c23', 'c24', 'c25', 'c26', 'c27', 'c28', 'c29', 'c30']
            ]
        ],
        'apple' => [
            'aliases' => ['iph', 'iphone', 'apple', 'ipad', 'mac', 'ابل', 'ايفون'],
            'devices' => [
                'iphone series' => ['iphone 7', 'iphone 8', 'iphone x', 'iphone 11', 'iphone 12', 'iphone 13', 'iphone 14', 'iphone 15', 'iphone 16', 'iphone se', 'iphone se 2', 'iphone se 3'],
                'ipad series' => ['ipad', 'ipad 2', 'ipad 3', 'ipad 4', 'ipad 5', 'ipad 6', 'ipad 7', 'ipad 8', 'ipad 9', 'ipad 10', 'ipad air', 'ipad pro', 'ipad mini'],
                'mac series' => ['macbook', 'macbook air', 'macbook pro', 'imac', 'mac pro', 'mac mini']
            ]
        ],
        'oneplus' => [
            'aliases' => ['onep', 'oneplus', 'one plus', 'ون بلس'],
            'devices' => [
                'oneplus series' => ['oneplus 1', 'oneplus 2', 'oneplus 3', 'oneplus 3t', 'oneplus 5', 'oneplus 5t', 'oneplus 6', 'oneplus 6t', 'oneplus 7', 'oneplus 7t', 'oneplus 8', 'oneplus 8t', 'oneplus 9', 'oneplus 9r', 'oneplus 9rt', 'oneplus 10', 'oneplus 10r', 'oneplus 10t', 'oneplus 11', 'oneplus 11r', 'oneplus 12'],
                'nord series' => ['nord', 'nord ce', 'nord ce 2', 'nord ce 3', 'nord ce 4', 'nord ce 5', 'nord 2', 'nord 2t', 'nord 3', 'nord 3t', 'nord 4', 'nord 5', 'nord 6', 'nord 7', 'nord 8', 'nord 9', 'nord 10', 'nord 11', 'nord 12', 'nord 13', 'nord 14']
            ]
        ],
        'vivo' => [
            'aliases' => ['viv', 'vivo', 'فيفو'],
            'devices' => [
                'v series' => ['v1', 'v2', 'v3', 'v4', 'v5', 'v6', 'v7', 'v8', 'v9', 'v10', 'v11', 'v12', 'v13', 'v14', 'v15', 'v16', 'v17', 'v18', 'v19', 'v20', 'v21', 'v22', 'v23', 'v24', 'v25', 'v26', 'v27', 'v28', 'v29', 'v30'],
                'y series' => ['y1', 'y2', 'y3', 'y4', 'y5', 'y6', 'y7', 'y8', 'y9', 'y10', 'y11', 'y12', 'y13', 'y14', 'y15', 'y16', 'y17', 'y18', 'y19', 'y20', 'y21', 'y22', 'y23', 'y24', 'y25', 'y26', 'y27', 'y28', 'y29', 'y30'],
                'x series' => ['x1', 'x2', 'x3', 'x4', 'x5', 'x6', 'x7', 'x8', 'x9', 'x10', 'x11', 'x12', 'x13', 'x14', 'x15', 'x16', 'x17', 'x18', 'x19', 'x20', 'x21', 'x22', 'x23', 'x24', 'x25', 'x26', 'x27', 'x28', 'x29', 'x30'],
                'iqoo series' => ['iqoo', 'iqoo 1', 'iqoo 2', 'iqoo 3', 'iqoo 4', 'iqoo 5', 'iqoo 6', 'iqoo 7', 'iqoo 8', 'iqoo 9', 'iqoo 10', 'iqoo 11', 'iqoo 12', 'iqoo 13', 'iqoo 14', 'iqoo 15', 'iqoo 16', 'iqoo 17', 'iqoo 18', 'iqoo 19', 'iqoo 20']
            ]
        ],
        'poco' => [
            'aliases' => ['poco', 'بوكو'],
            'devices' => [
                'poco x series' => ['x1', 'x2', 'x3', 'x4', 'x5', 'x6', 'x7', 'x8', 'x9', 'x10', 'x11', 'x12', 'x13', 'x14', 'x15', 'x16', 'x17', 'x18', 'x19', 'x20'],
                'poco f series' => ['f1', 'f2', 'f3', 'f4', 'f5', 'f6', 'f7', 'f8', 'f9', 'f10', 'f11', 'f12', 'f13', 'f14', 'f15', 'f16', 'f17', 'f18', 'f19', 'f20'],
                'poco m series' => ['m1', 'm2', 'm3', 'm4', 'm5', 'm6', 'm7', 'm8', 'm9', 'm10', 'm11', 'm12', 'm13', 'm14', 'm15', 'm16', 'm17', 'm18', 'm19', 'm20'],
                'poco c series' => ['c1', 'c2', 'c3', 'c4', 'c5', 'c6', 'c7', 'c8', 'c9', 'c10', 'c11', 'c12', 'c13', 'c14', 'c15', 'c16', 'c17', 'c18', 'c19', 'c20']
            ]
        ],
        'motorola' => [
            'aliases' => ['moto', 'motorola', 'موتورولا'],
            'devices' => [
                'moto g series' => ['g1', 'g2', 'g3', 'g4', 'g5', 'g6', 'g7', 'g8', 'g9', 'g10', 'g11', 'g12', 'g13', 'g14', 'g15', 'g16', 'g17', 'g18', 'g19', 'g20'],
                'moto e series' => ['e1', 'e2', 'e3', 'e4', 'e5', 'e6', 'e7', 'e8', 'e9', 'e10', 'e11', 'e12', 'e13', 'e14', 'e15', 'e16', 'e17', 'e18', 'e19', 'e20'],
                'moto x series' => ['x1', 'x2', 'x3', 'x4', 'x5', 'x6', 'x7', 'x8', 'x9', 'x10', 'x11', 'x12', 'x13', 'x14', 'x15', 'x16', 'x17', 'x18', 'x19', 'x20'],
                'moto z series' => ['z1', 'z2', 'z3', 'z4', 'z5', 'z6', 'z7', 'z8', 'z9', 'z10', 'z11', 'z12', 'z13', 'z14', 'z15', 'z16', 'z17', 'z18', 'z19', 'z20'],
                'moto one series' => ['one', 'one action', 'one fusion', 'one fusion+', 'one hyper', 'one macro', 'one vision', 'one zoom', 'one ace', 'one 5g', 'one 5g ace', 'one 5g uw ace']
            ]
        ],
        'nokia' => [
            'aliases' => ['nokia', 'نوكيا'],
            'devices' => [
                'nokia 1 series' => ['1', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6', '1.7', '1.8', '1.9', '1.10'],
                'nokia 2 series' => ['2', '2.1', '2.2', '2.3', '2.4', '2.5', '2.6', '2.7', '2.8', '2.9', '2.10'],
                'nokia 3 series' => ['3', '3.1', '3.2', '3.3', '3.4', '3.5', '3.6', '3.7', '3.8', '3.9', '3.10'],
                'nokia 4 series' => ['4', '4.1', '4.2', '4.3', '4.4', '4.5', '4.6', '4.7', '4.8', '4.9', '4.10'],
                'nokia 5 series' => ['5', '5.1', '5.2', '5.3', '5.4', '5.5', '5.6', '5.7', '5.8', '5.9', '5.10'],
                'nokia 6 series' => ['6', '6.1', '6.2', '6.3', '6.4', '6.5', '6.6', '6.7', '6.8', '6.9', '6.10'],
                'nokia 7 series' => ['7', '7.1', '7.2', '7.3', '7.4', '7.5', '7.6', '7.7', '7.8', '7.9', '7.10'],
                'nokia 8 series' => ['8', '8.1', '8.2', '8.3', '8.4', '8.5', '8.6', '8.7', '8.8', '8.9', '8.10'],
                'nokia 9 series' => ['9', '9.1', '9.2', '9.3', '9.4', '9.5', '9.6', '9.7', '9.8', '9.9', '9.10']
            ]
        ]
    ];
}

/**
 * خوارزمية تصنيف ذكية لفهم الأجهزة المتعددة والفواصل
 */
function smart_device_classification($product_name) {
    if (!$product_name) return [];
    
    $name_lc = mb_strtolower(trim($product_name));
    
    // 1) اكتشاف نوع الملحق
    $accessory_type = detect_accessory_type_enhanced($name_lc);
    
    // 2) فصل الأجهزة المتعددة بذكاء
    $devices = smart_split_devices($product_name);
    
    $results = [];
    
    foreach ($devices as $device_name) {
        $device_analysis = smart_analyze_device($device_name, $accessory_type);
        if ($device_analysis['brand']) {
            $results[] = $device_analysis;
        }
    }
    
    // إذا لم نجد أجهزة منفصلة، نحلل الاسم كجهاز واحد
    if (empty($results)) {
        $results[] = smart_analyze_device($product_name, $accessory_type);
    }
    
    return $results;
}

/**
 * فصل الأجهزة المتعددة بذكاء - إصلاح مشكلة التكرار
 */
function smart_split_devices($product_name) {
    $clean_name = trim($product_name);

    // حضّر معلومات الأساس من الاسم الأصلي
    $base_info = extract_base_info($clean_name);
    $prefix = '';
    // استخرج البادئة قبل أول موديل (مثل "LCD SAM")
    if (preg_match('/^([^\d()]+?)\s*(?=[A-Za-z\p{L}]+\s*\d)/u', $clean_name, $m)) {
        $prefix = trim($m[1]);
    } elseif (preg_match('/^([^()]+?)\s*(?=\w+)/u', $clean_name, $m)) {
        // احتياطي: خذ أول مقطع غير بين قوسين كبادئة
        $prefix = trim($m[1]);
    }
    // أزل اللاحقة بين أقواس من النهاية عند التقسيم
    $name_no_paren = preg_replace('/\([^)]*\)\s*$/u', '', $clean_name);
    $core = $name_no_paren;
    if ($prefix !== '' && mb_stripos($name_no_paren, $prefix) === 0) {
        $core = ltrim(mb_substr($name_no_paren, mb_strlen($prefix)));
    }

    // قسم النواة أولاً على / و | و \
    $chunks = preg_split('/\s*(?:\/|\||\\\\)\s*/u', $core);
    $parts = [];
    if (is_array($chunks)) {
        foreach ($chunks as $chunk) {
            $chunk = trim($chunk);
            if ($chunk === '') continue;
            // قسم كذلك على " - " (يسمح بمسافات اختيارية) فقط إذا كانت الأجزاء تبدو كرموز موديلات (تحتوي أرقام)
            $sub = [$chunk];
            if (preg_match('/\s*-\s*/u', $chunk)) {
                $tmp = preg_split('/\s*-\s*/u', $chunk);
                $hasModelish = 0;
                foreach ($tmp as $t) { if (preg_match('/\d/u', $t)) $hasModelish++; }
                if ($hasModelish >= 2) { $sub = $tmp; }
            }
            if (is_array($sub)) {
                foreach ($sub as $s) {
                    $s = trim($s);
                    if ($s !== '') $parts[] = $s;
                }
            } else {
                $parts[] = $chunk;
            }
        }
    }

    // نظف وابقِ على الأجزاء المعقولة فقط
    $parts = array_values(array_filter(array_map('trim', $parts), function($p){ return mb_strlen($p) > 1; }));
    if (count($parts) <= 1) {
        return [$clean_name];
    }

    // إعادة البناء النهائي باستخدام clean_and_rebuild_devices (يعتمد على accessory_type + additional_info)
    // لا تعيد العنصر الأصلي، فقط الأجزاء المفصولة مُعاد بناؤها
    if (!empty($prefix)) {
        $clean_name = trim($prefix) . ' ' . trim($core);
    }
    $rebuilt = clean_and_rebuild_devices($parts, $clean_name);
    return count($rebuilt) ? $rebuilt : [$clean_name];
}

/**
 * تنظيف وإعادة بناء الأجهزة لتجنب التكرار
 */
function clean_and_rebuild_devices($devices, $original_name) {
    $clean_devices = [];
    $base_info = extract_base_info($original_name);
    
    foreach ($devices as $device) {
        $clean_device = clean_single_device($device, $base_info);
        if ($clean_device && !in_array($clean_device, $clean_devices)) {
            $clean_devices[] = $clean_device;
        }
    }
    
    return array_values($clean_devices);
}

/**
 * استخراج المعلومات الأساسية من الاسم الأصلي
 */
function extract_base_info($product_name) {
    $name_lc = mb_strtolower($product_name);
    
    // البحث عن نوع الملحق
    $accessory_type = detect_accessory_type_enhanced($name_lc);
    
    // البحث عن معلومات إضافية (مثل MCN -EDITION)
    $additional_info = '';
    if (preg_match('/\(([^)]+)\)/', $product_name, $matches)) {
        $additional_info = trim($matches[1]);
    }

    // استنتاج البادئة العامة قبل أول موديل (مثل "LCD SAM")
    $prefix = '';
    if (preg_match('/^([^\d()]+?)\s*(?=[A-Za-z\p{L}]+\s*\d)/u', $product_name, $pm)) {
        $prefix = trim($pm[1]);
    }
    
    return [
        'accessory_type' => $accessory_type,
        'additional_info' => $additional_info,
        'original_name' => $product_name,
        'prefix' => $prefix
    ];
}

/**
 * تنظيف جهاز واحد وإعادة بناؤه
 */
function clean_single_device($device, $base_info) {
    $device = trim($device);
    if (empty($device) || mb_strlen($device) < 2) return null;
    
    // إزالة البادئة إن كانت مكررة داخل الجزء (مثل "LCD SAM" داخل الجزء)
    if (!empty($base_info['prefix'])) {
        $pref = trim($base_info['prefix']);
        // أزل البادئة من الجزء إذا كانت موجودة في بدايته فقط، كي لا نكررها لاحقاً
        if ($pref !== '' && mb_stripos($device, $pref) === 0) {
            $device = trim(mb_substr($device, mb_strlen($pref)));
        }
    }
    // إزالة أي أقواس زائدة داخل الجزء
    $device = preg_replace('/\([^)]*\)/u', '', $device);

    // إزالة الكلمات المكررة
    $device = remove_duplicate_words($device);
    
    // إعادة بناء الاسم الكامل: احترم البادئة في الاسم الأصلي إن وجدت (مثل LCD SAM)
    // حاول استخراج بادئة من الاسم الأصلي قبل أول موديل
    $prefix = !empty($base_info['prefix']) ? trim($base_info['prefix']) : trim($base_info['accessory_type']);
    $full_name = trim($prefix) . ' ' . $device;
    
    // إضافة المعلومات الإضافية إذا وجدت
    if (!empty($base_info['additional_info'])) {
        $full_name .= ' (' . $base_info['additional_info'] . ')';
    }
    
    return $full_name;
}

/**
 * إزالة الكلمات المكررة من اسم الجهاز
 */
function remove_duplicate_words($device) {
    $words = explode(' ', $device);
    $unique_words = [];
    
    foreach ($words as $word) {
        $word = trim($word);
        if (!empty($word) && !in_array($word, $unique_words)) {
            $unique_words[] = $word;
        }
    }
    
    return implode(' ', $unique_words);
}

/**
 * تحليل جهاز واحد بذكاء
 */
function smart_analyze_device($device_name, $accessory_type) {
    $device_lc = mb_strtolower(trim($device_name));
    
    // 1) البحث عن البراند باستخدام قاعدة البيانات الشاملة
    $brand = smart_detect_brand($device_lc);
    
    // 2) استخراج الموديل والسلسلة
    $model_info = extract_device_model($device_name, $brand);
    
    // 3) تحديد النوع
    $type = $accessory_type;
    
    return [
        'brand' => $brand,
        'model' => $model_info['model'],
        'series' => $model_info['series'],
        'type' => $type,
        'original_name' => $device_name,
        'confidence' => $model_info['confidence']
    ];
}

/**
 * اكتشاف البراند بذكاء
 */
function smart_detect_brand($name_lc) {
    $brands_db = get_comprehensive_brands_database();
    
    $best_match = null;
    $best_score = 0;
    
    foreach ($brands_db as $brand => $data) {
        $score = calculate_brand_match_score($name_lc, $data);
        if ($score > $best_score) {
            $best_score = $score;
            $best_match = $brand;
        }
    }
    
    // عتبة المطابقة
    return ($best_score >= 0.5) ? $best_match : null;
}

/**
 * حساب درجة مطابقة البراند
 */
function calculate_brand_match_score($name_lc, $brand_data) {
    $score = 0;
    
    // مطابقة الاختصارات
    foreach ($brand_data['aliases'] as $alias) {
        $alias_lc = mb_strtolower($alias);
        
        // مطابقة كاملة
        if ($name_lc === $alias_lc) {
            $score += 1.0;
            break;
        }
        
        // مطابقة كلمة مستقلة
        if (preg_match('/\b' . preg_quote($alias_lc, '/') . '\b/u', $name_lc)) {
            $score += 0.9;
            break;
        }
        
        // مطابقة جزئية
        if (mb_strpos($name_lc, $alias_lc) !== false) {
            $score += 0.6;
        }
    }
    
    // مطابقة الأجهزة المعروفة
    foreach ($brand_data['devices'] as $series => $devices) {
        foreach ($devices as $device) {
            $device_lc = mb_strtolower($device);
            
            // مطابقة كاملة للجهاز
            if (mb_strpos($name_lc, $device_lc) !== false) {
                $score += 0.8;
                break 2;
            }
            
            // مطابقة جزئية للجهاز
            $device_words = explode(' ', $device_lc);
            foreach ($device_words as $word) {
                if (mb_strlen($word) > 2 && mb_strpos($name_lc, $word) !== false) {
                    $score += 0.4;
                }
            }
        }
    }
    
    return min($score, 1.0); // لا تتجاوز 1.0
}

/**
 * استخراج موديل الجهاز والسلسلة - إصلاح الفئات
 */
function extract_device_model($device_name, $brand) {
    if (!$brand) return ['model' => null, 'series' => null, 'confidence' => 0];
    
    $brands_db = get_comprehensive_brands_database();
    if (!isset($brands_db[$brand])) {
        return ['model' => null, 'series' => null, 'confidence' => 0];
    }
    
    $device_lc = mb_strtolower($device_name);
    $best_match = null;
    $best_series = null;
    $best_confidence = 0;
    
    // البحث عن السلسلة أولاً (مثل A, S, Note, M)
    $series_patterns = [
        'galaxy a' => '/a\s*(\d+)/i',
        'galaxy s' => '/s\s*(\d+)/i', 
        'galaxy note' => '/note\s*(\d+)/i',
        'galaxy m' => '/m\s*(\d+)/i',
        'galaxy z' => '/z\s*(flip|fold)/i',
        'p series' => '/p\s*(\d+)/i',
        'mate series' => '/mate\s*(\d+)/i',
        'nova series' => '/nova\s*(\d+)/i',
        'reno series' => '/reno\s*(\d+)/i',
        'find series' => '/find\s*x/i',
        'gt series' => '/gt\s*(neo)?/i',
        'zero series' => '/zero\s*(x|pro)?/i',
        'hot series' => '/hot\s*(\d+)/i',
        'spark series' => '/spark\s*(\d+)/i',
        'camon series' => '/camon\s*(\d+)/i'
    ];
    
    foreach ($series_patterns as $series => $pattern) {
        if (preg_match($pattern, $device_name, $matches)) {
            $best_series = $series;
            $best_match = $matches[0];
            $best_confidence = 1.0;
            break;
        }
    }
    
    // إذا لم نجد سلسلة، ابحث في قاعدة البيانات
    if (!$best_series) {
        foreach ($brands_db[$brand]['devices'] as $series => $devices) {
            foreach ($devices as $device) {
                $device_lc_pattern = mb_strtolower($device);
                
                // مطابقة كاملة
                if (mb_strpos($device_lc, $device_lc_pattern) !== false) {
                    $confidence = 1.0;
                    if ($confidence > $best_confidence) {
                        $best_confidence = $confidence;
                        $best_match = $device;
                        $best_series = $series;
                    }
                    break;
                }
                
                // مطابقة جزئية
                $device_words = explode(' ', $device_lc_pattern);
                $match_count = 0;
                foreach ($device_words as $word) {
                    if (mb_strlen($word) > 2 && mb_strpos($device_lc, $word) !== false) {
                        $match_count++;
                    }
                }
                
                if ($match_count > 0) {
                    $confidence = $match_count / count($device_words);
                    if ($confidence > $best_confidence) {
                        $best_confidence = $confidence;
                        $best_match = $device;
                        $best_series = $series;
                    }
                }
            }
        }
    }
    
    return [
        'model' => $best_match,
        'series' => $best_series,
        'confidence' => $best_confidence
    ];
}

/**
 * التحقق من أن الأجزاء تنتمي لنفس السلسلة
 */
function is_same_device_series($parts) {
    if (count($parts) < 2) return false;
    
    // البحث عن أنماط مشتركة
    $patterns = [
        '/^([a-z]+)\s*(\d+)$/i', // مثل A30, A50
        '/^([a-z]+)\s*(\d+)\s*([a-z]+)$/i', // مثل Note 20, Note 10
        '/^([a-z]+)\s*(\d+)\s*([a-z]+)\s*(\d+)$/i' // مثل Galaxy S20, Galaxy S10
    ];
    
    $series_names = [];
    
    foreach ($parts as $part) {
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $part, $matches)) {
                $series_names[] = $matches[1]; // اسم السلسلة
                break;
            }
        }
    }
    
    // إذا كانت جميع الأجزاء تنتمي لنفس السلسلة
    if (count(array_unique($series_names)) === 1) {
        return true;
    }
    
    return false;
}

/**
 * اكتشاف نوع الملحق محسن
 */
function detect_accessory_type_enhanced($name_lc) {
    $accessory_patterns = [
        'lcd' => ['lcd', 'شاشة', 'display', 'screen', 'شاشه', 'شاشة lcd', 'lcd شاشة'],
        'battery' => ['battery', 'بطارية', 'bat', 'بطاريه', 'بطارية هاتف', 'battery phone'],
        'charger' => ['charger', 'شاحن', 'adapter', 'شاحن هاتف', 'phone charger', 'power adapter'],
        'cable' => ['cable', 'كابل', 'wire', 'cord', 'كابل شحن', 'charging cable', 'data cable'],
        'case' => ['case', 'غلاف', 'cover', 'protector', 'جراب', 'حافظة', 'phone case', 'phone cover'],
        'headphone' => ['headphone', 'سماعة', 'earphone', 'سماعات', 'headphones', 'earphones'],
        'camera' => ['camera', 'كاميرا', 'lens', 'عدسة', 'camera lens', 'phone camera'],
        'frame' => ['frame', 'فريم', 'إطار', 'frame phone', 'phone frame', 'body frame'],
        'body' => ['body', 'بدن', 'جسم', 'body phone', 'phone body', 'phone housing'],
        'speaker' => ['speaker', 'مكبر صوت', 'مكبر', 'phone speaker', 'external speaker'],
        'antenna' => ['antenna', 'هوائي', 'antenna phone', 'phone antenna'],
        'flex' => ['flex', 'فلكس', 'كابل مرن', 'flex cable', 'phone flex'],
        'motherboard' => ['motherboard', 'لوحة أم', 'mother board', 'main board', 'phone motherboard'],
        'camera_module' => ['camera module', 'وحدة كاميرا', 'camera unit', 'phone camera module']
    ];
    
    foreach ($accessory_patterns as $type => $patterns) {
        foreach ($patterns as $pattern) {
            if (mb_strpos($name_lc, $pattern) !== false) {
                return $type;
            }
        }
    }
    
    return 'other';
}
?>