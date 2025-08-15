<!-- CSS للتصميم الجديد -->
<style>
    :root {
        --primary-color: #2c5aa0;
        --secondary-color: #f8f9fa;
        --accent-color: #ff6b6b;
    }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, rgba(44,90,160,0.9) 0%, rgba(30,61,114,0.9) 100%),
                   url('<?php echo base_url.$_settings->info('cover') ?>');
        background-size: cover;
        background-position: center;
        padding: 120px 0;
        color: white;
        text-align: center;
        margin-top: 0;
    }

    .hero-content h1 {
        font-size: 3.5rem;
        font-weight: bold;
        margin-bottom: 1.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .hero-content p {
        font-size: 1.3rem;
        margin-bottom: 2rem;
        opacity: 0.9;
    }

    .btn-hero {
        background: var(--accent-color);
        border: none;
        padding: 15px 40px;
        font-size: 1.1rem;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(255,107,107,0.3);
        color: white;
    }

    .btn-hero:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(255,107,107,0.4);
        color: white;
    }

    /* Enhanced Product Cards - تصميم عصري واحترافي */
    .product-card {
        border: none;
        border-radius: 24px;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 8px 32px rgba(0,0,0,0.06);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        height: 100%;
        position: relative;
        backdrop-filter: blur(10px);
        animation: cardFadeIn 0.6s ease-out forwards;
        opacity: 0;
        transform: translateY(30px);
    }

    @keyframes cardFadeIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .product-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 20px 60px rgba(44,90,160,0.15);
    }

    .product-image {
        position: relative;
        overflow: hidden;
        height: 280px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        filter: brightness(1.05);
    }

    .product-card:hover .product-image img {
        transform: scale(1.08);
        filter: brightness(1.1) contrast(1.05);
    }

    .sale-badge {
        position: absolute;
        top: 16px;
        right: 16px;
        background: linear-gradient(135deg, var(--accent-color) 0%, #ff5252 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        box-shadow: 0 4px 15px rgba(255,107,107,0.4);
        z-index: 2;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .product-info {
        padding: 1.8rem 1.5rem 1.5rem;
        text-align: center;
        position: relative;
        background: white;
    }

    .product-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.8rem;
        line-height: 1.3;
        transition: color 0.3s ease;
    }

    .product-card:hover .product-title {
        color: var(--primary-color);
    }

    .product-description {
        color: #718096;
        font-size: 0.9rem;
        margin-bottom: 1.2rem;
        line-height: 1.5;
        font-weight: 400;
    }

    .product-price {
        font-size: 1.4rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary-color) 0%, #1e3d72 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1.5rem;
        text-shadow: 0 2px 4px rgba(44,90,160,0.1);
    }

    /* أزرار التفاعل المخفية */
    .product-actions {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(255,255,255,0.98) 0%, rgba(255,255,255,0.9) 100%);
        padding: 1.5rem;
        transform: translateY(100%);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        backdrop-filter: blur(10px);
    }

    .product-card:hover .product-actions {
        transform: translateY(0);
    }

    .btn-view, .btn-cart {
        border: none;
        border-radius: 16px;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 12px 20px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
    }

    .btn-view {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
        margin-bottom: 0.8rem;
        width: 100%;
    }

    .btn-view:hover {
        background: linear-gradient(135deg, #495057 0%, #343a40 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(108,117,125,0.3);
        color: white;
    }

    .btn-cart {
        background: linear-gradient(135deg, var(--primary-color) 0%, #1e3d72 100%);
        color: white;
        width: 100%;
    }

    .btn-cart:hover {
        background: linear-gradient(135deg, #1e3d72 0%, #0f1419 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(44,90,160,0.4);
        color: white;
    }

    /* تأثير الموجة عند الضغط */
    .btn-view::before, .btn-cart::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255,255,255,0.3);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-view:active::before, .btn-cart:active::before {
        width: 300px;
        height: 300px;
    }

    /* تصميم الألوان في الكاردات - مبسط وأنيق */
    .product-colors {
        margin-bottom: 0.8rem;
        padding: 0.5rem 0;
    }

    .colors-list {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-start;
    }

    .color-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .color-dot:hover {
        transform: scale(1.3);
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        border-color: #333;
    }

    .more-colors-text {
        font-size: 0.7rem;
        color: var(--primary-color);
        font-weight: 500;
        margin-left: 5px;
    }

    @media (max-width: 768px) {
        .hero-content h1 {
            font-size: 2.5rem;
        }

        .hero-content p {
            font-size: 1.1rem;
        }

        .hero-section {
            padding: 80px 0;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1>متجرك المتخصص في الأجهزة الذكية</h1>
            <p>نوفر لك أحدث الأجهزة والإكسسوارات بأفضل الأسعار وأعلى جودة</p>
            <button class="btn btn-hero btn-lg" onclick="window.location.href='./?p=products'">
                تسوق الآن
            </button>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="py-5" style="margin-top: 2rem;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold" style="color: var(--primary-color);">منتجاتنا المميزة</h2>
            <p class="lead text-muted">اختر من بين مجموعة واسعة من الأجهزة والإكسسوارات عالية الجودة</p>
        </div>
        <div class="row g-4">
            <?php
                $products = $conn->query("SELECT * FROM `products` where status = 1 order by rand() limit 8 ");
                while($row = $products->fetch_assoc()):
                    $upload_path = base_app.'/uploads/product_'.$row['id'];
                    $img = "";
                    if(is_dir($upload_path)){
                        $fileO = scandir($upload_path);
                        if(isset($fileO[2]))
                            $img = "uploads/product_".$row['id']."/".$fileO[2];
                    }
                    $inventory = $conn->query("SELECT * FROM inventory where product_id = ".$row['id']);
                    $inv = array();
                    while($ir = $inventory->fetch_assoc()){
                        $inv[$ir['size']] = $ir;
                    }

                    // الحصول على الألوان إذا كانت متوفرة
                    $colors = array();
                    if(isset($row['has_colors']) && $row['has_colors'] == 1) {
                        $colors_qry = $conn->query("SELECT * FROM product_colors where product_id = ".$row['id']);
                        if($colors_qry) {
                            while($color_row = $colors_qry->fetch_assoc()){
                                $colors[] = array(
                                    'name' => $color_row['color_name'],
                                    'code' => $color_row['color_code']
                                );
                            }
                        }
                    }
            ?>
            <div class="col-lg-3 col-md-6 mb-4" style="animation-delay: <?php echo ($products->num_rows % 4) * 0.1 ?>s;">
                <div class="card product-card">
                    <div class="product-image">
                        <?php if(!empty($colors) || count($inv) > 1): ?>
                        <div class="sale-badge">
                            <?php echo !empty($colors) ? 'ألوان متعددة' : 'عرض خاص' ?>
                        </div>
                        <?php endif; ?>
                        <img src="<?php echo validate_image($img) ?>" alt="<?php echo $row['product_name'] ?>" loading="lazy" />
                    </div>

                    <div class="product-info">
                        <h5 class="product-title"><?php echo $row['product_name'] ?></h5>
                        <p class="product-description">
                            <?php
                            $desc = strip_tags($row['description']);
                            echo strlen($desc) > 60 ? substr($desc, 0, 60) . '...' : $desc;
                            ?>
                        </p>

                        <!-- عرض الألوان إذا كانت متوفرة -->
                        <?php if(!empty($colors)): ?>
                        <div class="product-colors">
                            <div class="colors-list">
                                <?php foreach(array_slice($colors, 0, 3) as $color): ?>
                                <span class="color-dot" style="background: <?php echo $color['code'] ?>;" title="<?php echo $color['name'] ?>"></span>
                                <?php endforeach; ?>
                                <?php if(count($colors) > 3): ?>
                                <span class="more-colors-text">+<?php echo count($colors) - 3 ?> ألوان</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="product-price">
                            <?php
                            if(!empty($inv)) {
                                $first_inv = reset($inv);
                                if(isset($first_inv['price']) && $first_inv['price'] > 0) {
                                    echo number_format($first_inv['price']) . ' د.ع';
                                } else {
                                    echo 'السعر عند الطلب';
                                }
                            } else {
                                echo 'السعر عند الطلب';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- أزرار التفاعل المخفية -->
                    <div class="product-actions">
                        <button class="btn btn-view" onclick="viewProduct('<?php echo md5($row['id']) ?>')">
                            <i class="fas fa-eye me-2"></i>
                            عرض التفاصيل
                        </button>
                        <?php if(!empty($inv)): ?>
                        <button class="btn btn-cart" onclick="addToCart('<?php echo $row['id'] ?>', '<?php echo array_keys($inv)[0] ?>')">
                            <i class="fas fa-shopping-cart me-2"></i>
                            أضف للسلة
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<script>
// View product function
function viewProduct(productId) {
    window.location.href = './?p=view_product&id=' + productId;
}

// Add to cart function
function addToCart(productId, inventoryId) {
    // فحص تسجيل الدخول
    <?php if(!isset($_SESSION['userdata']['id'])): ?>
    uni_modal("","login.php");
    return false;
    <?php else: ?>

    start_loader();

    // الحصول على بيانات المخزون
    fetch('./', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_inventory&product_id=' + productId + '&size=' + inventoryId
    })
    .then(response => response.json())
    .then(inventoryData => {
        if(inventoryData.success) {
            // إضافة المنتج للسلة
            return fetch('classes/Master.php?f=add_to_cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'inventory_id=' + inventoryData.inventory_id + '&quantity=1&price=' + inventoryData.price
            });
        } else {
            throw new Error('فشل في الحصول على بيانات المنتج');
        }
    })
    .then(response => response.json())
    .then(data => {
        end_loader();
        if(data.status == 'success') {
            updateCartBadge();
            showNotification('تم إضافة المنتج للسلة بنجاح!');
        } else {
            alert('حدث خطأ أثناء إضافة المنتج للسلة');
        }
    })
    .catch(error => {
        end_loader();
        console.error('Error:', error);
        alert('حدث خطأ أثناء إضافة المنتج للسلة');
    });

    <?php endif; ?>
}

// Update cart badge
function updateCartBadge() {
    const cartBadge = document.querySelector('#cart-count');
    if (cartBadge) {
        fetch('classes/Master.php?f=get_cart_count')
        .then(response => response.json())
        .then(data => {
            if(data.count !== undefined) {
                cartBadge.textContent = data.count;
            }
        });
    }
}

// Show notification
function showNotification(message) {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--primary-color);
        color: white;
        padding: 15px 25px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        z-index: 9999;
        font-weight: 500;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Load cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartBadge();
});
</script>