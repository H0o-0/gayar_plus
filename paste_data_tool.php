<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أداة النسخ واللصق المباشر</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .step {
            background: #e7f3ff;
            padding: 15px;
            margin: 15px 0;
            border-radius: 10px;
            border-left: 4px solid #007bff;
        }
        textarea {
            width: 100%;
            min-height: 300px;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-family: monospace;
            font-size: 14px;
            resize: vertical;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #1e7e34;
        }
        .preview-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .preview-table th, .preview-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        .preview-table th {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 أداة النسخ واللصق المباشر</h1>
        
        <div class="step">
            <h3>📋 الخطوات:</h3>
            <ol>
                <li><strong>افتح ملف Excel</strong></li>
                <li><strong>حدد البيانات</strong> (اسم المنتج والسعر فقط)</li>
                <li><strong>انسخها</strong> (Ctrl+C)</li>
                <li><strong>الصقها</strong> في المربع أدناه</li>
                <li><strong>اضغط "تحويل"</strong></li>
            </ol>
        </div>

        <form method="post">
            <h3>الصق البيانات هنا:</h3>
            <textarea name="pasted_data" placeholder="الصق البيانات من Excel هنا...
مثال:
iPhone 13 Screen    250
Samsung Case        25
LCD Display         180"><?php echo htmlspecialchars($_POST['pasted_data'] ?? '') ?></textarea>
            
            <div>
                <button type="submit" class="btn">🔄 تحويل البيانات</button>
                <button type="button" class="btn" onclick="clearData()">🗑️ مسح</button>
            </div>
        </form>

        <?php
        if (isset($_POST['pasted_data']) && !empty($_POST['pasted_data'])) {
            $pasted_data = $_POST['pasted_data'];
            
            // تنظيف البيانات
            $lines = explode("\n", $pasted_data);
            $processed_data = [];
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                // تقسيم البيانات (Tab أو مسافات متعددة)
                $parts = preg_split('/\t+|\s{2,}/', $line, 2);
                
                if (count($parts) >= 2) {
                    $name = trim($parts[0]);
                    $price = trim($parts[1]);
                    
                    // تنظيف السعر من الرموز
                    $price = preg_replace('/[^\d\.]/', '', $price);
                    
                    if (!empty($name) && !empty($price)) {
                        $processed_data[] = [$name, $price];
                    }
                } else if (count($parts) == 1) {
                    // إذا كان هناك عمود واحد فقط
                    $name = trim($parts[0]);
                    if (!empty($name) && !is_numeric($name)) {
                        $processed_data[] = [$name, '0'];
                    }
                }
            }
            
            if (!empty($processed_data)) {
                echo "<h3>✅ تم تحويل البيانات بنجاح!</h3>";
                echo "<p>عدد المنتجات: " . count($processed_data) . "</p>";
                
                // إنشاء ملف CSV
                $csv_filename = 'pasted_data_' . time() . '.csv';
                $csv_content = "Product Name,Price\n";
                
                foreach ($processed_data as $row) {
                    $name = str_replace('"', '""', $row[0]);
                    $price = $row[1];
                    $csv_content .= "\"$name\",$price\n";
                }
                
                file_put_contents($csv_filename, $csv_content);
                
                echo "<p><a href='$csv_filename' download class='btn btn-success'>📥 تحميل ملف CSV</a></p>";
                
                // معاينة البيانات
                echo "<h3>معاينة البيانات:</h3>";
                echo "<table class='preview-table'>";
                echo "<tr><th>اسم المنتج</th><th>السعر</th></tr>";
                
                $preview_count = min(10, count($processed_data));
                for ($i = 0; $i < $preview_count; $i++) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($processed_data[$i][0]) . "</td>";
                    echo "<td>" . htmlspecialchars($processed_data[$i][1]) . "</td>";
                    echo "</tr>";
                }
                
                if (count($processed_data) > 10) {
                    echo "<tr><td colspan='2' style='text-align: center; color: #666;'>... و " . (count($processed_data) - 10) . " منتج آخر</td></tr>";
                }
                
                echo "</table>";
                
                echo "<div class='step'>";
                echo "<h3>🎯 الخطوة التالية:</h3>";
                echo "<ol>";
                echo "<li>حمل ملف CSV من الرابط أعلاه</li>";
                echo "<li><a href='admin/index.php?page=warehouse/upload'>اذهب لصفحة رفع المخزن</a></li>";
                echo "<li>ارفع ملف CSV</li>";
                echo "</ol>";
                echo "</div>";
                
            } else {
                echo "<div style='color: red; padding: 20px; background: #ffe6e6; border-radius: 10px;'>";
                echo "<h3>❌ لم يتم العثور على بيانات صالحة</h3>";
                echo "<p>تأكد من:</p>";
                echo "<ul>";
                echo "<li>نسخ البيانات من Excel بشكل صحيح</li>";
                echo "<li>وجود عمودين على الأقل (اسم المنتج والسعر)</li>";
                echo "<li>فصل الأعمدة بـ Tab أو مسافات</li>";
                echo "</ul>";
                echo "</div>";
            }
        }
        ?>

        <div class="step">
            <h3>💡 نصائح مهمة:</h3>
            <ul>
                <li><strong>في Excel:</strong> حدد البيانات واضغط Ctrl+C</li>
                <li><strong>هنا:</strong> اضغط Ctrl+V في المربع</li>
                <li><strong>تأكد:</strong> من وجود مسافة أو Tab بين اسم المنتج والسعر</li>
                <li><strong>لا تقلق:</strong> من الأسماء الطويلة، ستبقى كاملة</li>
            </ul>
        </div>

        <div style="margin-top: 30px; text-align: center;">
            <a href="admin/index.php?page=warehouse/upload" class="btn">🏠 العودة لصفحة الرفع</a>
        </div>
    </div>

    <script>
        function clearData() {
            document.querySelector('textarea[name="pasted_data"]').value = '';
        }
    </script>
</body>
</html>
