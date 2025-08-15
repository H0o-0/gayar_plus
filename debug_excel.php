<?php
// تشخيص مفصل لملف Excel

echo "<h2>تشخيص ملف Excel</h2>";

if (!isset($_FILES['excel_file'])) {
    echo '<form method="post" enctype="multipart/form-data">
            <input type="file" name="excel_file" accept=".xlsx,.xls">
            <button type="submit">تحليل الملف</button>
          </form>';
    exit;
}

$file = $_FILES['excel_file'];
echo "<h3>معلومات الملف:</h3>";
echo "<p>الاسم: " . $file['name'] . "</p>";
echo "<p>الحجم: " . $file['size'] . " بايت</p>";
echo "<p>النوع: " . $file['type'] . "</p>";

$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

// قراءة الملف حسب النوع
$data = [];
if ($file_extension === 'xlsx') {
    require_once('admin/warehouse/excel_reader.php');
    $data = readExcelFile($file['tmp_name']);
    echo "<p>نوع الملف: Excel حديث (.xlsx)</p>";
} else if ($file_extension === 'xls') {
    require_once('admin/warehouse/xls_reader.php');
    $data = readXLSFile($file['tmp_name']);
    echo "<p>نوع الملف: Excel قديم (.xls)</p>";
} else {
    echo "<p style='color: red;'>نوع ملف غير مدعوم</p>";
    exit;
}

echo "<h3>نتائج القراءة:</h3>";
echo "<p>عدد الصفوف: " . count($data) . "</p>";

if (!empty($data)) {
    echo "<h3>أول 10 صفوف:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    
    // رأس الجدول
    echo "<tr>";
    $max_cols = 0;
    foreach ($data as $row) {
        $max_cols = max($max_cols, count($row));
    }
    
    for ($i = 0; $i < min($max_cols, 10); $i++) {
        $col_letter = chr(65 + $i);
        echo "<th>العمود $col_letter</th>";
    }
    echo "</tr>";
    
    // البيانات
    for ($row_idx = 0; $row_idx < min(10, count($data)); $row_idx++) {
        $row = $data[$row_idx];
        echo "<tr>";
        
        for ($col_idx = 0; $col_idx < min($max_cols, 10); $col_idx++) {
            $cell_value = htmlspecialchars($row[$col_idx] ?? '');
            $cell_info = '';
            
            if (empty($cell_value)) {
                $cell_info = '<em style="color: #999;">فارغ</em>';
            } else if (is_numeric($cell_value)) {
                $cell_info = '<strong style="color: #007bff;">' . $cell_value . '</strong> <small>(رقم)</small>';
            } else {
                $cell_info = '<span style="color: #28a745;">' . $cell_value . '</span> <small>(نص)</small>';
            }
            
            echo "<td>$cell_info</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    // تحليل البيانات
    echo "<h3>تحليل البيانات:</h3>";
    
    for ($col = 0; $col < min($max_cols, 5); $col++) {
        $col_letter = chr(65 + $col);
        $empty_count = 0;
        $numeric_count = 0;
        $text_count = 0;
        $samples = [];
        
        for ($row_idx = 0; $row_idx < min(20, count($data)); $row_idx++) {
            $cell_value = $data[$row_idx][$col] ?? '';
            
            if (empty($cell_value)) {
                $empty_count++;
            } else if (is_numeric($cell_value)) {
                $numeric_count++;
                if (count($samples) < 3) $samples[] = $cell_value;
            } else {
                $text_count++;
                if (count($samples) < 3) $samples[] = $cell_value;
            }
        }
        
        echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0;'>";
        echo "<h4>العمود $col_letter:</h4>";
        echo "<p>فارغ: $empty_count | أرقام: $numeric_count | نصوص: $text_count</p>";
        echo "<p>عينات: " . implode(', ', $samples) . "</p>";
        
        if ($text_count > $numeric_count && $text_count > 2) {
            echo "<p style='color: green;'><strong>مناسب لأسماء المنتجات</strong></p>";
        } else if ($numeric_count > $text_count && $numeric_count > 2) {
            echo "<p style='color: blue;'><strong>مناسب للأسعار</strong></p>";
        }
        echo "</div>";
    }
    
    // اقتراحات
    echo "<h3>الاقتراحات:</h3>";
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
    
    // البحث عن أفضل عمود للأسماء
    $best_name_col = -1;
    $best_price_col = -1;
    
    for ($col = 0; $col < min($max_cols, 5); $col++) {
        $text_count = 0;
        $numeric_count = 0;
        
        for ($row_idx = 1; $row_idx < min(10, count($data)); $row_idx++) {
            $cell_value = $data[$row_idx][$col] ?? '';
            if (!empty($cell_value)) {
                if (is_numeric($cell_value)) {
                    $numeric_count++;
                } else {
                    $text_count++;
                }
            }
        }
        
        if ($text_count > 3 && $best_name_col == -1) {
            $best_name_col = $col;
        }
        if ($numeric_count > 3 && $best_price_col == -1) {
            $best_price_col = $col;
        }
    }
    
    if ($best_name_col >= 0) {
        $name_letter = chr(65 + $best_name_col);
        echo "<p><strong>اقتراح عمود اسم المنتج:</strong> العمود $name_letter</p>";
    }
    
    if ($best_price_col >= 0) {
        $price_letter = chr(65 + $best_price_col);
        echo "<p><strong>اقتراح عمود السعر:</strong> العمود $price_letter</p>";
    }
    
    echo "</div>";
    
} else {
    echo "<p style='color: red;'>فشل في قراءة البيانات من الملف</p>";
}

echo "<p><a href='admin/index.php?page=warehouse/upload'>العودة لصفحة الرفع</a></p>";
?>
