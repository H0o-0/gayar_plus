<?php
$pageTitle = 'الصفحة الرئيسية';
require_once 'config.php';
require_once 'classes/TextCleaner.php';
include 'inc/header.php';
?>

<!-- Pattern Background -->
<div class="pattern-background"></div>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-container">
        <div class="hero-content">
            <h1 class="hero-title">
                متجرك الأول 
                <span class="hero-highlight">للملحقات الذكية</span>
            </h1>
            <p class="hero-subtitle">
                اكتشف مجموعة متميزة من ملحقات الهواتف عالية الجودة مع ضمان الأصالة وأفضل الأسعار في العراق
            </p>
            <div class="hero-cta">
                <a href="#products" class="btn-primary">تسوق الآن</a>
                <a href="./?p=about" class="btn-secondary">تعرف علينا</a>
            </div>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number">
                        <?php
                        $product_count = $conn->query("SELECT COUNT(*) as count FROM products WHERE status = 1")->fetch_assoc()['count'];
                        echo number_format($product_count) . '+';
                        ?>
                    </span>
                    <span class="stat-label">منتج متنوع</span>
                </div>
                <div class="stat">
                    <span class="stat-number">50K+</span>
                    <span class="stat-label">عميل راضي</span>
                </div>
                <div class="stat">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">دعم فني</span>
                </div>
            </div>
        </div>
        
        <div class="hero-visual">
            <div class="hero-card">
                <h3 style="margin-bottom: 1rem;">جودة مضمونة</h3>
                <p>منتجات أصلية 100%</p>
            </div>
            <div class="hero-card">
                <i class="fas fa-shipping-fast" style="font-size: 2rem; color: var(--primary-blue); margin-bottom: 1rem;"></i>
                <h4>توصيل سريع</h4>
                <p>خلال 24 ساعة</p>
            </div>
            <div class="hero-card">
                <h4 style="margin-bottom: 0.5rem;">ضمان شامل</h4>
                <p>حتى سنة كاملة</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section" id="products">
    <div class="section-header">
        <h2 class="section-title">أحدث المنتجات</h2>
        <p class="section-subtitle">اكتشف أحدث الملحقات والمنتجات التي وصلت حديثاً إلى متجرنا</p>
    </div>
    
    <div class="products-grid" id="main-products-grid">
        <?php
        // Simplified query to check products
        $products_query = "
            SELECT 
                p.id,
                p.product_name,
                p.description,
                p.date_created,
                c.category as category_name,
                sc.sub_category as sub_category_name
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id 
            WHERE p.status = 1
            ORDER BY p.date_created DESC 
            LIMIT 12
        ";
        
        $featured_products = $conn->query($products_query);
        
        if($featured_products && $featured_products->num_rows > 0):
            while($product = $featured_products->fetch_assoc()):
                // Get inventory data separately with better error handling
                $inventory_query = "SELECT price, quantity FROM inventory WHERE product_id = " . intval($product['id']) . " LIMIT 1";
                $inventory_result = $conn->query($inventory_query);
                
                if($inventory_result && $inventory_result->num_rows > 0) {
                    $inventory = $inventory_result->fetch_assoc();
                    $product['price'] = $inventory['price'] > 0 ? $inventory['price'] : 50000;
                    $product['stock_quantity'] = $inventory['quantity'] > 0 ? $inventory['quantity'] : 1;
                } else {
                    // Set default values if no inventory found
                    $product['price'] = 50000; // Default price of 50,000 IQD
                    $product['stock_quantity'] = 1; // Set to 1 to make products available
                }
                
                // Get product images with proper error handling
                $upload_path = 'uploads/product_'.$product['id'];
                $main_image = null;
                $fallback_image = './assets/images/no-image.svg';
                
                if(is_dir($upload_path)){
                    $files = scandir($upload_path);
                    foreach($files as $file) {
                        if(!in_array($file, ['.', '..']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
                            $main_image = $upload_path.'/'.$file;
                            break;
                        }
                    }
                }
                
                // Use fallback if no image found
                $display_image = $main_image ? validate_image($main_image) : './assets/images/no-image.svg';
                
                // Format price safely
                $formatted_price = $product['price'] ? number_format($product['price']) . ' IQD' : 'السعر غير محدد';
                
                // Clean description - إزالة جميع الأكواد HTML مع الحفاظ على النص العربي
                $short_description = TextCleaner::cleanAndTruncate($product['description'], 80);
        ?>
        <div class="modern-product-card" data-product-id="<?= $product['id'] ?>">
            <div class="card-image-container">
                <img src="<?= $display_image ?>" 
                     alt="<?= htmlspecialchars($product['product_name']) ?>" 
                     class="product-image"
                     loading="lazy"
                     onerror="if(this.src.indexOf('no-image.svg') === -1) this.src='./assets/images/no-image.svg';">
                <div class="product-overlay">
                    <button class="quick-view-btn" onclick="viewProduct(<?= $product['id'] ?>)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <?php if($product['stock_quantity'] > 0): ?>
                <span class="stock-badge in-stock">متوفر</span>
                <?php else: ?>
                <span class="stock-badge out-of-stock">نفذ المخزون</span>
                <?php endif; ?>
            </div>
            
            <div class="card-content">
                <div class="product-category">
                    <?= $product['category_name'] ? htmlspecialchars($product['category_name']) : 'منتجات عامة' ?>
                </div>
                
                <h3 class="product-name">
                    <?= htmlspecialchars($product['product_name']) ?>
                </h3>
                
                <p class="product-desc">
                    <?= htmlspecialchars($short_description) ?>
                </p>
                
                <div class="product-meta">
                    <span class="product-price"><?= $formatted_price ?></span>
                    <span class="stock-info">
                        <i class="fas fa-box"></i> 
                        <?= $product['stock_quantity'] ?> قطعة
                    </span>
                </div>
                
                <div class="card-actions">
                    <?php if($product['stock_quantity'] > 0): ?>
                    <button class="add-to-cart-btn" 
                            onclick="addToCart(this, <?= $product['id'] ?>)"
                            data-product-id="<?= $product['id'] ?>"
                            data-product-name="<?= htmlspecialchars($product['product_name']) ?>">
                        <i class="fas fa-cart-plus"></i>
                        <span>أضف للسلة</span>
                    </button>
                    <?php else: ?>
                    <button class="add-to-cart-btn disabled" disabled>
                        <i class="fas fa-ban"></i>
                        <span>غير متوفر</span>
                    </button>
                    <?php endif; ?>
                    
                    <button class="view-details-btn" onclick="viewProduct(<?= $product['id'] ?>)">
                        <i class="fas fa-info-circle"></i>
                        تفاصيل
                    </button>
                </div>
            </div>
        </div>
        <?php 
            endwhile; 
        else:
        ?>
        <div class="no-products-message">
            <i class="fas fa-box-open"></i>
            <h3>لا توجد منتجات متاحة حالياً</h3>
            <p>نعمل على إضافة منتجات جديدة قريباً</p>
        </div>
        <?php endif; ?>
    </div>

</section>

<!-- Features Section -->
<section class="section">
    <div class="section-header">
        <h2 class="section-title">لماذا Gayar Plus؟</h2>
        <p class="section-subtitle">نحن نفخر بتقديم تجربة تسوق استثنائية تجمع بين الجودة والأمان والراحة</p>
    </div>
    
    <div class="features-grid">
        <div class="feature-card will-change">
            <div class="feature-icon">
                <i class="fas fa-shield-check"></i>
            </div>
            <h3 class="feature-title">ضمان الأصالة</h3>
            <p class="feature-description">جميع منتجاتنا أصلية 100% ومستوردة من مصادر موثقة مع ضمان شامل يصل إلى سنة كاملة</p>
        </div>

        <div class="feature-card will-change">
            <div class="feature-icon">
                <i class="fas fa-rocket"></i>
            </div>
            <h3 class="feature-title">توصيل فائق السرعة</h3>
            <p class="feature-description">نوصل طلبك في نفس اليوم داخل بغداد وخلال 24-48 ساعة لجميع المحافظات العراقية</p>
        </div>

        <div class="feature-card will-change">
            <div class="feature-icon">
                <i class="fas fa-headset"></i>
            </div>
            <h3 class="feature-title">دعم فني متخصص</h3>
            <p class="feature-description">فريق دعم فني محترف متاح على مدار الساعة لحل جميع استفساراتك ومساعدتك</p>
        </div>

        <div class="feature-card will-change">
            <div class="feature-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h3 class="feature-title">أمان المعاملات</h3>
            <p class="feature-description">نظام دفع آمن ومشفر مع حماية كاملة لبياناتك الشخصية والمالية</p>
        </div>
    </div>
</section>

<!-- Modern Footer Only -->

<!-- Working Cart System -->
<script>
// Cart variables
var cartCount = 0;

// Show notifications
function showNotification(message, type) {
    type = type || 'success';
    document.querySelectorAll('.cart-notification').forEach(function(n) { n.remove(); });
    
    var notification = document.createElement('div');
    notification.className = 'cart-notification';
    notification.style.cssText = 
        'position: fixed; top: 80px; right: 20px; z-index: 10000;' +
        'background: ' + (type === 'success' ? '#10b981' : '#ef4444') + ';' +
        'color: white; padding: 1rem 1.5rem; border-radius: 12px;' +
        'box-shadow: 0 10px 30px rgba(0,0,0,0.2); font-weight: 600;' +
        'max-width: 300px; font-size: 14px;';
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(function() { notification.remove(); }, 3000);
}

// Add to cart function
function addToCart(button, productId) {
    console.log('🛒 Adding product:', productId);
    
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
            cartCount = data.cart_count;
            updateCartDisplay(cartCount);
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

// View product details
function viewProduct(productId) {
    if (productId) {
        window.location.href = './?p=product_view_redirect&id=' + productId;
    }
}

// Update cart display
function updateCartDisplay(count) {
    var cartElements = document.querySelectorAll('.cart-count, .cart-badge, #cart-count');
    cartElements.forEach(function(element) {
        if (element) {
            element.textContent = count;
        }
    });
}

// Load cart count
function loadCartCount() {
    fetch('ajax/get_cart_count.php')
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            cartCount = data.count;
            updateCartDisplay(cartCount);
            console.log('📊 عدد عناصر السلة:', cartCount);
        }
    })
    .catch(function(error) {
        console.log('لا يمكن تحديث العدد:', error);
    });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 تحميل نظام السلة...');
    
    loadCartCount();
    
    var cartButtons = document.querySelectorAll('.cart-btn, .shopping-cart-btn');
    cartButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = './?p=cart';
        });
    });
    
    var addButtons = document.querySelectorAll('.add-to-cart-btn');
    console.log('✅ وُجد ' + addButtons.length + ' زر إضافة للسلة');
    
    console.log('🎉 تم تحميل النظام بنجاح!');
});

// Export functions globally
window.addToCart = addToCart;
window.viewProduct = viewProduct;
window.showNotification = showNotification;
</script>

<style>
/* ضمان ظهور الفوتر */
.footer {
    display: block !important;
    visibility: visible !important;
    position: relative !important;
    z-index: 1 !important;
    opacity: 1 !important;
}

/* إخفاء الفوتر القديم فقط */
.techstore-footer,
.old-footer {
    display: none !important;
}

/* إخفاء عناصر غير مرغوب فيها فقط */
.btn-circle,
.floating-btn {
    display: none !important;
}

/* Modern Product Cards Styles */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    padding: 2rem 0;
    max-width: 1200px;
    margin: 0 auto;
}

.modern-product-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #f0f0f0;
    position: relative;
}

.modern-product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    border-color: #e0e0e0;
}

.card-image-container {
    position: relative;
    height: 220px;
    overflow: hidden;
    background: #f8f9fa;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.modern-product-card:hover .product-image {
    transform: scale(1.05);
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.modern-product-card:hover .product-overlay {
    opacity: 1;
}

.quick-view-btn {
    background: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s ease;
    color: #333;
    font-size: 18px;
}

.quick-view-btn:hover {
    transform: scale(1.1);
}

.stock-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stock-badge.in-stock {
    background: #10b981;
    color: white;
}

.stock-badge.out-of-stock {
    background: #ef4444;
    color: white;
}

.card-content {
    padding: 1.5rem;
}

.product-category {
    color: #6b7280;
    font-size: 13px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.product-name {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 0.75rem 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-desc {
    color: #6b7280;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f3f4f6;
}

.product-price {
    font-size: 20px;
    font-weight: 800;
    color: #059669;
}

.stock-info {
    color: #6b7280;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.card-actions {
    display: flex;
    gap: 0.75rem;
}

.add-to-cart-btn {
    flex: 2;
    background: linear-gradient(135deg, #059669, #10b981);
    color: white;
    border: none;
    padding: 12px 16px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.add-to-cart-btn:hover:not(.disabled) {
    background: linear-gradient(135deg, #047857, #059669);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3);
}

.add-to-cart-btn.disabled {
    background: #9ca3af;
    cursor: not-allowed;
    opacity: 0.6;
}

.view-details-btn {
    flex: 1;
    background: white;
    color: #374151;
    border: 2px solid #e5e7eb;
    padding: 12px 16px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.view-details-btn:hover {
    border-color: #059669;
    color: #059669;
    background: #f0fdf4;
}

.no-products-message {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
}

.no-products-message i {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: #d1d5db;
}

.no-products-message h3 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: #374151;
}

/* تأثيرات الإشعارات */
.cart-notification {
    animation: slideInFromRight 0.3s ease-out;
}

/* Fix for devices dropdown menu */
.nav-devices .mega-menu {
    display: none !important;
}

.nav-devices.show .mega-menu {
    display: block !important;
}

@keyframes slideInFromRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        padding: 1rem;
    }
    
    .card-content {
        padding: 1rem;
    }
    
    .card-actions {
        flex-direction: column;
    }
    
    .view-details-btn {
        flex: auto;
    }
    
    .cart-notification {
        right: 10px;
        max-width: calc(100vw - 20px);
        font-size: 13px;
    }
}
</style>

<?php include 'inc/modern-footer.php'; ?>
