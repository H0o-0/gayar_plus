<?php
// ملف لإصلاح مشاكل التصنيف التلقائي
require_once('../config.php');

// إصلاح التصنيف التلقائي للمنتجات غير المصنفة أو المصنفة بشكل خاطئ
$fix_query = $conn->query("
    SELECT id, product_name 
    FROM temp_warehouse 
    WHERE (status = 'unclassified' OR suggested_brand IS NULL OR category_id IS NULL)
    ORDER BY created_at DESC
");

$fixed_count = 0;
$total_processed = 0;

if($fix_query) {
    while($row = $fix_query->fetch_assoc()) {
        $total_processed++;
        $classification = classifyProduct($row['product_name'], $conn);
        
        // تحديث التصنيف حتى لو كان جزئياً
        $update_fields = [];
        
        if($classification['brand']) {
            $update_fields[] = "suggested_brand = '" . $conn->real_escape_string($classification['brand']) . "'";
        }
        
        if($classification['category_id']) {
            $update_fields[] = "category_id = " . $classification['category_id'];
        }
        
        if($classification['sub_category_id']) {
            $update_fields[] = "sub_category_id = " . $classification['sub_category_id'];
        }
        
        // تحديث الحالة إلى مصنف إذا تم العثور على تصنيف
        if(!empty($update_fields)) {
            $update_fields[] = "status = 'classified'";
            $update_fields[] = "updated_at = NOW()";
            
            $update_sql = "UPDATE temp_warehouse SET " . implode(', ', $update_fields) . " WHERE id = " . $row['id'];
            
            if($conn->query($update_sql)) {
                $fixed_count++;
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode([
    'message' => "تم تصنيف $fixed_count منتج تلقائياً",
    'fixed_count' => $fixed_count
]);

// دالة التصنيف الذكي
function classifyProduct($product_name, $conn) {
    $name = strtolower($product_name);
    
    // الحصول على التصنيفات الأساسية
    $categories = [];
    $sub_categories = [];
    
    $cat_qry = $conn->query("SELECT * FROM categories WHERE status = 1");
    while($cat = $cat_qry->fetch_assoc()) {
        $categories[$cat['id']] = $cat['category'];
    }
    
    $sub_cat_qry = $conn->query("SELECT * FROM sub_categories WHERE status = 1");
    while($sub_cat = $sub_cat_qry->fetch_assoc()) {
        $sub_categories[$sub_cat['id']] = [
            'name' => $sub_cat['sub_category'],
            'parent_id' => $sub_cat['parent_id']
        ];
    }
    
    // قواعد التصنيف المحسنة
    $brands = [
        'Apple' => ['iphone', 'apple', 'ios', 'ايفون', 'آيفون', 'أيفون', 'ابل'],
        'Samsung' => ['samsung', 'galaxy', 'note', 'سامسونج', 'جالاكسي', 'سامسونغ'],
        'Huawei' => ['huawei', 'mate', 'p30', 'p40', 'هواوي'],
        'Honor' => ['honor', 'هونر'],
        'Xiaomi' => ['xiaomi', 'redmi', 'mi', 'شاومي', 'ريدمي'],
        'Oppo' => ['oppo', 'find', 'reno', 'أوبو', 'اوبو'],
        'Vivo' => ['vivo', 'nex', 'فيفو'],
        'OnePlus' => ['oneplus', 'one plus', 'ون بلس'],
        'LG' => ['lg', '엘지'],
        'Sony' => ['sony', 'xperia', 'سوني'],
        'Nokia' => ['nokia', 'نوكيا'],
        'Realme' => ['realme', 'ريل مي'],
        'Motorola' => ['motorola', 'موتورولا'],
        'Lenovo' => ['lenovo', 'لينوفو'],
        'Asus' => ['asus', 'zenfone', 'آسوس'],
        'Google' => ['google', 'pixel', 'جوجل'],
        'Nothing' => ['nothing', '낫ينغ'],
        'Infinix' => ['infinix', 'إنفينيكس'],
        'Tecno' => ['tecno', 'تكنو'],
        'Itel' => ['itel', 'آيتل'],
        'Tools' => ['tool', 'screwdriver', 'repair', 'kit', 'set', 'أدوات', 'ادوات', 'مفك', 'مفتاح']
    ];
    
    $product_types = [
        'Phone Parts' => ['screen', 'display', 'lcd', 'battery', 'charger', 'case', 'cover', 'glass', 'back', 'شاشة', 'بطارية', 'شاحن', 'غطاء', 'زجاج', 'خلفي'],
        'Audio' => ['earphone', 'headphone', 'speaker', 'bluetooth', 'سماعة', 'سماعات', 'سمارت', 'سمارت فون', 'سمارت فونز'],
        'Cables' => ['cable', 'usb', 'lightning', 'type-c', 'كابل', 'usb', 'usb-c'],
        'Power' => ['powerbank', 'charger', 'adapter', 'بطارية', 'شاحن', 'محول'],
        'Cases' => ['case', 'cover', 'protector', 'silicone', 'غطاء', 'حافظ', 'سيليكون'],
        'Screens' => ['screen', 'display', 'lcd', 'amoled', 'شاشة', 'عرض', 'الكريستال السائل', 'amoled'],
        'Batteries' => ['battery', 'power', 'بطارية', 'طاقة'],
        'Tools' => ['tool', 'screwdriver', 'repair', 'kit', 'opener', 'مفك', 'أداة', 'إصلاح', 'مجموعة', 'فتحة'],
        'Accessories' => ['accessory', 'accessories', 'holder', 'stand', 'mount', 'إكسسوارات', 'حامل', 'حاملة', 'مثبت']
    ];
    
    $suggested_brand = null;
    $suggested_category_id = null;
    $suggested_sub_category_id = null;
    
    // البحث عن العلامة التجارية
    foreach ($brands as $brand => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($name, $keyword) !== false) {
                $suggested_brand = $brand;
                break 2;
            }
        }
    }
    
    // البحث عن الفئة المناسبة
    foreach ($product_types as $type => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($name, $keyword) !== false) {
                foreach ($categories as $cat_id => $cat_name) {
                    if (stripos($cat_name, $type) !== false || ($type == 'Phone Parts' && stripos($cat_name, 'Accessories') !== false)) {
                        $suggested_category_id = $cat_id;
                        
                        foreach ($sub_categories as $sub_id => $sub_data) {
                            if ($sub_data['parent_id'] == $cat_id) {
                                $suggested_sub_category_id = $sub_id;
                                break 2;
                            }
                        }
                        break 2;
                    }
                }
                break 2;
            }
        }
    }
    
    // إذا لم نجد تصنيف محدد، نستخدم Accessories كافتراضي
    if (!$suggested_category_id) {
        foreach ($categories as $cat_id => $cat_name) {
            if (stripos($cat_name, 'Accessories') !== false || stripos($cat_name, 'إكسسوارات') !== false) {
                $suggested_category_id = $cat_id;
                foreach ($sub_categories as $sub_id => $sub_data) {
                    if ($sub_data['parent_id'] == $cat_id) {
                        $suggested_sub_category_id = $sub_id;
                        break;
                    }
                }
                break;
            }
        }
    }
    
    return [
        'brand' => $suggested_brand,
        'category_id' => $suggested_category_id,
        'sub_category_id' => $suggested_sub_category_id,
        'category_name' => $suggested_category_id ? $categories[$suggested_category_id] : null,
        'sub_category_name' => $suggested_sub_category_id ? $sub_categories[$suggested_sub_category_id]['name'] : null
    ];
}
?>
