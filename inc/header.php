<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Gayar Plus - متجرك الأول للملحقات</title>
    <link rel="icon" href="<?php echo validate_image('admin/images/cropped_circle_image.png') ?>" />
    
    <!-- Modern Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Bootstrap CSS for dropdown functionality -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Main Theme CSS (Unified: site-wide + modern-theme + navbar-fix) -->
    <link rel="stylesheet" href="assets/css/main-theme.css">
    <link rel="stylesheet" href="assets/css/modern-theme.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    <link rel="stylesheet" href="assets/css/product-card-unified.css">
    
    <!-- Mobile Optimizations -->
    <link rel="stylesheet" href="./assets/css/mobile-optimizations.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS for dropdown functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Site-Wide Modern Scripts (Based on preview.html) -->
    <script src="<?php echo base_url ?>assets/js/modern-scripts.js"></script>
    <script src="<?php echo base_url ?>assets/js/accessories-menu.js"></script>
    
    <script>
        // Ensure base_url is properly defined for all pages
        var _base_url_ = '<?php echo defined('base_url') ? base_url : '/'; ?>';
        
        // Global brand navigation function - defined early
        function goToBrand(brandId) {
            if (!brandId) {
                console.error('❌ Brand ID is required');
                return;
            }
            
            console.log('🏷️ الانتقال لصفحة البراند:', brandId);
            
            // البحث عن البراند في البلوكات للحصول على الاسم
            var brandBlock = document.querySelector('[data-brand-id="' + brandId + '"]');
            var brandName = 'البراند';
            
            if (brandBlock) {
                var nameAttr = brandBlock.getAttribute('data-brand-name');
                if (nameAttr && nameAttr !== 'null' && nameAttr.trim() !== '') {
                    brandName = nameAttr;
                }
            }
            
            // Show notification function
            var notification = document.createElement('div');
            notification.style.cssText = 
                'position: fixed; top: 80px; right: 20px; z-index: 10000;' +
                'background: #3b82f6; color: white; padding: 1rem 1.5rem; border-radius: 12px;' +
                'box-shadow: 0 10px 30px rgba(0,0,0,0.2); font-weight: 600;' +
                'max-width: 300px; font-size: 14px;';
            
            notification.textContent = '🔍 جاري تحميل منتجات ' + brandName + '...';
            document.body.appendChild(notification);
            
            setTimeout(function() { 
                if (notification.parentNode) {
                    notification.remove(); 
                }
            }, 3000);
            
            // الانتقال لصفحة المنتجات مع فلتر البراند
            setTimeout(function() {
                window.location.href = './?p=products&brand=' + brandId;
            }, 800);
        }
        
        // Make goToBrand globally available immediately
        window.goToBrand = goToBrand;
        
        // Initialize site-wide functionality
        function initializeSiteWide() {
            if (typeof initSiteWide === 'function') {
                try {
                    initSiteWide();
                } catch (e) {
                    console.error('Error initializing site-wide JavaScript:', e);
                }
            }
        }
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeSiteWide);
        } else {
            // DOM is already ready
            initializeSiteWide();
        }
    </script>
    
</head>
<body class="smooth-scroll">

<?php include 'topBarNav.php'; ?>
