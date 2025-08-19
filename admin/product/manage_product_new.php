<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `products` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}

// إذا كان هناك model_id، جلب معلومات الشركة والفئة
$selected_brand_id = '';
$selected_series_id = '';
$selected_model_id = '';

if(isset($model_id) && $model_id > 0) {
    $model_info = $conn->query("SELECT m.*, s.brand_id, s.name as series_name, b.name as brand_name 
                               FROM models m 
                               LEFT JOIN series s ON m.series_id = s.id 
                               LEFT JOIN brands b ON s.brand_id = b.id 
                               WHERE m.id = '{$model_id}'")->fetch_assoc();
    if($model_info) {
        $selected_brand_id = $model_info['brand_id'];
        $selected_series_id = $model_info['series_id'];
        $selected_model_id = $model_id;
    }
}
?>
<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "تعديل" : "إضافة" ?> منتج</h3>
	</div>
	<div class="card-body">
		<form action="" id="product-form-new" enctype="multipart/form-data">
			<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
            
            <!-- اختيار الشركة والفئة والموديل -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">تصنيف المنتج</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="brand_id" class="control-label">الشركة <span class="text-danger">*</span></label>
                                <select name="brand_id" id="brand_id" class="form-control select2" required>
                                    <option value="">اختر الشركة</option>
                                    <?php
                                    $brands = $conn->query("SELECT * FROM `brands` WHERE status = 1 ORDER BY sort_order ASC, name ASC");
                                    while($row = $brands->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $row['id'] ?>" <?php echo $selected_brand_id == $row['id'] ? 'selected' : '' ?>>
                                        <?php echo $row['name'] ?> <?php echo $row['name_ar'] ? '(' . $row['name_ar'] . ')' : '' ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="series_id" class="control-label">الفئة/السلسلة <span class="text-danger">*</span></label>
                                <select name="series_id" id="series_id" class="form-control" required>
                                    <option value="">اختر الشركة أولاً</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="model_id" class="control-label">الجهاز/الموديل <span class="text-danger">*</span></label>
                                <select name="model_id" id="model_id" class="form-control" required>
                                    <option value="">اختر الفئة أولاً</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <small class="text-muted">
                                ملاحظة: إذا لم تجد الموديل المطلوب، يمكنك إضافة موديل جديد من صفحة إدارة الموديلات
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات المنتج الأساسية -->
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">معلومات المنتج</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="product_name" class="control-label">اسم المنتج <span class="text-danger">*</span></label>
                        <textarea name="product_name" id="product_name" cols="30" rows="2" class="form-control" required><?php echo isset($product_name) ? $product_name : ''; ?></textarea>
                        <small class="text-muted">مثال: كفر حماية شفاف مع حلقة معدنية</small>
                    </div>
                    <div class="form-group">
                        <label for="description" class="control-label">الوصف التفصيلي</label>
                        <textarea name="description" id="description" cols="30" rows="3" class="form-control summernote"><?php echo isset($description) ? html_entity_decode($description) : ''; ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status" class="control-label">حالة المنتج</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>نشط</option>
                                    <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>غير نشط</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات المخزون والسعر -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">معلومات المخزون والسعر</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price" class="control-label">السعر (د.ع) <small class="text-muted">(اختياري)</small></label>
                                <input type="number" step="0.01" class="form-control" name="price" id="price" placeholder="اتركه فارغاً إذا لم يكن محدد">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantity" class="control-label">الكمية المتوفرة <small class="text-muted">(اختياري)</small></label>
                                <input type="number" class="form-control" name="quantity" id="quantity" placeholder="اتركه فارغاً إذا لم يكن محدد">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="unit" class="control-label">الوحدة</label>
                                <input type="text" class="form-control" name="unit" id="unit" placeholder="مثل: قطعة، علبة، كيلو" value="قطعة">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- خيارات المنتج (الألوان) -->
            <div class="card mb-3">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">خيارات المنتج</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="has_colors" name="has_colors" value="1">
                            <label class="custom-control-label" for="has_colors">هذا المنتج متوفر بألوان مختلفة</label>
                        </div>
                    </div>

                    <div id="colors-section" style="display: none;">
                        <div class="form-group">
                            <label for="colors" class="control-label">الألوان المتوفرة</label>
                            <div id="colors-container">
                                <div class="color-item mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="color_names[]" placeholder="اسم اللون (مثل: أزرق بحري)">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="color" class="form-control" name="color_codes[]" value="#007bff" title="اختر اللون">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-color">حذف</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-success" id="add-color">إضافة لون جديد</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- صور المنتج -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">صور المنتج</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="" class="control-label">صور المنتج</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="customFile" name="img[]" multiple accept="image/*,image/jpeg,image/png,image/gif,image/webp" onchange="displayImg(this,$(this))">
                            <label class="custom-file-label" for="customFile">اختر الصور (يمكن اختيار عدة صور)</label>
                        </div>
                        <small class="text-muted">الصيغ المدعومة: JPG, PNG, GIF, WEBP - الحد الأقصى: 5MB لكل صورة</small>
                    </div>
                    
                    <?php 
                    if(isset($id)):
                    $upload_path = "uploads/product_".$id;
                    if(is_dir(base_app.$upload_path)): 
                    ?>
                    <div class="existing-images mt-3">
                        <h6>الصور الحالية:</h6>
                        <?php 
                        $file= scandir(base_app.$upload_path);
                        foreach($file as $img):
                            if(in_array($img,array('.','..']))
                                continue;
                        ?>
                            <div class="d-inline-block m-2 position-relative">
                                <img src="<?php echo base_url.$upload_path.'/'.$img ?>" width="100px" height="100px" style="object-fit:cover;" class="img-thumbnail" alt="">
                                <button class="btn btn-sm btn-danger position-absolute" style="top: 5px; right: 5px;" type="button" onclick="removeImage('<?php echo base_app.$upload_path.'/'.$img ?>')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
			
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="product-form-new">حفظ</button>
		<a class="btn btn-flat btn-default" href="?page=product">إلغاء</a>
	</div>
</div>

<script>
$(document).ready(function(){
    // تهيئة Select2
    $('.select2').select2({
        placeholder: "اختر من القائمة",
        allowClear: true,
        width: '100%'
    });

    // تحديث الفئات عند تغيير الشركة
    $('#brand_id').change(function(){
        var brand_id = $(this).val();
        var series_select = $('#series_id');
        var model_select = $('#model_id');
        
        // إفراغ القوائم التابعة
        series_select.html('<option value="">جاري التحميل...</option>');
        model_select.html('<option value="">اختر الفئة أولاً</option>');
        
        if(brand_id) {
            $.ajax({
                url: 'product/ajax_get_series.php',
                type: 'POST',
                data: {brand_id: brand_id},
                dataType: 'json',
                success: function(response) {
                    if(response.status == 'success') {
                        var options = '<option value="">اختر الفئة</option>';
                        $.each(response.series, function(id, name) {
                            var selected = '<?php echo $selected_series_id ?>' == id ? 'selected' : '';
                            options += '<option value="' + id + '" ' + selected + '>' + name + '</option>';
                        });
                        series_select.html(options);
                        
                        // إذا كان هناك فئة محددة مسبقاً، قم بتحديث الموديلات
                        if('<?php echo $selected_series_id ?>') {
                            $('#series_id').trigger('change');
                        }
                    } else {
                        series_select.html('<option value="">لا توجد فئات متاحة</option>');
                    }
                },
                error: function() {
                    series_select.html('<option value="">خطأ في التحميل</option>');
                }
            });
        } else {
            series_select.html('<option value="">اختر الشركة أولاً</option>');
        }
    });

    // تحديث الموديلات عند تغيير الفئة
    $('#series_id').change(function(){
        var series_id = $(this).val();
        var model_select = $('#model_id');
        
        model_select.html('<option value="">جاري التحميل...</option>');
        
        if(series_id) {
            $.ajax({
                url: 'product/ajax_get_models.php',
                type: 'POST',
                data: {series_id: series_id},
                dataType: 'json',
                success: function(response) {
                    if(response.status == 'success') {
                        var options = '<option value="">اختر الموديل</option>';
                        $.each(response.models, function(id, name) {
                            var selected = '<?php echo $selected_model_id ?>' == id ? 'selected' : '';
                            options += '<option value="' + id + '" ' + selected + '>' + name + '</option>';
                        });
                        model_select.html(options);
                    } else {
                        model_select.html('<option value="">لا توجد موديلات متاحة</option>');
                    }
                },
                error: function() {
                    model_select.html('<option value="">خطأ في التحميل</option>');
                }
            });
        } else {
            model_select.html('<option value="">اختر الفئة أولاً</option>');
        }
    });

    // تحميل البيانات إذا كانت محددة مسبقاً
    if('<?php echo $selected_brand_id ?>') {
        $('#brand_id').trigger('change');
    }

    // باقي JavaScript كما هو من الملف الأصلي...
    // [كود إدارة الألوان والصور...]
    
    // حفظ النموذج
    $('#product-form-new').submit(function(e){
        e.preventDefault();
        var _this = $(this)
        $('.err-msg').remove();
        start_loader();
        
        $.ajax({
            url: _base_url_+"classes/Master.php?f=save_product_new",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error: err=>{
                console.log(err)
                alert_toast("An error occured",'error');
                end_loader();
            },
            success: function(resp){
                if(typeof resp =='object' && resp.status == 'success'){
                    location.href = "./?page=product";
                }else if(resp.status == 'failed' && !!resp.msg){
                    var el = $('<div>')
                    el.addClass("alert alert-danger err-msg").text(resp.msg)
                    _this.prepend(el)
                    el.show('slow')
                    $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                    end_loader()
                }else{
                    alert_toast("An error occured",'error');
                    end_loader();
                    console.log(resp)
                }
            }
        })
    })
});

// باقي الدوال...
function displayImg(input,_this) {
    // [كود عرض الصور]
}

function removeImage(path) {
    // [كود حذف الصور]
}
</script>
