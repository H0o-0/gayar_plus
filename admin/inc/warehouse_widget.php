<?php
// كارد المخزن المؤقت للداش بورد

// الحصول على إحصائيات المخزن
$warehouse_stats = $conn->query("
    SELECT 
        suggested_brand as brand,
        COUNT(*) as count,
        status
    FROM temp_warehouse 
    WHERE suggested_brand IS NOT NULL 
    GROUP BY suggested_brand, status
    ORDER BY count DESC
");

$stats_data = [];
$total_products = 0;
$unclassified_count = 0;

while($row = $warehouse_stats->fetch_assoc()) {
    $brand = $row['brand'];
    $count = $row['count'];
    $status = $row['status'];
    
    if(!isset($stats_data[$brand])) {
        $stats_data[$brand] = ['total' => 0, 'unclassified' => 0, 'classified' => 0, 'published' => 0];
    }
    
    $stats_data[$brand]['total'] += $count;
    $stats_data[$brand][$status] = $count;
    $total_products += $count;
    
    if($status == 'unclassified') {
        $unclassified_count += $count;
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

$last_import_data = $last_import->fetch_assoc();
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
                <div class="col-6">
                    <div class="text-center">
                        <h4 class="mb-1" style="color: #ffd700;"><?php echo $total_products ?></h4>
                        <small style="color: rgba(255,255,255,0.8);">إجمالي المنتجات</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center">
                        <h4 class="mb-1" style="color: #ff6b6b;"><?php echo $unclassified_count ?></h4>
                        <small style="color: rgba(255,255,255,0.8);">غير مصنف</small>
                    </div>
                </div>
            </div>
            
            <!-- شارت بسيط -->
            <div class="warehouse-chart mb-3">
                <h6 style="color: rgba(255,255,255,0.9); margin-bottom: 1rem;">توزيع المنتجات:</h6>
                <?php 
                $max_count = max(array_column($stats_data, 'total'));
                foreach($stats_data as $brand => $data): 
                    $percentage = ($data['total'] / $max_count) * 100;
                ?>
                <div class="chart-item mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span style="color: white; font-size: 0.9rem;"><?php echo $brand ?></span>
                        <span style="color: rgba(255,255,255,0.8); font-size: 0.8rem;"><?php echo $data['total'] ?></span>
                    </div>
                    <div class="progress" style="height: 6px; background: rgba(255,255,255,0.2);">
                        <div class="progress-bar" style="width: <?php echo $percentage ?>%; background: linear-gradient(90deg, #ffd700 0%, #ff6b6b 100%);"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
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
                <p style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">ابدأ برفع ملف Excel لإضافة المنتجات</p>
            </div>
            
            <?php endif; ?>
            
            <!-- أزرار الإجراءات -->
            <div class="d-flex gap-2 mt-3">
                <a href="index.php?page=warehouse/upload" class="btn btn-sm flex-fill" style="background: rgba(255,255,255,0.2); color: white; border: none; border-radius: 20px; font-weight: 500;">
                    <i class="fas fa-upload me-1"></i>
                    رفع Excel
                </a>
                <a href="index.php?page=warehouse/manage" class="btn btn-sm flex-fill" style="background: rgba(255,255,255,0.2); color: white; border: none; border-radius: 20px; font-weight: 500;">
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
