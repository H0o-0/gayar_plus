<?php
$page_title = "إتمام الطلب - Gayar Plus";
require_once 'initialize.php';
require_once 'classes/TextCleaner.php';

include 'inc/header.php';
?>

<script src="https://www.paypalobjects.com/api/checkout.js"></script>

<style>
/* Checkout Page Styles */
.checkout-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.checkout-header {
    text-align: center;
    margin-bottom: 2rem;
}

.checkout-header h1 {
    font-size: 2rem;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.checkout-content {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
}

@media (max-width: 768px) {
    .checkout-content {
        grid-template-columns: 1fr;
    }
}

.checkout-form {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--medium-gray);
}

.checkout-summary {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--medium-gray);
    position: sticky;
    top: 100px;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    color: var(--text-primary);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--medium-gray);
    border-radius: 8px;
    font-size: 1rem;
    transition: var(--transition);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.checkout-summary h2 {
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

.payment-methods {
    margin-top: 2rem;
}

.payment-methods h3 {
    color: var(--text-primary);
    font-size: 1.25rem;
    margin-bottom: 1rem;
}

.payment-buttons {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.btn-payment {
    background: var(--light-gray);
    color: var(--text-primary);
    border: 1px solid var(--medium-gray);
    padding: 1rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.btn-payment:hover {
    background: var(--primary-blue);
    color: white;
    border-color: var(--primary-blue);
}

.btn-payment.active {
    background: var(--primary-blue);
    color: white;
    border-color: var(--primary-blue);
}

.btn-place-order {
    background: linear-gradient(135deg, var(--primary-blue), var(--primary-navy));
    color: white;
    border: none;
    padding: 1rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: var(--transition);
    width: 100%;
    margin-top: 1.5rem;
}

.btn-place-order:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

/* PayPal button styling */
#paypal-button {
    width: 100%;
    margin-top: 1rem;
}

/* Empty cart message */
.empty-cart-message {
    text-align: center;
    padding: 3rem;
    color: var(--text-secondary);
}

.empty-cart-message i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: var(--medium-gray);
}

@media (max-width: 768px) {
    .checkout-container {
        margin: 1rem auto;
        padding: 0 0.5rem;
    }
    
    .checkout-content {
        gap: 1rem;
    }
    
    .checkout-form, .checkout-summary {
        padding: 1.5rem;
    }
}
</style>

<div class="checkout-container">
    <div class="checkout-header">
        <h1><i class="fas fa-credit-card"></i> إتمام الطلب</h1>
        <p>مراجعة طلبك وإتمام عملية الشراء</p>
    </div>
    
    <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
        <?php 
        $total = 0;
        $cart_items = [];
        foreach($_SESSION['cart'] as $item): 
            // Check if product_id exists, if not try to find the correct key
            $product_id = null;
            if (isset($item['product_id'])) {
                $product_id = $item['product_id'];
            } elseif (isset($item['id'])) {
                $product_id = $item['id'];
            } else {
                // Skip this item if we can't find a product ID
                continue;
            }
            
            // Ensure we have valid values for price and quantity
            $price = isset($item['price']) ? $item['price'] : 0;
            $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
            
            $product_query = $conn->query("SELECT p.*, c.category as brand_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = {$product_id} AND p.status = 1");
            if($product_query && $product_query->num_rows > 0):
                $product = $product_query->fetch_assoc();
                
                $subtotal = $price * $quantity;
                $total += $subtotal;
                
                $cart_items[] = [
                    'product' => $product,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
            endif;
        endforeach;
        ?>
    
        <div class="checkout-content">
            <div class="checkout-form">
                <h2>معلومات التوصيل</h2>
                <form action="" id="place_order">
                    <input type="hidden" name="amount" value="<?php echo $total ?>">
                    <input type="hidden" name="payment_method" value="cod">
                    <input type="hidden" name="paid" value="0">
                    
                    <div class="form-group">
                        <label for="fullname">الاسم الكامل</label>
                        <input type="text" id="fullname" name="fullname" class="form-control" placeholder="أدخل اسمك الكامل" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="delivery_address">عنوان التوصيل</label>
                        <textarea id="delivery_address" name="delivery_address" class="form-control" rows="4" placeholder="أدخل عنوانك الكامل للتوصيل" required><?php echo $_settings->userdata('default_delivery_address') ?? '' ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">رقم الهاتف</label>
                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="أدخل رقم هاتفك" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">ملاحظات إضافية (اختياري)</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="أي ملاحظات إضافية حول الطلب"></textarea>
                    </div>
                </form>
            </div>
            
            <div class="checkout-summary">
                <h2>ملخص الطلب</h2>
                
                <?php foreach($cart_items as $item): ?>
                <div class="summary-row">
                    <span class="summary-label"><?php echo htmlspecialchars($item['product']['product_name']); ?> (<?php echo $item['quantity']; ?>)</span>
                    <span class="summary-value"><?php echo TextCleaner::formatPrice($item['subtotal']); ?></span>
                </div>
                <?php endforeach; ?>
                
                <div class="total-row">
                    <span class="total-label">المجموع الإجمالي:</span>
                    <span class="total-value"><?php echo TextCleaner::formatPrice($total); ?></span>
                </div>
                
                <div class="payment-methods">
                    <h3>طريقة الدفع</h3>
                    <div class="payment-buttons">
                        <button class="btn-payment active" data-method="cod">
                            <i class="fas fa-money-bill-wave"></i>
                            الدفع عند الاستلام
                        </button>
                        <button class="btn-payment" data-method="paypal">
                            <i class="fab fa-paypal"></i>
                            الدفع عبر PayPal
                        </button>
                    </div>
                    
                    <div id="paypal-button"></div>
                    
                    <button class="btn-place-order" id="place_order_btn">
                        <i class="fas fa-check"></i> تأكيد الطلب
                    </button>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-cart-message">
            <i class="fas fa-shopping-cart"></i>
            <h3>سلة التسوق فارغة</h3>
            <p>لا توجد منتجات في سلة التسوق الخاصة بك</p>
            <a href="./" class="btn-payment" style="display: inline-flex; width: auto;">
                <i class="fas fa-shopping-bag"></i> تسوق الآن
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
// Payment method selection
document.addEventListener('DOMContentLoaded', function() {
    const paymentButtons = document.querySelectorAll('.btn-payment');
    const paymentMethodInput = document.querySelector('[name="payment_method"]');
    const paidInput = document.querySelector('[name="paid"]');
    const paypalButton = document.getElementById('paypal-button');
    
    paymentButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            paymentButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Update payment method
            const method = this.dataset.method;
            paymentMethodInput.value = method;
            
            // Show/hide PayPal button
            if (method === 'paypal') {
                paypalButton.style.display = 'block';
                paidInput.value = '1';
            } else {
                paypalButton.style.display = 'none';
                paidInput.value = '0';
            }
        });
    });
    
    // Place order button
    document.getElementById('place_order_btn').addEventListener('click', function() {
        placeOrder();
    });
});

// PayPal integration
paypal.Button.render({
    env: 'sandbox', // change for production if app is live,
 
    //app's client id's
    client: {
        sandbox: 'AdDNu0ZwC3bqzdjiiQlmQ4BRJsOarwyMVD_L4YQPrQm4ASuBg4bV5ZoH-uveg8K_l9JLCmipuiKt4fxn',
        //production: 'AaBHKJFEej4V6yaArjzSx9cuf-UYesQYKqynQVCdBlKuZKawDDzFyuQdidPOBSGEhWaNQnnvfzuFB9SM'
    },
 
    commit: true, // Show a 'Pay Now' button
 
    style: {
        color: 'blue',
        size: 'responsive'
    },
 
    payment: function(data, actions) {
        return actions.payment.create({
            payment: {
                transactions: [
                    {
                        //total purchase
                        amount: { 
                            total: <?php echo json_encode(number_format($total, 2, '.', '')); ?>, 
                            currency: 'USD' 
                        }
                    }
                ]
            }
        });
    },
 
    onAuthorize: function(data, actions) {
        return actions.payment.execute().then(function(payment) {
            // Set payment method to PayPal and mark as paid
            document.querySelector('[name="payment_method"]').value = "PayPal";
            document.querySelector('[name="paid"]').value = "1";
            placeOrder();
        });
    },
 
}, '#paypal-button');

function placeOrder() {
    const form = document.getElementById('place_order');
    const formData = new FormData(form);
    
    // Show loading
    const placeOrderBtn = document.getElementById('place_order_btn');
    const originalHTML = placeOrderBtn.innerHTML;
    placeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري إتمام الطلب...';
    placeOrderBtn.disabled = true;
    
    fetch('ajax/place_order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Show success message
            showNotification('تم إتمام الطلب بنجاح!', 'success');
            
            // Redirect to order confirmation page after delay
            setTimeout(function() {
                window.location.href = 'order_confirmation.php?id=' + data.order_id;
            }, 2000);
        } else {
            throw new Error(data.message || 'حدث خطأ أثناء إتمام الطلب');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ ' + error.message, 'error');
        placeOrderBtn.innerHTML = originalHTML;
        placeOrderBtn.disabled = false;
    });
}

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
</script>

<?php include 'inc/modern-footer.php'; ?>