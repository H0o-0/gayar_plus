<?php
// Ensure we have access to the database connection
if (!isset($conn)) {
    require_once 'initialize.php';
}
?>

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
            <div class="search-results" id="search-results">
                <!-- Search results will be populated here -->
            </div>
        </div>
    </div>
</div>

<script>
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
function openSearch() {
    const searchModal = document.getElementById('search-modal');
    if (searchModal) {
        searchModal.style.display = 'block';
        document.getElementById('search-input').focus();
    }
}

// Function to close search modal
function closeSearch() {
    const searchModal = document.getElementById('search-modal');
    if (searchModal) {
        searchModal.style.display = 'none';
    }
}
</script>