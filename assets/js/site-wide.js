/* 
 * Site-Wide Modern JavaScript for Gayar Plus
 * This file contains global JavaScript functions used across the site
 */

// Function to initialize site-wide functionality
function initSiteWide() {
    console.log('Initializing site-wide functionality');
    
    // Initialize device menu if it exists
    if (typeof window.GayarPlusMenu !== 'undefined' && typeof window.GayarPlusMenu.init === 'function') {
        try {
            window.GayarPlusMenu.init();
            console.log('Device menu initialized successfully');
        } catch (e) {
            console.error('Error initializing device menu:', e);
        }
    } else {
        console.log('Device menu not found or not ready for initialization');
    }
    
    // Other site-wide initializations can go here
    initializeCart();
    initializeSearch();
    
    // Initialize navbar scroll effect
    initNavbarScroll();
    initSmoothScroll();
}

// Function to initialize cart functionality
function initializeCart() {
    // Cart initialization code
    console.log('Cart functionality initialized');
}

// Function to initialize search functionality
function initializeSearch() {
    // Search initialization code
    console.log('Search functionality initialized');
}

// Ensure the function is available globally
window.initSiteWide = initSiteWide;

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSiteWide);
} else {
    // DOM is already ready
    initSiteWide();
}

// Also initialize on window load for better reliability
window.addEventListener('load', function() {
    console.log('Window loaded, re-initializing site-wide functionality');
    setTimeout(initSiteWide, 100);
});

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

// إضافة pattern background إذا لم يكن موجوداً
function ensurePatternBackground() {
    if (!document.querySelector('.pattern-background')) {
        const patternBg = document.createElement('div');
        patternBg.className = 'pattern-background';
        document.body.insertBefore(patternBg, document.body.firstChild);
    }
}

// تأكد من وجود عناصر التصميم الحديث
window.addEventListener('load', ensurePatternBackground);

// تصدير الوظائف للاستخدام العام
if (typeof window !== 'undefined') {
    // addToCart يتم تنفيذه في home.php
    window.showSuccessNotification = showSuccessNotification;
    window.initSiteWide = initSiteWide;
}
