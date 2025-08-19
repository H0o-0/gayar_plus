<?php 
// تضمين كلاس تنظيف النص
require_once 'classes/TextCleaner.php';

 $products = $conn->query("SELECT * FROM `products`  where md5(id) = '{$_GET['id']}' ");
 if($products->num_rows > 0){
     foreach($products->fetch_assoc() as $k => $v){
         $$k= $v;
     }
    $upload_path = base_app.'/uploads/product_'.$id;
    $img = "";
    if(is_dir($upload_path)){
        $fileO = scandir($upload_path);
        if(isset($fileO[2]))
            $img = "uploads/product_".$id."/".$fileO[2];
        // var_dump($fileO);
    }
    $inventory = $conn->query("SELECT * FROM inventory where product_id = ".$id);
    $inv = array();
    while($ir = $inventory->fetch_assoc()){
        $inv[] = $ir;
    }
 }
?>
<style>
    :root {
        --primary-color: #2c5aa0;
        --secondary-color: #f8f9fa;
        --accent-color: #ff6b6b;
    }

    .p-size {
        border-radius: 20px !important;
        transition: all 0.3s ease;
    }

    .p-size.active {
        background: var(--primary-color) !important;
        color: white !important;
        border-color: var(--primary-color) !important;
    }

    .p-size:hover {
        background: var(--primary-color) !important;
        color: white !important;
        border-color: var(--primary-color) !important;
    }

    .view-image {
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .view-image.active {
        border: 3px solid var(--primary-color);
    }

    .view-image:hover {
        transform: scale(1.05);
    }

    #display-img {
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        width: 100%;
        height: 400px;
        object-fit: contain;
        transition: opacity 0.3s ease;
    }

    /* إبقاء الصور ثابتة وعدم تحركها مع النص */
    .product-images-container {
        position: static;
        top: auto;
    }

    .main-image-container {
        width: 100%;
        height: 400px;
        overflow: hidden;
        border-radius: 15px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .thumbnail-images {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding: 10px 0;
    }

    .thumbnail-images::-webkit-scrollbar {
        height: 4px;
    }

    .thumbnail-images::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 2px;
    }

    .thumbnail-images::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 2px;
    }

    .view-image {
        flex-shrink: 0;
        width: 80px;
        height: 80px;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .view-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.4s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .sale-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, var(--accent-color) 0%, #ff6b6b 100%);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 2;
    }

    .product-info {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.8rem;
        line-height: 1.3;
    }

    .product-description {
        color: #666;
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 1rem;
        flex-grow: 1;
    }

    .product-price {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--accent-color);
        margin-top: auto;
    }

    .product-actions {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, var(--primary-color) 0%, #1e3d72 100%);
        padding: 1rem;
        transform: translateY(100%);
        transition: transform 0.3s ease;
        display: flex;
        gap: 10px;
    }

    .product-card:hover .product-actions {
        transform: translateY(0);
    }

    .product-actions .btn {
        flex: 1;
        border: none;
        border-radius: 25px;
        padding: 10px 15px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }

    .btn-view {
        background: rgba(255,255,255,0.2);
        color: white;
    }

    .btn-view:hover {
        background: white;
        color: var(--primary-color);
        transform: translateY(-2px);
    }

    /* تصميم الألوان */
    .product-colors {
        margin-bottom: 0.8rem;
        padding: 0.5rem 0;
    }

    .colors-list {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-start;
    }

    .color-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .color-dot:hover {
        transform: scale(1.2);
        box-shadow: 0 3px 8px rgba(0,0,0,0.2);
    }

    .color-dot:nth-child(1) {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    }

    .color-dot:nth-child(2) {
        background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
    }

    .color-dot:nth-child(3) {
        background: linear-gradient(135deg, #45b7d1 0%, #96c93d 100%);
    }

    .more-colors-text {
        font-size: 0.7rem;
        color: var(--primary-color);
        font-weight: 500;
        margin-left: 5px;
    }

    /* تصميم اختيار الألوان في صفحة المنتج */
    .color-selection {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .color-option {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        padding: 8px 12px;
        border: 2px solid transparent;
        border-radius: 25px;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .color-option:hover {
        background: #e9ecef;
        border-color: var(--primary-color);
    }

    .color-option input[type="radio"] {
        display: none;
    }

    .color-option input[type="radio"]:checked + .color-circle {
        border: 3px solid var(--primary-color);
        box-shadow: 0 0 0 2px white, 0 0 0 4px var(--primary-color);
    }

    .color-option input[type="radio"]:checked ~ .color-name {
        color: var(--primary-color);
        font-weight: bold;
    }

    .color-circle {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .color-name {
        font-size: 0.9rem;
        font-weight: 500;
        color: #666;
        transition: all 0.3s ease;
    }
</style>

<section class="py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="row gx-4 gx-lg-5 align-items-start">
            <div class="col-md-6">
                <div class="product-images-container">
                    <div class="main-image-container">
                        <img id="display-img" src="<?php echo validate_image($img) ?>" alt="<?php echo $product_name ?>" loading="lazy" />
                    </div>
                    <div class="thumbnail-images">
                        <?php
                            foreach($fileO as $k => $img):
                                if(in_array($img,array('.','..')))
                                    continue;
                        ?>
                            <div class="view-image <?php echo $k == 2 ? "active":'' ?>">
                                <img src="<?php echo validate_image('uploads/product_'.$id.'/'.$img) ?>" loading="lazy" alt="صورة المنتج" />
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h1 class="display-5 fw-bolder" style="color: var(--primary-color, #2c5aa0);"><?php echo $product_name ?></h1>
                <!-- الوصف المختصر تمت إزالته لتجنب طباعة HTML خام. سيتم عرض الوصف المنظف أدناه. -->
                <?php if(!empty($inv)): ?>
                <div class="fs-5 mb-5">
                    <span id="price"><?php echo number_format($inv[0]['price']) ?> د.ع</span>
                </div>

                <!-- عرض الألوان إذا كانت متوفرة -->
                <?php
                $product_colors = array();
                if(isset($has_colors) && $has_colors == 1) {
                    $colors_qry = $conn->query("SELECT * FROM product_colors where product_id = ".$id);
                    if($colors_qry) {
                        while($color_row = $colors_qry->fetch_assoc()){
                            $product_colors[] = $color_row;
                        }
                    }
                }
                ?>

                <?php if(!empty($product_colors)): ?>
                <div class="mb-4">
                    <h6 class="fw-bold mb-3" style="color: var(--primary-color);">اللون المستخدم:</h6>
                    <div class="color-selection">
                        <?php foreach($product_colors as $index => $color): ?>
                        <label class="color-option">
                            <input type="radio" name="selected_color" value="<?php echo $color['id'] ?>" <?php echo $index == 0 ? 'checked' : '' ?>>
                            <span class="color-circle" style="background: <?php echo $color['color_code'] ?>;" title="<?php echo $color['color_name'] ?>"></span>
                            <span class="color-name"><?php echo $color['color_name'] ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <form action="" id="add-cart">
                <div class="d-flex">
                    <input type="hidden" name="price" value="<?php echo $inv[0]['price'] ?>">
                    <input type="hidden" name="inventory_id" value="<?php echo $inv[0]['id'] ?>">
                    <input class="form-control text-center me-3" id="inputQuantity" type="num" value="1" style="max-width: 3rem" name="quantity" />
                    <button class="btn flex-shrink-0" type="submit" style="background: var(--primary-color, #2c5aa0); color: white; border-radius: 25px; padding: 12px 25px; font-weight: 500; transition: all 0.3s ease;">
                        <i class="fas fa-shopping-cart me-1"></i>
                        أضف للسلة
                    </button>
                </div>
                </form>
                <?php else: ?>
                <div class="fs-5 mb-5">
                    <span class="text-danger">المنتج غير متوفر حالياً</span>
                </div>
                <?php endif; ?>
                                <?php
                if(!empty($description)) {
                    $decoded = html_entity_decode($description);
                    $safe_html = TextCleaner::sanitizeForDescription($decoded);
                    if(!empty($safe_html)) {
                        echo '<div class="lead" dir="rtl">' . $safe_html . '</div>';
                    }
                }
                ?>
                
            </div>
        </div>
    </div>
</section>
<!-- Related items section-->
<section class="py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
    <div class="container px-4 px-lg-5 mt-5">
        <h2 class="fw-bolder mb-5 text-center" style="color: var(--primary-color);">منتجات متشابهة</h2>
        <div class="row gx-4 gx-lg-5 justify-content-center">
        <?php
            $products = $conn->query("SELECT * FROM `products` where status = 1 and (category_id = '{$category_id}' or sub_category_id = '{$sub_category_id}') and id !='{$id}' order by rand() limit 4 ");
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
                    $inv[] = $ir;
                }

                // الحصول على الألوان
                $colors = array();
                if(isset($row['has_colors']) && $row['has_colors'] == 1) {
                    $colors_qry = $conn->query("SELECT * FROM product_colors where product_id = ".$row['id']);
                    if($colors_qry) {
                        while($color_row = $colors_qry->fetch_assoc()){
                            $colors[] = $color_row['color_name'];
                        }
                    }
                }
        ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card product-card">
                    <div class="product-image">
                        <?php if(!empty($colors)): ?>
                        <div class="sale-badge">ألوان متعددة</div>
                        <?php endif; ?>
                        <img src="<?php echo validate_image($img) ?>" alt="<?php echo $row['product_name'] ?>" loading="lazy" />
                    </div>

                    <div class="product-info">
                        <h5 class="product-title"><?php echo $row['product_name'] ?></h5>
                        <p class="product-description">
                            <?php
                            if(!empty($row['description'])) {
                                echo TextCleaner::cleanAndTruncateUltra($row['description'], 60);
                            }
                            ?>
                        </p>

                        <!-- عرض الألوان إذا كانت متوفرة -->
                        <?php if(!empty($colors)): ?>
                        <div class="product-colors">
                            <div class="colors-list">
                                <?php foreach(array_slice($colors, 0, 3) as $color): ?>
                                <span class="color-dot" title="<?php echo $color ?>"></span>
                                <?php endforeach; ?>
                                <?php if(count($colors) > 3): ?>
                                <span class="more-colors-text">+<?php echo count($colors) - 3 ?> ألوان</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="product-price">
                            <?php
                            if(!empty($inv)) {
                                $first_inv = reset($inv);
                                if(isset($first_inv['price']) && $first_inv['price'] > 0) {
                                    echo number_format($first_inv['price']) . ' د.ع';
                                } else {
                                    echo 'السعر عند الطلب';
                                }
                            } else {
                                echo 'السعر عند الطلب';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- أزرار التفاعل المخفية -->
                    <div class="product-actions">
                        <button class="btn btn-view" onclick="viewProduct('<?php echo md5($row['id']) ?>')">
                            <i class="fas fa-eye me-2"></i>
                            عرض التفاصيل
                        </button>
                        
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<script>
    <?php if(!empty($inv)): ?>
    var inv = $.parseJSON('<?php echo json_encode($inv) ?>');
    <?php else: ?>
    var inv = [];
    <?php endif; ?>

    $(function(){
        $('.view-image').click(function(){
            var _img = $(this).find('img').attr('src');
            $('#display-img').attr('src',_img);
            $('.view-image').removeClass("active")
            $(this).addClass("active")
        })

        $('.p-size').click(function(){
            // تم إزالة أزرار الحجم
        })

        $('#add-cart').submit(function(e){
            e.preventDefault();
            if(inv.length === 0){
                alert('المنتج غير متوفر حالياً');
                return false;
            }
            if('<?php echo $_settings->userdata('id') ?>' <= 0){
                uni_modal("","login.php");
                return false;
            }
            start_loader();
            $.ajax({
                url:'classes/Master.php?f=add_to_cart',
                data:$(this).serialize(),
                method:'POST',
                dataType:"json",
                error:err=>{
                    console.log(err)
                    alert_toast("an error occured",'error')
                    end_loader()
                },
                success:function(resp){
                    if(typeof resp == 'object' && resp.status=='success'){
                        alert_toast("Product added to cart.",'success')
                        $('#cart-count').text(resp.cart_count)
                    }else{
                        console.log(resp)
                        alert_toast("an error occured",'error')
                    }
                    end_loader();
                }
            })
        })
    })
</script>