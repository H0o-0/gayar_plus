<?php
// Ensure we have access to the database connection
if (!isset($conn)) {
    require_once 'initialize.php';
}
?>

<!-- Additional CSS for navigation -->
<style>
/* Enhanced Navigation Styles */
.navbar {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 1000;
    backdrop-filter: blur(10px);
    overflow: visible;
}

.nav-container {
    max-width: 1200px !important;
    margin: 0 auto !important;
    padding: 0 1rem !important;
    height: 70px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}

/* Logo section - Far Left */
.logo-section {
    display: flex !important;
    align-items: center !important;
    order: 1 !important;
}

.logo-container {
    display: flex !important;
    align-items: center !important;
    gap: 0.75rem !important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
}

.site-logo {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
}

.site-name {
    font-size: 1.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #3b82f6, #1e40af);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Menu section - Center */
.menu-section {
    display: flex !important;
    align-items: center !important;
    order: 2 !important;
    flex: 1 !important;
    justify-content: center !important;
}

.nav-menu {
    display: flex !important;
    align-items: center !important;
    gap: 2rem !important;
    list-style: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

.nav-link {
    color: #475569;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    position: relative;
    text-align: right;
    direction: rtl;
}

.nav-link::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #3b82f6, #1e40af);
    transition: width 0.3s ease;
}

.nav-link:hover {
    color: #3b82f6;
    background: #eff6ff;
}

.nav-link:hover::before {
    width: 80%;
}

/* Cart section - Far Right */
.cart-section {
    display: flex !important;
    align-items: center !important;
    order: 3 !important;
}

.nav-actions {
    display: flex !important;
    align-items: center !important;
    gap: 1rem !important;
}

.cart-btn {
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    color: #475569;
    padding: 0.75rem;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.cart-btn:hover {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
    transform: translateY(-2px);
}

.cart-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    min-width: 20px;
    text-align: center;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .nav-container {
        padding: 0 0.5rem !important;
    }
    
    .nav-menu {
        display: none !important;
    }
    
    .logo-section {
        flex: 1 !important;
    }
    
    .cart-section {
        flex: 0 0 auto !important;
    }
}
</style>

<!-- Navigation Bar -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <!-- Logo - Far Left -->
        <div class="logo-section">
            <a href="./" class="logo-container">
                <img src="./admin/images/cropped_circle_image.png" alt="Gayar Plus Logo" class="site-logo" onerror="this.src='./assets/images/no-image.svg'">
                <span class="site-name">Gayar Plus</span>
            </a>
        </div>

        <!-- Navigation Menu - Center -->
        <div class="menu-section">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="./?p=about" class="nav-link">من نحن</a>
                </li>
                <li class="nav-item">
                    <a href="./?p=contact" class="nav-link">اتصل بنا</a>
                </li>
            </ul>
        </div>

        <!-- Cart Actions - Far Right -->
        <div class="cart-section">
            <div class="nav-actions">
                <a href="./?p=cart" class="cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge" id="cart-count">0</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<script>

// Load and update cart count for all pages
function loadCartCount() {
    fetch('ajax/get_cart_count.php')
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            var cartElements = document.querySelectorAll('.cart-count, .cart-badge, #cart-count, #mobile-cart-count');
            cartElements.forEach(function(element) {
                element.textContent = data.count;
                // Hide badge if count is 0
                if (data.count == 0) {
                    element.style.display = 'none';
                } else {
                    element.style.display = 'block';
                }
            });
            console.log('📊 عداد السلة محدث:', data.count);
        }
    })
    .catch(function(error) {
        console.log('لا يمكن تحديث عداد السلة:', error);
    });
}

// Initialize cart count when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('🛒 تحميل عداد السلة...');
    loadCartCount();
});

// Also load cart count when window loads for better reliability
window.addEventListener('load', function() {
    setTimeout(loadCartCount, 100);
});
</script>
