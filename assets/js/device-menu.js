// Device Menu JavaScript - Standalone Version
console.log('🔄 Loading Standalone DeviceMenu');

window.DeviceMenu = {
    isLoading: false,
    loadingCategories: false,
    currentRequest: null,
    
    init: function() {
        console.log('🚀 Initializing Device Menu...');
        
        this.setupBrandEvents();
        this.setupCategoryEvents();
        this.loadFirstBrand();
        
        console.log('✅ Device Menu initialized successfully');
    },
    
    setupBrandEvents: function() {
        console.log('🎯 Setting up brand events...');
        
        // Clean up existing event listeners to prevent duplicates
        const existingBrandListeners = document.querySelectorAll('.brand-item[data-listener="true"]');
        existingBrandListeners.forEach(item => {
            item.removeAttribute('data-listener');
        });
        
        // Use event delegation with polyfill for better compatibility
        document.addEventListener('mouseenter', (e) => {
            const target = e.target || e.srcElement;
            let brandItem = null;
            
            if (target.closest) {
                brandItem = target.closest('.brand-item');
            } else {
                // Fallback for older browsers
                let element = target;
                while (element && element !== document) {
                    if (element.classList && element.classList.contains('brand-item')) {
                        brandItem = element;
                        break;
                    }
                    element = element.parentNode;
                }
            }
            
            if (brandItem && brandItem.dataset.brand && !this.isLoading) {
                console.log('🖱️ Mouse entered brand:', brandItem.dataset.brand);
                this.selectBrand(brandItem);
            }
        }.bind(this), true);
        
        document.addEventListener('click', (e) => {
            const target = e.target || e.srcElement;
            let brandItem = null;
            
            if (target.closest) {
                brandItem = target.closest('.brand-item');
            } else {
                // Fallback for older browsers
                let element = target;
                while (element && element !== document) {
                    if (element.classList && element.classList.contains('brand-item')) {
                        brandItem = element;
                        break;
                    }
                    element = element.parentNode;
                }
            }
            
            if (brandItem && brandItem.dataset.brand && !this.isLoading) {
                console.log('🖱️ Clicked brand:', brandItem.dataset.brand);
                this.selectBrand(brandItem);
            }
        }.bind(this));
        
        console.log('✅ Brand events setup complete');
    },
    
    setupCategoryEvents: function() {
        // Remove any existing event listeners to prevent duplicates
        if (this.categoryEventHandler) {
            document.removeEventListener('mouseenter', this.categoryEventHandler, true);
        }
        if (this.categoryClickHandler) {
            document.removeEventListener('click', this.categoryClickHandler);
        }
        
        // Create bound event handlers with polyfill for older browsers
        this.categoryEventHandler = (e) => {
            // Polyfill for closest() method
            const target = e.target || e.srcElement;
            let categoryItem = null;
            
            if (target.closest) {
                categoryItem = target.closest('.category-item');
            } else {
                // Fallback for older browsers
                let element = target;
                while (element && element !== document) {
                    if (element.classList && element.classList.contains('category-item')) {
                        categoryItem = element;
                        break;
                    }
                    element = element.parentNode;
                }
            }
            
            if (categoryItem && categoryItem.dataset.category && !categoryItem.classList.contains('loading')) {
                console.log('📁 Category hovered:', categoryItem.dataset.category);
                this.selectCategory(categoryItem);
            }
        };
        
        this.categoryClickHandler = (e) => {
            // Polyfill for closest() method
            const target = e.target || e.srcElement;
            let categoryItem = null;
            
            if (target.closest) {
                categoryItem = target.closest('.category-item');
            } else {
                // Fallback for older browsers
                let element = target;
                while (element && element !== document) {
                    if (element.classList && element.classList.contains('category-item')) {
                        categoryItem = element;
                        break;
                    }
                    element = element.parentNode;
                }
            }
            
            if (categoryItem && categoryItem.dataset.category && !categoryItem.classList.contains('loading')) {
                console.log('📁 Category clicked:', categoryItem.dataset.category);
                this.selectCategory(categoryItem);
            }
        };
        
        // Use document-level event delegation for better reliability
        document.addEventListener('mouseenter', this.categoryEventHandler, true);
        document.addEventListener('click', this.categoryClickHandler);
        
        console.log('✅ Category events setup with document delegation');
    },
    
    selectBrand: function(brandItem) {
        console.log('🎯 Selecting brand:', brandItem.dataset.brand);
        
        // Remove active class from all brands
        document.querySelectorAll('.brand-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Add active class to selected brand
        brandItem.classList.add('active');
        
        // Load categories for this brand
        const brandId = brandItem.dataset.brand;
        console.log('📡 Loading categories for brand ID:', brandId);
        this.loadCategories(brandId);
        
        // Clear models
        const modelsSection = document.getElementById('models-section');
        if (modelsSection) {
            modelsSection.innerHTML = '<div class="menu-item no-data">اختر فئة لعرض الموديلات</div>';
        }
    },
    
    selectCategory: function(categoryItem) {
        // Remove active class from all categories
        document.querySelectorAll('.category-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Add active class to selected category
        categoryItem.classList.add('active');
        
        // Load models
        this.loadModels(categoryItem.dataset.category);
    },
    
    loadFirstBrand: function() {
        setTimeout(() => {
            const firstBrand = document.querySelector('.brand-item');
            if (firstBrand) {
                this.selectBrand(firstBrand);
            }
        }, 500);
    },
    
    loadCategories: function(brandId) {
        console.log('📡 Starting loadCategories for brand:', brandId);
        
        const categoriesSection = document.getElementById('categories-section');
        if (!categoriesSection) {
            console.error('❌ Categories section not found!');
            return;
        }
        
        // Prevent multiple simultaneous requests
        if (this.loadingCategories) {
            console.log('⏳ Categories already loading, skipping...');
            return;
        }
        this.loadingCategories = true;
        
        // Show loading
        categoriesSection.innerHTML = '<div class="menu-item loading"><i class="fas fa-spinner fa-spin"></i> جاري التحميل...</div>';
        
        const formData = new FormData();
        formData.append('brand_id', brandId);
        
        const url = this.getBaseUrl() + 'ajax/get_brand_categories.php';
        console.log('🌐 AJAX URL:', url);
        
        // Create AbortController here
        const controller = new AbortController();
        
        const timeoutId = setTimeout(() => {
            console.log('⏰ Request timeout - aborting');
            controller.abort();
        }, 10000); // 10 second timeout
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache'
            },
            signal: controller.signal
        })
        .then(response => {
            console.log('📥 Response received:', response.status, response.statusText);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('📊 Categories data received:', data);
            if (data.success && data.categories) {
                let html = '';
                data.categories.forEach(category => {
                    html += `<div class="menu-item category-item" data-category="${category.id}">${category.name}</div>`;
                });
                categoriesSection.innerHTML = html || '<div class="menu-item no-data">لا توجد فئات متاحة</div>';
                console.log('✅ Categories loaded successfully:', data.categories.length, 'items');
            } else {
                console.error('❌ Invalid response format:', data);
                categoriesSection.innerHTML = '<div class="menu-item error">فشل في تحميل الفئات</div>';
            }
        })
        .catch(error => {
            if (error.name !== 'AbortError') {
                console.error('❌ Error loading categories:', error);
                categoriesSection.innerHTML = '<div class="menu-item error">خطأ في تحميل الفئات</div>';
                
                // Try fallback URL
                const fallbackUrl = window.location.origin + '/gayar_plus/ajax/get_brand_categories.php';
                if (url !== fallbackUrl) {
                    console.log('🔄 Trying fallback URL:', fallbackUrl);
                    this.loadCategoriesWithFallback(brandId, fallbackUrl);
                }
            }
        })
        .finally(() => {
            clearTimeout(timeoutId);
            this.loadingCategories = false;
            console.log('🏁 loadCategories completed');
        });
    },
    
    loadModels: function(categoryId) {
        const modelsSection = document.getElementById('models-section');
        if (!modelsSection) return;
        
        modelsSection.innerHTML = '<div class="menu-item loading">جاري التحميل...</div>';
        
        const formData = new FormData();
        formData.append('category_id', categoryId);
        
        const url = this.getBaseUrl() + 'ajax/get_category_models.php';
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.models) {
                let html = '';
                data.models.forEach(model => {
                    html += `<div class="menu-item model-item" onclick="navigateToModel(${model.id})">${model.name}</div>`;
                });
                modelsSection.innerHTML = html || '<div class="menu-item no-data">لا توجد موديلات متاحة</div>';
            } else {
                modelsSection.innerHTML = '<div class="menu-item error">فشل في تحميل الموديلات</div>';
            }
        })
        .catch(error => {
            console.error('Error loading models:', error);
            modelsSection.innerHTML = '<div class="menu-item error">خطأ في تحميل الموديلات</div>';
        });
    },
    
    getBaseUrl: function() {
        // Use global _base_url_ if available
        if (typeof _base_url_ !== 'undefined' && _base_url_) {
            return _base_url_;
        }
        
        // Fallback logic for localhost and production
        const protocol = window.location.protocol;
        const host = window.location.host;
        const pathname = window.location.pathname;
        
        let baseUrl = protocol + '//' + host;
        
        // Handle localhost with project folder
        if (host.includes('localhost') || host.includes('127.0.0.1')) {
            if (pathname.includes('/gayar_plus/')) {
                baseUrl += '/gayar_plus/';
            } else {
                // Try to detect project folder from current path
                const pathParts = pathname.split('/');
                if (pathParts.length > 1 && pathParts[1]) {
                    baseUrl += '/' + pathParts[1] + '/';
                } else {
                    baseUrl += '/gayar_plus/';
                }
            }
        } else {
            // Production environment
            baseUrl += '/';
        }
        
        console.log('🌐 Base URL detected:', baseUrl);
        return baseUrl;
    },
    
    loadCategoriesWithFallback: function(brandId, fallbackUrl) {
        const formData = new FormData();
        formData.append('brand_id', brandId);
        
        fetch(fallbackUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const categoriesSection = document.getElementById('categories-section');
            if (data.success && data.categories) {
                let html = '';
                data.categories.forEach(category => {
                    html += `<div class="menu-item category-item" data-category="${category.id}">${category.name}</div>`;
                });
                categoriesSection.innerHTML = html || '<div class="menu-item no-data">لا توجد فئات متاحة</div>';
            } else {
                categoriesSection.innerHTML = '<div class="menu-item error">فشل في تحميل الفئات</div>';
            }
        })
        .catch(error => {
            console.error('Fallback also failed:', error);
        });
    }
};

// Initialize when DOM is ready
function initializeDeviceMenu() {
    console.log('🔄 Attempting Device Menu initialization...');
    
    // Check if elements exist
    const brandsContainer = document.getElementById('brands-container');
    const categoriesSection = document.getElementById('categories-section');
    const modelsSection = document.getElementById('models-section');
    
    if (!brandsContainer || !categoriesSection || !modelsSection) {
        console.log('❌ Required elements not found, retrying...');
        return false;
    }
    
    if (typeof window.DeviceMenu !== 'undefined') {
        try {
            window.DeviceMenu.init();
            console.log('✅ DeviceMenu initialized successfully');
            return true;
        } catch (e) {
            console.error('❌ DeviceMenu initialization failed:', e);
            return false;
        }
    }
    
    console.log('❌ DeviceMenu object not found');
    return false;
}

// Multiple initialization attempts with exponential backoff
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM Content Loaded - attempting initialization');
    
    if (initializeDeviceMenu()) {
        return;
    }
    
    // Retry with exponential backoff
    let retryCount = 0;
    const maxRetries = 5;
    
    function retryInit() {
        retryCount++;
        const delay = Math.pow(2, retryCount) * 100; // 200ms, 400ms, 800ms, 1600ms, 3200ms
        
        console.log(`🔄 Retry attempt ${retryCount}/${maxRetries} in ${delay}ms`);
        
        setTimeout(() => {
            if (initializeDeviceMenu() || retryCount >= maxRetries) {
                if (retryCount >= maxRetries) {
                    console.error('❌ Max retries reached, DeviceMenu initialization failed');
                }
                return;
            }
            retryInit();
        }, delay);
    }
    
    retryInit();
});

// Also try on window load as fallback
window.addEventListener('load', function() {
    console.log('🪟 Window loaded - attempting backup initialization');
    
    setTimeout(() => {
        if (document.getElementById('brands-container') && !window.DeviceMenu.initialized) {
            initializeDeviceMenu();
        }
    }, 1000);
});

console.log('✅ Standalone DeviceMenu script loaded');
