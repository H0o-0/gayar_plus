<?php
// Modern Products Page
require_once 'config.php';
require_once 'classes/TextCleaner.php';

$pageTitle = "منتجاتنا - Gayar Plus";
$title = "الملحقات الأكثر طلباً";
$sub_title = "اكتشف مجموعة متميزة من ملحقات الهواتف عالية الجودة";

// Handle filtering - FIXED VERSION with model support
$brand_id = null;
$brand_data = null;
$series_id = null;
$series_data = null;
$model_id = null;
$model_data = null;

// Debug what we receive
error_log("GET parameters: " . print_r($_GET, true));

// Handle model filtering first (highest priority)
if(isset($_GET['m']) || isset($_GET['model'])){
    $model_param = isset($_GET['m']) ? $_GET['m'] : $_GET['model'];
    error_log("Model parameter received: " . $model_param);
    
    if (is_numeric($model_param)) {
        $model_id = intval($model_param);
        $model_qry = $conn->query("SELECT pm.id, pm.model_name as name, pm.sub_category_id as series_id, s.name as series_name, b.name as brand_name, b.id as brand_id 
                                  FROM phone_models pm 
                                  LEFT JOIN series s ON pm.sub_category_id = s.id 
                                  LEFT JOIN brands b ON s.brand_id = b.id 
                                  WHERE pm.id = {$model_id} AND pm.status = 1");
    } else {
        $model_qry = $conn->query("SELECT pm.id, pm.model_name as name, pm.sub_category_id as series_id, s.name as series_name, b.name as brand_name, b.id as brand_id 
                                  FROM phone_models pm 
                                  LEFT JOIN series s ON pm.sub_category_id = s.id 
                                  LEFT JOIN brands b ON s.brand_id = b.id 
                                  WHERE md5(pm.id) = '{$model_param}' AND pm.status = 1");
    }
    
    if($model_qry && $model_qry->num_rows > 0){
        $model_data = $model_qry->fetch_assoc();
        $model_id = $model_data['id'];
        $brand_id = $model_data['brand_id'];
        $series_id = $model_data['series_id'];
        $title = "منتجات " . htmlspecialchars($model_data['name']);
        $sub_title = "جميع المنتجات المتوافقة مع " . htmlspecialchars($model_data['name']);
        $pageTitle = htmlspecialchars($model_data['name']) . " - Gayar Plus";
        error_log("Model found: ID = $model_id, Name = " . $model_data['name']);
    }
}

// Handle series filtering
if(isset($_GET['s']) && !$model_id){
    $series_param = $_GET['s'];
    error_log("Series parameter received: " . $series_param);
    
    if (is_numeric($series_param)) {
        $series_id = intval($series_param);
        $series_qry = $conn->query("SELECT s.*, b.name as brand_name, b.id as brand_id FROM series s LEFT JOIN brands b ON s.brand_id = b.id WHERE s.id = {$series_id} AND s.status = 1");
    } else {
        $series_qry = $conn->query("SELECT s.*, b.name as brand_name, b.id as brand_id FROM series s LEFT JOIN brands b ON s.brand_id = b.id WHERE md5(s.id) = '{$series_param}' AND s.status = 1");
    }
    
    if($series_qry && $series_qry->num_rows > 0){
        $series_data = $series_qry->fetch_assoc();
        $series_id = $series_data['id'];
        $brand_id = $series_data['brand_id'];
        $title = "منتجات " . htmlspecialchars($series_data['name']);
        $sub_title = "جميع منتجات سلسلة " . htmlspecialchars($series_data['name']);
        $pageTitle = htmlspecialchars($series_data['name']) . " - Gayar Plus";
        error_log("Series found: ID = $series_id, Name = " . $series_data['name']);
    }
}

// Handle brand filtering
if((isset($_GET['b']) || isset($_GET['brand'])) && !$model_id && !$series_id){
    $brand_param = isset($_GET['b']) ? $_GET['b'] : $_GET['brand'];
    error_log("Brand parameter received: " . $brand_param);
    
    if (is_numeric($brand_param)) {
        $brand_id = intval($brand_param);
        $brand_qry = $conn->query("SELECT * FROM brands WHERE id = {$brand_id} AND status = 1");
    } else {
        $brand_qry = $conn->query("SELECT * FROM brands WHERE md5(id) = '{$brand_param}' AND status = 1");
    }
    
    if($brand_qry && $brand_qry->num_rows > 0){
        $brand_data = $brand_qry->fetch_assoc();
        $brand_id = $brand_data['id'];
        $title = htmlspecialchars($brand_data['name']);
        $sub_title = "جميع منتجات " . htmlspecialchars($brand_data['name']);
        $pageTitle = htmlspecialchars($brand_data['name']) . " - Gayar Plus";
        error_log("Brand found: ID = $brand_id, Name = " . $brand_data['name']);
    } else {
        error_log("Brand not found for parameter: " . $brand_param);
    }
} else if(!$model_id && !$series_id) {
    error_log("No filtering parameters received");
}

include 'inc/header.php'
?>

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
                <h1 class="clean-title">🛍️ الملحقات الأكثر طلباً</h1>
                <p class="clean-subtitle">✨ اكتشف مجموعة متميزة من ملحقات الهواتف عالية الجودة</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="section">
    <div class="products-container">
        <div class="products-grid">
            <?php 
                // Build WHERE clause for filtering with priority: model > series > brand
                $whereData = "";
                $params = [];
                
                if($model_id) {
                    // Filter by model_id - highest priority
                    $whereData = " AND p.model_id = ?";
                    $params = [$model_id];
                    error_log("Filtering by model_id: $model_id");
                } elseif($series_id) {
                    // Filter by sub_category_id (series)
                    $whereData = " AND p.sub_category_id = ?";
                    $params = [$series_id];
                    error_log("Filtering by sub_category_id (series): $series_id");
                } elseif($brand_id) {
                    // Filter by category_id (brand)
                    $whereData = " AND p.category_id = ?";
                    $params = [$brand_id];
                    error_log("Filtering by category_id (brand): $brand_id");
                } else {
                    error_log("No filtering - showing all products");
                }
                
                // Execute the main query
                $sql = "SELECT p.*, b.name as brand_name, s.name as series_name, i.price 
                        FROM products p 
                        LEFT JOIN brands b ON p.category_id = b.id 
                        LEFT JOIN series s ON p.sub_category_id = s.id
                        LEFT JOIN inventory i ON p.id = i.product_id
                        WHERE p.status = 1 {$whereData} 
                        ORDER BY p.id DESC";

                error_log("Final SQL: $sql");
                error_log("Parameters: " . print_r($params, true));

                if(!empty($params)) {
                    $stmt = $conn->prepare($sql);
                    if($stmt) {
                        $stmt->bind_param("i", $params[0]);
                        $stmt->execute();
                        $products = $stmt->get_result();
                    } else {
                        echo "خطأ في الاستعلام: " . $conn->error;
                        $products = false;
                    }
                } else {
                    $products = $conn->query($sql);
                }

                error_log("Products found: " . ($products ? $products->num_rows : 0));

                if($products && $products->num_rows > 0):
                    while($product = $products->fetch_assoc()):
                        // Get product images
                        $image_path = 'uploads/product_'.$product['id'];
                        $images = [];
                        if(is_dir($image_path)) {
                            $files = scandir($image_path);
                            foreach($files as $file) {
                                if(!in_array($file, ['.', '..'])) {
                                    $images[] = $image_path.'/'.$file;
                                }
                            }
                        }
                        
                        // Default image if no images found
                        if(empty($images)) {
                            $images[] = 'assets/images/no-image.svg';
                        }
                        
                        // Format price correctly
                        $formatted_price = "السعر عند الطلب";
                        if(isset($product['price']) && $product['price'] > 0) {
                            $formatted_price = number_format($product['price'], 0, '.', ',') . " د.ع";
                        }
                ?>
                    <div class="modern-product-card" onclick="window.location.href='view_product.php?id=<?= md5($product['id']) ?>'">
                        <div class="card-image-container">
                            <img src="<?= validate_image($images[0]) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" loading="lazy">
                            <?php if($product['date_created'] >= date('Y-m-d', strtotime('-30 days'))): ?>
                                <div class="new-badge">جديد</div>
                            <?php endif; ?>
                        </div>
                        <div class="card-content">
                            <div class="product-category">
                                <?= isset($product['brand_name']) ? htmlspecialchars($product['brand_name']) : 'ملحقات' ?>
                            </div>
                            <h3 class="product-name"><?= htmlspecialchars($product['product_name']) ?></h3>
                            <p class="product-description"><?= mb_substr(strip_tags(html_entity_decode($product['description'])), 0, 100) ?>...</p>
                            <div class="card-actions">
                                <div class="product-price"><?= $formatted_price ?></div>
                                <button class="add-to-cart-btn" onclick="event.stopPropagation(); addToCart(this, <?= $product['id'] ?>)">
                                    <i class="fas fa-cart-plus"></i>
                                    أضف للسلة
                                </button>
                            </div>
                        </div>
                    </div>
                <?php 
                    endwhile;
                else:
                ?>
                    <div class="no-products">
                        <i class="fas fa-box-open"></i>
                        <h3>لا توجد منتجات</h3>
                        <p>عذراً، لا توجد منتجات متاحة في هذا القسم حالياً</p>
                    </div>
                <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* CSS Variables for consistency */
:root {
    --primary-blue: #3b82f6;
    --primary-navy: #1e40af;
    --accent-green: #10b981;
    --light-gray: #f8fafc;
    --medium-gray: #e2e8f0;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --white: #ffffff;
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.15);
    --shadow-xl: 0 20px 50px rgba(0, 0, 0, 0.2);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Clean Hero Section Styles */
.clean-hero {
    padding: 3rem 0 2rem;
    background: linear-gradient(135deg, var(--light-gray) 0%, var(--white) 100%);
    text-align: center;
    border-bottom: 1px solid var(--medium-gray);
}

.clean-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    letter-spacing: -0.025em;
}

.clean-subtitle {
    font-size: 1rem;
    color: var(--text-secondary);
    margin: 0;
    font-weight: 500;
}

.products-container {
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 2rem;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
    margin-bottom: 3rem;
    max-width: 1400px;
    margin-left: auto;
    margin-right: auto;
    padding: 0 2rem;
}

.modern-product-card {
    background: var(--white);
    border-radius: 20px;
    border: 1px solid var(--medium-gray);
    overflow: hidden;
    transition: var(--transition);
    cursor: pointer;
    position: relative;
    display: flex;
    flex-direction: column;
    height: 450px;
    width: 100%;
    box-shadow: var(--shadow-sm);
    transform: translateZ(0);
}

.modern-product-card:hover {
    transform: translateY(-8px) translateZ(0);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-blue);
}

.card-image-container {
    height: 280px;
    background: #ffffff;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}

.card-image-container img {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.modern-product-card:hover .card-image-container img {
    transform: scale(1.03);
}

.new-badge {
    position: absolute;
    top: 0.8rem;
    right: 0.8rem;
    background: #10b981;
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    z-index: 5;
}

.card-content {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    flex: 1;
    justify-content: space-between;
}

.product-category {
    font-size: 0.75rem;
    color: #3b82f6;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.25rem 0.6rem;
    background: #f0f7ff;
    border-radius: 12px;
    display: inline-block;
    width: fit-content;
    margin-bottom: 0.8rem;
}

.product-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.8rem;
    line-height: 1.4;
    height: 2.8rem;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.product-description {
    color: #64748b;
    margin-bottom: 1.2rem;
    line-height: 1.5;
    font-size: 0.9rem;
    height: 2.7rem;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.card-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-top: auto;
}

.product-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e40af;
    flex-shrink: 0;
}

.add-to-cart-btn {
    background: linear-gradient(135deg, #3b82f6, #1e40af);
    color: white;
    border: none;
    padding: 0.6rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    font-size: 0.85rem;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    white-space: nowrap;
    min-width: 100px;
}

.add-to-cart-btn:hover {
    background: linear-gradient(135deg, #1e40af, #1e3a8a);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.add-to-cart-btn i {
    font-size: 0.8rem;
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
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        padding: 0 1rem;
    }
    
    .modern-product-card {
        height: 420px;
    }
    
    .card-image-container {
        height: 200px;
        padding: 1rem;
    }
    
    .card-content {
        padding: 1.2rem;
    }
    
    .card-actions {
        flex-direction: row;
        gap: 0.8rem;
        align-items: center;
    }
    
    .add-to-cart-btn {
        padding: 0.5rem 0.8rem;
        font-size: 0.8rem;
        min-width: 90px;
    }
    
    .product-price {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .clean-title {
        font-size: 1.3rem;
    }
    
    .products-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
        padding: 0 1rem;
    }
    
    .modern-product-card {
        height: 400px;
        max-width: 350px;
        margin: 0 auto;
    }
    
    .card-image-container {
        height: 180px;
    }
    
    .card-actions {
        flex-direction: column;
        gap: 0.8rem;
    }
    
    .add-to-cart-btn {
        width: 100%;
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
</script>

<?php include 'inc/modern-footer.php'; ?>
