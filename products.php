<?php
// Products Page - Shows products with filtering options
require_once 'config.php';
require_once 'classes/TextCleaner.php';

// Initialize page variables
$pageTitle = "المنتجات - Gayar Plus";
$title = "جميع المنتجات";
$sub_title = "اكتشف مجموعتنا الكاملة من ملحقات الهواتف والأجهزة الذكية";

// Handle brand filtering
$brand_id = null;
$brand_data = null;

if(isset($_GET['brand'])){
    $brand_param = $_GET['brand'];
    
    // Check if it's a direct ID
    if (is_numeric($brand_param)) {
        $brand_id = intval($brand_param);
        $brand_qry = $conn->query("SELECT * FROM brands WHERE id = {$brand_id} AND status = 1");
    } elseif (base64_decode($brand_param, true) !== false && is_numeric(base64_decode($brand_param, true))) {
        // Handle base64 encoded ID
        $brand_id = intval(base64_decode($brand_param, true));
        $brand_qry = $conn->query("SELECT * FROM brands WHERE id = {$brand_id} AND status = 1");
    } else {
        // Handle MD5 hash
        $brand_qry = $conn->query("SELECT * FROM brands WHERE md5(id) = '{$brand_param}' AND status = 1");
    }
    
    if($brand_qry && $brand_qry->num_rows > 0){
        $brand_data = $brand_qry->fetch_assoc();
        $brand_id = $brand_data['id'];
        $brand_name = !empty($brand_data['name_ar']) ? $brand_data['name_ar'] : $brand_data['name'];
        $title = "منتجات " . $brand_name;
        $sub_title = "جميع المنتجات المتوفرة من " . $brand_name;
    }
}

// Handle search
$search_term = null;
if(isset($_GET['search']) && !empty($_GET['search'])){
    $search_term = $_GET['search'];
    $title = "نتائج البحث";
    $sub_title = "البحث عن: " . htmlspecialchars($search_term);
}

// منع تضمين topBarNav.php لأنه مُضمن في header.php
// include 'inc/topBarNav.php';

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
            <?php if(isset($brand_data)): ?>
            <a href="./?p=products&brand=<?= $brand_data['id'] ?>"><?= htmlspecialchars(!empty($brand_data['name_ar']) ? $brand_data['name_ar'] : $brand_data['name']) ?></a>
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
            
            <?php if(isset($_GET['search'])): ?>
            <div class="search-badge">
                <i class="fas fa-search"></i>
                <span>نتائج البحث عن: "<?= htmlspecialchars($_GET['search']) ?>"</span>
            </div>
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
                
                if($brand_id) {
                    $whereData = " AND p.brand_id = ?";
                    $params = [$brand_id];
                } elseif($search_term) {
                    $whereData = " AND (p.product_name LIKE ? OR p.description LIKE ?)";
                    $params = ["%{$search_term}%", "%{$search_term}%"];
                }
                
                // Execute the main query
                $sql = "SELECT p.*, 
                               c.category as category_name, 
                               sc.sub_category as sub_category_name,
                               b.name as brand_name, 
                               b.name_ar as brand_name_ar
                        FROM products p 
                        LEFT JOIN categories c ON p.category_id = c.id 
                        LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id 
                        LEFT JOIN brands b ON p.brand_id = b.id 
                        WHERE p.status = 1 {$whereData} 
                        ORDER BY p.date_created DESC";
                
                if(!empty($params)) {
                    $stmt = $conn->prepare($sql);
                    if($search_term) {
                        $stmt->bind_param("ss", $params[0], $params[1]);
                    } else {
                        $stmt->bind_param("i", $params[0]);
                    }
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
            ?>
            <div class="product-card will-change" onclick="window.location.href='./?p=product_view&id=<?= md5($row['id']) ?>'">
                <div class="product-image">
                    <?php if(!empty($images)): ?>
                        <img src="<?= validate_image($images[0]) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" loading="lazy">
                    <?php else: ?>
                        <i class="fas fa-mobile-alt"></i>
                    <?php endif; ?>
                    
                    <?php if(isset($row['featured']) && $row['featured'] == 1): ?>
                    <div class="product-badge">الأكثر مبيعاً</div>
                    <?php endif; ?>
                </div>
                
                <div class="product-info">
                    <div class="product-category">
                        <?php
                        // عرض الفئة والفئة الفرعية بدلاً من البراند
                        $category_display = '';
                        if (isset($row['sub_category_name']) && !empty($row['sub_category_name'])) {
                            $category_display = htmlspecialchars($row['sub_category_name']);
                        } elseif (isset($row['category_name']) && !empty($row['category_name'])) {
                            $category_display = htmlspecialchars($row['category_name']);
                        } elseif (isset($row['brand_name_ar']) && $row['brand_name_ar']) {
                            $category_display = htmlspecialchars($row['brand_name_ar']);
                        } elseif (isset($row['brand_name']) && $row['brand_name']) {
                            $category_display = htmlspecialchars($row['brand_name']);
                        } else {
                            $category_display = 'ملحقات';
                        }
                        echo $category_display;
                        ?>
                    </div>
                    <h3 class="product-title"><?= htmlspecialchars($row['product_name']) ?></h3>
                    <p class="product-description"><?= TextCleaner::cleanAndTruncate($row['description'], 100) ?></p>
                    
                    <?php if(isset($row['has_colors']) && $row['has_colors'] == 1 && !empty($row['colors'])): ?>
                    <?php 
                        $colors = json_decode($row['colors'], true);
                        if(is_array($colors) && count($colors) > 0):
                    ?>
                    <div class="product-colors">
                        <div class="color-dots">
                            <?php foreach(array_slice($colors, 0, 4) as $color): ?>
                            <div class="color-dot" 
                                 style="background-color: <?= htmlspecialchars($color['code']) ?>"
                                 title="<?= htmlspecialchars($color['name']) ?>"></div>
                            <?php endforeach; ?>
                            <?php if(count($colors) > 4): ?>
                            <span class="more-colors">+<?= count($colors) - 4 ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <div class="product-actions">
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

<style>
/* Products Page Styles */
.pattern-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%);
    z-index: -1;
}

.breadcrumb {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 1rem 0;
    margin-top: 80px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.breadcrumb-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.breadcrumb-nav {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
}

.breadcrumb-nav a {
    color: #667eea;
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb-nav a:hover {
    color: #764ba2;
}

.breadcrumb-separator {
    margin: 0 0.5rem;
    color: #a0a0a0;
    font-size: 0.8rem;
}

.breadcrumb-current {
    color: #2d3748;
    font-weight: 600;
}

.products-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4rem 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.products-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="white" opacity="0.1"/></svg>');
    background-size: 30px 30px;
    animation: float 20s linear infinite;
}

@keyframes float {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

.hero-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    position: relative;
    z-index: 1;
}

.hero-title {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.hero-subtitle {
    font-size: 1.3rem;
    opacity: 0.9;
    margin-bottom: 2rem;
    font-weight: 300;
}

.search-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.2);
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    backdrop-filter: blur(10px);
    font-weight: 500;
}

.products-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 4rem 1rem;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
}

.product-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    border: 1px solid rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    border-color: rgba(102, 126, 234, 0.2);
}

.product-image {
    height: 240px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.product-image img {
    max-width: 85%;
    max-height: 85%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-image i {
    font-size: 3rem;
    color: #e2e8f0;
}

.product-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: linear-gradient(135deg, #ff6b6b, #ff5722);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.product-info {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    flex: 1;
    gap: 0.75rem;
}

.product-category {
    color: #667eea;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2d3748;
    line-height: 1.4;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-description {
    color: #718096;
    font-size: 0.9rem;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin: 0;
    flex: 1;
}

.product-colors {
    margin: 0.5rem 0;
}

.color-dots {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.color-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
    cursor: pointer;
    flex-shrink: 0;
}

.color-dot:hover {
    transform: scale(1.3);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2), 0 0 0 2px rgba(59, 130, 246, 0.3);
    z-index: 2;
    position: relative;
}

.more-colors {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 600;
    margin-left: 0.5rem;
    background: #f3f4f6;
    padding: 0.125rem 0.375rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.product-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    gap: 1rem;
}

.product-price {
    font-size: 1.2rem;
    font-weight: 700;
    color: #667eea;
}

.add-to-cart {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 0.6rem 1.2rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    white-space: nowrap;
}

.add-to-cart:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.no-products {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
}

.no-products-content {
    background: white;
    border-radius: 20px;
    padding: 3rem 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    max-width: 500px;
    margin: 0 auto;
}

.no-products-content i {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1.5rem;
}

.no-products-content h3 {
    font-size: 1.5rem;
    color: #374151;
    margin-bottom: 1rem;
}

.no-products-content p {
    color: #6b7280;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    text-decoration: none;
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .product-info {
        padding: 1.25rem;
    }
    
    .product-actions {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .add-to-cart {
        justify-content: center;
    }
}
</style>

<script>
// Products page functionality
function addToCart(button, productId) {
    if(window.addToCart) {
        window.addToCart(button, productId);
    } else {
        // Fallback notification
        console.log('Adding product to cart:', productId);
        
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
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count
                var cartElements = document.querySelectorAll('.cart-count, .cart-badge, #cart-count');
                cartElements.forEach(function(element) {
                    if (element) {
                        element.textContent = data.cart_count;
                    }
                });
                
                // Show success
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
        .catch(error => {
            console.error('خطأ:', error);
            button.innerHTML = originalHTML;
            button.disabled = false;
        });
    }
}

// Initialize device menu for products page
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for the navigation to load, then initialize
    setTimeout(function() {
        if (typeof window.initDeviceMenu === 'function') {
            window.initDeviceMenu();
            console.log('✅ Device menu manually initialized for products page');
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