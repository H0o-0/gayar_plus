<?php
// Modern Product Viewer based on beautiful product viewer.html design
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

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT p.*, c.category as brand_name, sc.sub_category as series_name, p.product_name as model_name, p.product_name as image 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id 
                         WHERE MD5(p.id) = ? AND p.status = 1");

// Check if prepare was successful
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    header('Location: ./');
    exit;
}

$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if query was successful and has results
if(!$result || $result->num_rows == 0) {
    header('Location: ./');
    exit;
}

// احصل على بيانات المنتج
$product = $result->fetch_assoc();
$pageTitle = $product['product_name'];

// Use image from database if available, otherwise check uploads
$image_path = $product['image'] ? $product['image'] : 'uploads/product_'.$product['id'];
$images = [];
if(is_dir($image_path) && !$product['image']) {
    $files = scandir($image_path);
    foreach($files as $file) {
        if(!in_array($file, ['.', '..'])) {
            $images[] = $image_path.'/'.$file;
        }
    }
} elseif ($product['image']) {
    $images[] = $product['image'];
}

// احصل على معلومات السعر
$price_stmt = $conn->prepare("SELECT * FROM inventory WHERE product_id = ? LIMIT 1");
// Check if prepare was successful
if (!$price_stmt) {
    error_log("Price prepare failed: " . $conn->error);
    $price_info = null;
} else {
    $price_stmt->bind_param("i", $product['id']);
    $price_stmt->execute();
    $price_result = $price_stmt->get_result();
    $price_info = null;
    if($price_result && $price_result->num_rows > 0) {
        $price_info = $price_result->fetch_assoc();
    }
}

// All redirect checks are complete, now we can include the header
include 'inc/header.php';
ob_end_flush(); // Flush the output buffer
?>

<!-- Pattern Background -->
<div class="pattern-background"></div>

<!-- Breadcrumb -->
<section class="breadcrumb">
    <div class="breadcrumb-container">
        <nav class="breadcrumb-nav">
            <a href="./">الرئيسية</a>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-left"></i></span>
            <a href="./?p=products">المنتجات</a>
            <?php if($product['brand_name']): ?>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-left"></i></span>
            <a href="./?p=products&category=<?= md5($product['category_id']) ?>"><?= htmlspecialchars($product['brand_name']) ?></a>
            <?php endif; ?>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-left"></i></span>
            <span class="breadcrumb-current"><?= htmlspecialchars($product['product_name']) ?></span>
        </nav>
    </div>
</section>

<!-- Product Main Section -->
<section class="product-main">
    <div class="product-container">
        <div class="product-layout">
            <!-- Product Gallery -->
            <div class="product-gallery">
                <div class="main-image-container">
                    <?php if(!empty($images)): ?>
                        <img src="<?= validate_image($images[0]) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="main-image" id="mainImage">
                        <button class="zoom-btn" onclick="zoomImage()">
                            <i class="fas fa-search-plus"></i>
                        </button>
                    <?php else: ?>
                        <div class="main-image-placeholder">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if(count($images) > 1): ?>
                <div class="image-thumbnails">
                    <?php foreach($images as $index => $image): ?>
                    <div class="thumbnail <?= $index === 0 ? 'active' : '' ?>" onclick="changeMainImage('<?= validate_image($image) ?>', this)">
                        <img src="<?= validate_image($image) ?>" alt="صورة المنتج">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Product Info -->
            <div class="product-info">
                <div class="product-badges">
                    <?php if($product['featured'] == 1): ?>
                    <span class="badge badge-bestseller">الأكثر مبيعاً</span>
                    <?php endif; ?>
                    <span class="badge badge-new">جديد</span>
                </div>

                <h1 class="product-title"><?= htmlspecialchars($product['product_name']) ?></h1>

                <div class="product-rating">
                    <div class="stars">
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star empty"></i>
                    </div>
                    <span class="rating-text">4.5 من 5</span>
                    <a href="#reviews" class="rating-count">(142 تقييم)</a>
                </div>

                <div class="product-price">
                    <?php if($price_info): ?>
                        <span class="price-current"><?= TextCleaner::formatPrice($price_info['price']) ?></span>
                    <?php else: ?>
                        <span class="price-current">السعر عند الطلب</span>
                    <?php endif; ?>
                </div>

                <div class="product-description">
                    <?= TextCleaner::sanitizeForDescription($product['description']) ?>
                </div>

                <!-- Product Options -->
                <div class="product-options">
                    <div class="option-group">
                        <label class="option-label">الكمية:</label>
                        <div class="quantity-selector">
                            <div class="quantity-controls">
                                <button class="quantity-btn" onclick="decreaseQuantity()">-</button>
                                <input type="number" class="quantity-input" id="quantity" value="1" min="1">
                                <button class="quantity-btn" onclick="increaseQuantity()">+</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Actions -->
                <?php if($price_info): ?>
                <div class="product-actions">
                    <button class="btn btn-primary" onclick="addToCart(<?= $product['id'] ?>)">
                        <i class="fas fa-cart-plus"></i>
                        أضف إلى السلة
                    </button>
                    <button class="btn btn-secondary" onclick="buyNow(<?= $product['id'] ?>)">
                        <i class="fas fa-bolt"></i>
                        اشتري الآن
                    </button>
                    <button class="btn btn-wishlist" onclick="addToWishlist(<?= $product['id'] ?>)">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
                <?php else: ?>
                <div class="product-actions">
                    <button class="btn btn-secondary" disabled>
                        <i class="fas fa-clock"></i>
                        غير متوفر حالياً
                    </button>
                </div>
                <?php endif; ?>

                <!-- Product Features -->
                <div class="product-features">
                    <h3 class="features-title">المميزات الرئيسية</h3>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-check"></i></div>
                            <span>جودة عالية ومضمونة</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-check"></i></div>
                            <span>شحن مجاني لجميع أنحاء العراق</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-check"></i></div>
                            <span>ضمان شامل لمدة سنة</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-check"></i></div>
                            <span>خدمة عملاء 24/7</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Tabs -->
        <div class="product-tabs">
            <div class="tabs-nav">
                <button class="tab-btn active" onclick="showTab('description')">الوصف التفصيلي</button>
                <button class="tab-btn" onclick="showTab('specifications')">المواصفات</button>
                <button class="tab-btn" onclick="showTab('reviews')">التقييمات</button>
            </div>

            <div class="tab-content active" id="description">
                <h3>الوصف التفصيلي</h3>
                <div><?= TextCleaner::sanitizeForDescription($product['description']) ?></div>
            </div>

            <div class="tab-content" id="specifications">
                <h3>المواصفات التقنية</h3>
                <table class="specifications-table">
                    <tr>
                        <th>الشركة المصنعة</th>
                        <td><?= $product['brand_name'] ? htmlspecialchars($product['brand_name']) : 'غير محدد' ?></td>
                    </tr>
                    <?php if($product['series_name']): ?>
                    <tr>
                        <th>السلسلة</th>
                        <td><?= htmlspecialchars($product['series_name']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($product['model_name']): ?>
                    <tr>
                        <th>الموديل</th>
                        <td><?= htmlspecialchars($product['model_name']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th>حالة التوفر</th>
                        <td><?= $price_info ? 'متوفر' : 'غير متوفر' ?></td>
                    </tr>
                </table>
            </div>

            <div class="tab-content" id="reviews">
                <h3>تقييمات العملاء</h3>
                <div class="reviews-summary">
                    <div class="rating-overview">
                        <div class="rating-score">4.5</div>
                        <div class="stars">
                            <i class="fas fa-star star"></i>
                            <i class="fas fa-star star"></i>
                            <i class="fas fa-star star"></i>
                            <i class="fas fa-star star"></i>
                            <i class="fas fa-star star empty"></i>
                        </div>
                        <p>من أصل 142 تقييم</p>
                    </div>
                    <div class="rating-bars">
                        <div class="rating-bar">
                            <span>5 نجوم</span>
                            <div class="rating-bar-fill">
                                <div class="rating-bar-progress" style="width: 65%"></div>
                            </div>
                            <span>65%</span>
                        </div>
                        <div class="rating-bar">
                            <span>4 نجوم</span>
                            <div class="rating-bar-fill">
                                <div class="rating-bar-progress" style="width: 25%"></div>
                            </div>
                            <span>25%</span>
                        </div>
                        <div class="rating-bar">
                            <span>3 نجوم</span>
                            <div class="rating-bar-fill">
                                <div class="rating-bar-progress" style="width: 7%"></div>
                            </div>
                            <span>7%</span>
                        </div>
                        <div class="rating-bar">
                            <span>2 نجوم</span>
                            <div class="rating-bar-fill">
                                <div class="rating-bar-progress" style="width: 2%"></div>
                            </div>
                            <span>2%</span>
                        </div>
                        <div class="rating-bar">
                            <span>1 نجمة</span>
                            <div class="rating-bar-fill">
                                <div class="rating-bar-progress" style="width: 1%"></div>
                            </div>
                            <span>1%</span>
                        </div>
                    </div>
                </div>
                <p>سيتم إضافة التقييمات قريباً...</p>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
<section class="related-products">
    <div class="container">
        <h2 class="section-title">منتجات مشابهة</h2>
        <div class="products-grid">
            <?php
            $related_products = $conn->query("
                SELECT p.*, c.category as brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id != {$product['id']} 
                AND p.category_id = {$product['category_id']} 
                AND p.status = 1 
                ORDER BY RAND() 
                LIMIT 4
            ");
            
            if($related_products && $related_products->num_rows > 0):
                while($related = $related_products->fetch_assoc()):
            ?>
            <div class="product-card" onclick="viewProduct('<?= md5($related['id']) ?>')">
                <div class="product-card-image">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div class="product-card-info">
                    <h4 class="product-card-title"><?= htmlspecialchars($related['product_name']) ?></h4>
                    <div class="product-card-price">
                        <?php
                        $related_price = $conn->query("SELECT price FROM inventory WHERE product_id = {$related['id']} LIMIT 1");
                        if($related_price && $related_price->num_rows > 0) {
                            $price = $related_price->fetch_assoc()['price'];
                            echo TextCleaner::formatPrice($price);
                        } else {
                            echo 'السعر عند الطلب';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php endwhile; endif; ?>
        </div>
    </div>
</section>

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

function zoomImage() {
    const mainImage = document.getElementById('mainImage');
    // Implement zoom functionality
    window.open(mainImage.src, '_blank');
}

function increaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    quantityInput.value = parseInt(quantityInput.value) + 1;
}

function decreaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    if(parseInt(quantityInput.value) > 1) {
        quantityInput.value = parseInt(quantityInput.value) - 1;
    }
}

function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab content
    document.getElementById(tabName).classList.add('active');
    
    // Add active class to clicked button
    event.target.classList.add('active');
}

function addToCart(productId) {
    const quantity = document.getElementById('quantity').value;
    
    if(window.addToCart) {
        // استخدام الدالة العامة مع زر وهمي
        const fakeButton = {
            disabled: false,
            innerHTML: 'إضافة للسلة',
            style: {}
        };
        window.addToCart(fakeButton, productId);
    } else {
        // Fallback
        alert('تم إضافة المنتج إلى السلة!');
    }
}

function buyNow(productId) {
    // Redirect to checkout with this product
    addToCart(productId);
    setTimeout(() => {
        window.location.href = 'cart.php';
    }, 500);
}

function addToWishlist(productId) {
    // Implement wishlist functionality
    if(window.GayarPlus && window.GayarPlus.showSuccessNotification) {
        window.GayarPlus.showSuccessNotification('تم إضافة المنتج إلى المفضلة!');
    } else {
        alert('تم إضافة المنتج إلى المفضلة!');
    }
}

function viewProduct(productId) {
    window.location.href = `./?p=view_product&id=${productId}`;
}
</script>

<?php include 'inc/modern-footer.php'; ?>