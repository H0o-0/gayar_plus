<?php
require_once('../config.php');
header('Content-Type: application/json; charset=utf-8');

try {
    // Helpers
    $hasCol = function($table, $col) use ($conn) {
        $q = $conn->query("SHOW COLUMNS FROM `{$table}` LIKE '".$conn->real_escape_string($col)."'");
        return $q && $q->num_rows > 0;
    };

    // جلب الشركات والفئات والموديلات
    // NOTE: Use a schema-compatible ORDER BY (some schemas may not have sort_order)
    $brands_where = $hasCol('brands','status') ? 'WHERE status = 1' : '';
    $brands_query = "SELECT * FROM brands {$brands_where} ORDER BY name ASC LIMIT 8";
    $brands_result = $conn->query($brands_query);
    
    if (!$brands_result || $brands_result->num_rows == 0) {
        // إذا لم توجد جداول جديدة، استخدم النظام القديم
        $html = generateOldSystemMenu($conn);
    } else {
        $html = generateNewSystemMenu($conn, $brands_result);
    }
    
    echo json_encode([
        'status' => 'success',
        'html' => $html
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function generateNewSystemMenu($conn, $brands_result) {
    $html = '';
    $brand_count = 0;
    
    while ($brand = $brands_result->fetch_assoc()) {
        $brand_count++;
        
        // كل 4 شركات في صف جديد للشاشات الكبيرة
        if ($brand_count == 1) {
            $html .= '<div class="row">';
        }
        
        $html .= '<div class="col-lg-3 col-md-6 col-sm-12">';
        $html .= '<div class="brand-column">';
        
        // عنوان الشركة
        $brand_name = !empty($brand['name_ar']) ? $brand['name_ar'] : $brand['name'];
        $html .= '<div class="brand-title" data-brand-id="' . $brand['id'] . '">';
        if ($brand['logo']) {
            $html .= '<img src="' . htmlspecialchars($brand['logo']) . '" class="brand-logo" alt="' . htmlspecialchars($brand_name) . '">';
        }
        $html .= htmlspecialchars($brand_name);
        $html .= '</div>';
        
        // جلب الفئات/السلاسل للشركة
        // NOTE: Avoid referencing sort_order to support older schemas
        $series_where_status = $hasCol('series','status') ? 'AND status = 1' : '';
        $series_query = "SELECT * FROM series WHERE brand_id = {$brand['id']} {$series_where_status} ORDER BY name ASC LIMIT 6";
        $series_result = $conn->query($series_query);
        
        if ($series_result && $series_result->num_rows > 0) {
            while ($series = $series_result->fetch_assoc()) {
                $html .= '<div class="series-group">';
                
                $series_name = !empty($series['name_ar']) ? $series['name_ar'] : $series['name'];
                $html .= '<div class="series-title" data-brand-id="' . $brand['id'] . '" data-series-id="' . $series['id'] . '">';
                $html .= htmlspecialchars($series_name);
                $html .= '</div>';
                
                // جلب الموديلات للفئة
                // NOTE: Avoid referencing sort_order to support older schemas
                $models_where_status = $hasCol('models','status') ? 'AND status = 1' : '';
                $models_query = "SELECT * FROM models WHERE series_id = {$series['id']} {$models_where_status} ORDER BY name ASC LIMIT 5";
                $models_result = $conn->query($models_query);
                
                if ($models_result && $models_result->num_rows > 0) {
                    $html .= '<div class="models-list">';
                    while ($model = $models_result->fetch_assoc()) {
                        $model_name = !empty($model['name_ar']) ? $model['name_ar'] : $model['name'];
                        if ($model['model_number']) {
                            $model_name .= ' ' . $model['model_number'];
                        }
                        
                        $html .= '<a class="mega-menu-item" href="#" ';
                        $html .= 'data-brand-id="' . $brand['id'] . '" ';
                        $html .= 'data-series-id="' . $series['id'] . '" ';
                        $html .= 'data-model-id="' . $model['id'] . '">';
                        $html .= htmlspecialchars($model_name);
                        $html .= '</a>';
                    }
                    $html .= '</div>';
                } else {
                    $html .= '<div class="models-list">';
                    $html .= '<a class="mega-menu-item" href="#" data-brand-id="' . $brand['id'] . '" data-series-id="' . $series['id'] . '">جميع المنتجات</a>';
                    $html .= '</div>';
                }
                
                $html .= '</div>'; // end series-group
            }
        } else {
            $html .= '<div class="series-group">';
            $html .= '<a class="mega-menu-item" href="#" data-brand-id="' . $brand['id'] . '">جميع منتجات ' . htmlspecialchars($brand_name) . '</a>';
            $html .= '</div>';
        }
        
        $html .= '</div>'; // end brand-column
        $html .= '</div>'; // end col
        
        // إغلاق الصف كل 4 عناصر أو في النهاية
        if ($brand_count % 4 == 0 || $brand_count == $brands_result->num_rows) {
            $html .= '</div>'; // end row
            if ($brand_count < $brands_result->num_rows) {
                // ابدأ صف جديد إذا كانت هناك شركات أكثر
            }
        }
    }
    
    // إضافة رابط "عرض جميع الأجهزة" في النهاية
    $html .= '<div class="row mt-3">';
    $html .= '<div class="col-12 text-center">';
    $html .= '<a href="./?p=products" class="btn btn-outline-primary btn-lg">';
    $html .= '<i class="fas fa-th-large me-2"></i>عرض جميع الأجهزة';
    $html .= '</a>';
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

function generateOldSystemMenu($conn) {
    // النظام القديم كـ fallback
    $html = '';
    
    $cat_qry = $conn->query("SELECT * FROM categories WHERE status = 1 ORDER BY category ASC LIMIT 6");
    if ($cat_qry && $cat_qry->num_rows > 0) {
        $html .= '<div class="row">';
        $col_count = 0;
        
        while ($crow = $cat_qry->fetch_assoc()) {
            $col_count++;
            $html .= '<div class="col-lg-2 col-md-4 col-sm-6">';
            $html .= '<div class="brand-column">';
            $html .= '<div class="brand-title">' . htmlspecialchars($crow['category']) . '</div>';
            
            $sub_qry = $conn->query("SELECT * FROM sub_categories WHERE status = 1 AND parent_id = '{$crow['id']}' ORDER BY sub_category ASC LIMIT 5");
            if ($sub_qry && $sub_qry->num_rows > 0) {
                while ($srow = $sub_qry->fetch_assoc()) {
                    $html .= '<a class="mega-menu-item" href="./?p=products&c=' . md5($crow['id']) . '&s=' . md5($srow['id']) . '">';
                    $html .= htmlspecialchars($srow['sub_category']);
                    $html .= '</a>';
                }
            }
            
            $html .= '<a class="mega-menu-item" href="./?p=products&c=' . md5($crow['id']) . '">عرض الكل</a>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
    } else {
        $html = '<div class="col-12 text-center"><p class="text-muted">لا توجد أجهزة متاحة حالياً</p></div>';
    }
    
    return $html;
}

$conn->close();
?>
