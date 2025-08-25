/* Modern Homepage JavaScript for Gayar Plus */

// بيانات الأجهزة المنظمة (يمكن تحديثها من قاعدة البيانات لاحقاً)
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

// متغيرات عامة
let cartCount = 0;
const cartCountElement = document.getElementById('cart-count');

// تأثير شريط التنقل عند التمرير
function initNavbarScroll() {
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

// نظام القائمة المتعددة المستويات
function initMegaMenu() {
    const brandItems = document.querySelectorAll('.brand-item');
    const categoriesSection = document.getElementById('categories-section');
    const phonesSection = document.getElementById('phones-section');

    if (!categoriesSection || !phonesSection) return;

    brandItems.forEach(brandItem => {
        brandItem.addEventListener('mouseenter', function() {
            const brandKey = this.dataset.brand;
            
            // إذا كان لديه data-brand كرقم، نستخدم البيانات من الخادم
            if (brandKey && !isNaN(brandKey)) {
                loadBrandCategoriesFromServer(brandKey);
                return;
            }
            
            // استخدام البيانات المحلية للعرض التوضيحي
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
            }
        });
    });
}

// تحميل فئات العلامة التجارية من الخادم
async function loadBrandCategoriesFromServer(brandId) {
    try {
        const response = await fetch(`ajax/get_brand_categories.php?brand_id=${brandId}`);
        const data = await response.json();
        
        const categoriesSection = document.getElementById('categories-section');
        const phonesSection = document.getElementById('phones-section');
        
        if (data.success && data.categories.length > 0) {
            categoriesSection.innerHTML = '';
            data.categories.forEach(category => {
                const categoryElement = document.createElement('div');
                categoryElement.className = 'menu-item';
                categoryElement.textContent = category.name;
                categoryElement.dataset.categoryId = category.id;
                categoryElement.dataset.brand = brandId;
                
                // إضافة حدث التمرير للفئة
                categoryElement.addEventListener('mouseenter', function() {
                    loadCategoryModelsFromServer(category.id);
                });
                
                categoriesSection.appendChild(categoryElement);
            });
            
            // إعادة تعيين قسم الهواتف
            phonesSection.innerHTML = '<div class="menu-item">اختر فئة لعرض الهواتف</div>';
        } else {
            categoriesSection.innerHTML = '<div class="menu-item">لا توجد فئات</div>';
            phonesSection.innerHTML = '<div class="menu-item">اختر فئة لعرض الهواتف</div>';
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        const categoriesSection = document.getElementById('categories-section');
        const phonesSection = document.getElementById('phones-section');
        categoriesSection.innerHTML = '<div class="menu-item">خطأ في تحميل الفئات</div>';
        phonesSection.innerHTML = '<div class="menu-item">اختر فئة لعرض الهواتف</div>';
    }
}

// تحميل موديلات الفئة من الخادم
async function loadCategoryModelsFromServer(categoryId) {
    try {
        const response = await fetch(`ajax/get_category_models.php?category_id=${categoryId}`);
        const data = await response.json();
        
        const phonesSection = document.getElementById('phones-section');
        
        if (data.success && data.models.length > 0) {
            phonesSection.innerHTML = '';
            data.models.forEach(model => {
                const modelElement = document.createElement('div');
                modelElement.className = 'menu-item';
                modelElement.textContent = model.name;
                modelElement.addEventListener('click', () => {
                    window.location.href = `./?p=products&m=${btoa(model.id)}`;
                });
                phonesSection.appendChild(modelElement);
            });
        } else {
            phonesSection.innerHTML = '<div class="menu-item">لا توجد موديلات</div>';
        }
    } catch (error) {
        console.error('Error loading models:', error);
        const phonesSection = document.getElementById('phones-section');
        phonesSection.innerHTML = '<div class="menu-item">خطأ في تحميل الموديلات</div>';
    }
}

// إضافة إلى السلة - تم نقل هذه الوظيفة إلى cart_fix.js لتجنب التعارض
// Cart functionality moved to cart_fix.js to avoid conflicts

// دالة إظهار رسائل النجاح
function showSuccessNotification(message) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: linear-gradient(135deg, var(--accent-emerald), #10b981);
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

    // إظهار الإشعار
    requestAnimationFrame(() => {
        notification.style.transform = 'translateX(0)';
    });

    // إخفاء الإشعار
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// تأثيرات التفاعل المتقدمة
function initInteractiveEffects() {
    // تأثيرات الماوس على العناصر التفاعلية
    document.querySelectorAll('.interactive-hover').forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });

    // تحسين الأداء مع will-change
    document.querySelectorAll('.will-change').forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.willChange = 'transform, box-shadow';
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.willChange = 'auto';
        });
    });
}

// تمرير سلس للروابط
function initSmoothScroll() {
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

// تفعيل التأثيرات عند التحميل
function initAnimations() {
    // إضافة تأثيرات تدريجية للعناصر
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

    // مراقبة العناصر
    document.querySelectorAll('.product-card, .feature-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        observer.observe(el);
    });
}

// تهيئة جميع الوظائف عند تحميل الصفحة
function initHomepage() {
    initNavbarScroll();
    initMegaMenu();
    initInteractiveEffects();
    initSmoothScroll();
    initAnimations();
}

// تشغيل عند تحميل DOM
document.addEventListener('DOMContentLoaded', initHomepage);

// تشغيل عند تحميل النافذة بالكامل
window.addEventListener('load', function() {
    // أي وظائف إضافية تحتاج تحميل كامل للصفحة
    console.log('Homepage loaded successfully');
});

// تصدير الوظائف للاستخدام العام
if (typeof window !== 'undefined') {
    // addToCart يتم تنفيذه في home.php
    window.showSuccessNotification = showSuccessNotification;
}