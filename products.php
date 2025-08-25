<?php
// Modern Products Page with beautiful theme design
require_once 'config.php';
require_once 'classes/TextCleaner.php';

// Initialize page variables
$pageTitle = "منتجاتنا - Gayar Plus";
$title = "الملحقات الأكثر طلباً";
$sub_title = "اكتشف مجموعة متميزة من ملحقات الهواتف عالية الجودة";

// Function to get ID from either MD5 hash or direct ID
function get_id_from_param($param) {
    if (empty($param)) {
        return null;
    }
    
    // If it's a number, it's a direct ID
    if (is_numeric($param)) {
        return intval($param);
    }
    
    // If it's an MD5 hash, decode it
    if (strlen($param) == 24 && ctype_alnum($param)) { // MD5 hash is 32 chars, but base64 encoded it's 24
        // Try to decode as base64 first
        $decoded = base64_decode($param, true);
        if ($decoded !== false && is_numeric($decoded)) {
            return intval($decoded);
        }
    }
    
    // Try to decode as MD5 hash
    if (strlen($param) == 32 && ctype_xdigit($param)) {
        // We can't reverse MD5, so we need to query the database
        return $param; // Return the hash to be used in query
    }
    
    return null;
}

// Handle brand filtering (support both MD5 and direct ID)
$brand_id = null;
$brand_data = null;
if(isset($_GET['b'])){
    $brand_param = $_GET['b'];
    
    // Check if it's a direct ID
    if (is_numeric($brand_param)) {
        $brand_id = intval($brand_param);
        $brand_qry = $conn->query("SELECT * FROM brands where id = {$brand_id} AND status = 1");
    } else {
        // Handle MD5 hash
        $brand_qry = $conn->query("SELECT * FROM brands where md5(id) = '{$brand_param}' AND status = 1");
    }
    
    if($brand_qry && $brand_qry->num_rows > 0){
        $brand_data = $brand_qry->fetch_assoc();
        $brand_id = $brand_data['id'];
        $title = htmlspecialchars($brand_data['name']);
        $sub_title = "جميع منتجات " . htmlspecialchars($brand_data['name']);
        $pageTitle = htmlspecialchars($brand_data['name']) . " - Gayar Plus";
    }
}

// Handle series filtering (support both MD5 and direct ID)
$series_id = null;
$series_data = null;
if(isset($_GET['s'])){
    $series_param = $_GET['s'];
    
    // Check if it's a direct ID
    if (is_numeric($series_param)) {
        $series_id = intval($series_param);
        $series_qry = $conn->query("SELECT s.*, b.name as brand_name FROM series s LEFT JOIN brands b ON s.brand_id = b.id WHERE s.id = {$series_id} AND s.status = 1");
    } else {
        // Handle MD5 hash
        $series_qry = $conn->query("SELECT s.*, b.name as brand_name FROM series s LEFT JOIN brands b ON s.brand_id = b.id WHERE md5(s.id) = '{$series_param}' AND s.status = 1");
    }
    
    if($series_qry && $series_qry->num_rows > 0){
        $series_data = $series_qry->fetch_assoc();
        $series_id = $series_data['id'];
        $sub_title = htmlspecialchars($series_data['name']);
        if(isset($_GET['b']) && !empty($series_data['brand_name'])) {
            $title = htmlspecialchars($series_data['brand_name']) . " - " . htmlspecialchars($series_data['name']);
        } else {
            $title = htmlspecialchars($series_data['name']);
        }
        $pageTitle = htmlspecialchars($series_data['name']) . " - Gayar Plus";
    }
}

// Handle model filtering (support both MD5 and direct ID)
$model_id = null;
$model_data = null;
if(isset($_GET['m'])){
    $model_param = $_GET['m'];
    
    // Check if it's a direct ID
    if (is_numeric($model_param)) {
        $model_id = intval($model_param);
        $model_qry = $conn->query("SELECT m.*, s.name as series_name, b.name as brand_name FROM models m LEFT JOIN series s ON m.series_id = s.id LEFT JOIN brands b ON s.brand_id = b.id WHERE m.id = {$model_id} AND m.status = 1");
    } else {
        // Handle MD5 hash
        $model_qry = $conn->query("SELECT m.*, s.name as series_name, b.name as brand_name FROM models m LEFT JOIN series s ON m.series_id = s.id LEFT JOIN brands b ON s.brand_id = b.id WHERE md5(m.id) = '{$model_param}' AND m.status = 1");
    }
    
    if($model_qry && $model_qry->num_rows > 0){
        $model_data = $model_qry->fetch_assoc();
        $model_id = $model_data['id'];
        $sub_title = htmlspecialchars($model_data['name']);
        if(isset($_GET['s']) && !empty($model_data['series_name'])) {
            $title = htmlspecialchars($model_data['brand_name']) . " - " . htmlspecialchars($model_data['series_name']) . " - " . htmlspecialchars($model_data['name']);
        } else {
            $title = htmlspecialchars($model_data['name']);
        }
        $pageTitle = htmlspecialchars($model_data['name']) . " - Gayar Plus";
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
            <?php if(isset($_GET['b']) && isset($brand_data)): ?>
            <a href="./?p=products&b=<?= is_numeric($_GET['b']) ? md5($_GET['b']) : $_GET['b'] ?>"><?= htmlspecialchars($brand_data['name']) ?></a>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-left"></i></span>
            <?php endif; ?>
            <?php if(isset($_GET['s']) && isset($series_data)): ?>
            <a href="./?p=products&b=<?= $_GET['b'] ?? '' ?>&s=<?= is_numeric($_GET['s']) ? md5($_GET['s']) : $_GET['s'] ?>"><?= htmlspecialchars($series_data['name']) ?></a>
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
                
                if(isset($_GET['search']) && !empty($_GET['search'])) {
                    $search_term = TextCleaner::sanitize($_GET['search']);
                    $whereData = " and (p.product_name LIKE ? or p.description LIKE ?)";
                    $params[] = "%{$search_term}%";
                    $params[] = "%{$search_term}%";
                }
                elseif(isset($_GET['b']) && isset($_GET['s']) && isset($_GET['m'])) {
                    // Filter by brand, series, and model
                    // Handle both MD5 hashes and direct IDs
                    if (is_numeric($_GET['b'])) {
                        $brand_id = intval($_GET['b']);
                    } else {
                        $brand_result = $conn->query("SELECT id FROM brands WHERE md5(id) = '{$_GET['b']}' AND status = 1");
                        $brand_id = ($brand_result && $brand_result->num_rows > 0) ? $brand_result->fetch_assoc()['id'] : null;
                    }
                    
                    if (is_numeric($_GET['s'])) {
                        $series_id = intval($_GET['s']);
                    } else {
                        $series_result = $conn->query("SELECT id FROM series WHERE md5(id) = '{$_GET['s']}' AND status = 1");
                        $series_id = ($series_result && $series_result->num_rows > 0) ? $series_result->fetch_assoc()['id'] : null;
                    }
                    
                    if (is_numeric($_GET['m'])) {
                        $model_id = intval($_GET['m']);
                    } else {
                        $model_result = $conn->query("SELECT id FROM models WHERE md5(id) = '{$_GET['m']}' AND status = 1");
                        $model_id = ($model_result && $model_result->num_rows > 0) ? $model_result->fetch_assoc()['id'] : null;
                    }
                    
                    if($brand_id && $series_id && $model_id) {
                        $whereData = " and p.brand_id = ? and p.series_id = ? and p.model_id = ?";
                        $params = [$brand_id, $series_id, $model_id];
                    }
                }
                elseif(isset($_GET['b']) && isset($_GET['s'])) {
                    // Filter by brand and series
                    // Handle both MD5 hashes and direct IDs
                    if (is_numeric($_GET['b'])) {
                        $brand_id = intval($_GET['b']);
                    } else {
                        $brand_result = $conn->query("SELECT id FROM brands WHERE md5(id) = '{$_GET['b']}' AND status = 1");
                        $brand_id = ($brand_result && $brand_result->num_rows > 0) ? $brand_result->fetch_assoc()['id'] : null;
                    }
                    
                    if (is_numeric($_GET['s'])) {
                        $series_id = intval($_GET['s']);
                    } else {
                        $series_result = $conn->query("SELECT id FROM series WHERE md5(id) = '{$_GET['s']}' AND status = 1");
                        $series_id = ($series_result && $series_result->num_rows > 0) ? $series_result->fetch_assoc()['id'] : null;
                    }
                    
                    if($brand_id && $series_id) {
                        $whereData = " and p.brand_id = ? and p.series_id = ?";
                        $params = [$brand_id, $series_id];
                    }
                }
                elseif(isset($_GET['b'])) {
                    // Filter by brand only
                    // Handle both MD5 hashes and direct IDs
                    if (is_numeric($_GET['b'])) {
                        $brand_id = intval($_GET['b']);
                    } else {
                        $brand_result = $conn->query("SELECT id FROM brands WHERE md5(id) = '{$_GET['b']}' AND status = 1");
                        $brand_id = ($brand_result && $brand_result->num_rows > 0) ? $brand_result->fetch_assoc()['id'] : null;
                    }
                    
                    if($brand_id) {
                        $whereData = " and p.brand_id = ?";
                        $params = [$brand_id];
                    }
                }
                elseif(isset($_GET['s'])) {
                    // Filter by series only
                    // Handle both MD5 hashes and direct IDs
                    if (is_numeric($_GET['s'])) {
                        $series_id = intval($_GET['s']);
                    } else {
                        $series_result = $conn->query("SELECT id FROM series WHERE md5(id) = '{$_GET['s']}' AND status = 1");
                        $series_id = ($series_result && $series_result->num_rows > 0) ? $series_result->fetch_assoc()['id'] : null;
                    }
                    
                    if($series_id) {
                        $whereData = " and p.series_id = ?";
                        $params = [$series_id];
                    }
                }
                elseif(isset($_GET['m'])) {
                    // Filter by model only
                    // Handle both MD5 hashes and direct IDs
                    if (is_numeric($_GET['m'])) {
                        $model_id = intval($_GET['m']);
                    } else {
                        $model_result = $conn->query("SELECT id FROM models WHERE md5(id) = '{$_GET['m']}' AND status = 1");
                        $model_id = ($model_result && $model_result->num_rows > 0) ? $model_result->fetch_assoc()['id'] : null;
                    }
                    
                    if($model_id) {
                        $whereData = " and p.model_id = ?";
                        $params = [$model_id];
                    }
                }
                
                // Execute the main query
                $sql = "SELECT p.*, b.name as brand_name, s.name as series_name, m.name as model_name, p.image as image 
                        FROM products p 
                        LEFT JOIN brands b ON p.brand_id = b.id 
                        LEFT JOIN series s ON p.series_id = s.id 
                        LEFT JOIN models m ON p.model_id = m.id 
                        WHERE p.status = 1 {$whereData} 
                        ORDER BY p.featured DESC, p.id DESC";

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
            ?>
            <div class="product-card will-change" onclick="window.location.href='./?p=product_view&id=<?= md5($row['id']) ?>'">
                <div class="product-image">
                    <?php if(!empty($images)): ?>
                        <img src="<?= validate_image($images[0]) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" loading="lazy">
                    <?php else: ?>
                        <i class="fas fa-mobile-alt"></i>
                    <?php endif; ?>
                    
                    <?php if($row['featured'] == 1): ?>
                    <div class="product-badge">الأكثر مبيعاً</div>
                    <?php endif; ?>
                </div>
                
                <div class="product-info">
                    <div class="product-category"><?= $row['brand_name'] ? htmlspecialchars($row['brand_name']) : 'ملحقات' ?></div>
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

<style>
/* Products Page Specific Styles */
.products-hero {
    padding: 8rem 0 6rem;
    background: linear-gradient(135deg, var(--light-gray) 0%, var(--pure-white) 100%);
    text-align: center;
}

.hero-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 1rem;
    letter-spacing: -0.025em;
}

.hero-subtitle {
    font-size: 1.125rem;
    color: var(--text-secondary);
    max-width: 600px;
    margin: 0 auto 2rem;
}

.search-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, var(--primary-blue), var(--primary-navy));
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 20px;
    font-weight: 600;
    margin-top: 1rem;
}

.products-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2rem;
}

.product-card {
    background: var(--pure-white);
    border-radius: 24px;
    border: 1px solid var(--medium-gray);
    overflow: hidden;
    transition: var(--transition);
    cursor: pointer;
    position: relative;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-blue);
}

.product-image {
    height: 240px;
    background: var(--light-gray);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: var(--transition);
}

.product-image i {
    font-size: 4rem;
    color: var(--primary-blue);
    transition: var(--transition);
}

.product-card:hover .product-image img,
.product-card:hover .product-image i {
    transform: scale(1.1);
}

.product-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: var(--accent-emerald);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.product-info {
    padding: 2rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.product-category {
    font-size: 0.875rem;
    color: var(--text-light);
    font-weight: 500;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.product-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-description {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
    line-height: 1.6;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
}

.product-price {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--primary-navy);
}

.add-to-cart {
    background: var(--primary-blue);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.add-to-cart:hover {
    background: var(--primary-navy);
    transform: translateY(-2px);
}

.no-products {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    background: var(--light-gray);
    border-radius: 24px;
    border: 1px solid var(--medium-gray);
}

.no-products-content {
    max-width: 400px;
    margin: 0 auto;
}

.no-products i {
    font-size: 4rem;
    color: var(--primary-blue);
    margin-bottom: 2rem;
    opacity: 0.7;
}

.no-products h3 {
    color: var(--text-primary);
    margin-bottom: 1rem;
    font-size: 1.5rem;
    font-weight: 700;
}

.no-products p {
    color: var(--text-secondary);
    margin-bottom: 2rem;
    line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    
    .product-footer {
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
        showNotification('تم إضافة المنتج إلى السلة!', 'success');
    }
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${type === 'success' ? 'linear-gradient(135deg, var(--accent-emerald), #10b981)' : 'linear-gradient(135deg, #ef4444, #dc2626)'};
        color: white;
        padding: 1rem 2rem;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-xl);
        z-index: 9999;
        transform: translateX(100%);
        transition: var(--transition);
        font-weight: 600;
        max-width: 300px;
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

// Animate products on scroll
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
            }
        });
    }, observerOptions);

    // Observe product cards
    document.querySelectorAll('.product-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        observer.observe(card);
    });
});
</script>

<?php include 'inc/modern-footer.php'; ?>