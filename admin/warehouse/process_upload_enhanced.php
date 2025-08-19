<?php
require_once('../../config.php');
require_once('../../enhanced_classification.php');

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

require_once('csv_reader_arabic.php');

echo '<div class="alert alert-info">
        <i class="fas fa-file-csv"></i> 
        جار�� قراءة ملف CSV باستخدام قارئ CSV المحسن...
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

// م��اولة قراءة الملف باستخدام قارئ CSV المحسن
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

// معالجة البيانات وحفظها باستخدام النظام المحسن
$classifier = new EnhancedClassification($conn);
$processed_count = 0;
$classified_count = 0;
$errors = [];
$classification_stats = [];

// جلب كل الفئات والماركات مرة واحدة لتحسين الأداء
$categories_list = [];
$cat_qry = $conn->query("SELECT id, category FROM categories WHERE status = 1");
while($cat = $cat_qry->fetch_assoc()) {
    $categories_list[$cat['id']] = $cat['category'];
}

echo '<div class="alert alert-info">
        <h5><i class="icon fas fa-cogs"></i> جاري معالجة البيانات باستخدام النظام المحسن...</h5>
        <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: 0%" id="progress-bar"></div>
        </div>
      </div>';

$total_rows = count($data);
foreach ($data as $index => $row) {
    // تحديث شريط التقدم
    $progress = round(($index + 1) / $total_rows * 100);
    echo "<script>document.getElementById('progress-bar').style.width = '{$progress}%';</script>";
    
    // التحقق من وجود البيانات المطلوبة
    if (count($row) <= max($name_column, $price_column)) {
        $errors[] = "السطر " . ($index + $start_row) . ": بيانات غير كافية";
        continue;
    }

    // استخراج اسم المنتج والسعر
    $product_name = isset($row[$name_column]) ? trim($row[$name_column]) : '';
    $price = isset($row[$price_column]) ? trim($row[$price_column]) : '';

    // تنظيف اسم المنتج
    if (empty($product_name)) {
        $errors[] = "السطر " . ($index + $start_row) . ": اسم المنتج فارغ";
        continue;
    }

    // تنظيف السعر وتحويله لرقم
    $price = preg_replace('/[^\d.,]/', '', $price);
    $price = str_replace(',', '.', $price);
    $price = floatval($price);

    if ($price <= 0) {
        $errors[] = "السطر " . ($index + $start_row) . ": السعر غير صحيح ($price)";
        continue;
    }

    // التصنيف التلقائي باستخدام النظام المحسن
    $classification = $classifier->classifyProduct($product_name);

    // حفظ في قاعدة البيانات
    $product_name_escaped = $conn->real_escape_string($product_name);
    $category_id = $classification['category_id'] ? $classification['category_id'] : 'NULL';
    $sub_category_id = $classification['sub_category_id'] ? $classification['sub_category_id'] : 'NULL';
    $import_batch_escaped = $conn->real_escape_string($import_batch);
    $confidence = round($classification['confidence'] * 100, 1);

    $sql = "INSERT INTO temp_warehouse
            (product_name, original_price, category_id, sub_category_id, status, import_batch, raw_data, confidence_score)
            VALUES
            ('$product_name_escaped', $price, " .
            ($category_id !== 'NULL' ? $category_id : 'NULL') . ", " .
            ($sub_category_id !== 'NULL' ? $sub_category_id : 'NULL') . ", " .
            "'classified', '$import_batch_escaped', '" . $conn->real_escape_string(json_encode($row)) . "', $confidence)";
    
    if ($conn->query($sql)) {
        $processed_count++;

        if ($classification['category_id']) {
            $classified_count++;
        }

        // إحصائيات التصنيف
        $brand_key = $classification['category_id'] ? $categories_list[$classification['category_id']] : 'Unclassified';

        if (!isset($classification_stats[$brand_key])) {
            $classification_stats[$brand_key] = 0;
        }
        $classification_stats[$brand_key]++;

    } else {
        $errors[] = "السطر " . ($index + $start_row) . ": خطأ في قاعدة البيانات - " . $conn->error;
    }
}

// إضافة عمود confidence_score إذا لم يكن موجود
$check_column = $conn->query("SHOW COLUMNS FROM temp_warehouse LIKE 'confidence_score'");
if ($check_column->num_rows == 0) {
    $conn->query("ALTER TABLE temp_warehouse ADD COLUMN confidence_score DECIMAL(5,2) DEFAULT 0");
}

// تحديث إحصائيات المخزن (إذا كان الجدول موجود)
$check_table = $conn->query("SHOW TABLES LIKE 'warehouse_stats'");
if ($check_table && $check_table->num_rows > 0) {
    foreach ($classification_stats as $brand => $count) {
        if ($brand !== 'Unclassified') {
            $brand_escaped = $conn->real_escape_string($brand);

            $update_stats = "INSERT INTO warehouse_stats (brand, product_type, count)
                           VALUES ('$brand_escaped', 'devices', $count)
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
        <h5>📊 إحصائيات التصنيف المحسن</h5>
    </div>
    <div class="card-body">
        <div class="classification-preview">
            <?php foreach ($classification_stats as $brand => $count): ?>
                <div class="classification-item mb-2">
                    <strong><?php echo htmlspecialchars($brand) ?></strong>
                    <span class="badge badge-secondary mr-1"><?php echo $count ?> منتج</span>
                    <?php 
                    $percentage = $processed_count > 0 ? round(($count / $processed_count) * 100, 1) : 0;
                    echo "<span class='badge badge-info'>$percentage%</span>";
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-warning">
    <h6>⚠️ تحذيرات:</h6>
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
    <h5><i class="icon fas fa-check"></i> تم الانتهاء بنجاح!</h5>
    <p>تم رفع وتصنيف <?php echo $processed_count ?> منتج باستخدام النظام المحسن.</p>
    <p>معدل دقة التصنيف: <?php echo $processed_count > 0 ? round(($classified_count / $processed_count) * 100, 1) : 0 ?>%</p>
</div>

<div class="text-center mt-3">
    <a href="index.php?page=warehouse" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> الذهاب لإدارة المخزن
    </a>
    <a href="../../test_csv_upload.php" class="btn btn-info">
        <i class="fas fa-vial"></i> اختبار التصنيف
    </a>
</div>

<style>
.upload-stats {
    margin: 20px 0;
}
.stat-item {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin: 5px 0;
}
.stat-number {
    font-size: 2em;
    font-weight: bold;
}
.stat-label {
    font-size: 0.9em;
    color: #6c757d;
}
.classification-item {
    padding: 8px;
    background: #f8f9fa;
    border-radius: 5px;
    margin: 5px 0;
}
.progress {
    height: 25px;
    background-color: #e9ecef;
    border-radius: 15px;
}
.progress-bar {
    background: linear-gradient(90deg, #28a745, #20c997);
    border-radius: 15px;
    transition: width 0.3s ease;
}
</style>