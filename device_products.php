<?php
// Device Products Page - Shows products filtered by device selection
require_once 'config.php';
require_once 'classes/TextCleaner.php';

// Initialize page variables
$pageTitle = "منتجات الأجهزة - Gayar Plus";
$title = "منتجات الأجهزة";
$sub_title = "جميع المنتجات المرتبطة بالأجهزة المحددة";

// Handle device filtering (support both MD5, base64 and direct ID)
$brand_id = null;
$brand_data = null;
$series_id = null;
$series_data = null;
$model_id = null;
$model_data = null;

// Handle brand filtering
if(isset($_GET['brand'])){
    $brand_param = $_GET['brand'];
    
    // Check if it's a direct ID
    if (is_numeric($brand_param)) {
        $brand_id = intval($brand_param);
        $brand_qry = $conn->query("SELECT * FROM brands where id = {$brand_id} AND status = 1");
    } elseif (base64_decode($brand_param, true) !== false && is_numeric(base64_decode($brand_param, true))) {
        // Handle base64 encoded ID
        $brand_id = intval(base64_decode($brand_param, true));
        $brand_qry = $conn->query("SELECT * FROM brands where id = {$brand_id} AND status = 1");
    } else {
        // Handle MD5 hash
        $brand_qry = $conn->query("SELECT * FROM brands where md5(id) = '{$brand_param}' AND status = 1");
    }
    
    if($brand_qry && $brand_qry->num_rows > 0){
        $brand_data = $brand_qry->fetch_assoc();
        $brand_id = $brand_data['id'];
        $brand_name = !empty($brand_data['name_ar']) ? $brand_data['name_ar'] : $brand_data['name'];
        $title = "منتجات " . htmlspecialchars($brand_name);
        $sub_title = "جميع المنتجات المرتبطة بعلامة " . htmlspecialchars($brand_name);
        $pageTitle = "منتجات " . htmlspecialchars($brand_name) . " - Gayar Plus";
    }
}

// Handle series filtering
if(isset($_GET['series'])){
    $series_param = $_GET['series'];
    
    // Check if it's a direct ID
    if (is_numeric($series_param)) {
        $series_id = intval($series_param);
        $series_qry = $conn->query("SELECT s.*, b.name, b.name_ar as brand_name_ar FROM series s LEFT JOIN brands b ON s.brand_id = b.id WHERE s.id = {$series_id} AND s.status = 1");
    } elseif (base64_decode($series_param, true) !== false && is_numeric(base64_decode($series_param, true))) {
        // Handle base64 encoded ID
        $series_id = intval(base64_decode($series_param, true));
        $series_qry = $conn->query("SELECT s.*, b.name, b.name_ar as brand_name_ar FROM series s LEFT JOIN brands b ON s.brand_id = b.id WHERE s.id = {$series_id} AND s.status = 1");
    } else {
        // Handle MD5 hash
        $series_qry = $conn->query("SELECT s.*, b.name, b.name_ar as brand_name_ar FROM series s LEFT JOIN brands b ON s.brand_id = b.id WHERE md5(s.id) = '{$series_param}' AND s.status = 1");
    }
    
    if($series_qry && $series_qry->num_rows > 0){
        $series_data = $series_qry->fetch_assoc();
        $series_id = $series_data['id'];
        $series_name = !empty($series_data['name_ar']) ? $series_data['name_ar'] : $series_data['name'];
        $sub_title = "جميع المنتجات المرتبطة بسلسلة " . htmlspecialchars($series_name);
        if(isset($brand_data)) {
            $brand_name = !empty($brand_data['name_ar']) ? $brand_data['name_ar'] : $brand_data['name'];
            $title = htmlspecialchars($brand_name) . " - " . htmlspecialchars($series_name);
        } else {
            $title = "سلسلة " . htmlspecialchars($series_name);
        }
        $pageTitle = "سلسلة " . htmlspecialchars($series_name) . " - Gayar Plus";
    }
}

// Handle model filtering
if(isset($_GET['model'])){
    $model_param = $_GET['model'];
    
    // Check if it's a direct ID
    if (is_numeric($model_param)) {
        $model_id = intval($model_param);
        $model_qry = $conn->query("SELECT m.*, s.name as series_name, s.name_ar as series_name_ar, b.name as brand_name, b.name_ar as brand_name_ar FROM models m LEFT JOIN series s ON m.series_id = s.id LEFT JOIN brands b ON s.brand_id = b.id WHERE m.id = {$model_id} AND m.status = 1");
    } elseif (base64_decode($model_param, true) !== false && is_numeric(base64_decode($model_param, true))) {
        // Handle base64 encoded ID
        $model_id = intval(base64_decode($model_param, true));
        $model_qry = $conn->query("SELECT m.*, s.name as series_name, s.name_ar as series_name_ar, b.name as brand_name, b.name_ar as brand_name_ar FROM models m LEFT JOIN series s ON m.series_id = s.id LEFT JOIN brands b ON s.brand_id = b.id WHERE m.id = {$model_id} AND m.status = 1");
    } else {
        // Handle MD5 hash
        $model_qry = $conn->query("SELECT m.*, s.name as series_name, s.name_ar as series_name_ar, b.name as brand_name, b.name_ar as brand_name_ar FROM models m LEFT JOIN series s ON m.series_id = s.id LEFT JOIN brands b ON s.brand_id = b.id WHERE md5(m.id) = '{$model_param}' AND m.status = 1");
    }
    
    if($model_qry && $model_qry->num_rows > 0){
        $model_data = $model_qry->fetch_assoc();
        $model_id = $model_data['id'];
        $model_name = !empty($model_data['name_ar']) ? $model_data['name_ar'] : $model_data['name'];
        $sub_title = "جميع المنتجات المرتبطة بموديل " . htmlspecialchars($model_name);
        if(isset($series_data) && isset($brand_data)) {
            $brand_name = !empty($brand_data['name_ar']) ? $brand_data['name_ar'] : $brand_data['name'];
            $series_name = !empty($series_data['name_ar']) ? $series_data['name_ar'] : $series_data['name'];
            $title = htmlspecialchars($brand_name) . " - " . htmlspecialchars($series_name) . " - " . htmlspecialchars($model_name);
        } else {
            $title = "موديل " . htmlspecialchars($model_name);
        }
        $pageTitle = "موديل " . htmlspecialchars($model_name) . " - Gayar Plus";
    }
}

include 'inc/header.php'
?>

<!-- Pattern Background -->
<div class="pattern-background"></div>

<!-- Breadcrumb -->
<section class="breadcrumb">
    <div class="breadcrumb-container">
        <nav class="breadcrumb-nav">
            <a href="./">الرئيسية</a>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-left"></i></span>
            <?php if(isset($_GET['brand']) && isset($brand_data)): ?>
            <a href="./?p=device_products&brand=<?= $_GET['brand'] ?>"><?= htmlspecialchars(!empty($brand_data['name_ar']) ? $brand_data['name_ar'] : $brand_data['name']) ?></a>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-left"></i></span>
            <?php endif; ?>
            <?php if(isset($_GET['series']) && isset($series_data)): ?>
            <a href="./?p=device_products&brand=<?= $_GET['brand'] ?? '' ?>&series=<?= $_GET['series'] ?>"><?= htmlspecialchars(!empty($series_data['name_ar']) ? $series_data['name_ar'] : $series_data['name']) ?></a>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-left"></i></span>
            <?php endif; ?>
            <span class="breadcrumb-current">المنتجات</span>
        </nav>
    </div>
</section>

<!-- Products Hero Section -->
<section class="products-hero">
    <div class="hero-container">
        <div class="hero-content">
            <h1 class="hero-title"><?= $title ?></h1>
            <p class="hero-subtitle"><?= $sub_title ?></p>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="section">
    <div class="products-container">
        <div class="products-grid">
            <?php 
                // Build WHERE clause for filtering
                $whereData = "";
                $params = [];
                
                if($model_id) {
                    $whereData = " and p.model_id = ?";
                    $params = [$model_id];
                } elseif($series_id) {
                    // Since series_id doesn't exist in products table, filter by brand instead
                    if($brand_id) {
                        $whereData = " and p.brand_id = ?";
                        $params = [$brand_id];
                    }
                } elseif($brand_id) {
                    $whereData = " and p.brand_id = ?";
                    $params = [$brand_id];
                }
                
                // Execute the main query
                $sql = "SELECT p.*, c.category as brand_name, sc.sub_category as series_name, m.name as model_name, b.name as actual_brand_name, b.name_ar as actual_brand_name_ar
                        FROM products p 
                        LEFT JOIN categories c ON p.category_id = c.id 
                        LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id 
                        LEFT JOIN models m ON p.model_id = m.id 
                        LEFT JOIN brands b ON p.brand_id = b.id 
                        WHERE p.status = 1 {$whereData} 
                        ORDER BY p.date_created DESC";

                if(!empty($params)) {
                    $stmt = $conn->prepare($sql);
                    $types = str_repeat('s', count($params));
                    $stmt->bind_param($types, ...$params);
                    $stmt->execute();
                    $products = $stmt->get_result();
                } else {
                    $products = $conn->query($sql);
                }

                if($products && $products->num_rows > 0):
                    while($row = $products->fetch_assoc()): 
                        // Check uploads directory for images
                        $image_path = 'uploads/product_'.$row['id'];
                        $images = [];
                        if(is_dir($image_path)) {
                            $files = scandir($image_path);
                            foreach($files as $file) {
                                if(!in_array($file, ['.', '..'])) {
                                    $images[] = $image_path.'/'.$file;
                                }
                            }
                        }
                        
                        // Get pricing info
                        $inventory = $conn->query("SELECT * FROM inventory WHERE product_id = ".$row['id']);
                        $price_info = null;
                        
                        if($inventory && $inventory->num_rows > 0) {
                            $price_info = $inventory->fetch_assoc();
                        }
                        
                        // Set default value for featured if not set
                        $featured = isset($row['featured']) ? $row['featured'] : 0;
            ?>
            <div class="product-card will-change" onclick="window.location.href='./?p=product_view&id=<?= md5($row['id']) ?>'">
                <div class="product-image">
                    <?php if(!empty($images)): ?>
                        <img src="<?= validate_image($images[0]) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" loading="lazy">
                    <?php else: ?>
                        <i class="fas fa-mobile-alt"></i>
                    <?php endif; ?>
                    
                    <?php if($featured == 1): ?>
                    <div class="product-badge">الأكثر مبيعاً</div>
                    <?php endif; ?>
                </div>
                
                <div class="product-info">
                    <div class="product-category">
                        <?php 
                        if($model_data && $model_data['name']) {
                            echo htmlspecialchars($model_data['name']);
                        } elseif($series_data && !empty($series_data['name_ar'])) {
                            echo htmlspecialchars($series_data['name_ar']);
                        } elseif($series_data && !empty($series_data['name'])) {
                            echo htmlspecialchars($series_data['name']);
                        } elseif($brand_data && !empty($brand_data['name_ar'])) {
                            echo htmlspecialchars($brand_data['name_ar']);
                        } elseif($brand_data && !empty($brand_data['name'])) {
                            echo htmlspecialchars($brand_data['name']);
                        } else {
                            echo 'ملحقات';
                        }
                        ?>
                    </div>
                    <h3 class="product-title"><?= htmlspecialchars($row['product_name']) ?></h3>
                    <p class="product-description"><?= TextCleaner::cleanAndTruncate($row['description'], 100) ?></p>
                    
                    <div class="product-footer">
                        <span class="product-price">
                            <?php if($price_info && $price_info['price'] > 0): ?>
                                <?= TextCleaner::formatPrice($price_info['price']) ?>
                            <?php else: ?>
                                السعر عند الطلب
                            <?php endif; ?>
                        </span>
                        
                        <button class="add-to-cart" onclick="event.stopPropagation(); addToCart(this, <?= $row['id'] ?>)">
                            <i class="fas fa-cart-plus"></i>
                            أضف للسلة
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            
        <?php else: ?>
            <div class="no-products">
                <div class="no-products-content">
                    <i class="fas fa-search"></i>
                    <h3>لا توجد منتجات متاحة حالياً</h3>
                    <p>لم نتمكن من العثور على منتجات تطابق معايير بحثك</p>
                    <a href="./" class="btn btn-primary">
                        <i class="fas fa-home"></i>
                        العودة للرئيسية
                    </a>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'inc/modern-footer.php'; ?>

<script>
// Initialize device menu for device products page
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for the navigation to load, then initialize
    setTimeout(function() {
        if (typeof window.initDeviceMenu === 'function') {
            window.initDeviceMenu();
            console.log('✅ Device menu manually initialized for device products page');
        }
    }, 500);
    
    // Backup initialization
    setTimeout(function() {
        if (typeof window.initDeviceMenu === 'function') {
            window.initDeviceMenu();
        }
    }, 1500);
});
</script>