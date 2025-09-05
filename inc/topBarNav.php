<?php
// Ensure we have access to the database connection
if (!isset($conn)) {
    require_once 'initialize.php';
}
?>

<!-- Additional CSS for navigation -->
<style>
/* Reset and Base Styles */
* {
    box-sizing: border-box;
}

/* Enhanced Navigation Styles */
.navbar {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 1000;
    backdrop-filter: blur(10px);
    overflow: visible !important;
}

/* Navigation Container - اللوغو يمين، السلة يسار */
.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    height: 70px;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    direction: rtl !important;
    flex-direction: row !important;
}

/* Logo Section - أقصى اليمين */
.logo-section {
    flex: 0 0 auto !important;
    order: 1 !important;
}

/* Menu Section - الوسط */
.menu-section {
    flex: 1 !important;
    display: flex !important;
    justify-content: center !important;
    order: 2 !important;
}

/* Cart Section - أقصى اليسار */
.cart-section {
    flex: 0 0 auto !important;
    order: 3 !important;
}

.logo-container {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    transition: all 0.3s ease;
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

.nav-menu {
    display: flex;
    align-items: center;
    gap: 2rem;
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-item {
    position: relative;
}

.nav-link {
    color: #475569;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    display: block;
}

.nav-link:hover {
    color: #3b82f6;
    background: #eff6ff;
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
    top: -10px;
    right: -10px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.3rem 0.6rem;
    border-radius: 50%;
    min-width: 22px;
    min-height: 22px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
    border: 2px solid white;
    z-index: 10;
}

/* القائمة المنسدلة للملحقات - تصميم 3 أعمدة */
.accessories-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    width: 900px;
    max-height: 500px;
    overflow-y: auto;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 9999;
    margin-top: 5px;
    display: flex;
    flex-direction: row;
}

/* الأعمدة الثلاثة */
.dropdown-column {
    flex: 1;
    border-left: 1px solid #f1f5f9;
    min-height: 300px;
    max-height: 450px;
    overflow-y: auto;
}

.dropdown-column:last-child {
    border-left: none;
}

.column-brands {
    background: linear-gradient(180deg, #f8fafc, #f1f5f9) !important;
}

.column-series {
    background: linear-gradient(180deg, #ffffff, #fafafa) !important;
}

.column-models {
    background: linear-gradient(180deg, #f8fafc, #f1f5f9) !important;
}

/* سكرول بار للأعمدة */
.dropdown-column::-webkit-scrollbar {
    width: 4px;
}

.dropdown-column::-webkit-scrollbar-track {
    background: transparent;
}

.dropdown-column::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.dropdown-column::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.dropdown-toggle:hover + .accessories-dropdown,
.accessories-dropdown:hover {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* منع اختفاء القائمة عند السكرول */
.nav-item:hover .accessories-dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.accessories-dropdown {
    pointer-events: auto;
}

.accessories-dropdown::-webkit-scrollbar {
    width: 6px;
}

.accessories-dropdown::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.accessories-dropdown::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.accessories-dropdown::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Dropdown Header */
.dropdown-header {
    padding: 14px 16px;
    font-size: 13px;
    font-weight: 700;
    color: #4b5563;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #f1f5f9;
    text-align: right;
    background: #f9fafb;
}

/* Brand Items */
.dropdown-item {
    display: block;
    padding: 12px 16px;
    text-decoration: none;
    color: #374151;
    font-size: 15px;
    font-weight: 500;
    line-height: 1.4;
    transition: all 0.3s ease;
    position: relative;
    cursor: pointer;
    border: none;
    background: transparent;
}

.dropdown-item:hover {
    background: linear-gradient(135deg, #f0fdf4, #ecfdf5) !important;
    color: #14532d !important;
    padding-right: 20px;
    transform: translateX(-2px);
    box-shadow: 0 2px 8px rgba(34, 197, 94, 0.1);
}

.dropdown-item.active {
    background: linear-gradient(135deg, #16a34a, #22c55e) !important;
    color: #ffffff !important;
    font-weight: 600;
    border-right: 4px solid #15803d;
    transform: translateX(-2px);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.brand-item {
    font-size: 16px;
    font-weight: 600;
}

.brand-item:hover {
    background: linear-gradient(135deg, #f0fdf4, #ecfdf5) !important;
    color: #14532d !important;
}

.brand-item.active {
    background: linear-gradient(135deg, #16a34a, #22c55e) !important;
    color: #ffffff !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.series-item {
    font-size: 15px;
    font-weight: 500;
}

.series-item:hover {
    background: linear-gradient(135deg, #eff6ff, #dbeafe) !important;
    color: #1e3a8a !important;
}

.series-item.active {
    background: linear-gradient(135deg, #2563eb, #3b82f6) !important;
    color: #ffffff !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.model-item {
    font-size: 14px;
    font-weight: 500;
}

.model-item:hover {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9) !important;
    color: #334155 !important;
}

.dropdown-item i {
    margin-left: 8px;
}

/* Submenu Styles */
.has-submenu {
    position: relative;
}

.has-submenu::after {
    content: '\f054';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 10px;
    color: #9ca3af;
    transition: all 0.2s ease;
}

.has-submenu:hover::after {
    color: #3b82f6;
    left: 12px;
}

/* تنسيق العناصر داخل الأعمدة */
.dropdown-item {
    padding: 12px 16px;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s ease;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 8px;
}

.dropdown-item:hover {
    background: #3b82f6;
    color: white;
}

.dropdown-item.active {
    background: #3b82f6;
    color: white;
}

.dropdown-item i {
    font-size: 14px;
    width: 16px;
}

.dropdown-item:hover .submenu,
.submenu:hover {
    opacity: 1;
    visibility: visible;
    transform: translateX(0);
}

/* تحسين تجربة المستخدم - إبقاء القائمة مفتوحة عند التنقل */
.has-submenu:hover .submenu {
    opacity: 1;
    visibility: visible;
    transform: translateX(0);
    transition-delay: 0s;
}

.submenu {
    transition-delay: 0.2s;
}

.has-submenu:hover .submenu {
    transition-delay: 0s;
}

/* Third Level Models Menu Hover - أكثر تحديداً */
.has-models:hover > .models-submenu {
    opacity: 1;
    visibility: visible;
    transform: translateX(0);
    transition-delay: 0.1s;
}

.models-submenu:hover {
    opacity: 1;
    visibility: visible;
    transform: translateX(0);
}

/* إخفاء الموديلات عند عدم الإشارة */
.has-models:not(:hover) .models-submenu {
    opacity: 0;
    visibility: hidden;
    transform: translateX(10px);
    transition-delay: 0.3s;
}


/* Scrollbar Styles */
.accessories-dropdown::-webkit-scrollbar,
.submenu::-webkit-scrollbar,
.models-submenu::-webkit-scrollbar {
    width: 4px;
}

.accessories-dropdown::-webkit-scrollbar-track,
.submenu::-webkit-scrollbar-track,
.models-submenu::-webkit-scrollbar-track {
    background: #f8fafc;
}

.accessories-dropdown::-webkit-scrollbar-thumb,
.submenu::-webkit-scrollbar-thumb,
.models-submenu::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

/* تحسين الأداء والتداخل */
.has-models {
    position: relative;
    cursor: pointer !important;
}

.models-submenu {
    pointer-events: none;
}

.has-models:hover .models-submenu,
.models-submenu:hover {
    pointer-events: auto;
}

/* إصلاح مؤشر الماوس */
.has-models:hover {
    cursor: pointer !important;
}

.dropdown-item {
    cursor: pointer !important;
    user-select: none;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .nav-menu {
        display: none;
    }
    
    .nav-container {
        padding: 0 0.5rem;
    }
}
</style>

<!-- Navigation Bar -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <!-- Logo - Far Right -->
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
                    <a class="nav-link dropdown-toggle" href="#">الملحقات</a>
                    <div class="accessories-dropdown">
                        <!-- العمود الأول: البراندات -->
                        <div class="dropdown-column column-brands">
                            <div class="dropdown-header">العلامات التجارية</div>
                            <?php
                            try {
                                $brands_query = "SELECT id, name FROM brands WHERE status = 1 ORDER BY name ASC";
                                $brands_result = $conn->query($brands_query);
                                
                                if ($brands_result && $brands_result->num_rows > 0) {
                                    while ($brand = $brands_result->fetch_assoc()) {
                                        echo '<div class="dropdown-item brand-item" data-brand-id="' . $brand['id'] . '">';
                                        echo '<i class="fas fa-mobile-alt"></i>';
                                        echo htmlspecialchars($brand['name']);
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<div class="dropdown-item" style="color: #9ca3af;">لا توجد براندات</div>';
                                }
                            } catch (Exception $e) {
                                echo '<div class="dropdown-item" style="color: #ef4444;">خطأ في التحميل</div>';
                            }
                            ?>
                        </div>

                        <!-- العمود الثاني: الفئات -->
                        <div class="dropdown-column column-series">
                            <div class="dropdown-header">الفئات</div>
                            <div id="series-list">
                                <div class="dropdown-item" style="color: #9ca3af; font-style: italic;">اختر علامة تجارية</div>
                            </div>
                        </div>

                        <!-- العمود الثالث: الموديلات -->
                        <div class="dropdown-column column-models">
                            <div class="dropdown-header">الموديلات</div>
                            <div id="models-list">
                                <div class="dropdown-item" style="color: #9ca3af; font-style: italic;">اختر فئة</div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="./?p=about" class="nav-link">من نحن</a>
                </li>
                <li class="nav-item">
                    <a href="./?p=contact" class="nav-link">اتصل بنا</a>
                </li>
            </ul>
        </div>

        <!-- Cart Actions - Far Left -->
        <div class="cart-section">
            <a href="./?p=cart" class="cart-btn">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-badge" id="cart-count">0</span>
            </a>
        </div>
    </div>
</nav>

<script>
// Load and update cart count - Global function
function loadCartCount() {
    fetch('ajax/get_cart_count.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update all possible cart count elements across the site
            const cartElements = document.querySelectorAll('#cart-count, .cart-count, .cart-badge, #mobile-cart-count');
            cartElements.forEach(element => {
                if (element) {
                    element.textContent = data.count;
                    element.style.display = data.count > 0 ? 'flex' : 'none';
                }
            });
        }
    })
    .catch(error => console.error('Error loading cart count:', error));
}

// Make function globally available
window.loadCartCount = loadCartCount;
window.updateCartCount = loadCartCount;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCartCount();
    
    // Auto-refresh cart count every 30 seconds
    setInterval(loadCartCount, 30000);
});
</script>