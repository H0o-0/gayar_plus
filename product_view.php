<?php
// Professional Product Viewer with Creative Design
ob_start(); // Start output buffering to prevent headers already sent error
require_once 'config.php';
require_once 'classes/TextCleaner.php';

// Get product by ID
if(!isset($_GET['id'])) {
    header('Location: ./');
    exit;
}

$product_id = $_GET['id'];

// Check if the database connection is valid
if (!isset($conn) || !$conn) {
    header('Location: ./');
    exit;
}

// Handle both numeric IDs and MD5 hashes
$sql_condition = "";
if (is_numeric($product_id)) {
    // If it's a numeric ID, search directly
    $sql_condition = "p.id = ?";
    $search_value = $product_id;
} else {
    // If it's an MD5 hash, search by MD5
    $sql_condition = "MD5(p.id) = ?";
    $search_value = $product_id;
}

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT p.*, 
                         b.name as brand_name, 
                         b.name_ar as brand_name_ar, 
                         s.name as series_name, 
                         m.name as model_name,
                         c.category as category_name,
                         sc.sub_category as sub_category_name
                         FROM products p 
                         LEFT JOIN brands b ON p.brand_id = b.id 
                         LEFT JOIN models m ON p.model_id = m.id 
                         LEFT JOIN series s ON m.series_id = s.id 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id 
                         WHERE {$sql_condition} AND p.status = 1");

// Check if prepare was successful
if (!$stmt) {
    error_log("Prepare failed in product_view.php: " . $conn->error);
    // Include header first
    include 'inc/header.php';
    echo '<div style="text-align: center; padding: 50px; font-family: Arial, sans-serif;">';
    echo '<h2>خطأ في قاعدة البيانات</h2>';
    echo '<p>حدث خطأ أثناء جلب تفاصيل المنتج.</p>';
    echo '<a href="./" style="color: #007bff; text-decoration: none;">العودة للرئيسية</a>';
    echo '</div>';
    include 'inc/modern-footer.php';
    exit;
}

$stmt->bind_param("s", $search_value);
$stmt->execute();
$result = $stmt->get_result();

// Check if query was successful and has results
if(!$result || $result->num_rows == 0) {
    error_log("Product not found in product_view.php - ID: " . $product_id);
    // Include header first
    include 'inc/header.php';
    echo '<div style="text-align: center; padding: 50px; font-family: Arial, sans-serif;">';
    echo '<h2>المنتج غير موجود</h2>';
    echo '<p>عذراً، المنتج المطلوب غير متوفر أو تم حذفه.</p>';
    echo '<a href="./" style="color: #007bff; text-decoration: none;">العودة للرئيسية</a>';
    echo '</div>';
    include 'inc/modern-footer.php';
    exit;
}

// Get product data with error suppression
@$product = $result->fetch_assoc();
if (!$product) {
    error_log("Failed to fetch product data in product_view.php");
    include 'inc/header.php';
    echo '<div style="text-align: center; padding: 50px; font-family: Arial, sans-serif;">';
    echo '<h2>خطأ في جلب البيانات</h2>';
    echo '<p>حدث خطأ أثناء جلب بيانات المنتج.</p>';
    echo '<a href="./" style="color: #007bff; text-decoration: none;">العودة للرئيسية</a>';
    echo '</div>';
    include 'inc/modern-footer.php';
    exit;
}

// Clean and validate product data
$product['description'] = $product['description'] ?? '';
$product['product_name'] = $product['product_name'] ?? 'منتج غير محدد';
// Use brand_name_ar if available, otherwise use brand_name
$product['display_brand_name'] = (!empty($product['brand_name_ar']) ? $product['brand_name_ar'] : $product['brand_name']) ?? 'ملحقات عامة';

$pageTitle = $product['product_name'];

// Get images
$images = [];
// Check for uploaded images
$image_path = 'uploads/product_'.$product['id'];
if(is_dir($image_path)) {
    $files = scandir($image_path);
    foreach($files as $file) {
        if(!in_array($file, ['.', '..'])) {
            $images[] = $image_path.'/'.$file;
        }
    }
}

// احصل على معلومات السعر - استخدم سعر المنتج مباشرة من جدول products
$price_info = null;
if(isset($product['price']) && $product['price'] > 0) {
    $price_info = [
        'price' => $product['price'],
        'quantity' => $product['quantity'] ?? 0
    ];
}

// إذا لم يكن هناك سعر في جدول products، تحقق من جدول inventory
if(!$price_info) {
    $price_stmt = $conn->prepare("SELECT * FROM inventory WHERE product_id = ? LIMIT 1");
    if ($price_stmt) {
        @$price_stmt->bind_param("i", $product['id']);
        @$price_stmt->execute();
        $price_result = @$price_stmt->get_result();
        if($price_result && $price_result->num_rows > 0) {
            $price_info = @$price_result->fetch_assoc();
        }
    } else {
        error_log("Price prepare failed in product_view.php: " . $conn->error);
    }
}

// Get related products - Enhanced logic to show more relevant products
// If the product has a model_id, show products from the same model
// Otherwise, show products from the same category
$related_products = [];

// First, get the current product's model_id if available
$current_product_model = $product['model_id'];

// If product has a model_id, show other products from the same model
if ($current_product_model) {
    $related_stmt = $conn->prepare("SELECT p.*, b.name as brand_name, b.name_ar as brand_name_ar 
                                    FROM products p 
                                    LEFT JOIN brands b ON p.brand_id = b.id 
                                    WHERE p.id != ? AND p.model_id = ? AND p.status = 1 
                                    ORDER BY RAND() LIMIT 4");
    if ($related_stmt) {
        $related_stmt->bind_param("ii", $product['id'], $current_product_model);
        $related_stmt->execute();
        $related_result = $related_stmt->get_result();
        while($row = $related_result->fetch_assoc()) {
            $related_products[] = $row;
        }
    }
} 
// If no model_id or not enough products, fall back to same category
else if ($product['category_id']) {
    $related_stmt = $conn->prepare("SELECT p.*, b.name as brand_name, b.name_ar as brand_name_ar 
                                    FROM products p 
                                    LEFT JOIN brands b ON p.brand_id = b.id 
                                    WHERE p.id != ? AND p.category_id = ? AND p.status = 1 
                                    ORDER BY RAND() LIMIT 4");
    if ($related_stmt) {
        $related_stmt->bind_param("ii", $product['id'], $product['category_id']);
        $related_stmt->execute();
        $related_result = $related_stmt->get_result();
        while($row = $related_result->fetch_assoc()) {
            // Only add if we haven't reached the limit
            if (count($related_products) < 4) {
                $related_products[] = $row;
            }
        }
    }
}

// All checks complete, include header
include 'inc/header.php';
ob_end_flush(); // Flush the output buffer
?>

<!-- Product View Styles -->
<style>
.product-view-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.product-view-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 3rem;
}

@media (max-width: 768px) {
    .product-view-layout {
        grid-template-columns: 1fr;
    }
}

.product-images {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.main-image-wrapper {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    background: #f8f9fa;
    aspect-ratio: 1/1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.main-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.main-image:hover {
    transform: scale(1.02);
}

.thumbnail-container {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding: 0.5rem 0;
}

.thumbnail {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.2s ease;
    flex-shrink: 0;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.thumbnail:hover {
    border-color: #007bff;
}

.thumbnail.active {
    border-color: #007bff;
}

.thumbnail img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.product-details {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.product-title {
    font-size: 2rem;
    font-weight: 700;
    color: #212529;
    margin: 0;
}

.product-brand {
    font-size: 1.1rem;
    color: #6c757d;
    margin: 0;
}

.product-price {
    font-size: 1.8rem;
    font-weight: 700;
    color: #007bff;
    margin: 0.5rem 0;
}

.original-price {
    font-size: 1.2rem;
    color: #6c757d;
    text-decoration: line-through;
    margin-left: 0.5rem;
}

.product-description {
    color: #495057;
    line-height: 1.6;
    margin: 1rem 0;
}

.product-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin: 1rem 0;
}

.btn-primary {
    background: #007bff;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: background 0.2s ease;
}

.btn-primary:hover {
    background: #0069d9;
}

.btn-secondary {
    background: #28a745;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: background 0.2s ease;
}

.btn-secondary:hover {
    background: #218838;
}

.btn-wishlist {
    background: #f8f9fa;
    color: #6c757d;
    border: 1px solid #dee2e6;
    padding: 0.75rem;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.btn-wishlist:hover {
    background: #e9ecef;
    color: #dc3545;
}

.product-features {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 16px;
    padding: 2rem;
    margin: 1.5rem 0;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
}

.product-colors {
    margin: 1.5rem 0;
    padding: 1rem;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    background: #f8f9fa;
}

.color-label {
    font-weight: 600;
    color: #495057;
    font-size: 1rem;
    margin-bottom: 1rem;
    display: block;
}

.colors-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.color-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    min-width: 100px;
}

.color-option:hover {
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.15);
}

.color-option.selected {
    border-color: #007bff;
    background: #e7f3ff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.color-swatch {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px rgba(0,0,0,0.1);
    flex-shrink: 0;
}

.color-name {
    font-size: 0.9rem;
    font-weight: 500;
    color: #495057;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.feature-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    transition: all 0.3s ease;
    border: 1px solid #f1f3f5;
}

.feature-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    border-color: #007bff;
}

.feature-icon {
    color: #007bff;
    font-size: 1.5rem;
    background: rgba(0, 123, 255, 0.1);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.feature-text {
    color: #495057;
    font-weight: 500;
    font-size: 1.05rem;
    line-height: 1.5;
}

.specifications-table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
}

.specifications-table th,
.specifications-table td {
    padding: 1rem;
    text-align: right;
    border-bottom: 1px solid #e9ecef;
}

.specifications-table th {
    font-weight: 600;
    color: #495057;
    width: 30%;
    background-color: #f8f9fa;
}

.specifications-table tr:last-child td {
    border-bottom: none;
}

.specifications-table tr:hover {
    background-color: #f8f9fa;
}

.related-products {
    margin: 3rem 0;
}

.related-products .section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 1.5rem;
    text-align: center;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1.5rem;
}

.product-card {
    border: 1px solid #dee2e6;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    background: white;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.product-card-image {
    height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.product-card-image img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.product-card-image img:hover {
    transform: scale(1.05);
}

.product-card-info {
    padding: 1rem;
}

.product-card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #212529;
    margin: 0 0 0.5rem 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-card-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #007bff;
    margin: 0;
}
</style>

<!-- Product View Container -->
<div class="product-view-container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" style="margin-bottom: 1.5rem;">
        <ol style="display: flex; list-style: none; padding: 0; margin: 0;">
            <li><a href="./" style="color: #007bff; text-decoration: none;">الرئيسية</a></li>
            <li style="margin: 0 0.5rem; color: #6c757d;">/</li>
            <li><a href="./?p=products" style="color: #007bff; text-decoration: none;">المنتجات</a></li>
            <?php if(!empty($product['brand_id']) && !empty($product['display_brand_name'])): ?>
            <li style="margin: 0 0.5rem; color: #6c757d;">/</li>
            <li><a href="./?p=products&brand=<?= $product['brand_id'] ?>" style="color: #007bff; text-decoration: none;"><?= htmlspecialchars($product['display_brand_name']) ?></a></li>
            <?php endif; ?>
            <?php if(!empty($product['series_name'])): ?>
            <li style="margin: 0 0.5rem; color: #6c757d;">/</li>
            <li><a href="./?p=products&brand=<?= $product['brand_id'] ?? '' ?>&series=<?= $product['series_id'] ?? '' ?>" style="color: #007bff; text-decoration: none;"><?= htmlspecialchars($product['series_name']) ?></a></li>
            <?php endif; ?>
            <?php if(!empty($product['model_name'])): ?>
            <li style="margin: 0 0.5rem; color: #6c757d;">/</li>
            <li><a href="./?p=products&model=<?= $product['model_id'] ?? '' ?>" style="color: #007bff; text-decoration: none;"><?= htmlspecialchars($product['model_name']) ?></a></li>
            <?php endif; ?>
            <li style="margin: 0 0.5rem; color: #6c757d;">/</li>
            <li style="color: #6c757d;"><?= htmlspecialchars($product['product_name']) ?></li>
        </ol>
    </nav>

    <!-- Product Layout -->
    <div class="product-view-layout">
        <!-- Product Images -->
        <div class="product-images">
            <div class="main-image-wrapper">
                <?php if(!empty($images)): ?>
                    <img src="<?= validate_image($images[0]) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="main-image" id="mainImage">
                <?php else: ?>
                    <i class="fas fa-mobile-alt" style="font-size: 4rem; color: #6c757d;"></i>
                <?php endif; ?>
            </div>
            
            <?php if(count($images) > 1): ?>
            <div class="thumbnail-container">
                <?php foreach($images as $index => $image): ?>
                <div class="thumbnail <?= $index === 0 ? 'active' : '' ?>" onclick="changeMainImage('<?= validate_image($image) ?>', this)">
                    <img src="<?= validate_image($image) ?>" alt="صورة المنتج">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Product Details -->
        <div class="product-details">
            <h1 class="product-title"><?= htmlspecialchars($product['product_name']) ?></h1>
            
            <div class="product-price">
                <?php if($price_info): ?>
                    <?= TextCleaner::formatPrice($price_info['price']) ?>
                <?php else: ?>
                    السعر عند الطلب
                <?php endif; ?>
            </div>

            <div class="product-description">
                <?= TextCleaner::sanitizeForDescription($product['description']) ?>
            </div>
            
            <?php if(isset($product['has_colors']) && $product['has_colors'] == 1 && !empty($product['colors'])): ?>
            <?php 
                $colors = json_decode($product['colors'], true);
                if(is_array($colors) && count($colors) > 0):
            ?>
            <!-- Product Colors -->
            <div class="product-colors">
                <label class="color-label">الألوان المتوفرة:</label>
                <div class="colors-grid">
                    <?php foreach($colors as $index => $color): ?>
                    <div class="color-option <?= $index === 0 ? 'selected' : '' ?>" 
                         data-color-name="<?= htmlspecialchars($color['name']) ?>"
                         data-color-code="<?= htmlspecialchars($color['code']) ?>"
                         onclick="selectColor(this)">
                        <div class="color-swatch" style="background-color: <?= htmlspecialchars($color['code']) ?>"></div>
                        <span class="color-name"><?= htmlspecialchars($color['name']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Product Actions -->
            <div class="product-actions">
                <button class="btn btn-primary" onclick="addToCart(<?= $product['id'] ?>)">
                    <i class="fas fa-cart-plus"></i>
                    أضف إلى السلة
                </button>
            </div>

            <!-- Product Features -->
            <div class="product-features">
                <h3 style="margin-top: 0; color: #212529; font-size: 1.5rem; font-weight: 700; text-align: center; margin-bottom: 1.5rem;">
                    <i class="fas fa-star" style="color: #ffc107; margin-left: 0.5rem;"></i>
                    المميزات الرئيسية
                </h3>
                <div class="features-grid">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="feature-text">جودة عالية ومضمونة مع ضمان الأصالة</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-truck"></i></div>
                        <div class="feature-text">شحن مجاني سريع لجميع أنحاء العراق</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                        <div class="feature-text">ضمان شامل لمدة سنة ضد العيوب</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-headset"></i></div>
                        <div class="feature-text">خدمة عملاء متخصصة 24 ساعة في اليوم</div>
                    </div>
                </div>
            </div>

            <!-- Removed Technical Specifications section as requested -->
        </div>
    </div>

    <!-- Related Products -->
    <?php if(!empty($related_products)): ?>
    <div class="related-products">
        <h2 class="section-title">منتجات مشابهة</h2>
        <div class="products-grid">
            <?php foreach($related_products as $related): ?>
            <div class="product-card" onclick="viewProduct('<?= md5($related['id']) ?>')">
                <div class="product-card-image">
                    <?php 
                    // Get images for related product
                    $related_images = [];
                    $related_image_path = 'uploads/product_'.$related['id'];
                    if(is_dir($related_image_path)) {
                        $files = scandir($related_image_path);
                        foreach($files as $file) {
                            if(!in_array($file, ['.', '..'])) {
                                $related_images[] = $related_image_path.'/'.$file;
                            }
                        }
                    }
                    ?>
                    <?php if(!empty($related_images)): ?>
                        <img src="<?= validate_image($related_images[0]) ?>" alt="<?= htmlspecialchars($related['product_name']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    <?php else: ?>
                        <i class="fas fa-mobile-alt"></i>
                    <?php endif; ?>
                </div>
                <div class="product-card-info">
                    <h3 class="product-card-title"><?= htmlspecialchars($related['product_name']) ?></h3>
                    <div class="product-card-price">
                        <?php
                        $related_price_stmt = $conn->prepare("SELECT price FROM inventory WHERE product_id = ? LIMIT 1");
                        if($related_price_stmt) {
                            $related_price_stmt->bind_param("i", $related['id']);
                            $related_price_stmt->execute();
                            $related_price_result = $related_price_stmt->get_result();
                            if($related_price_result && $related_price_result->num_rows > 0) {
                                $price = $related_price_result->fetch_assoc()['price'];
                                echo TextCleaner::formatPrice($price);
                            } else {
                                echo 'السعر عند الطلب';
                            }
                        } else {
                            echo 'السعر عند الطلب';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Product viewer functionality
function changeMainImage(src, thumbnail) {
    document.getElementById('mainImage').src = src;
    
    // Remove active class from all thumbnails
    document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
    
    // Add active class to clicked thumbnail
    if(thumbnail) {
        thumbnail.classList.add('active');
    }
}

function addToCart(productId) {
    console.log('🛒 Adding product to cart:', productId);
    
    // Show loading state
    var button = document.querySelector('[onclick="addToCart(' + productId + ')"]');
    var originalHTML = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإضافة...';
    
    fetch('ajax/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=1'
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('خطأ في الشبكة: ' + response.status);
        }
        return response.json();
    })
    .then(function(data) {
        console.log('استجابة الخادم:', data);
        
        if (data.success) {
            // Update cart count in header if it exists
            var cartElements = document.querySelectorAll('.cart-count, .cart-badge, #cart-count');
            cartElements.forEach(function(element) {
                if (element) {
                    element.textContent = data.cart_count;
                }
            });
            
            // Show success notification
            showNotification('✅ تم إضافة المنتج للسلة!');
            
            // Show success state
            button.innerHTML = '<i class="fas fa-check"></i> تمت الإضافة!';
            button.style.background = '#10b981';
            
            setTimeout(function() {
                button.innerHTML = originalHTML;
                button.style.background = '';
                button.disabled = false;
            }, 2000);
        } else {
            throw new Error(data.message || 'فشل في إضافة المنتج');
        }
    })
    .catch(function(error) {
        console.error('خطأ:', error);
        showNotification('❌ ' + error.message, 'error');
        button.innerHTML = originalHTML;
        button.disabled = false;
    });
}

// Show notifications function
function showNotification(message, type) {
    type = type || 'success';
    document.querySelectorAll('.cart-notification').forEach(function(n) { n.remove(); });
    
    var notification = document.createElement('div');
    notification.className = 'cart-notification';
    notification.style.cssText = 
        'position: fixed; top: 80px; right: 20px; z-index: 10000;' +
        'background: ' + (type === 'success' ? '#10b981' : (type === 'info' ? '#3b82f6' : '#ef4444')) + ';' +
        'color: white; padding: 1rem 1.5rem; border-radius: 12px;' +
        'box-shadow: 0 10px 30px rgba(0,0,0,0.2); font-weight: 600;' +
        'max-width: 300px; font-size: 14px;';
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(function() { notification.remove(); }, 3000);
}

// Color selection function
function selectColor(colorOption) {
    // Remove selected class from all color options
    document.querySelectorAll('.color-option').forEach(function(option) {
        option.classList.remove('selected');
    });
    
    // Add selected class to clicked option
    colorOption.classList.add('selected');
    
    // Get color data
    var colorName = colorOption.getAttribute('data-color-name');
    var colorCode = colorOption.getAttribute('data-color-code');
    
    console.log('🎨 Selected color:', colorName, colorCode);
}

function viewProduct(productId) {
    if (!productId) return;
    
    // Check if it's already an MD5 hash or numeric ID
    if (typeof productId === 'number' || /^\d+$/.test(productId)) {
        // If numeric, use direct link that can handle conversion
        window.location.href = `product_view.php?id=${productId}`;
    } else {
        // If already MD5 hash, use router
        window.location.href = `./?p=product_view&id=${productId}`;
    }
}

// Initialize device menu for this page
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for the navigation to load, then initialize
    setTimeout(function() {
        if (typeof window.initDeviceMenu === 'function') {
            window.initDeviceMenu();
            console.log('✅ Device menu manually initialized for product view page');
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

<?php include 'inc/modern-footer.php'; ?>