<!-- CSS للتصميم الجديد للـ Navbar مع النظام الثلاثي -->
<style>
    :root {
        --primary-color: #2c5aa0;
        --secondary-color: #f8f9fa;
        --accent-color: #ff6b6b;
        --hover-color: #1e3d72;
        --text-dark: #333;
        --border-color: #dee2e6;
    }

    /* Enhanced Navbar */
    .navbar-custom {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--hover-color) 100%);
        padding: 0.5rem 0;
        box-shadow: 0 4px 25px rgba(0,0,0,0.1);
        position: relative;
        z-index: 1050;
    }

    .navbar-brand img {
        height: 50px;
        width: auto;
        transition: transform 0.3s ease;
    }

    .navbar-brand:hover img {
        transform: scale(1.05);
    }

    .navbar-nav .nav-link {
        color: white !important;
        font-weight: 500;
        padding: 0.75rem 1.25rem !important;
        transition: all 0.3s ease;
        border-radius: 25px;
        margin: 0 0.2rem;
        position: relative;
    }

    .navbar-nav .nav-link:hover {
        background-color: rgba(255,255,255,0.15);
        transform: translateY(-2px);
    }

    /* Advanced Mega Menu */
    .dropdown-mega {
        position: static !important;
    }

    .mega-menu {
        width: 100vw;
        left: 0 !important;
        right: auto !important;
        transform: translateX(-50%);
        margin-left: 50vw;
        border: none;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        padding: 3rem 2rem;
        margin-top: 0;
        max-height: 70vh;
        overflow-y: auto;
    }

    .mega-menu-section {
        margin-bottom: 2rem;
    }

    .mega-menu-section h5 {
        color: var(--primary-color);
        font-weight: bold;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 3px solid var(--primary-color);
        font-size: 1.25rem;
        position: relative;
    }

    .mega-menu-section h5::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        width: 50px;
        height: 3px;
        background: var(--accent-color);
    }

    .brand-column {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .brand-column:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }

    .brand-title {
        color: var(--primary-color);
        font-weight: bold;
        font-size: 1.1rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--border-color);
        display: flex;
        align-items: center;
    }

    .brand-logo {
        width: 24px;
        height: 24px;
        margin-left: 0.5rem;
        border-radius: 4px;
    }

    .series-group {
        margin-bottom: 1rem;
    }

    .series-title {
        color: var(--hover-color);
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
        padding: 0.25rem 0;
        transition: color 0.3s ease;
    }

    .series-title:hover {
        color: var(--accent-color);
    }

    .models-list {
        margin-right: 1rem;
        padding-right: 1rem;
        border-right: 2px solid var(--border-color);
    }

    .mega-menu-item {
        display: block;
        padding: 0.4rem 0.8rem;
        transition: all 0.3s ease;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        color: var(--text-dark);
        font-size: 0.9rem;
        margin-bottom: 0.2rem;
        border: 1px solid transparent;
    }

    .mega-menu-item:hover {
        background: linear-gradient(135deg, var(--accent-color) 0%, #ff5252 100%);
        color: white;
        transform: translateX(8px);
        text-decoration: none;
        border-color: var(--accent-color);
        box-shadow: 0 3px 10px rgba(255, 107, 107, 0.3);
    }

    /* Cart Icon */
    .cart-icon {
        background: linear-gradient(135deg, var(--accent-color) 0%, #ff5252 100%);
        border: none;
        color: white;
        border-radius: 25px;
        padding: 10px 20px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    }

    .cart-icon:hover {
        background: linear-gradient(135deg, #ff5252 0%, #d32f2f 100%);
        transform: scale(1.05);
        color: white;
        box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
    }

    .cart-badge {
        background: white;
        color: var(--accent-color);
        font-weight: bold;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    /* Transparent icon buttons */
    .navbar-custom .icon-btn {
        background: transparent !important;
        border: 2px solid rgba(255,255,255,0.3) !important;
        color: #fff !important;
        padding: 8px 12px;
        border-radius: 15px;
        line-height: 1;
        transition: all 0.3s ease;
    }

    .navbar-custom .icon-btn:hover {
        background-color: rgba(255,255,255,0.2) !important;
        border-color: rgba(255,255,255,0.6) !important;
        color: #fff !important;
        transform: translateY(-2px);
    }

    /* Mobile Responsiveness */
    @media (max-width: 991.98px) {
        .mega-menu {
            position: static !important;
            width: 100% !important;
            transform: none !important;
            margin-left: 0 !important;
            border-radius: 10px;
            margin: 1rem 0;
            padding: 2rem 1rem;
        }

        .brand-column {
            margin-bottom: 2rem;
        }

        .models-list {
            border-right: none;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
    }

    @media (max-width: 767.98px) {
        .mega-menu {
            padding: 1.5rem 1rem;
        }
        
        .brand-column {
            padding: 1rem;
        }
    }

    /* Loading Animation */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<!-- Enhanced Navigation -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="./">
            <img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="شعار المتجر" />
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto">
                <!-- الأجهزة Mega Menu -->
                <li class="nav-item dropdown dropdown-mega">
                    <a class="nav-link dropdown-toggle" href="#" id="devicesDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                        الأجهزة
                    </a>
                    <div class="dropdown-menu mega-menu" id="mega-menu-container">
                        <div class="container-fluid">
                            <div class="row" id="mega-menu-content">
                                <!-- هنا سيتم تحميل المحتوى ديناميكياً -->
                                <div class="col-12 text-center">
                                    <div class="loading-spinner"></div>
                                    <p class="mt-2">جاري تحميل الأجهزة...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- أدوات صيانة -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="toolsDropdown" role="button" data-toggle="dropdown">
                        أدوات صيانة
                    </a>
                    <ul class="dropdown-menu">
                        <li><span class="dropdown-item text-muted">قريباً</span></li>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link" href="./?p=products">العروض</a></li>
                <li class="nav-item"><a class="nav-link" href="./?p=about">من نحن</a></li>
                <li class="nav-item"><a class="nav-link" href="./?p=contact">اتصل بنا</a></li>
            </ul>
            
            <!-- Cart and Search Actions -->
            <div class="d-flex align-items-center">
                <button class="btn me-3 icon-btn" onclick="toggleSearch()">
                    <i class="fas fa-search" style="font-size: 1.2rem;"></i>
                </button>
                <button class="btn cart-icon" type="button" onclick="window.location.href='./?p=cart'">
                    <i class="fas fa-shopping-cart me-1"></i>
                    السلة
                    <span class="badge cart-badge ms-2 rounded-pill" id="cart-count">
                      <?php
                      if(isset($_SESSION['userdata']['id'])):
                        $count = $conn->query("SELECT SUM(quantity) as items from `cart` where client_id =".$_settings->userdata('id'))->fetch_assoc()['items'];
                        echo ($count > 0 ? $count : 0);
                      else:
                        echo "0";
                      endif;
                      ?>
                    </span>
                </button>
            </div>
        </div>
    </div>
</nav>

<script>
$(document).ready(function(){
    // تحميل القائمة الضخمة عند hover
    let megaMenuLoaded = false;
    
    $('#devicesDropdown').on('mouseenter focus', function() {
        if (!megaMenuLoaded) {
            loadMegaMenu();
            megaMenuLoaded = true;
        }
    });

    // منع إغلاق القائمة عند النقر داخلها
    $('.mega-menu').on('click', function(e) {
        e.stopPropagation();
    });

    $('#navbarNav').find('a.nav-link').click(function(){
        $('.navbar-collapse').removeClass('show');
    });
});

function loadMegaMenu() {
    $.ajax({
        url: 'inc/load_mega_menu.php',
        type: 'POST',
        dataType: 'json',
        beforeSend: function() {
            $('#mega-menu-content').html(`
                <div class="col-12 text-center">
                    <div class="loading-spinner"></div>
                    <p class="mt-2">جاري تحميل الأجهزة...</p>
                </div>
            `);
        },
        success: function(response) {
            if (response.status === 'success') {
                $('#mega-menu-content').html(response.html);
            } else {
                $('#mega-menu-content').html(`
                    <div class="col-12 text-center text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>خطأ في تحميل البيانات</p>
                    </div>
                `);
            }
        },
        error: function() {
            $('#mega-menu-content').html(`
                <div class="col-12 text-center text-danger">
                    <i class="fas fa-wifi fa-2x mb-2"></i>
                    <p>خطأ في الاتصال</p>
                </div>
            `);
        }
    });
}

// Toggle search function
function toggleSearch() {
    const searchModal = `
        <div class="modal fade" id="searchModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">البحث في المنتجات</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="./" method="GET">
                            <input type="hidden" name="p" value="products">
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="search" placeholder="ابحث عن المنتجات..." required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">يمكنك البحث باسم المنتج، الشركة، أو نوع الجهاز</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    `;

    if (!document.getElementById('searchModal')) {
        document.body.insertAdjacentHTML('beforeend', searchModal);
    }

    $('#searchModal').modal('show');
}

// دالة للتنقل إلى صفحة المنتجات
function navigateToProducts(brandId, seriesId, modelId) {
    let url = './?p=products';
    let params = [];
    
    if (brandId) params.push('brand=' + brandId);
    if (seriesId) params.push('series=' + seriesId);
    if (modelId) params.push('model=' + modelId);
    
    if (params.length > 0) {
        url += '&' + params.join('&');
    }
    
    window.location.href = url;
}

// التفاعل مع القائمة الضخمة
$(document).on('click', '.brand-title', function() {
    const brandId = $(this).data('brand-id');
    navigateToProducts(brandId);
});

$(document).on('click', '.series-title', function(e) {
    e.preventDefault();
    const brandId = $(this).data('brand-id');
    const seriesId = $(this).data('series-id');
    navigateToProducts(brandId, seriesId);
});

$(document).on('click', '.mega-menu-item', function(e) {
    e.preventDefault();
    const brandId = $(this).data('brand-id');
    const seriesId = $(this).data('series-id');
    const modelId = $(this).data('model-id');
    navigateToProducts(brandId, seriesId, modelId);
});
</script>
