<?php
// Ensure we have access to the database connection
if (!isset($conn)) {
    require_once 'initialize.php';
}
?>

<<<<<<< HEAD
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
    display: table !important;
    width: 100% !important;
    table-layout: fixed !important;
}

/* Logo section - Far right with more space */
.logo-section {
    position: absolute !important;
    right: 2rem !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    z-index: 10 !important;
    width: 200px !important;
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
    display: table-cell !important;
    vertical-align: middle !important;
    text-align: center !important;
}

.nav-menu {
    display: inline-flex !important;
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
    /* Arabic text specific */
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

/* Cart section - Far left */
.cart-section {
    position: absolute !important;
    left: 0 !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    z-index: 10 !important;
    width: 200px !important;
}

.nav-actions {
    display: inline-flex !important;
    align-items: center !important;
    gap: 1rem !important;
}

.search-btn, .cart-btn {
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

.search-btn:hover, .cart-btn:hover {
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

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.fa-spin {
    animation: spin 1s linear infinite;
}

/* Dropdown Menu Styles */
.dropdown {
    position: relative;
}

.dropdown-toggle {
    cursor: pointer;
    position: relative;
}

.dropdown-toggle::after {
    display: none;
}

.dropdown:hover .dropdown-toggle::after {
    display: none;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    min-width: 300px;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    border: 2px solid #f3f4f6;
    opacity: 0;
    visibility: hidden;
    transform: translateX(-50%) translateY(-10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 9999;
    margin-top: 1rem;
    display: block;
}

.dropdown:hover .dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
    display: block;
}

.dropdown-content {
    padding: 0;
    max-height: 300px;
    overflow-y: auto;
    overflow-x: hidden;
    border-radius: 12px;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

.dropdown-content::-webkit-scrollbar {
    width: 4px;
}

.dropdown-content::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 2px;
}

.dropdown-content::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.dropdown-content::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Fix scroll issues */
.dropdown-menu {
    overflow: visible;
}

.dropdown-content {
    position: relative;
}

/* Ensure proper submenu positioning */
.dropdown-item-wrapper {
    position: relative;
    overflow: visible;
}

.dropdown-item-wrapper:hover .submenu {
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateX(0) !important;
    display: block !important;
}

.dropdown-item {
    display: block;
    padding: 0.75rem 1.5rem;
    color: #374151;
    text-decoration: none;
    transition: all 0.3s ease;
    border-bottom: 1px solid #f3f4f6;
    position: relative;
}

.dropdown-item:last-child {
    border-bottom: none;
}

.dropdown-item:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #3b82f6;
    padding-left: 2rem;
}

.dropdown-item:hover .brand-name-dropdown {
    transform: translateX(5px);
}

.brand-name-dropdown {
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    display: block;
}

.dropdown-item.no-brands {
    text-align: center;
    color: #9ca3af;
    font-style: italic;
    cursor: default;
    background: none !important;
}

.dropdown-item.no-brands:hover {
    background: none !important;
    color: #9ca3af !important;
    padding-left: 1.5rem !important;
}

/* Mobile dropdown adjustments */
@media (max-width: 768px) {
    .dropdown-menu {
        left: 0;
        transform: translateX(0) translateY(-10px);
        min-width: 250px;
        margin-top: 0.5rem;
    }
    
    .dropdown:hover .dropdown-menu,
    .dropdown-menu.active {
        transform: translateX(0) translateY(0);
        opacity: 1;
        visibility: visible;
        display: block;
    }
    
    .dropdown-content {
        max-height: 300px;
    }
    
    .dropdown-item {
        padding: 1rem 1.25rem;
        font-size: 0.9rem;
    }
}

/* Force dropdown to show on hover for desktop */
@media (min-width: 769px) {
    .dropdown:hover .dropdown-menu,
    .dropdown-menu.show-dropdown {
        opacity: 1 !important;
        visibility: visible !important;
        display: block !important;
        transform: translateX(-50%) translateY(0) !important;
    }
}

/* Submenu Styles */
.dropdown-item-wrapper {
    position: relative;
    overflow: visible;
}

.brand-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 0.75rem 1.5rem;
    color: #374151;
    text-decoration: none;
    transition: all 0.3s ease;
    border-bottom: 1px solid #f3f4f6;
    position: relative;
}

.brand-item:last-child {
    border-bottom: none;
}

.brand-item:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #3b82f6;
    padding-left: 2rem;
}

.brand-item:hover .brand-name-dropdown {
    transform: translateX(5px);
}

.dropdown-item-wrapper:hover .submenu {
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateX(0) !important;
    display: block !important;
}

.submenu-arrow {
    font-size: 1.2rem;
    color: #9ca3af;
    transition: all 0.3s ease;
    margin-left: auto;
    font-weight: bold;
}

.brand-item:hover .submenu-arrow {
    color: #3b82f6;
    transform: translateX(3px);
}

.submenu {
    position: absolute;
    top: 0;
    left: 100%;
    background: white;
    min-width: 220px;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    border: 2px solid #f3f4f6;
    opacity: 0;
    visibility: hidden;
    transform: translateX(-10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 10001;
    margin-left: 0.5rem;
    display: block;
    pointer-events: none;
}

.submenu.active,
.dropdown-item-wrapper:hover .submenu {
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateX(0) !important;
    pointer-events: auto !important;
}

.submenu-content {
    padding: 0.5rem 0;
    max-height: 250px;
    overflow-y: auto;
}

.submenu-item {
    display: block;
    padding: 0.6rem 1.25rem;
    color: #374151;
    text-decoration: none;
    transition: all 0.3s ease;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.9rem;
}

.submenu-item:last-child {
    border-bottom: none;
}

.submenu-item:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #3b82f6;
    padding-left: 1.75rem;
}

.submenu-item:hover .category-name {
    transform: translateX(5px);
}

.all-category {
    background: #f8fafc;
    font-weight: 600;
    border-bottom: 2px solid #e5e7eb !important;
}

.all-category:hover {
    background: #3b82f6 !important;
    color: white !important;
}

.category-name {
    transition: all 0.3s ease;
    display: block;
}

/* Mobile submenu adjustments */
@media (max-width: 768px) {
    .submenu {
        position: static;
        opacity: 0;
        visibility: hidden;
        transform: none;
        margin-left: 0;
        margin-top: 0.5rem;
        min-width: 100%;
        border-radius: 8px;
        z-index: 1000;
    }
    
    .dropdown-item-wrapper.active .submenu,
    .submenu.active {
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
    }
    
    .submenu-content {
        max-height: 200px;
        overflow-y: auto;
    }
    
    .submenu-item {
        padding: 0.8rem 1rem;
        font-size: 0.85rem;
    }
}

/* Simple submenu hover effect */
.has-submenu:hover .submenu {
    opacity: 1 !important;
    visibility: visible !important;
}

.submenu:hover {
    opacity: 1 !important;
    visibility: visible !important;
}

.submenu-item:hover {
    background: #f0f0f0 !important;
    color: #007bff !important;
}
@media (max-width: 768px) {
    .nav-container {
        padding: 0 0.5rem;
    }
    
    .nav-menu {
        display: none;
    }
    
    .logo-container {
        flex: 1;
    }
    
    .nav-actions {
        flex: 0 0 auto;
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

        <!-- Menu - Center -->
        <div class="menu-section">
            <ul class="nav-menu">
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" onclick="return false;">ملحقات</a>
                    <div class="dropdown-menu">
                        <div class="dropdown-content">
                            <?php
                            // جلب البراندات من قاعدة البيانات مع عدد الفئات
                            $brands_query = "
                                SELECT 
                                    b.id,
                                    b.name,
                                    b.name_ar,
                                    COUNT(s.id) as categories_count
                                FROM brands b 
                                LEFT JOIN series s ON b.id = s.brand_id AND s.status = 1
                                WHERE b.status = 1 
                                GROUP BY b.id, b.name, b.name_ar
                                ORDER BY b.name ASC
                            ";
                            
                            $brands_result = $conn->query($brands_query);
                            
                            if($brands_result && $brands_result->num_rows > 0) {
                                while($brand = $brands_result->fetch_assoc()) {
                                    // استخدام الاسم العربي إذا كان متاحاً
                                    $display_name = !empty($brand['name_ar']) ? $brand['name_ar'] : $brand['name'];
                                    // التحقق من وجود جدول series
                                    $has_series_table = $conn->query("SHOW TABLES LIKE 'series'")->num_rows > 0;
                                    $has_categories = ($brand['categories_count'] > 0);
                                    
                                    // جلب الفئات فقط إذا كانت موجودة
                                    $categories = [];
                                    if($has_categories) {
                                        $categories_query = "
                                            SELECT 
                                                s.id,
                                                COALESCE(NULLIF(s.name_ar, ''), s.name) as category_name
                                            FROM series s 
                                            WHERE s.brand_id = " . intval($brand['id']) . " 
                                            AND s.status = 1 
                                            ORDER BY s.sort_order ASC, s.name ASC
                                        ";
                                        
                                        $categories_result = $conn->query($categories_query);
                                        if($categories_result) {
                                            while($cat = $categories_result->fetch_assoc()) {
                                                $categories[] = $cat;
                                            }
                                        }
                                    }
                            ?>
                            <div class="dropdown-item-wrapper" data-brand-id="<?= $brand['id'] ?>" 
                                 onmouseover="document.getElementById('submenu-<?= $brand['id'] ?>') && (document.getElementById('submenu-<?= $brand['id'] ?>').style.opacity = '1', document.getElementById('submenu-<?= $brand['id'] ?>').style.visibility = 'visible')"
                                 onmouseout="document.getElementById('submenu-<?= $brand['id'] ?>') && (document.getElementById('submenu-<?= $brand['id'] ?>').style.opacity = '0', document.getElementById('submenu-<?= $brand['id'] ?>').style.visibility = 'hidden')">
                                <a href="products.php?brand=<?= $brand['id'] ?>" class="dropdown-item brand-item">
                                    <span class="brand-name-dropdown"><?= htmlspecialchars($display_name) ?></span>
                                    <?php if($has_categories) { ?>
                                        <span class="submenu-arrow">‹</span>
                                    <?php } ?>
                                </a>
                                <?php if($has_categories && count($categories) > 0) { ?>
                                <div class="submenu" id="submenu-<?= $brand['id'] ?>" 
                                     style="position: absolute; top: 0; left: 100%; background: white; min-width: 200px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 9999; margin-left: 5px; opacity: 0; visibility: hidden; transition: all 0.3s ease;"
                                     onmouseover="this.style.opacity = '1'; this.style.visibility = 'visible'"
                                     onmouseout="this.style.opacity = '0'; this.style.visibility = 'hidden'">
                                    <div class="submenu-content" style="padding: 8px 0;">
                                        <a href="products.php?brand=<?= $brand['id'] ?>" class="submenu-item" style="display: block; padding: 8px 16px; color: #333; text-decoration: none; border-bottom: 1px solid #eee; font-weight: bold; background: #f8f9fa;">
                                            جميع الفئات
                                        </a>
                                        <?php foreach($categories as $category) { ?>
                                        <a href="products.php?brand=<?= $brand['id'] ?>&category=<?= $category['id'] ?>" class="submenu-item" style="display: block; padding: 8px 16px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='white'">
                                            <?= htmlspecialchars($category['category_name']) ?>
                                        </a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <?php 
                                }
                            } else {
                            ?>
                            <div class="dropdown-item no-brands">
                                <span>لا توجد علامات تجارية متاحة</span>
                            </div>
                            <?php 
                            }
                            ?>
                        </div>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="about.php" class="nav-link">من نحن</a>
                </li>
                <li class="nav-item">
                    <a href="contact.php" class="nav-link">اتصل بنا</a>
                </li>
            </ul>
        </div>

        <!-- Cart - Far Right -->
        <div class="cart-section">
            <div class="nav-actions">
                <a href="./?p=cart" class="cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge" id="cart-count">0</span>
                </a>
            </div>
=======
<!-- Navigation Bar -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <!-- Logo -->
        <a href="./" class="logo-container">
            <img src="./admin/images/cropped_circle_image.png" alt="Gayar Plus Logo" class="site-logo" onerror="this.src='./assets/images/no-image.svg'">
            <span class="site-name">Gayar Plus</span>
        </a>

        <!-- Navigation Menu -->
        <ul class="nav-menu">
            <!-- Devices Menu -->
            <li class="nav-item nav-devices">
                <a href="#" class="nav-link">الأجهزة</a>
                <div class="mega-menu">
                    <div class="menu-grid">
                        <!-- Brands Section -->
                        <div class="menu-section">
                            <h3 class="menu-title">الشركات</h3>
                            <div id="brands-container">
                                <?php
                                // Load brands from database
                                if (isset($conn)) {
                                    $brands_query = "SELECT id, name FROM brands WHERE status = 1 ORDER BY name ASC LIMIT 8";
                                    $brands_result = $conn->query($brands_query);
                                    
                                    if ($brands_result && $brands_result->num_rows > 0) {
                                        while ($brand = $brands_result->fetch_assoc()) {
                                            echo '<div class="menu-item brand-item" data-brand="' . $brand['id'] . '">';
                                            echo '<i class="fas fa-mobile brand-icon"></i>';
                                            echo htmlspecialchars($brand['name']);
                                            echo '</div>';
                                        }
                                    } else {
                                        echo '<div class="menu-item">لا توجد شركات</div>';
                                    }
                                } else {
                                    echo '<div class="menu-item">خطأ في الاتصال بقاعدة البيانات</div>';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <!-- Categories Section -->
                        <div class="menu-section">
                            <h3 class="menu-title">الفئات</h3>
                            <div id="categories-section">
                                <div class="menu-item">اختر شركة أولاً</div>
                            </div>
                        </div>
                        
                        <!-- Models Section -->
                        <div class="menu-section">
                            <h3 class="menu-title">الموديلات</h3>
                            <div id="phones-section">
                                <div class="menu-item">اختر فئة لعرض الموديلات</div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            
            <!-- Maintenance Tools Menu -->
            <li class="nav-item">
                <a href="#" class="nav-link">أدوات الصيانة</a>
                <div class="mega-menu">
                    <div class="menu-grid">
                        <div class="menu-section">
                            <div class="menu-item" onclick="window.location.href='./?p=maintenance'">
                                <i class="fas fa-tools"></i>
                                <span>صيانة الأجهزة</span>
                            </div>
                            <div class="menu-item" onclick="window.location.href='./?p=repair'">
                                <i class="fas fa-wrench"></i>
                                <span>إصلاح الأعطال</span>
                            </div>
                            <div class="menu-item" onclick="window.location.href='./?p=upgrade'">
                                <i class="fas fa-microchip"></i>
                                <span>ترقية الأجهزة</span>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            
            <!-- About Menu -->
            <li class="nav-item">
                <a href="./?p=about" class="nav-link">من نحن</a>
            </li>
            
            <!-- Contact Menu -->
            <li class="nav-item">
                <a href="./?p=contact" class="nav-link">اتصل بنا</a>
            </li>
        </ul>

        <!-- Navigation Actions -->
        <div class="nav-actions">
            <button class="search-btn" onclick="openSearch()">
                <i class="fas fa-search"></i>
            </button>
            <a href="./?p=cart" class="cart-btn">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-badge" id="cart-count">0</span>
            </a>
>>>>>>> cebc63a3bc4f7e2f5ae4119daff21338fea35eb8
        </div>
    </div>
</nav>

<!-- Search Modal -->
<div class="search-modal" id="search-modal" style="display: none;">
    <div class="search-container">
        <div class="search-header">
            <h3>البحث في المتجر</h3>
            <button class="close-search" onclick="closeSearch()">&times;</button>
        </div>
        <div class="search-body">
            <input type="text" class="search-input" id="search-input" placeholder="ابحث عن منتج، شركة، أو فئة...">
<<<<<<< HEAD
            <div class="search-results" id="search-results"></div>
=======
            <div class="search-results" id="search-results">
                <!-- Search results will be populated here -->
            </div>
>>>>>>> cebc63a3bc4f7e2f5ae4119daff21338fea35eb8
        </div>
    </div>
</div>

<script>
<<<<<<< HEAD
// Search functions
=======
// Device Menu Navigation System for Gayar Plus
// Isolated to prevent conflicts with other scripts

// Create a namespace for our functions
window.GayarPlusMenu = {
    // State variables
    hideTimeout: null,
    
    // Initialize the device menu
    init: function() {
        console.log('Initializing Gayar Plus Device Menu');
        const devicesNavItem = document.querySelector('.nav-devices');
        if (!devicesNavItem) {
            console.log('Devices nav item not found');
            return;
        }
        
        const megaMenu = devicesNavItem.querySelector('.mega-menu');
        if (!megaMenu) {
            console.log('Mega menu not found');
            return;
        }
        
        this.setupHoverEvents(devicesNavItem, megaMenu);
        this.setupBrandEvents();
        this.setupCategoryEvents();
        
        console.log('Gayar Plus Device Menu initialized successfully');
    },
    
    // Setup hover events for showing/hiding the menu
    setupHoverEvents: function(devicesNavItem, megaMenu) {
        // Show dropdown on hover
        devicesNavItem.addEventListener('mouseenter', () => {
            if (this.hideTimeout) {
                clearTimeout(this.hideTimeout);
                this.hideTimeout = null;
            }
            devicesNavItem.classList.add('show');
            console.log('Menu shown');
        });
        
        // Hide dropdown when mouse leaves
        devicesNavItem.addEventListener('mouseleave', () => {
            this.hideTimeout = setTimeout(() => {
                devicesNavItem.classList.remove('show');
                console.log('Menu hidden');
            }, 300);
        });
        
        // Keep menu visible when hovering over it
        megaMenu.addEventListener('mouseenter', () => {
            if (this.hideTimeout) {
                clearTimeout(this.hideTimeout);
                this.hideTimeout = null;
            }
            devicesNavItem.classList.add('show');
            console.log('Menu kept visible');
        });
        
        // Hide menu when leaving it
        megaMenu.addEventListener('mouseleave', () => {
            this.hideTimeout = setTimeout(() => {
                devicesNavItem.classList.remove('show');
                console.log('Menu hidden from mega menu');
            }, 300);
        });
    },
    
    // Setup brand item events
    setupBrandEvents: function() {
        // Wait a bit for the DOM to be fully ready
        setTimeout(() => {
            const brandItems = document.querySelectorAll('.brand-item');
            console.log('Found brand items:', brandItems.length);
            
            if (brandItems.length > 0) {
                brandItems.forEach(item => {
                    // Remove any existing event listeners to prevent duplicates
                    const clone = item.cloneNode(true);
                    item.parentNode.replaceChild(clone, item);
                    
                    clone.addEventListener('mouseenter', (e) => {
                        const brandId = clone.dataset.brand;
                        console.log('Brand item hovered:', brandId);
                        
                        // Remove active class from all items
                        document.querySelectorAll('.brand-item').forEach(i => i.classList.remove('active'));
                        
                        // Add active class to hovered item
                        clone.classList.add('active');
                        
                        // Load categories for this brand
                        this.loadBrandCategories(brandId);
                    });
                });
            } else {
                console.log('No brand items found, retrying in 500ms');
                // Retry in case the items are loaded dynamically
                setTimeout(() => this.setupBrandEvents(), 500);
            }
        }, 100);
    },
    
    // Setup category item events
    setupCategoryEvents: function() {
        // Wait a bit for the DOM to be fully ready
        setTimeout(() => {
            const categoriesSection = document.getElementById('categories-section');
            console.log('Categories section:', categoriesSection);
            
            if (categoriesSection) {
                // Use event delegation for dynamically added elements
                categoriesSection.addEventListener('mouseenter', (e) => {
                    const categoryItem = e.target.closest('.category-item');
                    if (categoryItem) {
                        e.stopPropagation();
                        const categoryId = categoryItem.dataset.categoryId;
                        console.log('Category item hovered:', categoryId);
                        this.loadCategoryModels(categoryId);
                    }
                }, true);
            } else {
                console.log('Categories section not found, retrying in 500ms');
                // Retry in case the section is loaded dynamically
                setTimeout(() => this.setupCategoryEvents(), 500);
            }
        }, 100);
    },
    
    // Load brand categories
    loadBrandCategories: function(brandId) {
        const categoriesSection = document.getElementById('categories-section');
        if (!categoriesSection) {
            console.log('Categories section not found');
            return;
        }
        
        // Show loading
        categoriesSection.innerHTML = '<div class="menu-item">جاري التحميل...</div>';
        console.log('Loading categories for brand:', brandId);
        
        // Use absolute path based on base URL to ensure it works on all pages
        const baseUrl = typeof _base_url_ !== 'undefined' ? _base_url_ : '/';
        const ajaxUrl = baseUrl + 'ajax/get_brand_categories.php?brand_id=' + brandId;
        console.log('AJAX URL for categories:', ajaxUrl);
        
        // Fetch categories
        fetch(ajaxUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Brand categories response:', data);
                if (data.success && data.categories.length > 0) {
                    categoriesSection.innerHTML = '';
                    data.categories.forEach(category => {
                        const element = document.createElement('div');
                        element.className = 'menu-item category-item';
                        element.textContent = category.name;
                        element.dataset.categoryId = category.id;
                        
                        categoriesSection.appendChild(element);
                    });
                } else {
                    categoriesSection.innerHTML = '<div class="menu-item">لا توجد فئات</div>';
                }
            })
            .catch(error => {
                console.error('Error loading brand categories:', error);
                categoriesSection.innerHTML = '<div class="menu-item">حدث خطأ في التحميل</div>';
            });
    },
    
    // Load category models
    loadCategoryModels: function(categoryId) {
        const phonesSection = document.getElementById('phones-section');
        if (!phonesSection) {
            console.log('Phones section not found');
            return;
        }
        
        // Show loading
        phonesSection.innerHTML = '<div class="menu-item">جاري التحميل...</div>';
        console.log('Loading models for category:', categoryId);
        
        // Use absolute path based on base URL to ensure it works on all pages
        const baseUrl = typeof _base_url_ !== 'undefined' ? _base_url_ : '/';
        const ajaxUrl = baseUrl + 'ajax/get_category_models.php?category_id=' + categoryId;
        console.log('AJAX URL for models:', ajaxUrl);
        
        // Fetch models
        fetch(ajaxUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Category models response:', data);
                if (data.success && data.models.length > 0) {
                    phonesSection.innerHTML = '';
                    data.models.forEach(model => {
                        const element = document.createElement('div');
                        element.className = 'menu-item model-item';
                        element.textContent = model.name;
                        element.dataset.modelId = model.id;
                        
                        // Add click event for navigation
                        element.addEventListener('click', (e) => {
                            e.stopPropagation();
                            const selectedBrand = document.querySelector('.brand-item.active');
                            const brandId = selectedBrand ? selectedBrand.dataset.brand : '';
                            if (brandId) {
                                window.location.href = './?p=device_products&brand=' + btoa(brandId) + '&series=' + btoa(categoryId) + '&model=' + btoa(model.id);
                            } else {
                                window.location.href = './?p=device_products&model=' + btoa(model.id);
                            }
                        });
                        
                        phonesSection.appendChild(element);
                    });
                } else {
                    phonesSection.innerHTML = '<div class="menu-item">لا توجد موديلات</div>';
                }
            })
            .catch(error => {
                console.error('Error loading category models:', error);
                phonesSection.innerHTML = '<div class="menu-item">حدث خطأ في التحميل</div>';
            });
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM content loaded, initializing menu');
    // Small delay to ensure DOM is fully ready
    setTimeout(() => {
        if (typeof window.GayarPlusMenu !== 'undefined') {
            window.GayarPlusMenu.init();
        }
    }, 300); // Increased delay to ensure everything is loaded
});

// Also initialize on window load for better reliability
window.addEventListener('load', function() {
    console.log('Window loaded, initializing menu');
    setTimeout(() => {
        if (typeof window.GayarPlusMenu !== 'undefined') {
            window.GayarPlusMenu.init();
        }
    }, 300);
});

// Function to open search modal
>>>>>>> cebc63a3bc4f7e2f5ae4119daff21338fea35eb8
function openSearch() {
    const searchModal = document.getElementById('search-modal');
    if (searchModal) {
        searchModal.style.display = 'block';
<<<<<<< HEAD
        document.getElementById('search-input')?.focus();
    }
}

=======
        document.getElementById('search-input').focus();
    }
}

// Function to close search modal
>>>>>>> cebc63a3bc4f7e2f5ae4119daff21338fea35eb8
function closeSearch() {
    const searchModal = document.getElementById('search-modal');
    if (searchModal) {
        searchModal.style.display = 'none';
    }
}
<<<<<<< HEAD

// No JavaScript needed - CSS handles everything

// Dropdown Menu JavaScript
$(document).ready(function() {
    // Debug: Check if brands are loaded
    console.log('Dropdown brands count:', $('.brand-item').length);
    console.log('Submenu elements count:', $('.submenu').length);
    
    // Debug each submenu
    $('.submenu').each(function(index) {
        var id = $(this).attr('id');
        var itemsCount = $(this).find('.submenu-item').length;
        console.log('Submenu ' + id + ' has ' + itemsCount + ' items');
    });
    
    // Handle dropdown on mobile devices
    if ($(window).width() <= 768) {
        $('.dropdown-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $dropdown = $(this).parent('.dropdown');
            var $menu = $dropdown.find('.dropdown-menu');
            
            // Close other dropdowns
            $('.dropdown-menu').not($menu).removeClass('active');
            
            // Toggle current dropdown
            $menu.toggleClass('active');
            console.log('Mobile dropdown toggled');
        });
        
        // Handle submenu on mobile
        $(document).on('click', '.has-submenu', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $submenu = $(this).find('.submenu');
            
            // Close other submenus
            $('.submenu').not($submenu).removeClass('active');
            
            // Toggle current submenu
            $submenu.toggleClass('active');
            console.log('Mobile submenu toggled');
        });
        
        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').removeClass('active');
                $('.submenu').removeClass('active');
            }
        });
    } else {
        // Desktop hover behavior
        $('.dropdown').on('mouseenter', function() {
            $(this).find('.dropdown-menu').addClass('show-dropdown');
            console.log('Desktop dropdown shown');
        }).on('mouseleave', function() {
            $(this).find('.dropdown-menu').removeClass('show-dropdown');
            // Hide all submenus when leaving main dropdown
            document.querySelectorAll('.submenu').forEach(function(submenu) {
                submenu.style.opacity = '0';
                submenu.style.visibility = 'hidden';
                submenu.style.transform = 'translateX(-10px)';
            });
        });
        
        // Enhanced submenu hover handling
        $('.dropdown-item-wrapper').on('mouseenter', function() {
            var brandId = $(this).data('brand-id');
            if (brandId) {
                showSubmenu(brandId);
            }
        }).on('mouseleave', function() {
            var brandId = $(this).data('brand-id');
            if (brandId) {
                // Delay hiding to allow moving to submenu
                setTimeout(function() {
                    var submenu = document.getElementById('submenu-' + brandId);
                    if (submenu && !submenu.matches(':hover')) {
                        hideSubmenu(brandId);
                    }
                }, 100);
            }
        });
        
        // Keep submenu visible when hovering over it
        $('.submenu').on('mouseenter', function() {
            $(this).css({
                'opacity': '1',
                'visibility': 'visible',
                'transform': 'translateX(0)'
            });
        }).on('mouseleave', function() {
            $(this).css({
                'opacity': '0',
                'visibility': 'hidden',
                'transform': 'translateX(-10px)'
            });
        });
    }
    
    // Prevent dropdown link from navigating when clicking on toggle
    $('.dropdown-toggle').on('click', function(e) {
        if ($(window).width() > 768) {
            e.preventDefault();
            return false;
        }
    });
    
    // Close dropdown on escape key
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) { // Escape
            $('.dropdown-menu').removeClass('active show-dropdown');
            $('.submenu').removeClass('active');
        }
    });
    
    // Add smooth scrolling for dropdown items
    $('.dropdown-item, .submenu-item').on('click', function() {
        // Close dropdown after clicking
        $(this).closest('.dropdown-menu').removeClass('active show-dropdown');
        $('.submenu').removeClass('active');
    });
});

// Load and update cart count for all pages
function loadCartCount() {
    fetch('ajax/get_cart_count.php')
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            var cartElements = document.querySelectorAll('.cart-count, .cart-badge, #cart-count, #mobile-cart-count');
            cartElements.forEach(function(element) {
                if (element) {
                    element.textContent = data.count;
                    // Hide badge if count is 0
                    if (data.count == 0) {
                        element.style.display = 'none';
                    } else {
                        element.style.display = 'block';
                    }
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
=======
>>>>>>> cebc63a3bc4f7e2f5ae4119daff21338fea35eb8
</script>