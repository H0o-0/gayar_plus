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
                                $cat_qry = $conn->query("SELECT * FROM categories where status = 1 LIMIT 4");
                                while($crow = $cat_qry->fetch_assoc()):
                                ?>
                                <div class="col-md-3 mega-menu-column">
                                    <h6><?php echo $crow['category'] ?></h6>
                                    <?php
                                    $sub_qry = $conn->query("SELECT * FROM sub_categories where status = 1 and parent_id = '{$crow['id']}' LIMIT 4");
                                    if($sub_qry->num_rows > 0):
                                        while($srow = $sub_qry->fetch_assoc()):
                                    ?>
                                        <a class="mega-menu-item" href="./?p=products&c=<?php echo md5($crow['id']) ?>&s=<?php echo md5($srow['id']) ?>"><?php echo $srow['sub_category'] ?></a>
                                    <?php
                                        endwhile;
                                    else:
                                    ?>
                                        <a class="mega-menu-item" href="./?p=products&c=<?php echo md5($crow['id']) ?>">جميع المنتجات</a>
                                    <?php endif; ?>
                                    <a class="mega-menu-item" href="./?p=products&c=<?php echo md5($crow['id']) ?>">عرض الكل</a>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- الإكسسوارات Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="accessoriesDropdown" role="button" data-toggle="dropdown">
                        الإكسسوارات
                    </a>
                    <ul class="dropdown-menu">
                        <?php
                        $acc_categories = $conn->query("SELECT * FROM categories where status = 1");
                        while($acc_row = $acc_categories->fetch_assoc()):
                        ?>
                        <li><a class="dropdown-item" href="./?p=products&c=<?php echo md5($acc_row['id']) ?>"><?php echo $acc_row['category'] ?></a></li>
                        <?php endwhile; ?>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link" href="./?p=products">العروض</a></li>
                <li class="nav-item"><a class="nav-link" href="./?p=about">من نحن</a></li>
                <li class="nav-item"><a class="nav-link" href="./?p=contact">اتصل بنا</a></li>
            </ul>
            <!-- Cart and User Actions -->
            <div class="d-flex align-items-center">
                <button class="btn me-3" style="background: none; border: none; color: white;" onclick="toggleSearch()">
                    <i class="fas fa-search" style="font-size: 1.2rem;"></i>
                </button>
                <?php if(!isset($_SESSION['userdata']['id'])): ?>
                <button class="btn me-3" style="background: none; border: none; color: white;" id="login-btn">
                    <i class="fas fa-user" style="font-size: 1.2rem;"></i>
                </button>
                <?php else: ?>
                <a href="./?p=my_account" class="btn me-3" style="background: none; border: none; color: white;" title="حسابي">
                    <i class="fas fa-user" style="font-size: 1.2rem;"></i>
                </a>
                <?php endif; ?>
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
                </div>
            </div>
        </nav>
<script>
$(function(){
    $('#login-btn').click(function(){
        uni_modal("","login.php")
    })
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

    const modal = new bootstrap.Modal(document.getElementById('searchModal'));
    modal.show();
}
</script>