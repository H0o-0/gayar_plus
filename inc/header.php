<!DOCTYPE html>
<html lang="ar">
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
    
    <!-- Search Modal Styles -->
    <style>
    .search-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding-top: 100px;
    }
    
    .search-container {
        background: white;
        border-radius: 16px;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
    }
    
    .search-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--medium-gray);
    }
    
    .search-header h3 {
        color: var(--primary-navy);
        font-weight: 700;
        margin: 0;
    }
    
    .close-search {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text-secondary);
        padding: 0.5rem;
    }
    
    .search-body {
        padding: 2rem;
    }
    
    .search-input {
        width: 100%;
        padding: 1rem;
        border: 2px solid var(--medium-gray);
        border-radius: 12px;
        font-size: 1rem;
        margin-bottom: 1rem;
    }
    
    .search-input:focus {
        outline: none;
        border-color: var(--primary-blue);
    }
    
    .search-results {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .search-result-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 8px;
        cursor: pointer;
        transition: var(--transition);
    }
    
    .search-result-item:hover {
        background: var(--light-gray);
    }
    
    .search-result-item img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .no-results {
        text-align: center;
        color: var(--text-secondary);
        padding: 2rem;
    }
    </style>
</head>
<body class="smooth-scroll">

<?php include 'topBarNav.php'; ?>