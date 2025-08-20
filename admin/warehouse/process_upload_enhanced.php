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

require_once('csv_reader_advanced.php'); // Keep for utility functions like read_csv_advanced
require_once('../../enhanced_classification.php'); // The new classification class

echo '<div class="alert alert-info">
        <i class="fas fa-file-csv"></i> 
        جاري قراءة ملف CSV باستخدام القارئ المطور...
      </div>';

// Use a robust CSV reading function
$csvReadResult = read_csv_advanced($file['tmp_name']);

if ($csvReadResult['status'] === 'error') {
    echo '<div class="alert alert-danger">
            <h5><i class="icon fas fa-times"></i> فشل في قراءة الملف!</h5>
            ' . htmlspecialchars($csvReadResult['message']) . '
          </div>';
    exit;
}

$csv_data = $csvReadResult['data'];
echo '<div class="alert alert-success">
        <i class="fas fa-check-circle"></i> 
        تم قراءة الملف بنجاح! تم العثور على ' . count($csv_data) . ' سطر.
      </div>';

// Skip data cleaning and row filtering here as it will be done in the loop

// معالجة البيانات وحفظها باستخدام النظام المطور
$classifier = new EnhancedClassification($conn);
$processed_count = 0;
$classified_count = 0;
$errors = [];
$classification_stats = [];

echo '<div class="alert alert-info">
        <h5><i class="icon fas fa-cogs"></i> جاري معالجة البيانات باستخدام نظام التصنيف المطور...</h5>
        <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: 0%" id="progress-bar"></div>
        </div>
      </div>';

$total_rows = count($csv_data);
foreach ($csv_data as $index => $row) {
    if ($index + 1 < $start_row) {
        continue;
    }

    // تحديث شريط التقدم
    $progress = round(($index + 1) / $total_rows * 100);
    echo "<script>document.getElementById('progress-bar').style.width = '{$progress}%';</script>";
    
    if (count($row) <= max($name_column, $price_column)) {
        $errors[] = "السطر " . ($index + 1) . ": بيانات غير كافية";
        continue;
    }

    $product_name = isset($row[$name_column]) ? trim($row[$name_column]) : '';
    $price_text = isset($row[$price_column]) ? trim($row[$price_column]) : '';

    if (empty($product_name)) {
        $errors[] = "السطر " . ($index + 1) . ": اسم المنتج فارغ";
        continue;
    }

    $price = preg_replace('/[^\d.,]/', '', $price_text);
    $price = str_replace(',', '.', $price);
    $price = floatval($price);

    if ($price <= 0) {
        $errors[] = "السطر " . ($index + 1) . ": السعر غير صحيح ($price_text)";
        continue;
    }

    // التصنيف باستخدام الكلاس المطور
    $classification = $classifier->classifyProduct($product_name);

    $product_name_escaped = $conn->real_escape_string($product_name);
    $category_id = $classification['category_id'] ? $classification['category_id'] : 'NULL';
    $status = $category_id !== 'NULL' ? 'classified' : 'unclassified';
    $import_batch_escaped = $conn->real_escape_string($import_batch);
    $confidence = round($classification['confidence'] * 100, 1);

    // The temp_warehouse table seems to have an older structure.
    // We will insert category_id, and leave sub_category_id as NULL.
    $sql = "INSERT INTO temp_warehouse
            (product_name, original_price, category_id, sub_category_id, status, import_batch, raw_data, confidence_score)
            VALUES
            ('{$product_name_escaped}', '{$price}', {$category_id}, NULL, '{$status}', '{$import_batch_escaped}', '" . $conn->real_escape_string(json_encode($row)) . "', '{$confidence}')
            ON DUPLICATE KEY UPDATE
            original_price = VALUES(original_price),
            category_id = VALUES(category_id),
            sub_category_id = VALUES(sub_category_id),
            status = VALUES(status),
            confidence_score = VALUES(confidence_score),
            raw_data = VALUES(raw_data)";

    if ($conn->query($sql)) {
        $processed_count++;

        if ($status === 'classified') {
            $classified_count++;
        }

        // For stats, we need to map category_id back to a name.
        // This part of the script was broken anyway because it was using a different stats logic.
        // I will simplify it to use the brand name from the classification result.
        $brand_key = !empty($classification['brand']) ? $classification['brand'] : 'Unclassified';
        if (!isset($classification_stats[$brand_key])) {
            $classification_stats[$brand_key] = 0;
        }
        $classification_stats[$brand_key]++;

    } else {
        $errors[] = "السطر " . ($index + 1) . ": خطأ في قاعدة البيانات - " . $conn->error;
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