<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('initialize.php');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فحص قاعدة البيانات - متجر ملحقات الهواتف</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .table-card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .header-blue { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; }
        .success-bg { background: #d4edda; color: #155724; }
        .error-bg { background: #f8d7da; color: #721c24; }
        .warning-bg { background: #fff3cd; color: #856404; }
        .info-bg { background: #d1ecf1; color: #0c5460; }
        .pre-scrollable { max-height: 300px; overflow-y: auto; }
    </style>
</head>
<body>

<div class="container-fluid mt-4">
    <h1 class="text-center mb-4">
        <i class="fas fa-database"></i> فحص قاعدة البيانات - متجر ملحقات الهواتف
    </h1>

    <!-- أدوات سريعة -->
    <div class="card table-card">
        <div class="card-header header-blue">
            <h3><i class="fas fa-tools"></i> أدوات سريعة</h3>
        </div>
        <div class="card-body text-center">
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="setup_new_system.php" class="btn btn-primary me-md-2">
                    <i class="fas fa-cog"></i> إعداد النظام الجديد
                </a>
                <a href="database/apply_new_structure.php" class="btn btn-success me-md-2">
                    <i class="fas fa-database"></i> تطبيق الهيكل
                </a>
                <a href="database/migrate_data.php" class="btn btn-warning me-md-2">
                    <i class="fas fa-exchange-alt"></i> ترحيل البيانات
                </a>
                <a href="admin/product/manage_product_new.php" class="btn btn-info me-md-2">
                    <i class="fas fa-plus"></i> إضافة منتج جديد
                </a>
            </div>
        </div>
    </div>

    <!-- معلومات عامة عن قاعدة البيانات -->
    <div class="card table-card">
        <div class="card-header header-blue">
            <h3><i class="fas fa-info-circle"></i> معلومات عامة عن قاعدة البيانات</h3>
        </div>
        <div class="card-body">
            <?php
            try {
                // معلومات الخادم
                $server_info = $conn->server_info;
                $client_info = $conn->client_info;
                $host_info = $conn->host_info;
                
                echo "<div class='alert info-bg'>";
                echo "<h5>معلومات الاتصال:</h5>";
                echo "<ul>";
                echo "<li><strong>إصدار الخادم:</strong> $server_info</li>";
                echo "<li><strong>إصدار العميل:</strong> $client_info</li>";
                echo "<li><strong>معلومات المضيف:</strong> $host_info</li>";
                echo "</ul>";
                echo "</div>";
                
                // قائمة الجداول
                $tables_result = $conn->query("SHOW TABLES");
                $tables = [];
                while ($row = $tables_result->fetch_array()) {
                    $tables[] = $row[0];
                }
                
                echo "<h5>الجداول الموجودة (" . count($tables) . "):</h5>";
                echo "<div class='row'>";
                foreach ($tables as $table) {
                    $icon = 'fas fa-table text-secondary';
                    if (in_array($table, ['brands', 'series', 'models'])) {
                        $icon = 'fas fa-star text-warning';
                    } elseif (in_array($table, ['categories', 'sub_categories'])) {
                        $icon = 'fas fa-folder text-primary';
                    } elseif ($table == 'products') {
                        $icon = 'fas fa-box text-success';
                    } elseif ($table == 'migration_mapping') {
                        $icon = 'fas fa-exchange-alt text-info';
                    }
                    echo "<div class='col-md-3 mb-2'>";
                    echo "<span class='badge bg-light text-dark'><i class='$icon'></i> $table</span>";
                    echo "</div>";
                }
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='alert error-bg'><strong>خطأ:</strong> " . $e->getMessage() . "</div>";
            }
            ?>
        </div>
    </div>

    <!-- النظام القديم -->
    <div class="card table-card">
        <div class="card-header header-blue">
            <h3><i class="fas fa-folder"></i> النظام القديم (الفئات)</h3>
        </div>
        <div class="card-body">
            <?php
            try {
                if (in_array('categories', $tables) && in_array('sub_categories', $tables)) {
                    $old_categories = $conn->query("SELECT COUNT(*) as count FROM categories WHERE status = 1")->fetch_assoc()['count'];
                    $old_sub_categories = $conn->query("SELECT COUNT(*) as count FROM sub_categories WHERE status = 1")->fetch_assoc()['count'];
                    $old_products = $conn->query("SELECT COUNT(*) as count FROM products WHERE category_id IS NOT NULL AND sub_category_id IS NOT NULL")->fetch_assoc()['count'];
                    
                    echo "<div class='row text-center'>";
                    echo "<div class='col-md-4'><div class='card'><div class='card-body'>";
                    echo "<h4 class='text-primary'>$old_categories</h4><p>الفئات الرئيسية</p>";
                    echo "</div></div></div>";
                    echo "<div class='col-md-4'><div class='card'><div class='card-body'>";
                    echo "<h4 class='text-info'>$old_sub_categories</h4><p>الفئات الفرعية</p>";
                    echo "</div></div></div>";
                    echo "<div class='col-md-4'><div class='card'><div class='card-body'>";
                    echo "<h4 class='text-success'>$old_products</h4><p>المنتجات المرتبطة</p>";
                    echo "</div></div></div>";
                    echo "</div>";
                    
                    // عرض بعض البيانات القديمة
                    echo "<h5 class='mt-4'>عينة من البيانات القديمة:</h5>";
                    $old_data_query = "SELECT c.category, sc.sub_category, COUNT(p.id) as products_count
                                       FROM categories c
                                       LEFT JOIN sub_categories sc ON c.id = sc.parent_id
                                       LEFT JOIN products p ON c.id = p.category_id AND sc.id = p.sub_category_id
                                       WHERE c.status = 1 AND sc.status = 1
                                       GROUP BY c.id, sc.id
                                       ORDER BY products_count DESC
                                       LIMIT 10";
                    
                    $old_result = $conn->query($old_data_query);
                    if ($old_result && $old_result->num_rows > 0) {
                        echo "<table class='table table-sm table-striped'>";
                        echo "<thead><tr><th>الفئة الرئيسية</th><th>الفئة الفرعية</th><th>عدد المنتجات</th></tr></thead>";
                        echo "<tbody>";
                        while ($row = $old_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['sub_category']) . "</td>";
                            echo "<td><span class='badge bg-primary'>" . $row['products_count'] . "</span></td>";
                            echo "</tr>";
                        }
                        echo "</tbody></table>";
                    }
                } else {
                    echo "<div class='alert warning-bg'>الجداول القديمة غير موجودة</div>";
                }
            } catch (Exception $e) {
                echo "<div class='alert error-bg'><strong>خطأ:</strong> " . $e->getMessage() . "</div>";
            }
            ?>
        </div>
    </div>

    <!-- النظام الجديد -->
    <div class="card table-card">
        <div class="card-header header-blue">
            <h3><i class="fas fa-star"></i> النظام الجديد (الشركات > الفئات > الموديلات)</h3>
        </div>
        <div class="card-body">
            <?php
            try {
                $new_tables_exist = in_array('brands', $tables) && in_array('series', $tables) && in_array('models', $tables);
                
                if ($new_tables_exist) {
                    $brands_count = $conn->query("SELECT COUNT(*) as count FROM brands WHERE status = 1")->fetch_assoc()['count'];
                    $series_count = $conn->query("SELECT COUNT(*) as count FROM series WHERE status = 1")->fetch_assoc()['count'];
                    $models_count = $conn->query("SELECT COUNT(*) as count FROM models WHERE status = 1")->fetch_assoc()['count'];
                    $products_with_model = $conn->query("SELECT COUNT(*) as count FROM products WHERE model_id IS NOT NULL")->fetch_assoc()['count'];
                    
                    echo "<div class='row text-center'>";
                    echo "<div class='col-md-3'><div class='card'><div class='card-body'>";
                    echo "<h4 class='text-warning'>$brands_count</h4><p>الشركات</p>";
                    echo "</div></div></div>";
                    echo "<div class='col-md-3'><div class='card'><div class='card-body'>";
                    echo "<h4 class='text-info'>$series_count</h4><p>الفئات</p>";
                    echo "</div></div></div>";
                    echo "<div class='col-md-3'><div class='card'><div class='card-body'>";
                    echo "<h4 class='text-success'>$models_count</h4><p>الموديلات</p>";
                    echo "</div></div></div>";
                    echo "<div class='col-md-3'><div class='card'><div class='card-body'>";
                    echo "<h4 class='text-primary'>$products_with_model</h4><p>المنتجات المرتبطة</p>";
                    echo "</div></div></div>";
                    echo "</div>";
                    
                    if ($brands_count > 0) {
                        echo "<h5 class='mt-4'>البيانات الجديدة (هيكل ثلاثي المستويات):</h5>";
                        $new_data_query = "SELECT b.name as brand_name, s.name as series_name, m.name as model_name, 
                                                  COUNT(p.id) as products_count
                                           FROM brands b
                                           LEFT JOIN series s ON b.id = s.brand_id
                                           LEFT JOIN models m ON s.id = m.series_id
                                           LEFT JOIN products p ON m.id = p.model_id
                                           WHERE b.status = 1 AND s.status = 1 AND m.status = 1
                                           GROUP BY b.id, s.id, m.id
                                           ORDER BY b.name, s.name, m.name
                                           LIMIT 15";
                        
                        $new_result = $conn->query($new_data_query);
                        if ($new_result && $new_result->num_rows > 0) {
                            echo "<div class='table-responsive'>";
                            echo "<table class='table table-sm table-striped'>";
                            echo "<thead><tr><th>الشركة</th><th>الفئة</th><th>الموديل</th><th>المنتجات</th></tr></thead>";
                            echo "<tbody>";
                            while ($row = $new_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><span class='badge bg-warning text-dark'>" . htmlspecialchars($row['brand_name']) . "</span></td>";
                                echo "<td>" . htmlspecialchars($row['series_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['model_name']) . "</td>";
                                echo "<td><span class='badge bg-primary'>" . $row['products_count'] . "</span></td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
                            echo "</div>";
                        }
                    }
                } else {
                    echo "<div class='alert error-bg'>الجداول الجديدة غير موجودة بعد</div>";
                    echo "<div class='alert info-bg'>";
                    echo "<p><i class='fas fa-info-circle'></i> يرجى تطبيق الهيكل الجديد أولاً من خلال الضغط على زر 'إعداد النظام الجديد' أعلاه</p>";
                    echo "</div>";
                }
            } catch (Exception $e) {
                echo "<div class='alert error-bg'><strong>خطأ:</strong> " . $e->getMessage() . "</div>";
            }
            ?>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
