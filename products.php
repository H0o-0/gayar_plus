<?php
// Modern Products Page
require_once 'config.php';
require_once 'classes/TextCleaner.php';

$pageTitle = "منتجاتنا - Gayar Plus";
$title = "الملحقات الأكثر طلباً";
$sub_title = "اكتشف مجموعة متميزة من ملحقات الهواتف عالية الجودة";

// Handle brand filtering
$brand_id = null;
$brand_data = null;
if(isset($_GET['b'])){
    $brand_param = $_GET['b'];
    
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
    }
}

include 'inc/header.php'
?>

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
                
                if($brand_id) {
                    $whereData = " AND p.brand_id = ?";
                    $params = [$brand_id];
                }
                
                // Execute the main query
                $sql = "SELECT p.*, b.name as brand_name, p.image as image 
                        FROM products p 
                        LEFT JOIN brands b ON p.brand_id = b.id 
                        WHERE p.status = 1 {$whereData} 
                        ORDER BY p.featured DESC, p.id DESC";

                if(!empty($params)) {
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $params[0]);
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
            <div class="product-card" onclick="window.location.href='./?p=product_view&id=<?= md5($row['id']) ?>'">
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

/* Products Page Styles */
.products-hero {
    padding: 8rem 0 4rem;
    background: linear-gradient(135deg, var(--light-gray) 0%, var(--white) 100%);
    text-align: center;
}

.hero-title {
    font-size: 3rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 1rem;
    letter-spacing: -0.025em;
}

.hero-subtitle {
    font-size: 1.25rem;
    color: var(--text-secondary);
    max-width: 700px;
    margin: 0 auto 2rem;
    line-height: 1.6;
}

.products-container {
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 2rem;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
    gap: 3rem;
    margin-top: 3rem;
    margin-bottom: 4rem;
}

.product-card {
    background: var(--white);
    border-radius: 24px;
    border: 1px solid var(--medium-gray);
    overflow: hidden;
    transition: var(--transition);
    cursor: pointer;
    position: relative;
    display: flex;
    flex-direction: column;
    height: auto;
    min-height: 580px;
    box-shadow: var(--shadow-sm);
    transform: translateZ(0);
}

.product-card:hover {
    transform: translateY(-12px) translateZ(0);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-blue);
}

.product-image {
    height: 320px;
    background: var(--light-gray);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.4s ease;
}

.product-image i {
    font-size: 6rem;
    color: var(--primary-blue);
    opacity: 0.4;
    transition: all 0.4s ease;
}

.product-card:hover .product-image img,
.product-card:hover .product-image i {
    transform: scale(1.08);
}

.product-badge {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    background: linear-gradient(135deg, var(--accent-green), #059669);
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 700;
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    z-index: 10;
}

.product-info {
    padding: 2.5rem;
    display: flex;
    flex-direction: column;
    flex: 1;
    justify-content: space-between;
    gap: 1rem;
}

.product-category {
    font-size: 0.9rem;
    color: var(--text-secondary);
    font-weight: 600;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.4rem 1rem;
    background: #f0f7ff;
    border-radius: 20px;
    display: inline-block;
    width: fit-content;
    border: 1px solid #dbeafe;
}

.product-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1.2rem;
    line-height: 1.4;
    min-height: 4rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-description {
    color: var(--text-secondary);
    margin-bottom: 2rem;
    line-height: 1.7;
    font-size: 1.05rem;
    min-height: 5.6rem;
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
    padding-top: 1.5rem;
    border-top: 2px solid #f1f5f9;
    gap: 1rem;
}

.product-price {
    font-size: 2rem;
    font-weight: 800;
    color: var(--primary-navy);
    background: linear-gradient(135deg, var(--primary-blue), var(--primary-navy));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    flex-shrink: 0;
}

.add-to-cart {
    background: linear-gradient(135deg, var(--primary-blue), var(--primary-navy));
    color: white;
    border: none;
    padding: 1.2rem 2rem;
    border-radius: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 1rem;
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    white-space: nowrap;
}

.add-to-cart:hover {
    background: linear-gradient(135deg, var(--primary-navy), #1e3a8a);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5);
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
