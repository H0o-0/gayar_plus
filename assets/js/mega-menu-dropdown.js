/**
 * قائمة الملحقات المتدرجة الاحترافية
 * Mega Menu Dropdown JavaScript Handler
 * 
 * هذا الملف يدير التفاعل مع القائمة المتدرجة ثلاثية المستويات:
 * المستوى الأول: العلامات التجارية (Brands)
 * المستوى الثاني: الفئات (Categories/Series) 
 * المستوى الثالث: الموديلات (Models)
 */

/**
 * Polyfill for Element.closest() for older browsers
 */
if (!Element.prototype.closest) {
    Element.prototype.closest = function(s) {
        var matches = (this.document || this.ownerDocument).querySelectorAll(s),
            i,
            el = this;
        do {
            i = matches.length;
            while (--i >= 0 && matches.item(i) !== el) {};
        } while ((i < 0) && (el = el.parentElement));
        return el;
    };
}

/**
 * Helper function to find closest element (for better browser compatibility)
 */
function findClosest(element, selector) {
    if (!element || !selector) return null;
    
    // Use native closest if available
    if (element.closest) {
        return element.closest(selector);
    }
    
    // Fallback for older browsers
    let current = element;
    while (current && current !== document) {
        if (current.matches && current.matches(selector)) {
            return current;
        }
        if (current.className && current.className.indexOf && selector.startsWith('.')) {
            const className = selector.substring(1);
            if (current.className.indexOf(className) !== -1) {
                return current;
            }
        }
        current = current.parentElement;
    }
    return null;
}

class MegaMenuDropdown {
    constructor() {
        this.baseUrl = window._base_url_ || './';
        this.cache = {
            brands: null,
            categories: {},
            models: {}
        };
        this.loadingStates = {
            categories: {},
            models: {}
        };
        this.isMobile = window.innerWidth <= 768;
        this.init();
    }

    /**
     * تهيئة القائمة المتدرجة
     */
    init() {
        console.log('🔧 تهيئة القائمة المتدرجة...');
        
        // تحديث حالة الموبايل عند تغيير حجم الشاشة
        window.addEventListener('resize', () => {
            this.isMobile = window.innerWidth <= 768;
            this.handleResponsiveChanges();
        });

        // تهيئة أحداث الموبايل
        this.initializeMobileEvents();
        
        // إضافة مستمعي الأحداث للتفاعل مع القائمة
        this.attachEventListeners();
        
        console.log('✅ تم تهيئة القائمة المتدرجة بنجاح');
    }

    /**
     * تهيئة أحداث الموبايل
     */
    initializeMobileEvents() {
        if (this.isMobile) {
            // toggle القائمة الرئيسية للموبايل
            document.addEventListener('click', (e) => {
                const megaMenuToggle = findClosest(e.target, '.mega-menu-toggle');
                if (megaMenuToggle) {
                    e.preventDefault();
                    const dropdown = findClosest(megaMenuToggle, '.mega-menu-dropdown');
                    if (dropdown) {
                        dropdown.classList.toggle('mobile-active');
                    }
                }

                // toggle القوائم الفرعية للموبايل
                const brandLink = findClosest(e.target, '.brand-main-link');
                if (brandLink) {
                    const hasSubmenu = brandLink.parentElement.querySelector('.categories-submenu');
                    if (hasSubmenu) {
                        e.preventDefault();
                        brandLink.parentElement.classList.toggle('mobile-expanded');
                    }
                }

                const categoryLink = findClosest(e.target, '.category-link');
                if (categoryLink) {
                    const hasSubmenu = categoryLink.parentElement.querySelector('.models-submenu');
                    if (hasSubmenu) {
                        e.preventDefault();
                        categoryLink.parentElement.classList.toggle('mobile-expanded');
                    }
                }

                // إغلاق القوائم عند النقر خارجها
                if (!findClosest(e.target, '.mega-menu-dropdown')) {
                    document.querySelectorAll('.mega-menu-dropdown').forEach(dropdown => {
                        dropdown.classList.remove('mobile-active');
                    });
                    document.querySelectorAll('.brand-menu-item, .category-menu-item').forEach(item => {
                        item.classList.remove('mobile-expanded');
                    });
                }
            });
        }
    }

    /**
     * إدارة التغييرات المتجاوبة
     */
    handleResponsiveChanges() {
        // إعادة تعيين الحالات عند تغيير حجم الشاشة
        document.querySelectorAll('.mega-menu-dropdown').forEach(dropdown => {
            dropdown.classList.remove('mobile-active');
        });
        document.querySelectorAll('.brand-menu-item, .category-menu-item').forEach(item => {
            item.classList.remove('mobile-expanded');
        });
    }

    /**
     * إضافة مستمعي الأحداث
     */
    attachEventListeners() {
        // تحميل الفئات عند تمرير الماوس على العلامة التجارية (الهيكل الجديد والقديم)
        document.addEventListener('mouseenter', (e) => {
            // الهيكل الجديد
            const brandItem = findClosest(e.target, '.brand-menu-item[data-brand-id]');
            if (brandItem && !this.isMobile) {
                const brandId = brandItem.getAttribute('data-brand-id');
                this.loadCategoriesForBrand(brandId, brandItem);
                return;
            }
            
            // الهيكل القديم (topBarNav.php)
            const brandContainer = findClosest(e.target, '.brand-item-container[data-brand-id]');
            if (brandContainer && !this.isMobile) {
                const brandId = brandContainer.getAttribute('data-brand-id');
                this.loadCategoriesForExistingBrand(brandId, brandContainer);
            }
        }, true);

        // تحميل الموديلات عند تمرير الماوس على الفئة
        document.addEventListener('mouseenter', (e) => {
            const categoryItem = findClosest(e.target, '.category-menu-item[data-category-id]');
            if (categoryItem && !this.isMobile) {
                const categoryId = categoryItem.getAttribute('data-category-id');
                const brandContainer = findClosest(categoryItem, '[data-brand-id]');
                if (brandContainer) {
                    const brandId = brandContainer.getAttribute('data-brand-id');
                    this.loadModelsForCategory(categoryId, brandId, categoryItem);
                }
            }
        }, true);
    }

    /**
     * تحميل الفئات للعلامة التجارية المحددة
     */
    async loadCategoriesForBrand(brandId, brandElement) {
        // التحقق من وجود البيانات في الذاكرة المؤقتة
        if (this.cache.categories[brandId]) {
            this.displayCategories(brandId, this.cache.categories[brandId], brandElement);
            return;
        }

        // التحقق من حالة التحميل
        if (this.loadingStates.categories[brandId]) {
            return;
        }

        const submenu = brandElement.querySelector('.categories-submenu');
        const categoriesList = submenu.querySelector('.categories-list');
        
        try {
            this.loadingStates.categories[brandId] = true;
            
            // عرض مؤشر التحميل
            this.showLoadingSpinner(categoriesList);

            const formData = new FormData();
            formData.append('brand_id', brandId);

            const response = await fetch(this.baseUrl + 'ajax/get_brand_categories.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                // حفظ البيانات في الذاكرة المؤقتة
                this.cache.categories[brandId] = data.categories;
                this.displayCategories(brandId, data.categories, brandElement);
            } else {
                throw new Error(data.message || 'خطأ في تحميل الفئات');
            }

        } catch (error) {
            console.error('خطأ في تحميل فئات العلامة التجارية:', error);
            this.showErrorMessage(categoriesList, 'خطأ في تحميل الفئات');
        } finally {
            this.loadingStates.categories[brandId] = false;
        }
    }

    /**
     * عرض الفئات في القائمة الفرعية
     */
    displayCategories(brandId, categories, brandElement) {
        // البحث عن submenu بالطرق المختلفة
        let submenu = brandElement.querySelector('.categories-submenu') || brandElement.querySelector('.brand-submenu');
        let categoriesList = null;
        
        if (submenu) {
            categoriesList = submenu.querySelector('.categories-list') || submenu.querySelector('.submenu-list');
        }
        
        if (!categoriesList) {
            console.warn('لم يتم العثور على حاوي الفئات');
            return;
        }
        
        const brandNameElement = brandElement.querySelector('.brand-name-text') || brandElement.querySelector('.brand-name');
        const brandName = brandNameElement ? brandNameElement.textContent : 'العلامة التجارية';
        
        let categoriesHTML = '';

        // إضافة رابط "جميع الفئات"
        categoriesHTML += `
            <a href="products.php?brand=${brandId}" class="submenu-link all-categories">
                جميع الفئات
            </a>
        `;

        // إضافة الفئات
        if (categories.length > 0) {
            categories.forEach(category => {
                categoriesHTML += `
                    <a href="products.php?brand=${brandId}&category=${category.id}" class="submenu-link" data-category-id="${category.id}" data-brand-id="${brandId}">
                        ${this.escapeHtml(category.name)}
                    </a>
                `;
            });
        } else {
            categoriesHTML += `
                <div class="no-data-message" style="padding: 1rem; text-align: center; color: #9ca3af;">
                    <i class="fas fa-folder-open"></i>
                    <p>لا توجد فئات متاحة</p>
                </div>
            `;
        }

        categoriesList.innerHTML = categoriesHTML;
        
        console.log(`✅ تم عرض ${categories.length} فئة للعلامة التجارية ${brandName}`);
    }

    /**
     * تحميل الموديلات للفئة المحددة مع معلومات محسنة
     */
    async loadModelsForCategory(categoryId, brandId, categoryElement) {
        const cacheKey = `${brandId}_${categoryId}`;
        
        // التحقق من وجود البيانات في الذاكرة المؤقتة
        if (this.cache.models[cacheKey]) {
            this.displayModels(categoryId, brandId, this.cache.models[cacheKey], categoryElement);
            return;
        }

        // التحقق من حالة التحميل
        if (this.loadingStates.models[cacheKey]) {
            return;
        }

        const submenu = categoryElement.querySelector('.models-submenu');
        const modelsList = submenu.querySelector('.models-list');
        
        try {
            this.loadingStates.models[cacheKey] = true;

            const formData = new FormData();
            formData.append('category_id', categoryId);
            formData.append('brand_id', brandId);

            // استخدام الملف المحسن مع معلومات إضافية
            const response = await fetch(this.baseUrl + 'ajax/get_models_enhanced.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                // حفظ البيانات في الذاكرة المؤقتة
                this.cache.models[cacheKey] = {
                    models: data.models,
                    category_info: data.category_info,
                    total_count: data.total_count
                };
                this.displayModels(categoryId, brandId, data.models, categoryElement, data.category_info);
            } else {
                throw new Error(data.message || 'خطأ في تحميل الموديلات');
            }

        } catch (error) {
            console.error('خطأ في تحميل موديلات الفئة:', error);
            this.showErrorMessage(modelsList, 'خطأ في تحميل الموديلات');
        } finally {
            this.loadingStates.models[cacheKey] = false;
        }
    }

    /**
     * عرض الموديلات في القائمة الفرعية
     */
    displayModels(categoryId, brandId, models, categoryElement) {
        const submenu = categoryElement.querySelector('.models-submenu');
        const modelsList = submenu.querySelector('.models-list');
        const categoryName = categoryElement.querySelector('.category-name').textContent;
        
        // تحديث رأس قائمة الموديلات
        const modelsHeader = submenu.querySelector('.models-header');
        if (modelsHeader) {
            modelsHeader.innerHTML = `
                <h5 class="models-category-name">${categoryName}</h5>
                <p class="models-count">${models.length} موديل متاح</p>
            `;
        }

        let modelsHTML = '';

        // إضافة رابط "جميع الموديلات"
        modelsHTML += `
            <a href="products.php?brand=${brandId}&category=${categoryId}" class="model-link all-models-link">
                جميع الموديلات
            </a>
        `;

        // إضافة الموديلات
        if (models.length > 0) {
            models.forEach(model => {
                modelsHTML += `
                    <a href="products.php?brand=${brandId}&category=${categoryId}&model=${model.id}" class="model-link">
                        ${this.escapeHtml(model.name)}
                    </a>
                `;
            });
        } else {
            modelsHTML += `
                <div class="no-data-message">
                    <i class="fas fa-mobile-alt"></i>
                    <p>لا توجد موديلات متاحة</p>
                </div>
            `;
        }

        modelsList.innerHTML = modelsHTML;
    }

    /**
     * عرض مؤشر التحميل
     */
    showLoadingSpinner(container) {
        container.innerHTML = `
            <div class="loading-spinner active">
                <div class="spinner"></div>
                <p>جاري التحميل...</p>
            </div>
        `;
    }

    /**
     * عرض رسالة خطأ
     */
    showErrorMessage(container, message) {
        container.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>${message}</p>
            </div>
        `;
    }

    /**
     * تنظيف HTML لمنع XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * التنقل إلى صفحة المنتجات مع المرشحات
     */
    navigateToProducts(params = {}) {
        const urlParams = new URLSearchParams();
        
        Object.keys(params).forEach(key => {
            if (params[key]) {
                urlParams.append(key, params[key]);
            }
        });

        const url = `products.php?${urlParams.toString()}`;
        window.location.href = url;
    }

    /**
     * إظهار إشعار
     */
    showNotification(message, type = 'info') {
        // إزالة الإشعارات السابقة
        document.querySelectorAll('.mega-menu-notification').forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = 'mega-menu-notification';
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 20000;
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            font-weight: 600;
            max-width: 300px;
            font-size: 14px;
            animation: slideInRight 0.3s ease-out;
        `;
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // إضافة الأنيميشن
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
        
        // إزالة الإشعار بعد 3 ثوان
        setTimeout(() => {
            notification.remove();
            style.remove();
        }, 3000);
    }

    /**
     * تحديد أول علامة تجارية لعرضها افتراضياً
     */
    async loadFirstBrand() {
        try {
            const response = await fetch(this.baseUrl + 'ajax/get_first_brand.php');
            const data = await response.json();
            
            if (data.success && data.brand) {
                return data.brand;
            }
        } catch (error) {
            console.error('خطأ في تحميل أول علامة تجارية:', error);
        }
        return null;
    }

    /**
     * تحديد أول فئة لعرضها افتراضياً
     */
    async loadFirstCategory(brandId) {
        try {
            const formData = new FormData();
            formData.append('brand_id', brandId);
            
            const response = await fetch(this.baseUrl + 'ajax/get_first_category.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.success && data.category) {
                return data.category;
            }
        } catch (error) {
            console.error('خطأ في تحميل أول فئة:', error);
        }
        return null;
    }

    /**
     * تحديث عداد السلة
     */
    updateCartCount(count) {
        const cartElements = document.querySelectorAll('.cart-count, .cart-badge, #cart-count');
        cartElements.forEach(element => {
            if (element) {
                element.textContent = count;
                element.style.display = count > 0 ? 'inline-block' : 'none';
            }
        });
    }

    /**
     * تحميل عدد عناصر السلة
     */
    async loadCartCount() {
        try {
            const response = await fetch(this.baseUrl + 'ajax/get_cart_count.php');
            const data = await response.json();
            
            if (data.success) {
                this.updateCartCount(data.count);
                return data.count;
            }
        } catch (error) {
            console.error('خطأ في تحميل عدد عناصر السلة:', error);
        }
        return 0;
    }

    /**
     * تتبع النقرات لإحصائيات الاستخدام
     */
    trackMenuInteraction(type, data = {}) {
        const trackingData = {
            action: 'mega_menu_interaction',
            type: type,
            timestamp: new Date().toISOString(),
            user_agent: navigator.userAgent,
            ...data
        };
        
        // إرسال البيانات إلى نظام التتبع (اختياري)
        console.log('📊 تتبع تفاعل القائمة:', trackingData);
        
        // يمكن إضافة إرسال البيانات إلى خادم التحليلات هنا
        // fetch('/analytics/track', { method: 'POST', body: JSON.stringify(trackingData) });
    }

    /**
     * تحسين القائمة الموجودة في topBarNav.php
     */
    enhanceExistingDropdown() {
        console.log('🔧 تحسين القائمة الموجودة...');
        
        // إضافة تفاعل hover للعلامات التجارية الموجودة
        const brandLinks = document.querySelectorAll('.brand-main-link');
        brandLinks.forEach(link => {
            const brandContainer = findClosest(link, '[data-brand-id]');
            if (brandContainer) {
                link.addEventListener('mouseenter', () => {
                    if (!this.isMobile) {
                        const brandId = brandContainer.getAttribute('data-brand-id');
                        this.loadCategoriesForExistingBrand(brandId, brandContainer);
                    }
                });
            }
        });
        
        console.log('✅ تم تحسين القائمة الموجودة');
    }

    /**
     * تحميل فئات للعلامة التجارية في القائمة الموجودة
     */
    async loadCategoriesForExistingBrand(brandId, brandElement) {
        // التحقق من وجود البيانات في الذاكرة المؤقتة
        if (this.cache.categories[brandId]) {
            this.displayCategoriesForExisting(brandId, this.cache.categories[brandId], brandElement);
            return;
        }

        // التحقق من حالة التحميل
        if (this.loadingStates.categories[brandId]) {
            return;
        }

        const submenu = brandElement.querySelector('.brand-submenu');
        if (!submenu) {
            console.warn('لم يتم العثور على brand-submenu');
            return;
        }
        
        const submenuList = submenu.querySelector('.submenu-list');
        if (!submenuList) {
            console.warn('لم يتم العثور على submenu-list');
            return;
        }
        
        try {
            this.loadingStates.categories[brandId] = true;
            
            // عرض مؤشر التحميل
            this.showLoadingSpinner(submenuList);

            const formData = new FormData();
            formData.append('brand_id', brandId);

            const response = await fetch(this.baseUrl + 'ajax/get_brand_categories.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                // حفظ البيانات في الذاكرة المؤقتة
                this.cache.categories[brandId] = data.categories;
                this.displayCategoriesForExisting(brandId, data.categories, brandElement);
            } else {
                throw new Error(data.message || 'خطأ في تحميل الفئات');
            }

        } catch (error) {
            console.error('خطأ في تحميل فئات العلامة التجارية:', error);
            this.showErrorMessage(submenuList, 'خطأ في تحميل الفئات');
        } finally {
            this.loadingStates.categories[brandId] = false;
        }
    }

    /**
     * عرض الفئات للقائمة الموجودة
     */
    displayCategoriesForExisting(brandId, categories, brandElement) {
        const submenu = brandElement.querySelector('.brand-submenu');
        const submenuList = submenu.querySelector('.submenu-list');
        
        if (!submenuList) {
            console.warn('لم يتم العثور على submenu-list');
            return;
        }
        
        let categoriesHTML = '';

        // إضافة رابط "جميع الفئات"
        categoriesHTML += `
            <a href="products.php?brand=${brandId}" class="submenu-link all-categories">
                جميع الفئات
            </a>
        `;

        // إضافة الفئات
        if (categories.length > 0) {
            categories.forEach(category => {
                categoriesHTML += `
                    <a href="products.php?brand=${brandId}&category=${category.id}" class="submenu-link" data-category-id="${category.id}" data-brand-id="${brandId}">
                        ${this.escapeHtml(category.name)}
                    </a>
                `;
            });
        } else {
            categoriesHTML += `
                <div class="no-data-message" style="padding: 1rem; text-align: center; color: #9ca3af;">
                    <i class="fas fa-folder-open"></i>
                    <p>لا توجد فئات متاحة</p>
                </div>
            `;
        }

        submenuList.innerHTML = categoriesHTML;
        
        const brandNameElement = brandElement.querySelector('.brand-name');
        const brandName = brandNameElement ? brandNameElement.textContent : 'العلامة التجارية';
        
        console.log(`✅ تم عرض ${categories.length} فئة للعلامة التجارية ${brandName}`);
    }

    /**
     * إنشاء submenu للعلامة التجارية
     */
    createSubmenuForBrand(brandId, brandElement) {
        const submenuHTML = `
            <div class="brand-submenu">
                <div class="submenu-list">
                    <div class="loading-spinner active">
                        <div class="spinner"></div>
                        <p>جاري تحميل الفئات...</p>
                    </div>
                </div>
            </div>
        `;
        
        brandElement.insertAdjacentHTML('beforeend', submenuHTML);
        
        // تحميل الفئات
        this.loadCategoriesForExistingBrand(brandId, brandElement);
    }
}

/**
 * دوال مساعدة عامة للقائمة
 */

/**
 * الانتقال إلى صفحة العلامة التجارية
 */
function goToBrandPage(brandId, brandName = '') {
    if (!brandId) {
        console.error('❌ Brand ID مطلوب');
        return;
    }
    
    console.log('🏷️ الانتقال لصفحة العلامة التجارية:', brandId, brandName);
    
    // إظهار إشعار التحميل
    if (window.megaMenu) {
        const displayName = brandName || 'العلامة التجارية';
        window.megaMenu.showNotification(`🔍 جاري تحميل منتجات ${displayName}...`, 'info');
    }
    
    // تأخير صغير لإظهار التفاعل البصري
    setTimeout(() => {
        window.location.href = `products.php?brand=${brandId}`;
    }, 300);
}

/**
 * الانتقال إلى صفحة الفئة
 */
function goToCategoryPage(brandId, categoryId, categoryName = '') {
    if (!brandId || !categoryId) {
        console.error('❌ Brand ID و Category ID مطلوبان');
        return;
    }
    
    console.log('📂 الانتقال لصفحة الفئة:', brandId, categoryId, categoryName);
    
    // إظهار إشعار التحميل
    if (window.megaMenu) {
        const displayName = categoryName || 'الفئة';
        window.megaMenu.showNotification(`🔍 جاري تحميل منتجات ${displayName}...`, 'info');
    }
    
    setTimeout(() => {
        window.location.href = `products.php?brand=${brandId}&category=${categoryId}`;
    }, 300);
}

/**
 * الانتقال إلى صفحة الموديل
 */
function goToModelPage(brandId, categoryId, modelId, modelName = '') {
    if (!brandId || !categoryId || !modelId) {
        console.error('❌ جميع المعرفات مطلوبة (Brand, Category, Model)');
        return;
    }
    
    console.log('📱 الانتقال لصفحة الموديل:', brandId, categoryId, modelId, modelName);
    
    // إظهار إشعار التحميل
    if (window.megaMenu) {
        const displayName = modelName || 'الموديل';
        window.megaMenu.showNotification(`🔍 جاري تحميل منتجات ${displayName}...`, 'info');
    }
    
    setTimeout(() => {
        window.location.href = `products.php?brand=${brandId}&category=${categoryId}&model=${modelId}`;
    }, 300);
}

/**
 * تهيئة القائمة عند تحميل الصفحة
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 تهيئة القائمة المتدرجة...');
    
    // إنشاء مثيل من القائمة المتدرجة
    window.megaMenu = new MegaMenuDropdown();
    
    // تحميل عدد عناصر السلة
    window.megaMenu.loadCartCount();
    
    console.log('🎉 تم تحميل نظام القائمة المتدرجة بنجاح!');
});

/**
 * تصدير الوظائف للاستخدام العام
 */
window.goToBrandPage = goToBrandPage;
window.goToCategoryPage = goToCategoryPage; 
window.goToModelPage = goToModelPage;

/**
 * معالج أخطاء JavaScript عام
 */
window.addEventListener('error', function(e) {
    console.error('خطأ في JavaScript:', e.error);
    
    // إظهار إشعار ودود للمستخدم
    if (window.megaMenu) {
        window.megaMenu.showNotification('حدث خطأ تقني، يرجى إعادة المحاولة', 'error');
    }
});

/**
 * معالج الوعود المرفوضة
 */
window.addEventListener('unhandledrejection', function(e) {
    console.error('Promise مرفوض:', e.reason);
    e.preventDefault();
    
    if (window.megaMenu) {
        window.megaMenu.showNotification('خطأ في تحميل البيانات', 'error');
    }
});
