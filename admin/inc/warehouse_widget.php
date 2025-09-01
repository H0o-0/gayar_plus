<?php
// كارد المخزن المؤقت للداش بورد - محسن مع شعارات الشركات

// التحقق من وجود جدول المخزن المؤقت
$table_exists = $conn->query("SHOW TABLES LIKE 'temp_warehouse'");
if($table_exists->num_rows == 0) {
    // إنشاء الجدول إذا لم يكن موجوداً
    $create_table = "CREATE TABLE IF NOT EXISTS `temp_warehouse` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `product_name` text NOT NULL,
        `original_price` decimal(10,2) DEFAULT NULL,
        `suggested_brand` varchar(100) DEFAULT NULL,
        `confirmed_brand` varchar(100) DEFAULT NULL,
        `suggested_type` varchar(100) DEFAULT NULL,
        `confirmed_type` varchar(100) DEFAULT NULL,
        `category_id` int(11) DEFAULT NULL,
        `sub_category_id` int(11) DEFAULT NULL,
        `status` enum('unclassified','classified','published') DEFAULT 'unclassified',
        `import_batch` varchar(50) DEFAULT NULL,
        `raw_data` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `idx_brand` (`suggested_brand`),
        KEY `idx_status` (`status`),
        KEY `idx_category` (`category_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->query($create_table);
}

// إجماليات دقيقة من الجدول كاملاً
$total_products = (int)$conn->query("SELECT COUNT(*) as c FROM temp_warehouse")->fetch_assoc()['c'];
$unclassified_count = (int)$conn->query("SELECT COUNT(*) as c FROM temp_warehouse WHERE status = 'unclassified'")->fetch_assoc()['c'];
$classified_count   = (int)$conn->query("SELECT COUNT(*) as c FROM temp_warehouse WHERE status = 'classified'")->fetch_assoc()['c'];
$published_count    = (int)$conn->query("SELECT COUNT(*) as c FROM temp_warehouse WHERE status = 'published'")->fetch_assoc()['c'];

// توزيع العلامات التجارية بحسب الحالة (يتجاهل القيم الفارغة)
$brand_rows = $conn->query("
    SELECT 
        suggested_brand as brand,
        status,
        COUNT(*) as count
    FROM temp_warehouse 
    WHERE suggested_brand IS NOT NULL AND suggested_brand <> ''
    GROUP BY suggested_brand, status
    ORDER BY count DESC
");

$stats_data = [];
if($brand_rows) {
    while($row = $brand_rows->fetch_assoc()) {
        $brand = $row['brand'];
        $count = (int)$row['count'];
        $status = $row['status'];
        
        if(!isset($stats_data[$brand])) {
            $stats_data[$brand] = ['total' => 0, 'unclassified' => 0, 'classified' => 0, 'published' => 0];
        }
        $stats_data[$brand]['total'] += $count;
        $stats_data[$brand][$status] = $count;
    }
}

// الحصول على آخر دفعة استيراد
$last_import = $conn->query("
    SELECT import_batch, COUNT(*) as count, MAX(created_at) as last_date 
    FROM temp_warehouse 
    WHERE import_batch IS NOT NULL 
    GROUP BY import_batch 
    ORDER BY last_date DESC 
    LIMIT 1
");

$last_import_data = $last_import ? $last_import->fetch_assoc() : null;

// دالة للحصول على شعار الشركة
function getBrandLogo($brand) {
    $logos = [
        'Apple' => 'Apple-Logo.webp',
        'Samsung' => 'sansung logo.jpg',
        'Huawei' => '-logo-huawei-.jpg',
        'Xiaomi' => 'Xiaomi_logo.png',
        'Oppo' => 'oppo-logo.png',
        'Vivo' => 'vivo-logo.png',
        'LG' => 'lg-logo-.png'
    ];
    
    return isset($logos[$brand]) ? $logos[$brand] : null;
}
?>

<div class="col-lg-6 col-md-12 mb-4">
    <div class="card warehouse-widget" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 15px; box-shadow: 0 8px 25px rgba(102,126,234,0.3);">
        <div class="card-header" style="background: transparent; border: none; padding: 1.5rem 1.5rem 0;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0" style="color: white; font-weight: 700;">
                    <i class="fas fa-warehouse me-2"></i>
                    مخزن البيانات المؤقت
                </h5>
                <span class="badge" style="background: rgba(255,255,255,0.2); color: white; font-size: 0.9rem;">
                    <?php echo $total_products ?> منتج
                </span>
            </div>
        </div>
        
        <div class="card-body" style="padding: 1rem 1.5rem 1.5rem;">
            <?php if(!empty($stats_data)): ?>
            
            <!-- إحصائيات سريعة -->
            <div class="row mb-3">
                <div class="col-4">
                    <div class="text-center">
                        <h4 class="mb-1" style="color: #ffd700;"><?php echo $total_products ?></h4>
                        <small style="color: rgba(255,255,255,0.8);">إجمالي</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-center">
                        <h4 class="mb-1" style="color: #ff6b6b;"><?php echo $unclassified_count ?></h4>
                        <small style="color: rgba(255,255,255,0.8);">غير مصنف</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-center">
                        <h4 class="mb-1" style="color: #4ecdc4;"><?php echo $published_count ?></h4>
                        <small style="color: rgba(255,255,255,0.8);">منشور</small>
                    </div>
                </div>
            </div>
            
            <!-- شارت دائري لتوزيع العلامات التجارية -->
            <div class="warehouse-chart mb-3">
                <h6 style="color: rgba(255,255,255,0.9); margin-bottom: 1rem;">توزيع العلامات التجارية:</h6>
                <canvas id="brand-chart" style="height: 250px; max-height: 250px;"></canvas>
            </div>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Chart !== 'undefined' && document.getElementById('brand-chart')) {
                    var ctx = document.getElementById('brand-chart').getContext('2d');
                    
                    var chartData = {
                        labels: [<?php
                            $labels = [];
                            foreach($stats_data as $brand => $data) {
                                $labels[] = "'" . addslashes($brand) . "'";
                            }
                            echo implode(',', $labels);
                        ?>],
                        datasets: [{
                            label: 'توزيع المنتجات',
                            data: [<?php
                                $counts = [];
                                foreach($stats_data as $brand => $data) {
                                    $counts[] = $data['total'];
                                }
                                echo implode(',', $counts);
                            ?>],
                            backgroundColor: [
                                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
                                '#E7E9ED', '#8DDF3C', '#FBCF55', '#A478F0', '#FF6B6B', '#4ECDC4'
                            ],
                            borderWidth: 0
                        }]
                    };

                    new Chart(ctx, {
                        type: 'doughnut',
                        data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            legend: {
                                display: false
                            },
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        fontColor: 'white',
                                        boxWidth: 12,
                                        padding: 15
                                    }
                                },
                                title: {
                                    display: false
                                }
                            }
                        }
                    });
                } else {
                    console.error('Chart.js is not loaded or canvas element not found.');
                }
            });
            </script>
            
            <!-- معلومات آخر استيراد -->
            <?php if($last_import_data): ?>
            <div class="last-import mb-3" style="background: rgba(255,255,255,0.1); padding: 0.8rem; border-radius: 8px;">
                <small style="color: rgba(255,255,255,0.8);">آخر استيراد:</small>
                <div style="color: white; font-weight: 500;">
                    <?php echo $last_import_data['count'] ?> منتج - 
                    <?php echo date('Y/m/d H:i', strtotime($last_import_data['last_date'])) ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            
            <!-- حالة المخزن الفارغ -->
            <div class="text-center py-3">
                <i class="fas fa-inbox fa-3x mb-3" style="color: rgba(255,255,255,0.5);"></i>
                <h6 style="color: rgba(255,255,255,0.8);">المخزن فارغ</h6>
                <p style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">ابدأ برفع ملف CSV لإضافة المنتجات</p>
            </div>
            
            <?php endif; ?>
            
            <!-- أزرار الإجراءات -->
            <div class="d-flex gap-2 mt-3">
                <a href="index.php?page=warehouse/upload" class="btn btn-sm flex-fill" style="background: rgba(255,255,255,0.2); color: white; border: none; border-radius: 20px; font-weight: 500;">
                    <i class="fas fa-upload me-1"></i>
                    رفع CSV
                </a>
                <a href="index.php?page=warehouse" class="btn btn-sm flex-fill" style="background: rgba(255,255,255,0.2); color: white; border: none; border-radius: 20px; font-weight: 500;">
                    <i class="fas fa-cogs me-1"></i>
                    إدارة المخزن
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.warehouse-widget .progress-bar {
    transition: width 0.6s ease;
}

.warehouse-widget .chart-item:hover .progress-bar {
    opacity: 0.8;
}

.warehouse-widget .btn:hover {
    background: rgba(255,255,255,0.3) !important;
    transform: translateY(-1px);
    transition: all 0.3s ease;
}

.warehouse-widget .card {
    transition: transform 0.3s ease;
}

.warehouse-widget .card:hover {
    transform: translateY(-2px);
}
</style>