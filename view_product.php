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

// Get product images
$images = [];
$image_dir = "uploads/product_" . $product['id'];

// First check if product has image field set
if(!empty($product['image'])) {
    if(file_exists($product['image'])) {
        $images[] = $product['image'];
    }
}

// Then check uploads directory
if(is_dir($image_dir)) {
    $files = scandir($image_dir);
    foreach($files as $file) {
        if(!in_array($file, ['.', '..']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
            $full_path = $image_dir . '/' . $file;
            if(!in_array($full_path, $images)) {
                $images[] = $full_path;
            }
        }
    }
}

// If no images found, use default
if(empty($images)) {
    $images[] = 'assets/images/no-image.svg';
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
                <div class="image-thumbnails" style="display: flex; gap: 8px; margin-top: 12px; overflow-x: auto;">
                    <?php foreach($images as $index => $image): ?>
                    <div class="thumbnail <?= $index === 0 ? 'active' : '' ?>" onclick="changeMainImage('<?= validate_image($image) ?>', this)" style="width: 60px; height: 60px; border-radius: 8px; overflow: hidden; cursor: pointer; border: 2px solid <?= $index === 0 ? '#3b82f6' : 'transparent' ?>; transition: border-color 0.3s;">
                        <img src="<?= validate_image($image) ?>" alt="صورة المنتج" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Product Info -->
            <div class="product-info">
                <div class="product-badges">
                    <?php if(isset($product['featured']) && $product['featured'] == 1): ?>
                    <span class="badge badge-bestseller">الأكثر مبيعاً</span>
                    <?php endif; ?>
                </div>

                <h1 class="product-title"><?= htmlspecialchars($product['product_name']) ?></h1>


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
                <div class="product-features" style="padding: 24px; margin-top: 24px;">
                    <h3 class="features-title" style="color: #1e293b; font-size: 1.125rem; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-star" style="color: #f59e0b;"></i>
                        المميزات الرئيسية
                    </h3>
                    <div class="features-list" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                        <div class="feature-item" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <div class="feature-icon" style="width: 32px; height: 32px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-check" style="color: white; font-size: 14px;"></i>
                            </div>
                            <span style="color: #374151; font-weight: 500;">جودة عالية ومضمونة</span>
                        </div>
                        <div class="feature-item" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <div class="feature-icon" style="width: 32px; height: 32px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-shipping-fast" style="color: white; font-size: 14px;"></i>
                            </div>
                            <span style="color: #374151; font-weight: 500;">شحن مجاني لجميع أنحاء العراق</span>
                        </div>
                        <div class="feature-item" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <div class="feature-icon" style="width: 32px; height: 32px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-shield-alt" style="color: white; font-size: 14px;"></i>
                            </div>
                            <span style="color: #374151; font-weight: 500;">ضمان شامل لمدة سنة</span>
                        </div>
                        <div class="feature-item" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <div class="feature-icon" style="width: 32px; height: 32px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-headset" style="color: white; font-size: 14px;"></i>
                            </div>
                            <span style="color: #374151; font-weight: 500;">خدمة عملاء 24/7</span>
                        </div>
                    </div>
                </div>
            </div>
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
    
    fetch('ajax/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=' + quantity
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount();
            showNotification('✅ تم إضافة المنتج للسلة!');
        } else {
            showNotification('❌ ' + (data.message || 'فشل في إضافة المنتج'), 'error');
        }
    })
    .catch(error => {
        console.error('خطأ:', error);
        showNotification('❌ حدث خطأ في الاتصال', 'error');
    });
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
    window.location.href = `view_product.php?id=${productId}`;
}
</script>

<!-- Related Products Section -->
<?php
// Get related products (same brand or category)
$related_products = [];
if($product_id) {
    $related_query = "SELECT p.*, c.category as brand_name 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     WHERE p.status = 1 AND p.id != ? 
                     AND (p.category_id = ? OR p.sub_category_id = ?) 
                     ORDER BY RAND() 
                     LIMIT 4";
    
    $related_stmt = $conn->prepare($related_query);
    $related_stmt->bind_param("iii", $product['id'], $product['category_id'], $product['sub_category_id']);
    $related_stmt->execute();
    $related_result = $related_stmt->get_result();
    
    while($row = $related_result->fetch_assoc()) {
        $related_products[] = $row;
    }
}
?>

<?php if(!empty($related_products)): ?>
<div class="related-products" style="margin-top: 4rem; padding: 2rem 0;">
    <div class="container">
        <h2 class="section-title" style="text-align: center; font-size: 1.5rem; font-weight: bold; margin-bottom: 2rem;">منتجات ذات صلة</h2>
        <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; max-width: 1200px; margin: 0 auto;">
            <?php foreach($related_products as $related): ?>
                <?php
                // Get related product images
                $related_images = [];
                $related_image_dir = "uploads/product_" . $related['id'];
                
                if (is_dir($related_image_dir)) {
                    $files = scandir($related_image_dir);
                    foreach ($files as $file) {
                        if ($file != '.' && $file != '..' && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            $related_images[] = $related_image_dir . '/' . $file;
                        }
                    }
                }
                
                if (empty($related_images)) {
                    $related_images[] = 'dist/img/no-image-available.svg';
                }
                
                // Get price
                $related_price_stmt = $conn->prepare("SELECT price FROM inventory WHERE product_id = ? LIMIT 1");
                $related_price_stmt->bind_param("i", $related['id']);
                $related_price_stmt->execute();
                $related_price_result = $related_price_stmt->get_result();
                
                $formatted_price = "السعر عند الطلب";
                if($related_price_result && $related_price_result->num_rows > 0) {
                    $related_price_data = $related_price_result->fetch_assoc();
                    if($related_price_data['price'] > 0) {
                        $formatted_price = number_format($related_price_data['price'], 0, '.', ',') . " د.ع";
                    }
                }
                ?>
                <div class="modern-product-card" onclick="window.location.href='view_product.php?id=<?= md5($related['id']) ?>'" style="background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); overflow: hidden; cursor: pointer; transition: all 0.3s ease;">
                    <div class="card-image-container" style="height: 200px; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                        <img src="<?= validate_image($related_images[0]) ?>" alt="<?= htmlspecialchars($related['product_name']) ?>" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php if($related['date_created'] >= date('Y-m-d', strtotime('-30 days'))): ?>
                            <div class="new-badge" style="position: absolute; top: 12px; right: 12px; background: #10b981; color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">جديد</div>
                        <?php endif; ?>
                    </div>
                    <div class="card-content" style="padding: 20px;">
                        <div class="product-category" style="color: #3b82f6; font-size: 0.875rem; font-weight: 600; margin-bottom: 8px;">
                            <?= isset($related['brand_name']) ? htmlspecialchars($related['brand_name']) : 'ملحقات' ?>
                        </div>
                        <h3 class="product-name" style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin-bottom: 8px; line-height: 1.4;"><?= htmlspecialchars($related['product_name']) ?></h3>
                        <p class="product-description" style="color: #64748b; font-size: 0.875rem; line-height: 1.5; margin-bottom: 16px;"><?= mb_substr(strip_tags(html_entity_decode($related['description'])), 0, 100) ?>...</p>
                        <div class="card-actions" style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                            <div class="product-price" style="font-size: 1.125rem; font-weight: 700; color: #059669;"><?= $formatted_price ?></div>
                            <button class="add-to-cart-btn" onclick="event.stopPropagation(); addToCartRelated(this, <?= $related['id'] ?>)" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 6px;">
                                <i class="fas fa-cart-plus"></i>
                                أضف للسلة
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
// Add to cart function for related products
function addToCartRelated(button, productId) {
    console.log('🛒 Adding related product:', productId);
    
    if (button.disabled) return;
    
    button.disabled = true;
    var originalHTML = button.innerHTML;
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
            updateCartCount();
            showNotification('✅ تم إضافة المنتج للسلة!');
            
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

function updateCartCount() {
    fetch('ajax/get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartBadges = document.querySelectorAll('.cart-count, .cart-badge, #cart-count, #mobile-cart-count');
                cartBadges.forEach(badge => {
                    if (badge) {
                        badge.textContent = data.count;
                        badge.style.display = data.count > 0 ? 'flex' : 'none';
                    }
                });
            }
        })
        .catch(error => console.error('Error updating cart count:', error));
}

function showNotification(message, type = 'success') {
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = message;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'error' ? '#ef4444' : '#10b981'};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        font-weight: 500;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>
<?php endif; ?>

<?php include 'inc/modern-footer.php'; ?>