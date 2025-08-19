<?php 
$title = "Your Phone Deserves The Best";
$sub_title = "Explore our accessories for your phone.";
if(isset($_GET['b'])){
    $brand_qry = $conn->query("SELECT * FROM brands where md5(id) = '{$_GET['b']}'");
    if($brand_qry->num_rows > 0){
        $title = $brand_qry->fetch_assoc()['name'];
    }
}
if(isset($_GET['s'])){
    $series_qry = $conn->query("SELECT * FROM series where md5(id) = '{$_GET['s']}'");
    if($series_qry->num_rows > 0){
        $sub_title = $series_qry->fetch_assoc()['name'];
    }
}
?>

<style>
    /* تحسينات للعملة العراقية */
    .product-price {
        color: #28a745;
        font-weight: bold;
        font-size: 1.1rem;
    }
    
    .currency-symbol {
        color: #28a745;
        font-weight: 600;
        margin-left: 3px;
    }
    
    .product-item {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .product-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    /* Unified product card image container */
    .product-card .product-image {
        position: relative;
        width: 100%;
        height: 220px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .product-card .product-image img {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        background: #ffffff;
        transition: transform 0.3s ease;
    }
    .product-item:hover .product-image img {
        transform: scale(1.05);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #2c5aa0 0%, #1e3d72 100%);
        border: none;
        border-radius: 25px;
        padding: 10px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(44,90,160,0.4);
    }
</style>

<!-- Header-->
<header class="bg-dark py-5" id="main-header">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder"><?php echo htmlspecialchars($title) ?></h1>
            <p class="lead fw-normal text-white-50 mb-0"><?php echo htmlspecialchars($sub_title) ?></p>
        </div>
    </div>
</header>

<!-- Section-->
<section class="py-5">
    <div class="container-fluid px-4 px-lg-5 mt-5">
    <?php 
                if(isset($_GET['search'])){
                    echo "<h4 class='text-center'><b>نتائج البحث عن '".htmlspecialchars($_GET['search'])."'</b></h4>";
                }
            ?>
        
        <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
           
            <?php 
                $whereData = "";
                if(isset($_GET['search']))
                    $whereData = " and (p.product_name LIKE '%{$_GET['search']}%' or p.description LIKE '%{$_GET['search']}%')";
                elseif(isset($_GET['b']) && isset($_GET['s']))
                    $whereData = " and s.brand_id = '{$_GET['b']}' and m.series_id = '{$_GET['s']}'";
                elseif(isset($_GET['b']))
                    $whereData = " and s.brand_id = '{$_GET['b']}'";
                elseif(isset($_GET['s']))
                    $whereData = " and m.series_id = '{$_GET['s']}'";
                    
                $products = $conn->query("SELECT p.*, m.name as model, s.name as series, b.name as brand FROM `products` p inner join models m on p.model_id = m.id inner join series s on m.series_id = s.id inner join brands b on s.brand_id = b.id where p.status = 1 {$whereData} order by rand() ");
                while($row = $products->fetch_assoc()):
                    $upload_path = base_app.'/uploads/product_'.$row['id'];
                    $img = "";
                    if(is_dir($upload_path)){
                        $fileO = scandir($upload_path);
                        if(isset($fileO[2]))
                            $img = "uploads/product_".$row['id']."/".$fileO[2];
                    }
                    $inventory = $conn->query("SELECT * FROM inventory where product_id = ".$row['id']);
                    $inv = array();
                    while($ir = $inventory->fetch_assoc()){
                        $inv[$ir['size']] = $ir;
                    }
                    
                    // الحصول على الألوان إذا كانت متوفرة
                    $colors = array();
                    if(isset($row['has_colors']) && $row['has_colors'] == 1) {
                        $colors_qry = $conn->query("SELECT * FROM product_colors where product_id = ".$row['id']);
                        if($colors_qry) {
                            while($color_row = $colors_qry->fetch_assoc()){
                                $colors[] = array(
                                    'name' => $color_row['color_name'],
                                    'code' => $color_row['color_code']
                                );
                            }
                        }
                    }
            ?>
            <div class="col mb-5">
                <div class="card h-100 product-item product-card">
                    <!-- Product image-->
                    <div class="product-image">
                        <img src="<?php echo validate_image($img) ?>" loading="lazy" alt="<?php echo htmlspecialchars($row['product_name']) ?>">
                    </div>
                    
                    <!-- Product details-->
                    <div class="card-body p-4">
                        <div class="text-center">
                            <!-- Product name-->
                            <h5 class="fw-bolder"><?php echo htmlspecialchars($row['product_name']) ?></h5>
                            
                            <!-- عرض الألوان إذا كانت متوفرة -->
                            <?php if(!empty($colors)): ?>
                            <div class="product-colors mb-2">
                                <small class="text-muted">الألوان المتوفرة:</small><br>
                                <?php foreach(array_slice($colors, 0, 4) as $color): ?>
                                <span class="badge" style="background-color: <?php echo htmlspecialchars($color['code']) ?>; color: white; margin: 2px;" title="<?php echo htmlspecialchars($color['name']) ?>">
                                    <?php echo htmlspecialchars($color['name']) ?>
                                </span>
                                <?php endforeach; ?>
                                <?php if(count($colors) > 4): ?>
                                <small class="text-primary">+<?php echo count($colors) - 4 ?> ألوان أخرى</small>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Product prices with Iraqi Dinar -->
                            <div class="product-prices">
                                <?php foreach($inv as $size => $details): ?>
                                    <div class="price-item mb-1">
                                        <strong><?php echo htmlspecialchars($size) ?>:</strong> 
                                        <span class="product-price">
                                            <span class="currency-symbol">د.ع</span><?php echo number_format($details['price']) ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                                
                                <?php if(empty($inv)): ?>
                                    <span class="text-muted">السعر عند الطلب</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product actions-->
                    <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                        <div class="text-center">
                            <a class="btn btn-primary" href=".?p=view_product&id=<?php echo md5($row['id']) ?>">
                                <i class="fas fa-eye me-2"></i>عرض التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            
            <?php 
                if($products->num_rows <= 0){
                    echo "<div class='col-12'>";
                    echo "<h4 class='text-center text-muted'><i class='fas fa-search'></i> لا توجد منتجات متاحة حالياً</h4>";
                    echo "<p class='text-center'><a href='./' class='btn btn-primary'>العودة للصفحة الرئيسية</a></p>";
                    echo "</div>";
                }
            ?>
        </div>
    </div>
</section>