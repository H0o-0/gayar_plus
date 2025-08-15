<?php
// تعريف دالة base_url()
function base_url($path = '') {
    // يمكنك تغيير هذا حسب بيئة الإنتاج
    $base = 'http://localhost/pet_shop/';
    return $base . ltrim($path, '/');
}

// تعريف ثابت أيضًا للتوافق مع الكود القديم
define('BASE_URL', base_url());
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>متجر الحيوانات الأليفة</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo base_url('plugins/fontawesome-free/css/all.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('dist/css/adminlte.min.css'); ?>">
    <!-- إضافة أية أوراق أنماط إضافية هنا -->
</head>
<body class="hold-transition layout-top-nav">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
            <div class="container">
                <a href="<?php echo base_url(); ?>index3.html" class="navbar-brand">
                    <img src="<?php echo base_url('dist/img/AdminLTELogo.png'); ?>" alt="AdminLTE Logo">
                    <span class="brand-text font-weight-light">AdminLTE 3</span>
                </a>

                <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                    <!-- Left navbar links -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="<?php echo base_url(); ?>index3.html" class="nav-link">الرئيسية</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">اتصل بنا</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">قائمة منسدلة</a>
                            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                                <li><a href="#" class="dropdown-item">إجراء ما</a></li>
                                <li><a href="#" class="dropdown-item">إجراء آخر</a></li>

                                <li class="dropdown-divider"></li>

                                <!-- مستوى ثاني من القائمة المنسدلة -->
                                <li class="dropdown-submenu dropdown-hover">
                                    <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">تحوم للإجراء</a>
                                    <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                                        <li>
                                            <a tabindex="-1" href="#" class="dropdown-item">مستوى 2</a>
                                        </li>

                                        <!-- مستوى ثالث من القائمة المنسدلة -->
                                        <li class="dropdown-submenu">
                                            <a id="dropdownSubMenu3" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">مستوى 2</a>
                                            <ul aria-labelledby="dropdownSubMenu3" class="dropdown-menu border-0 shadow">
                                                <li><a href="#" class="dropdown-item">مستوى 3</a></li>
                                                <li><a href="#" class="dropdown-item">مستوى 3</a></li>
                                            </ul>
                                        </li>
                                        <!-- نهاية مستوى ثالث -->

                                        <li><a href="#" class="dropdown-item">مستوى 2</a></li>
                                        <li><a href="#" class="dropdown-item">مستوى 2</a></li>
                                    </ul>
                                </li>
                                <!-- نهاية مستوى ثاني -->
                            </ul>
                        </li>
                    </ul>

                    <!-- نموذج البحث -->
                    <form class="form-inline ml-0 ml-md-3">
                        <div class="input-group input-group-sm">
                            <input class="form-control form-control-navbar" type="search" placeholder="بحث" aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-navbar" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- روابط شريط التنقل اليمنى -->
                <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                    <!-- قائمة الرسائل المنسدلة -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <i class="fas fa-comments"></i>
                            <span class="badge badge-danger navbar-badge">3</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <a href="#" class="dropdown-item">
                                <!-- بداية الرسالة -->
                                <div class="media">
                                    <img src="<?php echo base_url('dist/img/user1-128x128.jpg'); ?>" alt="صورة المستخدم" class="img-size-50 mr-3 img-circle">
                                    <div class="media-body">
                                        <h3 class="dropdown-item-title">
                                            براد ديزل
                                            <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                                        </h3>
                                        <p class="text-sm">اتصل بي متى استطعت...</p>
                                        <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> منذ 4 ساعات</p>
                                    </div>
                                </div>
                                <!-- نهاية الرسالة -->
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <!-- بداية الرسالة -->
                                <div class="media">
                                    <img src="<?php echo base_url('dist/img/user8-128x128.jpg'); ?>" alt="صورة المستخدم" class="img-size-50 img-circle mr-3">
                                    <div class="media-body">
                                        <h3 class="dropdown-item-title">
                                            جون بيرس
                                            <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                                        </h3>
                                        <p class="text-sm">لقد تلقيت رسالتك</p>
                                        <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> منذ 4 ساعات</p>
                                    </div>
                                </div>
                                <!-- نهاية الرسالة -->
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <!-- بداية الرسالة -->
                                <div class="media">
                                    <img src="<?php echo base_url('dist/img/user3-128x128.jpg'); ?>" alt="صورة المستخدم" class="img-size-50 img-circle mr-3">
                                    <div class="media-body">
                                        <h3 class="dropdown-item-title">
                                            نورا سيلفستر
                                            <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                                        </h3>
                                        <p class="text-sm">الموضوع هنا</p>
                                        <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> منذ 4 ساعات</p>
                                    </div>
                                </div>
                                <!-- نهاية الرسالة -->
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item dropdown-footer">عرض جميع الرسائل</a>
                        </div>
                    </li>
                    <!-- قائمة الإشعارات المنسدلة -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <i class="far fa-bell"></i>
                            <span class="badge badge-warning navbar-badge">15</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <span class="dropdown-header">15 إشعار</span>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-envelope mr-2"></i> 4 رسائل جديدة
                                <span class="float-right text-muted text-sm">3 دقائق</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-users mr-2"></i> 8 طلبات صداقة
                                <span class="float-right text-muted text-sm">12 ساعة</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-file mr-2"></i> 3 تقارير جديدة
                                <span class="float-right text-muted text-sm">2 يوم</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item dropdown-footer">عرض جميع الإشعارات</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                            <i class="fas fa-th-large"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- /.navbar -->

        <!-- محتوى الصفحة يأتي هنا -->
        <div class="content-wrapper">
            <!-- محتوى الصفحة -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Footer -->
        <footer class="main-footer">
            <div class="container">
                <div class="float-right d-none d-sm-inline">
                    أية معلومات إضافية
                </div>
                <strong>حقوق النشر &copy; <?php echo date('Y'); ?> <a href="<?php echo base_url(); ?>">متجر الحيوانات الأليفة</a>.</strong> جميع الحقوق محفوظة.
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- JS -->
    <script src="<?php echo base_url('plugins/jquery/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo base_url('dist/js/adminlte.min.js'); ?>"></script>
    <!-- إضافة أية مكتبات جافا سكريبت إضافية هنا -->
</body>
</html>