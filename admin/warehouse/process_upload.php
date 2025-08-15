<?php
require_once('../../config.php');

// تعيين الترميز الصحيح للعربية
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['csv_file'])) {
    echo '<div class="alert alert-danger">لم يتم رفع أي ملف!</div>';
    exit;
}

$file = $_FILES['csv_file'];
$import_batch = $_POST['import_batch'] ?? 'batch_' . date('Y_m_d_H_i_s');
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
//  البداية: الحل الجديد والمحسن باستخدام قارئ CSV
// ==================================================================

require_once('csv_reader_arabic.php');

echo '<div class="alert alert-info">
        <i class="fas fa-file-csv"></i> 
        جاري قراءة ملف CSV باستخدام قارئ CSV المحسن...
      </div>';

// تشخيص الملف قبل القراءة
$file_info = diagnose_excel_file($file['tmp_name']);
echo '<div class="alert alert-info">
        <h5><i class="icon fas fa-info-circle"></i> معلومات الملف:</h5>
        <ul>
            <li>نوع الملف: ' . ($file_info['file_type'] ?? $file_extension) . '</li>
            <li>حجم الملف: ' . number_format($file_info['file_size']) . ' بايت</li>
            <li>الترميز المكتشف: ' . implode(', ', $file_info['detected_encodings'] ?? ['غير معروف']) . '</li>
            <li>يحتوي على نصوص عربية: ' . ($file_info['has_arabic'] ? 'نعم' : 'لا') . '</li>
        </ul>';

// عرض عينة من النصوص والأرقام المستخرجة
if (!empty($file_info['sample_texts']) || !empty($file_info['sample_numbers'])) {
    echo '<h6>عينة من البيانات المستخرجة:</h6>
          <ul>';
    
    if (!empty($file_info['sample_texts'])) {
        echo '<li>نصوص: ' . implode(', ', $file_info['sample_texts']) . '</li>';
    }
    
    if (!empty($file_info['sample_numbers'])) {
        echo '<li>أرقام: ' . implode(', ', $file_info['sample_numbers']) . '</li>';
    }
    
    echo '</ul>';
}

echo '</div>';

// محاولة قراءة الملف باستخدام قارئ CSV المحسن
$csv_data = read_csv_arabic_enhanced($file['tmp_name']);

if (empty($csv_data)) {
    echo '<div class="alert alert-danger">
            <h5><i class="icon fas fa-times"></i> فشل في قراءة الملف!</h5>
            لم يتم العثور على أي بيانات. يرجى التأكد من أن الملف غير فارغ.
          </div>';
    exit;
}

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
        // تحويل الترميز إذا لزم الأمر
        if (!empty($cell_value) && !mb_check_encoding($cell_value, 'UTF-8')) {
            $encodings = ['Windows-1256', 'ISO-8859-6', 'CP1256'];
            foreach ($encodings as $enc) {
                $converted = @iconv($enc, 'UTF-8//IGNORE', $cell_value);
                if ($converted && mb_check_encoding($converted, 'UTF-8')) {
                    $cell_value = $converted;
                    break;
                }
            }
        }
        echo '<td>' . htmlspecialchars($cell_value) . '</td>';
    }
    echo '</tr>';
}

echo '      </tbody>
            </table>
        </div>
      </div>';
      
// تنظيف البيانات وتحويل الترميز
foreach ($csv_data as &$row) {
    foreach ($row as &$cell) {
        if (!empty($cell) && !mb_check_encoding($cell, 'UTF-8')) {
            $encodings = ['Windows-1256', 'ISO-8859-6', 'CP1256'];
            foreach ($encodings as $enc) {
                $converted = @iconv($enc, 'UTF-8//IGNORE', $cell);
                if ($converted && mb_check_encoding($converted, 'UTF-8')) {
                    $cell = $converted;
                    break;
                }
            }
        }
    }
}

// تحويل البيانات للتنسيق المطلوب وتطبيق start_row
$data = [];
foreach ($csv_data as $index => $row) {
    // تحويل إلى مصفوفة عددية للحفاظ على التناسق
    $numeric_row = array_values($row);
    if ($index + 1 >= $start_row) {
        $data[] = $numeric_row;
    }
}

// ==================================================================
//  النهاية: الحل الجديد
// ==================================================================

// دوال التصنيف الذكي - تعتمد على التصنيفات الأساسية
function classifyProduct($product_name, $conn) {
    $name = strtolower($product_name);

    // الحصول على التصنيفات الأساسية من قاعدة البيانات
    $categories = [];
    $sub_categories = [];

    $cat_qry = $conn->query("SELECT * FROM categories WHERE status = 1");
    while($cat = $cat_qry->fetch_assoc()) {
        $categories[$cat['id']] = $cat['category'];
    }

    $sub_cat_qry = $conn->query("SELECT * FROM sub_categories WHERE status = 1");
    while($sub_cat = $sub_cat_qry->fetch_assoc()) {
        $sub_categories[$sub_cat['id']] = [
            'name' => $sub_cat['sub_category'],
            'parent_id' => $sub_cat['parent_id']
        ];
    }

    // تصنيف العلامات التجارية (للهواتف وقطع الغيار)
    $brands = [
        'iPhone' => ['iphone', 'apple', 'ios', 'ايفون', 'آيفون', 'أيفون', 'ابل'],
        'Samsung' => ['samsung', 'galaxy', 'note', 'سامسونج', 'جالاكسي'],
        'Huawei' => ['huawei', 'honor', 'mate', 'p30', 'p40', 'هواوي', 'هونر'],
        'Xiaomi' => ['xiaomi', 'redmi', 'mi', 'شاومي', 'ريدمي'],
        'Oppo' => ['oppo', 'find', 'reno', 'أوبو', 'اوبو'],
        'Vivo' => ['vivo', 'nex', 'فيفو'],
        'OnePlus' => ['oneplus', 'one plus', 'ون بلس'],
        'LG' => ['lg'],
        'Sony' => ['sony', 'xperia', 'سوني'],
        'Tools' => ['tool', 'screwdriver', 'repair', 'kit', 'set', 'أدوات', 'ادوات', 'مفك']
    ];

    // تصنيف أنواع المنتجات حسب النظام الأساسي
    $product_types = [
        // إذا كان النظام للحيوانات الأليفة
        'Food' => ['food', 'eat', 'meal', 'nutrition', 'feed'],
        'Accessories' => ['accessory', 'accessories', 'toy', 'collar', 'leash', 'bed'],

        // إذا كان للهواتف (يمكن إضافة فئات جديدة)
        'Phone Parts' => ['screen', 'display', 'lcd', 'battery', 'charger', 'case', 'cover', 'glass', 'back'],
        'Tools' => ['tool', 'screwdriver', 'repair', 'kit', 'opener']
    ];

    $suggested_brand = null;
    $suggested_category_id = null;
    $suggested_sub_category_id = null;

    // البحث عن العلامة التجارية
    foreach ($brands as $brand => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($name, $keyword) !== false) {
                $suggested_brand = $brand;
                break 2;
            }
        }
    }

    // البحث عن الفئة المناسبة
    foreach ($product_types as $type => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($name, $keyword) !== false) {
                // البحث عن الفئة في قاعدة البيانات
                foreach ($categories as $cat_id => $cat_name) {
                    if (stripos($cat_name, $type) !== false ||
                        ($type == 'Phone Parts' && stripos($cat_name, 'Accessories') !== false)) {
                        $suggested_category_id = $cat_id;

                        // البحث عن الفئة الفرعية المناسبة
                        foreach ($sub_categories as $sub_id => $sub_data) {
                            if ($sub_data['parent_id'] == $cat_id) {
                                $suggested_sub_category_id = $sub_id;
                                break;
                            }
                        }
                        break 2;
                    }
                }
                break;
            }
        }
    }

    // إذا لم نجد تصنيف محدد، نستخدم Accessories كافتراضي
    if (!$suggested_category_id) {
        foreach ($categories as $cat_id => $cat_name) {
            if (stripos($cat_name, 'Accessories') !== false) {
                $suggested_category_id = $cat_id;
                // أول فئة فرعية متاحة
                foreach ($sub_categories as $sub_id => $sub_data) {
                    if ($sub_data['parent_id'] == $cat_id) {
                        $suggested_sub_category_id = $sub_id;
                        break;
                    }
                }
                break;
            }
        }
    }

    return [
        'brand' => $suggested_brand,
        'category_id' => $suggested_category_id,
        'sub_category_id' => $suggested_sub_category_id,
        'category_name' => $suggested_category_id ? $categories[$suggested_category_id] : null,
        'sub_category_name' => $suggested_sub_category_id ? $sub_categories[$suggested_sub_category_id]['name'] : null
    ];
}

// معالجة البيانات وحفظها
$processed_count = 0;
$classified_count = 0;
$errors = [];
$classification_stats = [];

foreach ($data as $index => $row) {
    // تنظيف البيانات من المسافات والرموز الغريبة مع الحفاظ على العربية
    $row = array_map('trim', $row);
    $row = array_map(function($item) {
        // إزالة الرموز الغريبة لكن نحافظ على العربية والإنجليزية والأرقام
        $item = preg_replace('/[^\w\s\-\.\u0600-\u06FF\u0660-\u0669]/u', '', $item);
        // إزالة المسافات الإضافية
        $item = preg_replace('/\s+/', ' ', $item);
        return trim($item);
    }, $row);

    // مرونة في عدد الأعمدة - نستخدم ما هو متاح
    $available_columns = count($row);
    $actual_name_column = min($name_column, $available_columns - 1);
    $actual_price_column = min($price_column, $available_columns - 1);

    // إذا كان لدينا عمود واحد فقط، نعتبره اسم المنتج والسعر = 0
    if ($available_columns == 1) {
        $product_name = trim($row[0] ?? '');
        $price = 0;
    }
    // إذا كان لدينا عمودين، الأول اسم والثاني سعر
    else if ($available_columns == 2) {
        $product_name = trim($row[0] ?? '');
        $price = floatval(str_replace(',', '', $row[1] ?? 0));
    }
    // إذا كان لدينا أكثر من عمودين، نستخدم الأعمدة المحددة
    else {
        $product_name = trim($row[$actual_name_column] ?? '');
        $price = floatval(str_replace(',', '', $row[$actual_price_column] ?? 0));
    }

    // التحقق من صحة اسم المنتج
    if (empty($product_name) || strlen($product_name) < 2 || is_numeric($product_name)) {
        $errors[] = "السطر " . ($index + $start_row) . ": اسم المنتج غير صحيح ('$product_name')";
        continue;
    }

    // السماح بسعر = 0 (يمكن تعديله لاحقاً)
    if ($price < 0) {
        $errors[] = "السطر " . ($index + $start_row) . ": السعر سالب ('$price')";
        continue;
    }
    
    // التصنيف التلقائي
    $classification = classifyProduct($product_name, $conn);

    // حفظ في قاعدة البيانات
    $product_name_escaped = $conn->real_escape_string($product_name);
    $suggested_brand = $classification['brand'] ? $conn->real_escape_string($classification['brand']) : 'NULL';
    $category_id = $classification['category_id'] ? $classification['category_id'] : 'NULL';
    $sub_category_id = $classification['sub_category_id'] ? $classification['sub_category_id'] : 'NULL';
    $import_batch_escaped = $conn->real_escape_string($import_batch);

    $sql = "INSERT INTO temp_warehouse
            (product_name, original_price, suggested_brand, category_id, sub_category_id, status, import_batch, raw_data)
            VALUES
            ('$product_name_escaped', $price, " .
            ($suggested_brand !== 'NULL' ? "'$suggested_brand'" : 'NULL') . ", " .
            ($category_id !== 'NULL' ? $category_id : 'NULL') . ", " .
            ($sub_category_id !== 'NULL' ? $sub_category_id : 'NULL') . ", " .
            "'unclassified', '$import_batch_escaped', '" . $conn->real_escape_string(json_encode($row)) . "')";
    
    if ($conn->query($sql)) {
        $processed_count++;

        if ($classification['brand'] || $classification['category_id']) {
            $classified_count++;
        }

        // إحصائيات التصنيف
        $brand_key = $classification['brand'] ?: 'Unclassified';
        $category_key = $classification['category_name'] ?: 'Unclassified';

        if (!isset($classification_stats[$brand_key])) {
            $classification_stats[$brand_key] = [];
        }
        if (!isset($classification_stats[$brand_key][$category_key])) {
            $classification_stats[$brand_key][$category_key] = 0;
        }
        $classification_stats[$brand_key][$category_key]++;

    } else {
        $errors[] = "السطر " . ($index + $start_row) . ": خطأ في قاعدة البيانات - " . $conn->error;
    }
}

// تحديث إحصائيات المخزن
foreach ($classification_stats as $brand => $categories) {
    foreach ($categories as $category => $count) {
        if ($brand !== 'Unclassified' && $category !== 'Unclassified') {
            $brand_escaped = $conn->real_escape_string($brand);
            $category_escaped = $conn->real_escape_string($category);

            $update_stats = "INSERT INTO warehouse_stats (brand, product_type, count)
                           VALUES ('$brand_escaped', '$category_escaped', $count)
                           ON DUPLICATE KEY UPDATE count = count + $count";
            $conn->query($update_stats);
        }
    }
}

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
            <?php foreach ($classification_stats as $brand => $types): ?>
                <div class="classification-item">
                    <strong><?php echo $brand ?></strong>
                    <div class="ml-3">
                        <?php foreach ($types as $type => $count): ?>
                            <span class="badge badge-secondary mr-1"><?php echo $type ?>: <?php echo $count ?></span>
                        <?php endforeach; ?>
                    </div>
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
