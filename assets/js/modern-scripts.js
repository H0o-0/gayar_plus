/**
 * Modern Scripts for Gayar Plus - Complete JavaScript Functionality
 * Extracted from the beautiful new design with enhancements
 * Professional interactions, animations, and database integration
 */

// ========== البيانات والمتغيرات العامة ==========

// بيانات الأجهزة المنظمة المستخرجة من الكود الأصلي
const devicesDatabase = {
    apple: {
        name: 'آبل Apple',
        categories: {
            'iphone-15': {
                name: 'iPhone 15 Series',
                phones: ['iPhone 15', 'iPhone 15 Plus', 'iPhone 15 Pro', 'iPhone 15 Pro Max']
            },
            'iphone-14': {
                name: 'iPhone 14 Series', 
                phones: ['iPhone 14', 'iPhone 14 Plus', 'iPhone 14 Pro', 'iPhone 14 Pro Max']
            },
            'ipad': {
                name: 'iPad Series',
                phones: ['iPad Air', 'iPad Pro 11"', 'iPad Pro 12.9"', 'iPad Mini']
            }
        }
    },
    samsung: {
        name: 'سامسونج Samsung',
        categories: {
            'galaxy-s24': {
                name: 'Galaxy S24 Series',
                phones: ['Galaxy S24', 'Galaxy S24+', 'Galaxy S24 Ultra']
            },
            'galaxy-a': {
                name: 'Galaxy A Series', 
                phones: ['Galaxy A54', 'Galaxy A34', 'Galaxy A24']
            }
        }
    },
    huawei: {
        name: 'هواوي Huawei',
        categories: {
            'p-series': {
                name: 'P Series',
                phones: ['P60 Pro', 'P60', 'P50 Pro']
            },
            'mate-series': {
                name: 'Mate Series',
                phones: ['Mate 50 Pro', 'Mate 50']
            }
        }
    },
    xiaomi: {
        name: 'شاومي Xiaomi',
        categories: {
            'xiaomi-13': {
                name: 'Xiaomi 13 Series',
                phones: ['Xiaomi 13', 'Xiaomi 13 Pro', 'Xiaomi 13 Ultra']
            },
            'redmi-note': {
                name: 'Redmi Note Series',
                phones: ['Redmi Note 12', 'Redmi Note 12 Pro']
            }
        }
    }
};

// متغيرات عامة مستخرجة من الكود الأصلي
let cartCount = 0;
const cartCountElement = document.getElementById('cart-count');

// ========== وظائف شريط التنقل ==========

// تأثير شريط التنقل عند التمرير - مستخرج من الكود الأصلي
function initializeNavbarScrollEffect() {
    window.addEventListener('scroll', () => {
        const navbar = document.getElementById('navbar');
        if (navbar) {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
    });
}

// ========== نظام القائمة المتعددة المستويات ==========

// نظام القائمة المتعددة المستويات - مستخرج من الكود الأصلي
function initializeNavigationSystem() {
    const brandItems = document.querySelectorAll('.brand-item');
    const categoriesSection = document.getElementById('categories-section');
    const phonesSection = document.getElementById('phones-section');

    if (brandItems.length > 0 && categoriesSection && phonesSection) {
        brandItems.forEach(brandItem => {
            brandItem.addEventListener('mouseenter', function() {
                const brandKey = this.dataset.brand;
                const brandData = devicesDatabase[brandKey];
                
                if (brandData) {
                    // تحديث قسم الفئات
                    categoriesSection.innerHTML = '';
                    Object.keys(brandData.categories).forEach(categoryKey => {
                        const category = brandData.categories[categoryKey];
                        const categoryElement = document.createElement('div');
                        categoryElement.className = 'menu-item';
                        categoryElement.textContent = category.name;
                        categoryElement.dataset.category = categoryKey;
                        categoryElement.dataset.brand = brandKey;
                        
                        // إضافة حدث التمرير للفئة
                        categoryElement.addEventListener('mouseenter', function() {
                            const phones = devicesDatabase[this.dataset.brand].categories[this.dataset.category].phones;
                            phonesSection.innerHTML = '';
                            phones.forEach(phone => {
                                const phoneElement = document.createElement('div');
                                phoneElement.className = 'menu-item';
                                phoneElement.textContent = phone;
                                phonesSection.appendChild(phoneElement);
                            });
                        });
                        
                        categoriesSection.appendChild(categoryElement);
                    });
                } else {
                    // إذا لم تكن البيانات متوفرة، اطلبها من الخادم
                    loadBrandDataFromServer(brandKey, categoriesSection, phonesSection);
                }
            });
        });
    }
}

// تحميل بيانات العلامة التجارية من قاعدة البيانات
async function loadBrandDataFromServer(brandKey, categoriesSection, phonesSection) {
    try {
        const response = await fetch(`ajax/get_brand_data.php?brand=${brandKey}`);
        const data = await response.json();
        
        if (data.success) {
            devicesDatabase[brandKey] = data.data;
            
            // تحديث قسم الفئات
            categoriesSection.innerHTML = '';
            Object.keys(data.data.categories).forEach(categoryKey => {
                const category = data.data.categories[categoryKey];
                const categoryElement = document.createElement('div');
                categoryElement.className = 'menu-item';
                categoryElement.textContent = category.name;
                categoryElement.dataset.category = categoryKey;
                categoryElement.dataset.brand = brandKey;
                
                categoryElement.addEventListener('mouseenter', function() {
                    const phones = devicesDatabase[this.dataset.brand].categories[this.dataset.category].phones;
                    phonesSection.innerHTML = '';
                    phones.forEach(phone => {
                        const phoneElement = document.createElement('div');
                        phoneElement.className = 'menu-item';
                        phoneElement.textContent = phone;
                        phonesSection.appendChild(phoneElement);
                    });
                });
                
                categoriesSection.appendChild(categoryElement);
            });
        }
    } catch (error) {
        console.error('Error loading brand data:', error);
        categoriesSection.innerHTML = '<div class="menu-item">خطأ في تحميل البيانات</div>';
    }
}

// ========== نظام السلة ==========

// تم نقل وظائف السلة إلى home.php لتجنب التعارض
// Cart functionality moved to home.php to avoid conflicts

// تحديث عداد السلة - مستخرج من الكود الأصلي
function updateCartCount(count) {
    if (cartCountElement) {
        cartCountElement.textContent = count;
        
        // تأثير على أيقونة السلة - مستخرج من الكود الأصلي
        const cartBtn = document.querySelector('.cart-btn');
        if (cartBtn) {
            cartBtn.style.transform = 'scale(1.1)';
            setTimeout(() => {
                cartBtn.style.transform = '';
            }, 200);
        }
    }
}

// تحميل عداد السلة من الخادم
async function loadCartCount() {
    try {
        const response = await fetch('ajax/get_cart_count.php');
        const data = await response.json();
        
        if (data.success && cartCountElement) {
            cartCountElement.textContent = data.count;
            cartCount = data.count;
        }
    } catch (error) {
        console.error('Error loading cart count:', error);
    }
}

// ========== نظام الإشعارات ==========

// دالة إظهار رسائل النجاح - مستخرجة ومحسنة من الكود الأصلي
function showSuccessNotification(message) {
    showNotification(message, 'success');
}

// دالة إظهار رسائل الخطأ
function showErrorNotification(message) {
    showNotification(message, 'error');
}

// دالة عامة لإظهار الإشعارات - مستخرجة ومحسنة من الكود الأصلي
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'var(--accent-emerald)' : '#ef4444';
    const bgGradient = type === 'success' ? '#10b981' : '#dc2626';
    
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: linear-gradient(135deg, ${bgColor}, ${bgGradient});
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

    // إظهار الإشعار - مستخرج من الكود الأصلي
    requestAnimationFrame(() => {
        notification.style.transform = 'translateX(0)';
    });

    // إخفاء الإشعار - مستخرج من الكود الأصلي
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// ========== التأثيرات التفاعلية ==========

// تأثيرات التفاعل المتقدمة - مستخرج من الكود الأصلي
function initializeInteractiveEffects() {
    document.querySelectorAll('.interactive-hover').forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });

    // تحسين الأداء مع will-change - مستخرج من الكود الأصلي
    document.querySelectorAll('.will-change').forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.willChange = 'transform, box-shadow';
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.willChange = 'auto';
        });
    });
}

// ========== التمرير والحركة ==========

// تمرير سلس للروابط - مستخرج من الكود الأصلي
function initializeSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            // Skip empty or just "#" hrefs
            if (!href || href === '#' || href === '') return;
            // Additional safety check for valid CSS selector
            if (href.length > 1) {
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
}

// تأثيرات الظهور التدريجي للعناصر - مستخرج من الكود الأصلي
function initializeScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // مراقبة العناصر - مستخرج من الكود الأصلي
    document.querySelectorAll('.product-card, .feature-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        observer.observe(el);
    });
}

// ========== البحث والتصفية ==========

// بحث المنتجات
function initializeSearch() {
    const searchBtn = document.querySelector('.search-btn');
    const searchModal = document.getElementById('search-modal');
    const searchInput = document.getElementById('search-input');
    
    if (searchBtn && searchModal) {
        searchBtn.addEventListener('click', () => {
            searchModal.style.display = 'block';
            if (searchInput) searchInput.focus();
        });
        
        // إغلاق البحث عند النقر خارجه
        searchModal.addEventListener('click', (e) => {
            if (e.target === searchModal) {
                searchModal.style.display = 'none';
            }
        });
        
        // البحث المباشر
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch(e.target.value);
                }, 300);
            });
        }
    }
}

// تنفيذ البحث
async function performSearch(query) {
    if (query.length < 2) return;
    
    try {
        const response = await fetch(`ajax/search_products.php?q=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        const resultsContainer = document.getElementById('search-results');
        if (resultsContainer) {
            if (data.success && data.products.length > 0) {
                resultsContainer.innerHTML = data.products.map(product => `
                    <div class="search-result-item" onclick="viewProduct(${product.id})">
                        <img src="${product.image}" alt="${product.name}">
                        <div>
                            <h4>${product.name}</h4>
                            <p>${product.price} د.ع</p>
                        </div>
                    </div>
                `).join('');
            } else {
                resultsContainer.innerHTML = '<div class="no-results">لم يتم العثور على نتائج</div>';
            }
        }
    } catch (error) {
        console.error('Error searching:', error);
    }
}

// عرض المنتج
function viewProduct(productId) {
    // Ensure we have a valid productId
    if (!productId) {
        console.error('viewProduct: productId is required');
        return;
    }
    
    // Convert to MD5 hash if it's a numeric ID
    let productHash = productId;
    if (typeof productId === 'number' || /^\d+$/.test(productId)) {
        // If it's a numeric ID, we need to convert it to MD5
        // For now, redirect to a page that can handle the conversion
        window.location.href = `product_view.php?id=${productId}`;
    } else {
        // Assume it's already an MD5 hash
        window.location.href = `./?p=product_view&id=${productHash}`;
    }
}

// ========== إدارة السلة المتقدمة ==========

// تحديث كمية المنتج في السلة
async function updateCartQuantity(productId, quantity) {
    try {
        const response = await fetch('ajax/update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            updateCartCount(data.cart_count);
            showSuccessNotification('تم تحديث الكمية بنجاح');
            
            // تحديث السعر الإجمالي إذا كنا في صفحة السلة
            if (data.item_total) {
                const itemTotalElement = document.querySelector(`[data-item-id="${productId}"] .item-total`);
                if (itemTotalElement) {
                    itemTotalElement.textContent = data.item_total + ' د.ع';
                }
            }
            
            if (data.cart_total) {
                const cartTotalElement = document.querySelector('.cart-total');
                if (cartTotalElement) {
                    cartTotalElement.textContent = data.cart_total + ' د.ع';
                }
            }
        } else {
            showErrorNotification(data.message || 'خطأ في تحديث الكمية');
        }
    } catch (error) {
        console.error('Error updating cart:', error);
        showErrorNotification('خطأ في الاتصال بالخادم');
    }
}

// حذف المنتج من السلة
async function removeFromCart(productId) {
    if (!confirm('هل أنت متأكد من حذف هذا المنتج من السلة؟')) {
        return;
    }
    
    try {
        const response = await fetch('ajax/remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            updateCartCount(data.cart_count);
            showSuccessNotification('تم حذف المنتج من السلة');
            
            // إزالة العنصر من الصفحة
            const itemElement = document.querySelector(`[data-item-id="${productId}"]`);
            if (itemElement) {
                itemElement.remove();
            }
            
            // تحديث الإجمالي
            if (data.cart_total !== undefined) {
                const cartTotalElement = document.querySelector('.cart-total');
                if (cartTotalElement) {
                    cartTotalElement.textContent = data.cart_total + ' د.ع';
                }
            }
        } else {
            showErrorNotification(data.message || 'خطأ في حذف المنتج');
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
        showErrorNotification('خطأ في الاتصال بالخادم');
    }
}

// ========== وظائف إضافية محسنة ==========

// تحميل المنتجات بالتصفح اللانهائي
let isLoadingProducts = false;
let currentPage = 1;

function initializeInfiniteScroll() {
    window.addEventListener('scroll', () => {
        if (isLoadingProducts) return;
        
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 1000) {
            loadMoreProducts();
        }
    });
}

async function loadMoreProducts() {
    if (isLoadingProducts) return;
    
    isLoadingProducts = true;
    currentPage++;
    
    try {
        const response = await fetch(`ajax/load_more_products.php?page=${currentPage}`);
        const data = await response.json();
        
        if (data.success && data.products.length > 0) {
            const productsGrid = document.querySelector('.products-grid');
            if (productsGrid) {
                data.products.forEach(product => {
                    const productElement = createProductCard(product);
                    productsGrid.appendChild(productElement);
                });
            }
        }
    } catch (error) {
        console.error('Error loading more products:', error);
    } finally {
        isLoadingProducts = false;
    }
}

// إنشاء بطاقة منتج
function createProductCard(product) {
    const card = document.createElement('div');
    card.className = 'product-card will-change';
    card.innerHTML = `
        <div class="product-image">
            <img src="${product.image}" alt="${product.name}">
            ${product.badge ? `<div class="product-badge">${product.badge}</div>` : ''}
        </div>
        <div class="product-info">
            <div class="product-category">${product.category}</div>
            <h3 class="product-title">${product.name}</h3>
            <p class="product-description">${product.description}</p>
            <div class="product-footer">
                <span class="product-price">${product.price} د.ع</span>
                <button class="add-to-cart" onclick="addToCart(this, ${product.id})">
                    <i class="fas fa-cart-plus"></i>
                    أضف للسلة
                </button>
            </div>
        </div>
    `;
    return card;
}

// تحديث أسعار المنتجات في الوقت الفعلي
function updateProductPrices() {
    fetch('ajax/get_updated_prices.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                data.prices.forEach(item => {
                    const priceElement = document.querySelector(`[data-product-id="${item.id}"] .product-price`);
                    if (priceElement) {
                        priceElement.textContent = item.price + ' د.ع';
                    }
                });
            }
        })
        .catch(error => console.error('Error updating prices:', error));
}

// مراقبة حالة الاتصال
function monitorConnection() {
    window.addEventListener('online', () => {
        showSuccessNotification('تم استعادة الاتصال بالإنترنت');
    });
    
    window.addEventListener('offline', () => {
        showErrorNotification('انقطع الاتصال بالإنترنت');
    });
}

// ========== التهيئة الرئيسية ==========

// تفعيل جميع الوظائف عند تحميل الصفحة - مستخرج ومحسن من الكود الأصلي
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 بدء تحميل Gayar Plus Modern Scripts...');
    
    // الوظائف الأساسية المستخرجة من الكود الأصلي
    loadCartCount();
    initializeNavbarScrollEffect();
    initializeNavigationSystem();
    initializeInteractiveEffects();
    initializeSmoothScrolling();
    initializeScrollAnimations();
    
    // الوظائف المحسنة الجديدة
    initializeSearch();
    initializeInfiniteScroll();
    monitorConnection();
    
    // إضافة مستمع للنقر على زر السلة
    const cartBtn = document.querySelector('.cart-btn');
    if (cartBtn && !cartBtn.href) {
        cartBtn.addEventListener('click', () => {
            window.location.href = 'cart.php';
        });
    }
    
    // تحديث الأسعار كل 5 دقائق
    setInterval(updateProductPrices, 300000);
    
    console.log('✅ تم تحميل جميع وظائف Gayar Plus بنجاح!');
});

// ========== تصدير الوظائف للاستخدام العام ==========

// تصدير الدوال للاستخدام العام
window.GayarPlus = {
    // وظائف السلة
    addToCart,
    updateCartQuantity,
    removeFromCart,
    loadCartCount,
    
    // وظائف المنتجات
    viewProduct,
    performSearch,
    loadMoreProducts,
    
    // وظائف الإشعارات
    showSuccessNotification,
    showErrorNotification,
    showNotification,
    
    // وظائف البيانات
    devicesDatabase,
    loadBrandDataFromServer,
    
    // وظائف التفاعل
    initializeInteractiveEffects,
    initializeScrollAnimations,
    
    // المتغيرات العامة
    cartCount
};

// إضافة بعض الوظائف المساعدة
window.GayarPlus.utils = {
    // تنسيق الأرقام
    formatNumber: (num) => {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },
    
    // تنسيق العملة
    formatCurrency: (amount) => {
        return window.GayarPlus.utils.formatNumber(amount) + ' د.ع';
    },
    
    // التحقق من صحة البريد الإلكتروني
    validateEmail: (email) => {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },
    
    // التحقق من صحة رقم الهاتف العراقي
    validateIraqiPhone: (phone) => {
        const re = /^(\+964|0)?7[0-9]{9}$/;
        return re.test(phone);
    }
};

console.log('📱 Gayar Plus - Modern JavaScript Framework Loaded Successfully! 🇮🇶');