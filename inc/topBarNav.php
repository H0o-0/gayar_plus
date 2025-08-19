<!-- CSS للتصميم الجديد للـ Navbar -->
<style>
    :root {
        --primary-color: #2c5aa0;
        --secondary-color: #f8f9fa;
        --accent-color: #ff6b6b;
    }

    /* Enhanced Navbar */
    .navbar-custom {
        background: linear-gradient(135deg, var(--primary-color) 0%, #1e3d72 100%);
        padding: 0.5rem 0;
        box-shadow: 0 2px 20px rgba(0,0,0,0.1);
    }

    .navbar-brand img {
        height: 50px;
        width: auto;
    }

    .navbar-nav .nav-link {
        color: white !important;
        font-weight: 500;
        padding: 0.75rem 1.25rem !important;
        transition: all 0.3s ease;
        border-radius: 25px;
        margin: 0 0.2rem;
    }

    .navbar-nav .nav-link:hover {
        background-color: rgba(255,255,255,0.1);
        transform: translateY(-1px);
    }

    /* Advanced Dropdown */
    .dropdown-mega {
        position: static !important;
    }

    .mega-menu {
        width: 100%;
        left: 0 !important;
        right: auto !important;
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        background: white;
        padding: 2rem;
        margin-top: 0.5rem;
    }

    .mega-menu-column h6 {
        color: var(--primary-color);
        font-weight: bold;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-color);
    }

    .mega-menu-item {
        display: block;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        color: #333;
    }

    .mega-menu-item:hover {
        background-color: var(--secondary-color);
        transform: translateX(5px);
        text-decoration: none;
        color: var(--primary-color);
    }

    /* Cart Icon */
    .cart-icon {
        background: var(--accent-color);
        border: none;
        color: white;
        border-radius: 25px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }

    .cart-icon:hover {
        background: #ff5252;
        transform: scale(1.05);
        color: white;
    }

    .cart-badge {
        background: white;
        color: var(--accent-color);
        font-weight: bold;
    }

    /* Transparent icon buttons (search, user) */
    .navbar-custom .icon-btn {
        background: transparent !important;
        border: none !important;
        color: #fff !important;
        padding: 6px 8px;
        border-radius: 10px;
        line-height: 1;
    }
    .navbar-custom .icon-btn:hover,
    .navbar-custom .icon-btn:focus,
    .navbar-custom .icon-btn:active {
        background-color: rgba(255,255,255,0.15) !important;
        color: #fff !important;
        box-shadow: none !important;
        outline: none !important;
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
                <!-- الأجهزة Dropdown -->
                <li class="nav-item dropdown dropdown-mega">
                    <a class="nav-link dropdown-toggle" href="#" id="devicesDropdown" role="button" data-toggle="dropdown">
                        الأجهزة
                    </a>
                    <div class="dropdown-menu mega-menu">
                        <div class="container">
                            <div class="row">
                                <?php
                                $brand_qry = $conn->query("SELECT * FROM brands where status = 1 LIMIT 4");
                                while($brow = $brand_qry->fetch_assoc()):
                                ?>
                                <div class="col-md-3 mega-menu-column">
                                    <h6><?php echo $brow['name'] ?></h6>
                                    <?php
                                    $series_qry = $conn->query("SELECT * FROM series where status = 1 and brand_id = '{$brow['id']}' LIMIT 4");
                                    if($series_qry->num_rows > 0):
                                        while($srow = $series_qry->fetch_assoc()):
                                    ?>
                                        <a class="mega-menu-item" href="./?p=products&b=<?php echo md5($brow['id']) ?>&s=<?php echo md5($srow['id']) ?>"><?php echo $srow['name'] ?></a>
                                    <?php
                                        endwhile;
                                    else:
                                    ?>
                                        <a class="mega-menu-item" href="./?p=products&b=<?php echo md5($brow['id']) ?>">جميع المنتجات</a>
                                    <?php endif; ?>
                                    <a class="mega-menu-item" href="./?p=products&b=<?php echo md5($brow['id']) ?>">عرض الكل</a>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- أدوات صيانة (Placeholder) -->
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
$(function(){
    $('#navbarNav').find('a.nav-link').click(function(){
        $('.navbar-collapse').removeClass('show')
    })
})

// Toggle search function
function toggleSearch() {
    // Create search modal or toggle search bar
    const searchModal = `
        <div class="modal fade" id="searchModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">البحث في المنتجات</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="./" method="GET">
                            <input type="hidden" name="p" value="products">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="ابحث عن المنتجات..." required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> بحث
                                </button>
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

    $('#searchModal').modal({
        show: true,
        backdrop: 'static',
        keyboard: true
    });
}
</script>