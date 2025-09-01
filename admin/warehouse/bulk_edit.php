<?php
if(!isset($_GET['ids'])) {
    echo '<div class="alert alert-danger">لم يتم تحديد منتجات للتحرير</div>';
    exit;
}

$ids = explode(',', $_GET['ids']);
$ids = array_map('intval', $ids);
$ids = array_filter($ids, function($id) { return $id > 0; });

if(empty($ids)) {
    echo '<div class="alert alert-danger">معرفات المنتجات غير صحيحة</div>';
    exit;
}

// الحصول على بيانات المنتجات
$ids_str = implode(',', $ids);
$products_query = $conn->query("SELECT * FROM temp_warehouse WHERE id IN ($ids_str) ORDER BY id");
$products = [];
while($row = $products_query->fetch_assoc()) {
    $products[] = $row;
}

// الحصول على التصنيفات
$categories = $conn->query("SELECT * FROM categories WHERE status = 1");
$sub_categories = $conn->query("SELECT * FROM sub_categories WHERE status = 1");

// تحويل المصفوفة إلى مصفوفة مرتبة حسب المعرف
$categories_array = [];
while($cat = $categories->fetch_assoc()) {
    $categories_array[$cat['id']] = $cat['category'];
}

$sub_categories_array = [];
$sub_categories->data_seek(0);
while($sub_cat = $sub_categories->fetch_assoc()) {
    $sub_categories_array[$sub_cat['id']] = $sub_cat;
}
?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">تحرير منتجات متعددة (<?php echo count($products) ?> منتج)</h3>
        <div class="card-tools">
            <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=warehouse">
                <i class="fa fa-arrow-left"></i> العودة للمخزن
            </a>
        </div>
    </div>
    <div class="card-body">
        <form id="bulk-edit-form" enctype="multipart/form-data">
            <input type="hidden" name="ids" value="<?php echo implode(',', $ids) ?>">
            
            <!-- معلومات مشتركة لجميع المنتجات -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">معلومات مشتركة لجميع المنتجات</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category_id">الفئة الرئيسية:</label>
                                <select class="form-control" id="category_id" name="category_id">
                                    <option value="">اختر الفئة (سيتم تطبيقها على جميع المنتجات)</option>
                                    <?php foreach($categories_array as $cat_id => $cat_name): ?>
                                    <option value="<?php echo $cat_id ?>"><?php echo $cat_name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sub_category_id">الفئة الفرعية:</label>
                                <select class="form-control" id="sub_category_id" name="sub_category_id" disabled>
                                    <option value="">اختر الفئة أولاً</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="brand">العلامة التجارية:</label>
                                <input type="text" class="form-control" id="brand" name="brand" placeholder="مثل: iPhone, Samsung, Huawei (سيتم تطبيقها على جميع المنتجات)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price_multiplier">مضاعف السعر:</label>
                                <input type="number" class="form-control" id="price_multiplier" name="price_multiplier" 
                                       value="1" min="0.1" step="0.1" placeholder="مثال: 1.1 لزيادة السعر بنسبة 10%">
                                <small class="text-muted">سيتم ضرب الأسعار الأصلية بهذا الرقم</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- قائمة المنتجات للتحرير الفردي -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">تحرير كل منتج على حدة</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>اسم المنتج</th>
                                    <th>السعر الأصلي</th>
                                    <th>السعر الجديد</th>
                                    <th>اسم المنتج الجديد</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($products as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['product_name']) ?></td>
                                    <td><?php echo number_format($product['original_price']) ?> د.ع</td>
                                    <td>
                                        <input type="number" class="form-control price-input" 
                                               name="prices[<?php echo $product['id'] ?>]" 
                                               value="<?php echo $product['original_price'] ?>" 
                                               min="0" step="0.01" data-original="<?php echo $product['original_price'] ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" 
                                               name="names[<?php echo $product['id'] ?>]" 
                                               value="<?php echo htmlspecialchars($product['product_name']) ?>">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- أزرار الإجراءات -->
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-globe"></i> نشر جميع المنتجات
                </button>
                <button type="button" class="btn btn-primary btn-lg" id="save-draft">
                    <i class="fas fa-save"></i> حفظ كمسودة
                </button>
                <a href="./index.php?page=warehouse" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function(){
    // تحديث الفئات الفرعية عند تغيير الفئة الرئيسية
    $('#category_id').change(function(){
        var selectedCategory = $(this).val();
        $('#sub_category_id').prop('disabled', !selectedCategory);
        
        if(selectedCategory) {
            // جلب الفئات الفرعية عبر AJAX
            $.post('ajax_actions.php', {
                action: 'get_subcategories',
                category_id: selectedCategory
            }, function(response) {
                if(response.status == 'success') {
                    $('#sub_category_id').empty().append('<option value="">اختر الفئة الفرعية</option>');
                    $.each(response.subcategories, function(id, name) {
                        $('#sub_category_id').append('<option value="' + id + '">' + name + '</option>');
                    });
                }
            }, 'json');
        } else {
            $('#sub_category_id').empty().append('<option value="">اختر الفئة أولاً</option>');
        }
    });
    
    // تحديث الأسعار عند تغيير مضاعف السعر
    $('#price_multiplier').on('input', function(){
        var multiplier = parseFloat($(this).val()) || 1;
        $('.price-input').each(function(){
            var originalPrice = parseFloat($(this).data('original')) || 0;
            var newPrice = (originalPrice * multiplier).toFixed(2);
            $(this).val(newPrice);
        });
    });
    
    // معالجة نشر المنتجات
    $('#bulk-edit-form').on('submit', function(e){
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('action', 'bulkPublish');
        
        $.ajax({
            url: 'ajax_actions.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                $('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري النشر...');
            },
            success: function(response) {
                if(response.status == 'success') {
                    alert_toast(response.message, 'success');
                    setTimeout(function(){
                        window.location.href = 'index.php?page=warehouse';
                    }, 1500);
                } else {
                    alert_toast('خطأ: ' + response.message, 'error');
                    $('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-globe"></i> نشر جميع المنتجات');
                }
            },
            error: function() {
                alert_toast('حدث خطأ أثناء معالجة الطلب', 'error');
                $('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-globe"></i> نشر جميع المنتجات');
            }
        });
    });
    
    // حفظ كمسودة
    $('#save-draft').click(function(){
        var formData = new FormData($('#bulk-edit-form')[0]);
        formData.append('action', 'bulkSaveDraft');
        
        $.ajax({
            url: 'ajax_actions.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                $('#save-draft').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...');
            },
            success: function(response) {
                if(response.status == 'success') {
                    alert_toast(response.message, 'success');
                    setTimeout(function(){
                        window.location.href = 'index.php?page=warehouse';
                    }, 1500);
                } else {
                    alert_toast('خطأ: ' + response.message, 'error');
                    $('#save-draft').prop('disabled', false).html('<i class="fas fa-save"></i> حفظ كمسودة');
                }
            },
            error: function() {
                alert_toast('حدث خطأ أثناء معالجة الطلب', 'error');
                $('#save-draft').prop('disabled', false).html('<i class="fas fa-save"></i> حفظ كمسودة');
            }
        });
    });
});
</script>