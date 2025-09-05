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
        $sub_title = "جميع المنتجات المرتبطة بسلسلة " . htmlspecialchars($series_data['name']);
        if(isset($brand_data)) {
            $title = htmlspecialchars($brand_data['name']) . " - " . htmlspecialchars($series_data['name']);
        } else {
            $title = "سلسلة " . htmlspecialchars($series_data['name']);
        }
        $pageTitle = "سلسلة " . htmlspecialchars($series_data['name']) . " - Gayar Plus";
    }
}

// Handle model filtering - support both 'model' and 'm' parameters
if(isset($_GET['model']) || isset($_GET['m'])){
    $model_param = isset($_GET['m']) ? $_GET['m'] : $_GET['model'];
    
    // Check if it's a direct ID
    if (is_numeric($model_param)) {
        $model_id = intval($model_param);
        // البحث في جدول phone_models مع ربطه بجدول categories بدلاً من series
        $model_qry = $conn->query("SELECT pm.id, pm.model_name as name, pm.sub_category_id as series_id, c.category as series_name, b.name as brand_name, b.id as brand_id 
                                  FROM phone_models pm 
                                  LEFT JOIN categories c ON pm.sub_category_id = c.id 
                                  LEFT JOIN brands b ON c.brand_id = b.id 
                                  WHERE pm.id = {$model_id} AND pm.status = 1");
    } elseif (base64_decode($model_param, true) !== false && is_numeric(base64_decode($model_param, true))) {
        $model_id = intval(base64_decode($model_param, true));
        $model_qry = $conn->query("SELECT pm.id, pm.model_name as name, pm.sub_category_id as series_id, c.category as series_name, b.name as brand_name, b.id as brand_id 
                                  FROM phone_models pm 
                                  LEFT JOIN categories c ON pm.sub_category_id = c.id 
                                  LEFT JOIN brands b ON c.brand_id = b.id 
                                  WHERE pm.id = {$model_id} AND pm.status = 1");
    } else {
        // Handle MD5 hash
        $model_qry = $conn->query("SELECT pm.id, pm.model_name as name, pm.sub_category_id as series_id, c.category as series_name, b.name as brand_name, b.id as brand_id 
                                  FROM phone_models pm 
                                  LEFT JOIN categories c ON pm.sub_category_id = c.id 
                                  LEFT JOIN brands b ON c.brand_id = b.id 
                                  WHERE md5(pm.id) = '{$model_param}' AND pm.status = 1");
    }
    
    if($model_qry && $model_qry->num_rows > 0){
        $model_data = $model_qry->fetch_assoc();
        $model_id = $model_data['id'];
        $model_name = $model_data['name'];
        
        // جلب بيانات السلسلة والعلامة التجارية من بيانات الموديل
        if($model_data['series_id'] && !$series_data) {
            $series_id = $model_data['series_id'];
            $series_qry = $conn->query("SELECT * FROM categories WHERE id = {$series_id}");
            if($series_qry && $series_qry->num_rows > 0) {
                $series_data = $series_qry->fetch_assoc();
            }
        }
        
        if($model_data['brand_id'] && !$brand_data) {
            $brand_id = $model_data['brand_id'];
            $brand_qry = $conn->query("SELECT * FROM brands WHERE id = {$brand_id}");
            if($brand_qry && $brand_qry->num_rows > 0) {
                $brand_data = $brand_qry->fetch_assoc();
            }
        }
        
        $sub_title = "جميع المنتجات المرتبطة بموديل " . htmlspecialchars($model_name);
        
        if($brand_data && $series_data) {
            $brand_name = $brand_data['name'];
            $series_name = isset($series_data['category']) ? $series_data['category'] : $series_data['name'];
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

<!-- Custom CSS for Device Products Page -->
<style>
.device-product-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #f1f5f9;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.device-product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.device-product-card .card-image-container {
    height: 200px;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.device-product-card .card-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.device-product-card:hover .card-image-container img {
    transform: scale(1.05);
}

.device-product-card .no-image-placeholder {
    width: 80px;
    height: 80px;
    background: #e2e8f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 2rem;
}

.device-product-card .product-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.device-product-card .product-content {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.device-product-card .product-header {
    cursor: pointer;
    flex: 1;
}

.device-product-card .product-category {
    color: #3b82f6;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.device-product-card .product-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 8px 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.device-product-card .product-description {
    color: #64748b;
    font-size: 0.875rem;
    line-height: 1.5;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.device-product-card .product-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
    margin-top: auto;
}

.device-product-card .product-price {
    font-size: 1.125rem;
    font-weight: 700;
    color: #059669;
    flex: 1;
}

.device-product-card .add-to-cart-btn {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.device-product-card .add-to-cart-btn:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.device-product-card .add-to-cart-btn:active {
    transform: translateY(0);
}

/* Grid Layout */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 24px;
    padding: 20px 0;
}

/* Clean Hero Section Styles */
.clean-hero {
    padding: 3rem 0 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    text-align: center;
    border-bottom: 1px solid #e2e8f0;
}

.clean-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
    letter-spacing: -0.025em;
}

.clean-subtitle {
    font-size: 1rem;
    color: #64748b;
    margin: 0;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .clean-hero {
        padding: 2rem 0 1.5rem;
    }
    
    .clean-title {
        font-size: 1.5rem;
    }
    
    .clean-subtitle {
        font-size: 0.9rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }
    
    .device-product-card .product-content {
        padding: 16px;
    }
    
    .device-product-card .add-to-cart-btn {
        padding: 8px 12px;
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    .clean-title {
        font-size: 1.3rem;
    }
    
    .products-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
}
</style>

<!-- Clean Hero Section -->
<section class="clean-hero">
    <div class="hero-container">
        <div class="hero-content">
            <?php if(isset($model_data)): ?>
                <h1 class="clean-title">📱 <?= htmlspecialchars($model_data['name']) ?></h1>
                <p class="clean-subtitle">🛍️ ملحقات متوافقة مع جهازك</p>
            <?php elseif(isset($series_data)): ?>
                <h1 class="clean-title">📱 <?= htmlspecialchars($series_data['name']) ?></h1>
                <p class="clean-subtitle">🛍️ ملحقات السلسلة</p>
            <?php elseif(isset($brand_data)): ?>
                <h1 class="clean-title">🏷️ <?= htmlspecialchars($brand_data['name']) ?></h1>
                <p class="clean-subtitle">🛍️ جميع منتجات العلامة التجارية</p>
            <?php else: ?>
                <h1 class="clean-title">🛍️ الملحقات</h1>
                <p class="clean-subtitle">✨ اكتشف مجموعة متميزة من الملحقات</p>
            <?php endif; ?>
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
                
                // Execute the main query - البحث في المنتجات حسب الموديل فقط
                if($model_id) {
                    // البحث في المنتجات للموديل المحدد فقط - بدون fallback
                    $sql = "SELECT p.*, b.name as brand_name, s.name as series_name 
                            FROM products p 
                            LEFT JOIN brands b ON p.category_id = b.id 
                            LEFT JOIN series s ON p.sub_category_id = s.id 
                            WHERE p.status = 1 AND p.model_id = {$model_id}
                            ORDER BY p.date_created DESC";
                } elseif($series_id) {
                    $sql = "SELECT p.*, b.name as brand_name, s.name as series_name 
                            FROM products p 
                            LEFT JOIN brands b ON p.category_id = b.id 
                            LEFT JOIN series s ON p.sub_category_id = s.id 
                            WHERE p.status = 1 AND p.sub_category_id = {$series_id}
                            ORDER BY p.date_created DESC";
                } elseif($brand_id) {
                    $sql = "SELECT p.*, b.name as brand_name, s.name as series_name 
                            FROM products p 
                            LEFT JOIN brands b ON p.category_id = b.id 
                            LEFT JOIN series s ON p.sub_category_id = s.id 
                            WHERE p.status = 1 AND p.category_id = {$brand_id}
                            ORDER BY p.date_created DESC";
                } else {
                    $sql = "SELECT p.*, b.name as brand_name, s.name as series_name 
                            FROM products p 
                            LEFT JOIN brands b ON p.category_id = b.id 
                            LEFT JOIN series s ON p.sub_category_id = s.id 
                            WHERE p.status = 1 
                            ORDER BY p.date_created DESC";
                }

                $products = $conn->query($sql);

                if($products && $products->num_rows > 0):
                    while($row = $products->fetch_assoc()): 
                        // Use image from database if available, otherwise check uploads
                        $image_path = isset($row['image']) && $row['image'] ? $row['image'] : 'uploads/product_'.$row['id'];
                        $images = [];
                        if(is_dir($image_path) && (!isset($row['image']) || !$row['image'])) {
                            $files = scandir($image_path);
                            foreach($files as $file) {
                                if(!in_array($file, ['.', '..'])) {
                                    $images[] = $image_path.'/'.$file;
                                }
                            }
                        } elseif (isset($row['image']) && $row['image']) {
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
            <div class="device-product-card">
                <div class="card-image-container" onclick="window.location.href='./?p=product_view&id=<?= md5($row['id']) ?>'">
                    <?php if(!empty($images)): ?>
                        <img src="<?= validate_image($images[0]) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" loading="lazy">
                    <?php else: ?>
                        <div class="no-image-placeholder">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($featured == 1): ?>
                    <div class="product-badge">الأكثر مبيعاً</div>
                    <?php endif; ?>
                </div>
                
                <div class="product-content">
                    <div class="product-header" onclick="window.location.href='./?p=product_view&id=<?= md5($row['id']) ?>'">
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
                        <p class="product-description"><?= TextCleaner::cleanAndTruncate($row['description'], 80) ?></p>
                    </div>
                    
                    <div class="product-actions">
                        <div class="product-price">
                            <?php if($price_info && $price_info['price'] > 0): ?>
                                <?= TextCleaner::formatPrice($price_info['price']) ?>
                            <?php else: ?>
                                السعر عند الطلب
                            <?php endif; ?>
                        </div>
                        
                        <button class="add-to-cart-btn" onclick="event.stopPropagation(); addToCart(this, <?= $row['id'] ?>)">
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
                    <h3>لا توجد منتجات متاحة</h3>
                    <p>
                        <?php if($model_id): ?>
                            لم يتم العثور على منتجات منشورة لموديل <?= htmlspecialchars($model_data['name'] ?? 'المحدد') ?>
                        <?php else: ?>
                            لم نتمكن من العثور على منتجات تطابق معايير بحثك
                        <?php endif; ?>
                    </p>
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

<script>
// Add to cart functionality for device products page
function addToCart(button, productId) {
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإضافة...';
    button.disabled = true;
    
    fetch('ajax/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&qty=1`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success === true) {
            showNotification('تم إضافة المنتج إلى السلة بنجاح!', 'success');
            updateCartCount();
        } else {
            showNotification(data.message || 'حدث خطأ أثناء إضافة المنتج', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ في الاتصال', 'error');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Update cart count
function updateCartCount() {
    fetch('ajax/get_cart_count.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update all possible cart count elements
            const cartElements = document.querySelectorAll('#cart-count, .cart-count, .cart-badge, #mobile-cart-count');
            cartElements.forEach(element => {
                if (element) {
                    element.textContent = data.count;
                    element.style.display = data.count > 0 ? 'block' : 'none';
                }
            });
        }
    })
    .catch(error => console.error('Error updating cart count:', error));
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${type === 'success' ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #ef4444, #dc2626)'};
        color: white;
        padding: 1rem 2rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        z-index: 9999;
        transform: translateX(100%);
        transition: all 0.3s ease;
        font-weight: 600;
        max-width: 300px;
        font-size: 0.9rem;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);

    // Show notification
    requestAnimationFrame(() => {
        notification.style.transform = 'translateX(0)';
    });

    // Hide notification
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Load cart count on page load
    updateCartCount();
    
    // Initialize device menu if available
    setTimeout(function() {
        if (typeof window.initDeviceMenu === 'function') {
            window.initDeviceMenu();
            console.log('✅ Device menu manually initialized for device products page');
        }
    }, 500);
});
</script>
<?php include 'inc/modern-footer.php'; ?>
