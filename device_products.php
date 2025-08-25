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
        $title = "منتجات " . htmlspecialchars($brand_data['name']);
        $sub_title = "جميع المنتجات المرتبطة بعلامة " . htmlspecialchars($brand_data['name']);
        $pageTitle = "منتجات " . htmlspecialchars($brand_data['name']) . " - Gayar Plus";
    }
}

// Handle series filtering
if(isset($_GET['series'])){
    $series_param = $_GET['series'];
    
    // Check if it's a direct ID
    if (is_numeric($series_param)) {
        $series_id = intval($series_param);
        $series_qry = $conn->query("SELECT s.*, b.name as brand_name FROM series s LEFT JOIN brands b ON s.brand_id = b.id WHERE s.id = {$series_id} AND s.status = 1");
    } elseif (base64_decode($series_param, true) !== false && is_numeric(base64_decode($series_param, true))) {
        // Handle base64 encoded ID
        $series_id = intval(base64_decode($series_param, true));
        $series_qry = $conn->query("SELECT s.*, b.name as brand_name FROM series s LEFT JOIN brands b ON s.brand_id = b.id WHERE s.id = {$series_id} AND s.status = 1");
    } else {
        // Handle MD5 hash
        $series_qry = $conn->query("SELECT s.*, b.name as brand_name FROM series s LEFT JOIN brands b ON s.brand_id = b.id WHERE md5(s.id) = '{$series_param}' AND s.status = 1");
    }
    
    if($series_qry && $series_qry->num_rows > 0){
        $series_data = $series_qry->fetch_assoc();
        $series_id = $series_data['id'];
        $sub_title = "جميع المنتجات المرتبطة بسلسلة " . htmlspecialchars($series_data['name']);
        if(isset($brand_data)) {
            $title = htmlspecialchars($brand_data['name']) . " - " . htmlspecialchars($series_data['name']);
        } else {
            $title = "سلسلة " . htmlspecialchars($series_data['name']);
        }
        $pageTitle = "سلسلة " . htmlspecialchars($series_data['name']) . " - Gayar Plus";
    }
}

// Handle model filtering
if(isset($_GET['model'])){
    $model_param = $_GET['model'];
    
    // Check if it's a direct ID
    if (is_numeric($model_param)) {
        $model_id = intval($model_param);
        $model_qry = $conn->query("SELECT m.*, s.name as series_name, b.name as brand_name FROM models m LEFT JOIN series s ON m.series_id = s.id LEFT JOIN brands b ON s.brand_id = b.id WHERE m.id = {$model_id} AND m.status = 1");
    } elseif (base64_decode($model_param, true) !== false && is_numeric(base64_decode($model_param, true))) {
        // Handle base64 encoded ID
        $model_id = intval(base64_decode($model_param, true));
        $model_qry = $conn->query("SELECT m.*, s.name as series_name, b.name as brand_name FROM models m LEFT JOIN series s ON m.series_id = s.id LEFT JOIN brands b ON s.brand_id = b.id WHERE m.id = {$model_id} AND m.status = 1");
    } else {
        // Handle MD5 hash
        $model_qry = $conn->query("SELECT m.*, s.name as series_name, b.name as brand_name FROM models m LEFT JOIN series s ON m.series_id = s.id LEFT JOIN brands b ON s.brand_id = b.id WHERE md5(m.id) = '{$model_param}' AND m.status = 1");
    }
    
    if($model_qry && $model_qry->num_rows > 0){
        $model_data = $model_qry->fetch_assoc();
        $model_id = $model_data['id'];
        $sub_title = "جميع المنتجات المرتبطة بموديل " . htmlspecialchars($model_data['name']);
        if(isset($series_data) && isset($brand_data)) {
            $title = htmlspecialchars($brand_data['name']) . " - " . htmlspecialchars($series_data['name']) . " - " . htmlspecialchars($model_data['name']);
        } else {
            $title = "موديل " . htmlspecialchars($model_data['name']);
        }
        $pageTitle = "موديل " . htmlspecialchars($model_data['name']) . " - Gayar Plus";
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
            <a href="./?p=device_products&brand=<?= $_GET['brand'] ?>"><?= htmlspecialchars($brand_data['name']) ?></a>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-left"></i></span>
            <?php endif; ?>
            <?php if(isset($_GET['series']) && isset($series_data)): ?>
            <a href="./?p=device_products&brand=<?= $_GET['brand'] ?? '' ?>&series=<?= $_GET['series'] ?>"><?= htmlspecialchars($series_data['name']) ?></a>
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
                    $whereData = " and p.series_id = ?";
                    $params = [$series_id];
                } elseif($brand_id) {
                    $whereData = " and p.brand_id = ?";
                    $params = [$brand_id];
                }
                
                // Execute the main query
                $sql = "SELECT p.*, b.name as brand_name, s.name as series_name, m.name as model_name, p.image as image 
                        FROM products p 
                        LEFT JOIN brands b ON p.brand_id = b.id 
                        LEFT JOIN series s ON p.series_id = s.id 
                        LEFT JOIN models m ON p.model_id = m.id 
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
                        // Use image from database if available, otherwise check uploads
                        $image_path = $row['image'] ? $row['image'] : 'uploads/product_'.$row['id'];
                        $images = [];
                        if(is_dir($image_path) && !$row['image']) {
                            $files = scandir($image_path);
                            foreach($files as $file) {
                                if(!in_array($file, ['.', '..'])) {
                                    $images[] = $image_path.'/'.$file;
                                }
                            }
                        } elseif ($row['image']) {
                            $images[] = $row['image'];
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
                        } elseif($series_data && $series_data['name']) {
                            echo htmlspecialchars($series_data['name']);
                        } elseif($brand_data && $brand_data['name']) {
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