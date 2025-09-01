<?php
// جلب الإحصائيات من قاعدة البيانات
$total_products = $conn->query("SELECT COUNT(*) as count FROM `products`")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM `orders`")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM `users` WHERE type = 2")->fetch_assoc()['count'];
$total_admins = $conn->query("SELECT COUNT(*) as count FROM `users` WHERE type = 1")->fetch_assoc()['count'];

// حساب إجمالي المبيعات
$total_sales_result = $conn->query("SELECT SUM(amount) as total FROM `orders` WHERE status = 1");
$total_sales = $total_sales_result->fetch_assoc()['total'] ?? 0;

// حساب الطلبات الجديدة (آخر 7 أيام)
$new_orders = $conn->query("SELECT COUNT(*) as count FROM `orders` WHERE date_created >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'];

// التاريخ الحالي
$current_date = date('l, F j, Y');
$current_time = date('h:i A');
?>

<style>
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-left: 4px solid;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
    border-radius: 50%;
    transform: translate(30px, -30px);
}

.stat-card.products { border-left-color: #4CAF50; }
.stat-card.orders { border-left-color: #2196F3; }
.stat-card.users { border-left-color: #FF9800; }
.stat-card.security { border-left-color: #F44336; }

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    margin-bottom: 1rem;
}

.stat-card.products .stat-icon { background: linear-gradient(135deg, #4CAF50, #45a049); }
.stat-card.orders .stat-icon { background: linear-gradient(135deg, #2196F3, #1976D2); }
.stat-card.users .stat-icon { background: linear-gradient(135deg, #FF9800, #F57C00); }
.stat-card.security .stat-icon { background: linear-gradient(135deg, #F44336, #D32F2F); }

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #7f8c8d;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
}

.stat-change {
    font-size: 0.8rem;
    padding: 0.2rem 0.5rem;
    border-radius: 20px;
    font-weight: 500;
}

.stat-change.positive {
    background: #e8f5e8;
    color: #4CAF50;
}

.stat-change.negative {
    background: #ffebee;
    color: #F44336;
}

.quick-actions {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-top: 2rem;
}

.action-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    margin: 0.5rem;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
}

.date-time {
    text-align: right;
    opacity: 0.9;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .dashboard-cards {
        grid-template-columns: 1fr;
    }

    .dashboard-header {
        padding: 1.5rem;
        text-align: center;
    }

    .date-time {
        text-align: center;
        margin-top: 1rem;
    }
}
</style>

<div class="dashboard-header">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="mb-2">مرحباً بك في <?php echo $_settings->info('name') ?></h1>
            <p class="mb-0 opacity-75">لوحة التحكم الرئيسية - إدارة متجر قطع غيار الهواتف</p>
        </div>
        <div class="col-md-4">
            <div class="date-time">
                <div><i class="fas fa-calendar-alt"></i> <?php echo $current_date ?></div>
                <div><i class="fas fa-clock"></i> <?php echo $current_time ?></div>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-cards">
    <!-- بطاقة المنتجات -->
    <div class="stat-card products">
        <div class="stat-icon">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-number"><?php echo number_format($total_products) ?></div>
        <div class="stat-label">إجمالي المنتجات</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> نشط
        </div>
    </div>

    <!-- بطاقة الطلبات -->
    <div class="stat-card orders">
        <div class="stat-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-number"><?php echo number_format($total_orders) ?></div>
        <div class="stat-label">إجمالي الطلبات</div>
        <div class="stat-change positive">
            <i class="fas fa-plus"></i> <?php echo $new_orders ?> جديد هذا الأسبوع
        </div>
    </div>

    <!-- بطاقة المستخدمين -->
    <div class="stat-card users">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-number"><?php echo number_format($total_users) ?></div>
        <div class="stat-label">العملاء المسجلين</div>
        <div class="stat-change positive">
            <i class="fas fa-user-plus"></i> <?php echo $total_admins ?> مدير
        </div>
    </div>

    <!-- بطاقة الأمان -->
    <div class="stat-card security">
        <div class="stat-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div class="stat-number">99%</div>
        <div class="stat-label">مستوى الأمان</div>
        <div class="stat-change positive">
            <i class="fas fa-check"></i> محمي
        </div>
    </div>
</div>

<!-- كارد المخزن المؤقت -->
<div class="row mt-4">
    <?php include('inc/warehouse_widget.php'); ?>

    <!-- كارد إضافي للمستقبل -->
    <div class="col-lg-6 col-md-12 mb-4">
        <div class="card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border: none; border-radius: 15px; box-shadow: 0 8px 25px rgba(17,153,142,0.3);">
            <div class="card-body text-center" style="padding: 2rem;">
                <i class="fas fa-chart-line fa-3x mb-3" style="color: rgba(255,255,255,0.8);"></i>
                <h5 style="color: white; font-weight: 700;">تقارير المبيعات</h5>
                <p style="color: rgba(255,255,255,0.8);">عرض تقارير مفصلة عن المبيعات والأرباح</p>
                <a href="#" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white; border: none; border-radius: 20px; font-weight: 500;">
                    قريباً
                </a>
            </div>
        </div>
    </div>
</div>

<!-- إجراءات سريعة -->
<div class="quick-actions">
    <h4 class="mb-3"><i class="fas fa-bolt"></i> إجراءات سريعة</h4>
    <a href="<?php echo base_url ?>admin/?page=product/manage_product" class="action-btn">
        <i class="fas fa-plus"></i> إضافة منتج جديد
    </a>
    <a href="<?php echo base_url ?>admin/?page=orders" class="action-btn">
        <i class="fas fa-list"></i> عرض الطلبات
    </a>
    <a href="<?php echo base_url ?>admin/?page=inventory" class="action-btn">
        <i class="fas fa-warehouse"></i> إدارة المخزون
    </a>
    <a href="<?php echo base_url ?>admin/?page=system_info" class="action-btn">
        <i class="fas fa-cog"></i> إعدادات النظام
    </a>
</div>
