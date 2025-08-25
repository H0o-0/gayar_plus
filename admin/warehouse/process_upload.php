<?php 
require_once('../../config.php'); 
require_once('csv_reader_advanced.php');
require_once('device_splitter_enhanced.php');

// تعيين الترميز الصحيح للعربية
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['csv_file'])) {
    echo '<div class="alert alert-danger">لم يتم رفع أي ملف!</div>';
    exit;
}

$file = $_FILES['csv_file'];
$import_batch = $_POST['import_batch'] ?? 'batch' . date('Y_m_d_H_i_s');
$name_column = intval($_POST['name_column'] ?? 0);
$price_column = intval($_POST['price_column'] ?? 1);
$start_row = intval($_POST['start_row'] ?? 2);

// التحقق من نوع الملف
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if ($file_extension !== 'csv') {
    echo '<div class="alert alert-danger">نوع الملف غير مدعوم! يرجى رفع ملف CSV فقط.</div>';
    exit;
}

// ==================================================================
// البداية: الحل الجديد والمحسن باستخدام قارئ CSV
// ==================================================================

echo '<div class="alert alert-info">
<i class="fas fa-file-csv"></i>
جاري قراءة ملف CSV باستخدام قارئ CSV المحسن...
</div>';

// محاولة قراءة الملف باستخدام قارئ CSV المحسن
$csv_data_raw = read_csv_advanced($file['tmp_name']);

if (!is_array($csv_data_raw) || !isset($csv_data_raw['status']) || $csv_data_raw['status'] === 'error') {
    $err_msg = '';
    if (is_array($csv_data_raw) && isset($csv_data_raw['message'])) {
        $err_msg = htmlspecialchars($csv_data_raw['message']);
    } else {
        $err_msg = 'سبب غير معروف. يرجى التأكد من ترميز الملف والفواصل.';
    }
    echo '<div class="alert alert-danger">
    <h5><i class="icon fas fa-times"></i> فشل في قراءة الملف!</h5>
    ' . $err_msg . '
    </div>';
    exit;
}

$csv_data = $csv_data_raw['data'];

echo '<div class="alert alert-success">
<i class="fas fa-check-circle"></i>
تم قراءة الملف بنجاح! تم العثور على ' . count($csv_data) . ' سطر.
</div>';

// عرض عينة من البيانات المقروءة
echo '<div class="alert alert-info">
<h5><i class="icon fas fa-table"></i> عينة من البيانات المقروءة:</h5>
<div class="table-responsive">
<table class="table table-bordered table-sm">
<thead>
<tr>
<th>#</th>
<th>العمود 1</th>
<th>العمود 2</th>
<th>العمود 3</th>
</tr>
</thead>
<tbody>';

// عرض أول 5 صفوف كعينة
$sample_rows = array_slice($csv_data, 0, 5);
foreach ($sample_rows as $index => $row) {
    echo '<tr>';
    echo '<td>' . ($index + 1) . '</td>';
    for ($i = 0; $i < min(3, count($row)); $i++) {
        $cell_value = $row[$i];
        echo '<td>' . htmlspecialchars($cell_value) . '</td>';
    }
    echo '</tr>';
}

echo '</tbody>
</table>
</div>
</div>';

// تشخيص الأعمدة
echo '<div class="alert alert-warning">
<h5><i class="icon fas fa-info-circle"></i> تحليل الأعمدة:</h5>
<p>عمود الاسم المحدد: <strong>العمود ' . ($name_column + 1) . '</strong></p>
<p>عمود السعر المحدد: <strong>العمود ' . ($price_column + 1) . '</strong></p>
</div>';

// عرض أول صف للتأكد من الأعمدة
if (!empty($csv_data[0])) {
    echo '<div class="alert alert-info">
    <h5>الصف الأول (العناوين):</h5>
    <ul>';
    foreach ($csv_data[0] as $index => $header) {
        $selected = '';
        if ($index == $name_column) $selected = ' <span class="badge badge-primary">اسم المنتج</span>';
        if ($index == $price_column) $selected = ' <span class="badge badge-success">السعر</span>';
        echo '<li>العمود ' . ($index + 1) . ': <strong>' . htmlspecialchars($header) . '</strong>' . $selected . '</li>';
    }
    echo '</ul></div>';
}

// تحويل البيانات للتنسيق المطلوب وتطبيق start_row
$data = [];
foreach ($csv_data as $index => $row) {
    if ($index + 1 >= $start_row) {
        $data[] = $row;
    }
}

// معاينة البيانات قبل المعالجة
echo '<div class="card">
<div class="card-header">
<h5>معاينة البيانات قبل المعالجة</h5>
</div>
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered">
<thead>
<tr>
<th>الصف</th>
<th>اسم المنتج</th>
<th>السعر</th>
<th>الحالة</th>
</tr>
</thead>
<tbody>';

$preview_count = 0;
foreach ($data as $index => $row) {
    if ($preview_count >= 5) break;

    $product_name = isset($row[$name_column]) ? $row[$name_column] : 'غير موجود';
    $price = isset($row[$price_column]) ? $row[$price_column] : 'غير موجود';
    $price_numeric = extract_price($price);

    $status = 'صحيح';
    $status_class = 'success';

    if (empty(trim($product_name))) {
        $status = 'اسم فارغ';
        $status_class = 'danger';
    } elseif ($price_numeric <= 0) {
        $status = 'سعر غير صحيح';
        $status_class = 'warning';
    }

    echo '<tr>
            <td>' . ($index + $start_row) . '</td>
            <td>' . htmlspecialchars($product_name) . '</td>
            <td>' . htmlspecialchars($price) . ' (' . $price_numeric . ')</td>
            <td><span class="badge badge-' . $status_class . '">' . $status . '</span></td>
          </tr>';

    $preview_count++;
}

echo '</tbody>
</table>
</div>
</div>
</div>';

// ==================================================================
// النهاية: الحل الجديد
// ==================================================================

// معالجة البيانات وحفظها
$processed_count = 0;
$classified_count = 0;
$errors = [];
$classification_stats = [];

// جلب كل الفئات والماركات مرة واحدة لتحسين الأداء
$brands_dictionary = get_brands_dictionary();
$accessories_dictionary = get_accessories_dictionary();

foreach ($data as $index => $row) {
    // أضف هذا للتشخيص
    if ($index < 3) { // عرض أول 3 صفوف للتشخيص
        echo '<div class="alert alert-info">
        <strong>الصف ' . ($index + $start_row) . ':</strong><br>
        اسم المنتج (العمود ' . ($name_column + 1) . '): ' . htmlspecialchars($row[$name_column] ?? 'فارغ') . '<br>
        السعر (العمود ' . ($price_column + 1) . '): ' . htmlspecialchars($row[$price_column] ?? 'فارغ') . '
        </div>';
    }

    if (count($row) <= max($name_column, $price_column)) {
        $errors[] = "السطر " . ($index + $start_row) . ": بيانات غير كافية";
        continue;
    }

    $product_name = isset($row[$name_column]) ? trim($row[$name_column]) : '';
    $price = isset($row[$price_column]) ? trim($row[$price_column]) : '';

    if (empty($product_name)) {
        $errors[] = "السطر " . ($index + $start_row) . ": اسم المنتج فارغ";
        continue;
    }

    $price_numeric = extract_price($price);
    if ($price_numeric <= 0) {
        $errors[] = "السطر " . ($index + $start_row) . ": السعر غير صحيح ($price)";
        continue;
    }

    // استخدام النظام الجديد لتقسيم الأجهزة المتعددة
    $split_devices = enhanced_split_multiple_devices($product_name);
    $analysis_results = [];
    
    foreach ($split_devices as $device_name) {
        $analysis = smart_device_classification($device_name);
        if (is_array($analysis) && count($analysis) > 0) {
            // إضافة معلومة إضافية عن التقسيم
            foreach ($analysis as &$single_analysis) {
                $single_analysis['original_name'] = $device_name;
                $single_analysis['was_split'] = count($split_devices) > 1;
                $single_analysis['split_from'] = $product_name;
            }
            $analysis_results = array_merge($analysis_results, $analysis);
        } else {
            // إذا فشل التصنيف، أضف تحليل أساسي
            $analysis_results[] = [
                'brand' => detect_brand_from_name_enhanced(mb_strtolower($device_name)),
                'type' => detect_accessory_type_enhanced(mb_strtolower($device_name)),
                'model' => null,
                'series' => null,
                'confidence' => 0.3,
                'original_name' => $device_name,
                'was_split' => count($split_devices) > 1,
                'split_from' => $product_name
            ];
        }
    }

    // معالجة الأجهزة المتعددة - إنشاء سجل منفصل لكل جهاز
    $current_processed = 0;

    foreach ($analysis_results as $analysis) {
        // تهيئة جميع المتغيرات في بداية كل حلقة لتجنب الأخطاء
        $brand_id_val = null;
        $series_id_val = null;
        $model_id_val = null;
        $category_id_val = null;
        $sub_category_id_val = null;

        // اسم الجهاز المنفصل الناتج من الخوارزمية (مطلوب مبكراً لاستخراج السلسلة)
        $enhanced_product_name = $analysis['original_name'] ?? $product_name;

        $suggested_brand = $analysis['brand'];
        $suggested_type = $analysis['type'] ?? 'other';
        $device_model = $analysis['model'] ?? '';
        $device_series = $analysis['series'] ?? '';
        $confidence = $analysis['confidence'] ?? 0;

        // initialize ids to avoid undefined notices
        $brand_id_val = 'NULL';
        $series_id_val = 'NULL';
        $model_id_val = 'NULL';
        $category_id_val = 'NULL';
        $sub_category_id_val = 'NULL';

        // حاول اكتشاف البراند إذا كان فارغاً
        if (empty($suggested_brand)) {
            $det = smart_detect_brand(mb_strtolower($enhanced_product_name));
            if ($det) { $suggested_brand = $det; }
        }

        if (!empty($suggested_brand)) {
            // توحيد اسم البراند من الاختصارات إلى الاسم القياسي
            $aliases = [
                'sam' => 'samsung', 'samsung' => 'samsung', 'سام' => 'samsung', 'سامسونج' => 'samsung',
                'apple' => 'apple', 'ابل' => 'apple', 'ايفون' => 'apple', 'iphone' => 'apple',
                'oppo' => 'oppo', 'أوبو' => 'oppo', 'اوبو' => 'oppo',
                'vivo' => 'vivo', 'فيفو' => 'vivo',
                'huawei' => 'huawei', 'هواوي' => 'huawei',
                'xiaomi' => 'xiaomi', 'شاومي' => 'xiaomi', 'ريدمي' => 'xiaomi', 'redmi' => 'xiaomi', 'mi' => 'xiaomi',
                'infinix' => 'infinix', 'انفنكس' => 'infinix', 'tecno' => 'tecno', 'تكنو' => 'tecno',
                'nokia' => 'nokia', 'نوكيا' => 'nokia', 'oneplus' => 'oneplus', 'ون بلس' => 'oneplus'
            ];
            $brand_lookup = strtolower($suggested_brand);
            if (isset($aliases[$brand_lookup])) {
                $brand_lookup = $aliases[$brand_lookup];
                $suggested_brand = ucfirst($brand_lookup);
            }
            $brand_lookup_escaped = $conn->real_escape_string($brand_lookup);

            // البحث في جدول brands أولاً
            $bq = $conn->query("SELECT id FROM brands WHERE LOWER(name) = '{$brand_lookup_escaped}' LIMIT 1");
            if ($bq && $bq->num_rows > 0) {
                $brand_id_val = (int)$bq->fetch_assoc()['id'];
            }

            // البحث في جدول الفئات كاحتياطي
            $cat_q = $conn->query("SELECT id FROM categories WHERE LOWER(category) = '{$brand_lookup_escaped}' OR LOWER(name) = '{$brand_lookup_escaped}' LIMIT 1");
            if ($cat_q && $cat_q->num_rows > 0) {
                $category_id_val = (int)$cat_q->fetch_assoc()['id'];

                // تخمين السلسلة من اسم الجهاز للعلامات المعروفة (مثل A/M/S/Note لسامسونج)
                if ($brand_lookup === 'samsung') {
                    $series_key = '';
                    if (preg_match('/\b(note)\s*\d+/i', $enhanced_product_name)) {
                        $series_key = 'note';
                    } elseif (preg_match('/\b([amsz])\s*\d{1,3}\b/i', $enhanced_product_name, $mm)) {
                        $series_key = strtoupper($mm[1]);
                    }
                    if ($series_key !== '') {
                        $series_like = $conn->real_escape_string($series_key);
                        // جدول series عندما يتوفر brand_id
                        if ($brand_id_val !== null) {
                            $sq = $conn->query("SELECT id FROM series WHERE brand_id = {$brand_id_val} AND (LOWER(name) = '{$series_like}' OR LOWER(name) LIKE '%{$series_like}%') LIMIT 1");
                            if ($sq && $sq->num_rows > 0) {
                                $series_id_val = (int)$sq->fetch_assoc()['id'];
                            }
                        }
                        // fallback: sub_categories
                        if ($series_id_val === null) {
                            $sub_q = $conn->query("SELECT id FROM sub_categories WHERE parent_id = {$category_id_val} AND (LOWER(sub_category) = '{$series_like}' OR LOWER(sub_category) LIKE '%{$series_like}%') LIMIT 1");
                            if ($sub_q && $sub_q->num_rows > 0) {
                                $sub_category_id_val = (int)$sub_q->fetch_assoc()['id'];
                            }
                        }
                    }
                } else {
                    // محاولة عامة باستخدام السلسلة المكتشفة من التحليل
                    if (!empty($device_series)) {
                        $series_lookup = strtolower($device_series);
                        $series_lookup_escaped = $conn->real_escape_string($series_lookup);
                        if ($brand_id_val !== null) {
                            $sq = $conn->query("SELECT id FROM series WHERE brand_id = {$brand_id_val} AND (LOWER(name) LIKE '%{$series_lookup_escaped}%') LIMIT 1");
                            if ($sq && $sq->num_rows > 0) {
                                $series_id_val = (int)$sq->fetch_assoc()['id'];
                            }
                        }
                        if ($series_id_val === null) {
                            $sub_q = $conn->query("SELECT id FROM sub_categories WHERE parent_id = {$category_id_val} AND (LOWER(sub_category) LIKE '%{$series_lookup_escaped}%' OR LOWER(name) LIKE '%{$series_lookup_escaped}%') LIMIT 1");
                            if ($sub_q && $sub_q->num_rows > 0) {
                                $sub_category_id_val = (int)$sub_q->fetch_assoc()['id'];
                            }
                        }
                    }
                }
            }
        }
        
        // تحديد الحالة بناءً على وجود التصنيف ودرجة الثقة
        $status = 'unclassified';
        if (($brand_id_val !== null || $series_id_val !== null || $category_id_val !== null || $sub_category_id_val !== null) && $confidence >= 0.5) {
            $status = 'classified';
        } elseif ($confidence >= 0.8) {
            $status = 'classified'; // حتى لو لم نجد الفئة، درجة الثقة عالية
        }
        
        // اسم الجهاز المنفصل الناتج من الخوارزمية + إضافة البراند كبادئة إذا لم يظهر
        $enhanced_product_name = $analysis['original_name'] ?? $product_name;
        if (!empty($suggested_brand)) {
            $brand_token = (strtolower($suggested_brand) === 'samsung') ? 'SAM' : strtoupper($suggested_brand);
            if (mb_stripos($enhanced_product_name, $brand_token) === false || mb_stripos($enhanced_product_name, $brand_token) > 10) {
                if (preg_match('/^\s*lcd\s*/i', $enhanced_product_name)) {
                    $enhanced_product_name = preg_replace('/^\s*lcd\s*/i', 'LCD ' . $brand_token . ' ', $enhanced_product_name);
                } else {
                    $enhanced_product_name = $brand_token . ' ' . $enhanced_product_name;
                }
                $enhanced_product_name = trim(preg_replace('/\s+/', ' ', $enhanced_product_name));
            }
        }
        
        // تنظيف البيانات للإدراج
        $product_name_escaped = $conn->real_escape_string($enhanced_product_name);
        $suggested_brand_escaped = $conn->real_escape_string($suggested_brand);
        $suggested_type_escaped = $conn->real_escape_string($suggested_type);
        $import_batch_escaped = $conn->real_escape_string($import_batch);
        
        // إدراج في قاعدة البيانات مع معالجة القيم الفارغة
        $brand_id_value = ($brand_id_val !== null) ? $brand_id_val : 'NULL';
        $series_id_value = ($series_id_val !== null) ? $series_id_val : 'NULL';
        $model_id_value = ($model_id_val !== null) ? $model_id_val : 'NULL';
        $category_id_value = ($category_id_val !== null) ? $category_id_val : 'NULL';
        $sub_category_id_value = ($sub_category_id_val !== null) ? $sub_category_id_val : 'NULL';
        
        $sql = "INSERT INTO temp_warehouse 
                (product_name, original_price, suggested_brand, suggested_type, brand_id, series_id, model_id, status, import_batch, category_id, sub_category_id, raw_data, confidence)
                VALUES 
                ('$product_name_escaped', $price_numeric, '$suggested_brand_escaped', '$suggested_type_escaped', $brand_id_value, $series_id_value, $model_id_value, '$status', '$import_batch_escaped', $category_id_value, $sub_category_id_value, '" . $conn->real_escape_string(json_encode($row)) . "', $confidence)";
        
        if ($conn->query($sql)) {
            $current_processed++;
        } else {
            error_log("خطأ في إدراج المنتج: " . $conn->error);
        }
    }

    // إذا لم يتم معالجة أي جهاز، أدرج السجل الأصلي
    if ($current_processed == 0) {
        $suggested_brand = 'unknown';
        $suggested_type = 'other';
        $status = 'unclassified';
        
        $product_name_escaped = $conn->real_escape_string($product_name);
        $suggested_brand_escaped = $conn->real_escape_string($suggested_brand);
        $suggested_type_escaped = $conn->real_escape_string($suggested_type);
        $import_batch_escaped = $conn->real_escape_string($import_batch);

        $sql = "INSERT INTO temp_warehouse 
                (product_name, original_price, suggested_brand, suggested_type, status, import_batch, raw_data)
                VALUES 
                ('$product_name_escaped', $price_numeric, '$suggested_brand_escaped', '$suggested_type_escaped', '$status', '$import_batch_escaped', '" . $conn->real_escape_string(json_encode($row)) . "')";

        if ($conn->query($sql)) {
            $current_processed++;
        }
    }

    // إحصائيات التصنيف
    foreach ($analysis_results as $analysis) {
        $brand_key = $analysis['brand'] ? ucfirst($analysis['brand']) : 'Unclassified';
        if (!isset($classification_stats[$brand_key])) {
            $classification_stats[$brand_key] = 0;
        }
        $classification_stats[$brand_key]++;
    }
    
    $processed_count += $current_processed;
}

// إصلاح الإحصائيات النهائية
$total_processed = 0;
$total_classified = 0;

foreach ($classification_stats as $brand => $count) {
    $total_processed += $count;
    if ($brand !== 'Unclassified') {
        $total_classified += $count;
    }
}

// تحديث الإحصائيات العامة
$processed_count = $total_processed;
$classified_count = $total_classified;

// عرض النتائج
?>

<div class="upload-stats">
    <div class="row">
        <div class="col-md-3">
            <div class="stat-item">
                <div class="stat-number text-primary"><?php echo $processed_count ?></div>
                <div class="stat-label">منتج تم معالجته</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-item">
                <div class="stat-number text-success"><?php echo $classified_count ?></div>
                <div class="stat-label">منتج تم تصنيفه</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-item">
                <div class="stat-number text-warning"><?php echo $processed_count - $classified_count ?></div>
                <div class="stat-label">منتج غير مصنف</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-item">
                <div class="stat-number text-danger"><?php echo count($errors) ?></div>
                <div class="stat-label">خطأ</div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($classification_stats)): ?>
<div class="card">
    <div class="card-header">
        <h5>إحصائيات التصنيف</h5>
    </div>
    <div class="card-body">
        <div class="classification-preview">
            <?php foreach ($classification_stats as $brand => $count): ?>
            <div class="classification-item mb-2">
                <strong><?php echo htmlspecialchars($brand) ?></strong>
                <span class="badge badge-secondary mr-1"><?php echo $count ?> منتج</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-warning">
    <h6>تحذيرات:</h6>
    <ul class="mb-0">
        <?php foreach (array_slice($errors, 0, 10) as $error): ?>
        <li><?php echo $error ?></li>
        <?php endforeach; ?>
        <?php if (count($errors) > 10): ?>
        <li>... و <?php echo count($errors) - 10 ?> خطأ آخر</li>
        <?php endif; ?>
    </ul>
</div>
<?php endif; ?>

<div class="alert alert-success">
    <h5><i class="icon fas fa-check"></i> تم الانتهاء!</h5>
    تم رفع وتصنيف <?php echo $processed_count ?> منتج بنجاح. يمكنك الآن مراجعة المنتجات في صفحة إدارة المخزن.
</div>

<div class="text-center mt-3">
    <a href="index.php?page=warehouse" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> الذهاب لإدارة المخزن
    </a>
</div>
