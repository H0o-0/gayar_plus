<!DOCTYPE html>
<html>
<head>
    <title>Debug Submenu</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>اختبار القائمة الفرعية</h2>
    
    <?php
    require_once('initialize.php');
    
    // اختبار بسيط للقائمة الفرعية
    $brand_id = 1; // Apple
    $display_name = "Apple";
    ?>
    
    <div style="position: relative; width: 300px; margin: 50px;">
        <div class="dropdown-item-wrapper" data-brand-id="<?= $brand_id ?>">
            <a href="#" class="brand-item" onmouseover="showSubmenu(<?= $brand_id ?>)" onmouseout="hideSubmenu(<?= $brand_id ?>)" style="display: block; padding: 10px; background: #f0f0f0; border: 1px solid #ccc;">
                <span><?= $display_name ?></span>
                <span style="float: right;">‹</span>
            </a>
            
            <div class="submenu" id="submenu-<?= $brand_id ?>" style="display: none; position: absolute; right: 100%; top: 0; background: white; min-width: 180px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 10000; margin-right: 10px;">
                <div class="submenu-content" style="padding: 4px 0;">
                    <?php
                    // جلب فئات Apple
                    $categories_query = "
                        SELECT 
                            s.id,
                            COALESCE(NULLIF(s.name_ar, ''), s.name) as category_name
                        FROM series s 
                        WHERE s.brand_id = $brand_id 
                        AND s.status = 1 
                        ORDER BY s.sort_order ASC, s.name ASC
                    ";
                    
                    $categories_result = $conn->query($categories_query);
                    if($categories_result && $categories_result->num_rows > 0) {
                        while($category = $categories_result->fetch_assoc()) {
                    ?>
                    <a href="#" class="submenu-item" style="display: block; padding: 8px 12px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">
                        <?= htmlspecialchars($category['category_name']) ?>
                    </a>
                    <?php 
                        }
                    } else {
                        echo "<div style='padding: 8px 12px; color: #999;'>لا توجد فئات</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function showSubmenu(brandId) {
        console.log('=== SUBMENU TEST ===');
        console.log('Brand ID:', brandId);
        
        var submenu = document.getElementById('submenu-' + brandId);
        console.log('Submenu element:', submenu);
        
        if (submenu) {
            submenu.style.display = 'block';
            console.log('✅ Submenu shown');
        } else {
            console.log('❌ Submenu not found');
        }
    }
    
    function hideSubmenu(brandId) {
        var submenu = document.getElementById('submenu-' + brandId);
        if (submenu) {
            submenu.style.display = 'none';
        }
    }
    </script>
</body>
</html>
