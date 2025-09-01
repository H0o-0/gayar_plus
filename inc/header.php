<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Gayar Plus - متجرك الأول للملحقات</title>
    <link rel="icon" href="<?php echo validate_image('admin/images/cropped_circle_image.png') ?>" />
    
    <!-- Modern Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Bootstrap CSS for dropdown functionality -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Site-Wide Modern CSS (Based on preview.html) -->
    <link rel="stylesheet" href="./assets/css/site-wide.css">
    
    <!-- Mobile Optimizations -->
    <link rel="stylesheet" href="./assets/css/mobile-optimizations.css">
    
    <!-- Mega Menu Fix -->
    <link rel="stylesheet" href="./assets/css/mega-menu-fix.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS for dropdown functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Site-Wide Modern Scripts (Based on preview.html) -->
    <script src="./assets/js/site-wide.js"></script>
    
    <!-- Mega Menu Dropdown JavaScript -->
    <script src="./assets/js/mega-menu-dropdown.js"></script>
    
    <!-- Mega Menu Performance Optimizations -->
    <link rel="stylesheet" href="./assets/css/mega-menu-performance.css">
    <script>
        // Ensure base_url is properly defined for all pages
        var _base_url_ = '<?php echo defined('base_url') ? base_url : '/'; ?>';
        
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
