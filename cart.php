<?php
$page_title = "سلة التسوق - Gayar Plus";
require_once 'config.php';
require_once 'classes/TextCleaner.php';

// Removed direct topBarNav.php include to prevent duplicate navbar
// include 'inc/topBarNav.php';

include 'inc/header.php';
?>

<style>
/* Cart Page Styles */
.cart-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.cart-header {
    text-align: center;
    margin-bottom: 2rem;
}

.cart-header h1 {
    font-size: 2rem;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.cart-content {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
}

@media (max-width: 768px) {
    .cart-content {
        grid-template-columns: 1fr;
    }
}

.cart-items {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--medium-gray);
}

.cart-item {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem 0;
    border-bottom: 1px solid var(--light-gray);
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item-image {
    width: 100px;
    height: 100px;
    border-radius: 12px;
    overflow: hidden;
    background: var(--light-gray);
    display: flex;
    align-items: center;
    justify-content: center;
}

.cart-item-image img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.cart-item-image i {
    font-size: 2rem;
    color: var(--text-light);
}

.cart-item-details {
    flex: 1;
}

.cart-item-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.cart-item-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--primary-blue);
    margin-bottom: 1rem;
}

.cart-item-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.quantity-btn {
    width: 36px;
    height: 36px;
    border: 1px solid var(--medium-gray);
    background: var(--light-gray);
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    transition: var(--transition);
}

.quantity-btn:hover {
    background: var(--primary-blue);
    color: white;
    border-color: var(--primary-blue);
}

.quantity-input {
    width: 50px;
    height: 36px;
    border: 1px solid var(--medium-gray);
    border-radius: 8px;
    text-align: center;
    font-size: 1rem;
}

.remove-item {
    background: none;
    border: none;
    color: var(--danger-red);
    cursor: pointer;
    font-size: 1.2rem;
    transition: var(--transition);
    padding: 0.5rem;
    border-radius: 8px;
}

.remove-item:hover {
    background: var(--light-gray);
}

.cart-summary {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--medium-gray);
    position: sticky;
    top: 100px;
}

.cart-summary h2 {
    font-size: 1.5rem;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--light-gray);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.summary-label {
    color: var(--text-secondary);
}

.summary-value {
    font-weight: 600;
    color: var(--text-primary);
}

.total-row {
    display: flex;
    justify-content: space-between;
    margin: 1.5rem 0;
    padding: 1rem 0;
    border-top: 1px solid var(--light-gray);
    border-bottom: 1px solid var(--light-gray);
    font-size: 1.25rem;
    font-weight: 700;
}

.total-label {
    color: var(--text-primary);
}

.total-value {
    color: var(--primary-blue);
}

.cart-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.btn-checkout {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-navy) 100%);
    color: white;
    border: none;
    padding: 1rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: var(--transition);
    text-align: center;
}

.btn-checkout:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-continue {
    background: white;
    color: var(--primary-blue);
    border: 2px solid var(--primary-blue);
    padding: 1rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: var(--transition);
    text-align: center;
}

.btn-continue:hover {
    background: var(--light-gray);
}

.empty-cart {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 16px;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--medium-gray);
}

.empty-cart i {
    font-size: 4rem;
    color: var(--primary-blue);
    margin-bottom: 2rem;
    opacity: 0.7;
}

.empty-cart h2 {
    font-size: 2rem;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.empty-cart p {
    color: var(--text-secondary);
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .cart-container {
        margin: 1rem auto;
        padding: 0 0.5rem;
    }
    
    .cart-content {
        gap: 1rem;
    }
    
    .cart-items, .cart-summary {
        padding: 1.5rem;
    }
    
    .cart-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .cart-item-image {
        width: 80px;
        height: 80px;
    }
}
</style>

<div class="cart-container">
    <div class="cart-header">
        <h1><i class="fas fa-shopping-cart"></i> سلة التسوق</h1>
        <p>مراجعة وتعديل المنتجات في سلة التسوق الخاصة بك</p>
    </div>
    
    <div class="cart-content">
        <div class="cart-items">
            <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                <?php 
                $total = 0;
                $cart_has_items = false;
                foreach($_SESSION['cart'] as $cart_item_key => $item): 
                    // Debug: Check what keys are available in the item
                    error_log("Cart item: " . print_r($item, true));
                    
                    // Check if product_id exists, if not try to find the correct key
                    $product_id = null;
                    if (isset($item['product_id'])) {
                        $product_id = $item['product_id'];
                    } elseif (isset($item['id'])) {
                        $product_id = $item['id'];
                    } else {
                        // Skip this item if we can't find a product ID
                        error_log("No product ID found in cart item: " . print_r($item, true));
                        continue;
                    }
                    
                    // Ensure we have valid values for price and quantity
                    $price = isset($item['price']) && $item['price'] > 0 ? $item['price'] : 50000;
                    $quantity = isset($item['quantity']) && $item['quantity'] > 0 ? $item['quantity'] : 1;
                    $product_name = isset($item['name']) ? $item['name'] : 'منتج غير محدد';
                    
                    // Try to get product info from database
                    $product_query = $conn->query("SELECT p.*, c.category as brand_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = {$product_id} AND p.status = 1");
                    if($product_query && $product_query->num_rows > 0) {
                        $product = $product_query->fetch_assoc();
                        $product_name = $product['product_name'];
                        
                        // Try to get actual price from inventory first, then product table
                        $price_query = $conn->query("SELECT price FROM inventory WHERE product_id = {$product_id} LIMIT 1");
                        if($price_query && $price_query->num_rows > 0) {
                            $price_data = $price_query->fetch_assoc();
                            if($price_data['price'] > 0) {
                                $price = $price_data['price'];
                            }
                        } elseif(isset($product['price']) && $product['price'] > 0) {
                            $price = $product['price'];
                        }
                    } else {
                        // Product not found in database, use cart data
                        $product = array(
                            'id' => $product_id,
                            'product_name' => $product_name,
                            'brand_name' => null
                        );
                    }
                    
                    $cart_has_items = true;
                    
                    // Get product image
                    $image_path = 'uploads/product_'.$product_id;
                    $image = '';
                    if(is_dir($image_path)) {
                        $files = scandir($image_path);
                        foreach($files as $file) {
                            if(!in_array($file, ['.', '..']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
                                $image = $image_path.'/'.$file;
                                break;
                            }
                        }
                    }
                    
                    $subtotal = $price * $quantity;
                    $total += $subtotal;
                ?>
                <div class="cart-item" data-product-id="<?= $product['id'] ?>">
                    <div class="cart-item-image">
                        <?php if($image): ?>
                            <img src="<?= validate_image($image) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                        <?php else: ?>
                            <i class="fas fa-mobile-alt"></i>
                        <?php endif; ?>
                    </div>
                    
                    <div class="cart-item-details">
                        <h3 class="cart-item-title"><?= htmlspecialchars($product['product_name']) ?></h3>
                        <div class="cart-item-price"><?= TextCleaner::formatPrice($price) ?></div>
                        
                        <div class="cart-item-actions">
                            <div class="quantity-control">
                                <button class="quantity-btn decrease-qty" data-product-id="<?= $product['id'] ?>">-</button>
                                <input type="number" class="quantity-input" value="<?= $quantity ?>" min="1" max="10" data-product-id="<?= $product['id'] ?>">
                                <button class="quantity-btn increase-qty" data-product-id="<?= $product['id'] ?>">+</button>
                            </div>
                            <button class="remove-item" data-product-id="<?= $product['id'] ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if(!$cart_has_items): ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h2>سلة التسوق فارغة</h2>
                    <p>لا توجد منتجات في سلة التسوق الخاصة بك</p>
                    <a href="./" class="btn-checkout">تسوق الآن</a>
                </div>
                <?php endif; ?>
            <?php else: ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h2>سلة التسوق فارغة</h2>
                <p>لا توجد منتجات في سلة التسوق الخاصة بك</p>
                <a href="./" class="btn-checkout">تسوق الآن</a>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
        <div class="cart-summary">
            <h2>ملخص الطلب</h2>
            <div class="summary-row">
                <span class="summary-label">عدد العناصر:</span>
                <span class="summary-value"><?= count($_SESSION['cart']) ?></span>
            </div>
            <div class="summary-row">
                <span class="summary-label">الشحن:</span>
                <span class="summary-value">مجاني</span>
            </div>
            <div class="total-row">
                <span class="total-label">المجموع الإجمالي:</span>
                <span class="total-value"><?= TextCleaner::formatPrice($total) ?></span>
            </div>
            <div class="cart-actions">
                <button class="btn-checkout" onclick="proceedToCheckout()">إتمام الطلب</button>
                <button class="btn-continue" onclick="window.location.href='./'">متابعة التسوق</button>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Cart functionality
function updateCartQuantity(productId, quantity) {
    fetch('ajax/update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=' + quantity
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        } else {
            alert('حدث خطأ أثناء تحديث الكمية');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء تحديث الكمية');
    });
}

function removeFromCart(productId) {
    if(confirm('هل أنت متأكد من إزالة هذا المنتج من السلة؟')) {
        fetch('ajax/remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('حدث خطأ أثناء إزالة المنتج');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إزالة المنتج');
        });
    }
}

function proceedToCheckout() {
    window.location.href = './?p=checkout';
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Quantity increase buttons
    document.querySelectorAll('.increase-qty').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const input = this.parentElement.querySelector('.quantity-input');
            let quantity = parseInt(input.value);
            if(quantity < 10) {
                quantity++;
                input.value = quantity;
                updateCartQuantity(productId, quantity);
            }
        });
    });
    
    // Quantity decrease buttons
    document.querySelectorAll('.decrease-qty').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const input = this.parentElement.querySelector('.quantity-input');
            let quantity = parseInt(input.value);
            if(quantity > 1) {
                quantity--;
                input.value = quantity;
                updateCartQuantity(productId, quantity);
            }
        });
    });
    
    // Quantity input change
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            let quantity = parseInt(this.value);
            if(isNaN(quantity) || quantity < 1) quantity = 1;
            if(quantity > 10) quantity = 10;
            this.value = quantity;
            updateCartQuantity(productId, quantity);
        });
    });
    
    // Remove item buttons
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            removeFromCart(productId);
        });
    });
});

// Initialize device menu for cart page
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for the navigation to load, then initialize
    setTimeout(function() {
        if (typeof window.initDeviceMenu === 'function') {
            window.initDeviceMenu();
            console.log('✅ Device menu manually initialized for cart page');
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