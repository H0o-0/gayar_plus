<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `products` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Update ": "Create New " ?> Product</h3>
	</div>
	<div class="card-body">
		<form action="" id="product-form" enctype="multipart/form-data">
			<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
            <div class="form-group">
				<label for="brand_id" class="control-label">Brand</label>
                <select name="brand_id" id="brand_id" class="custom-select select2" required>
                <option value=""></option>
                <?php
                    $qry = $conn->query("SELECT * FROM `brands` order by name asc");
                    while($row= $qry->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($brand_id) && $brand_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                <?php endwhile; ?>
                </select>
			</div>
            <div class="form-group">
				<label for="series_id" class="control-label">Series</label>
                <select name="series_id" id="series_id" class="custom-select" required>
                <option value="" selected="" disabled="">Select Brand First</option>
                <?php if(isset($brand_id) && isset($series_id)): ?>
                    <?php
                        $series_qry = $conn->query("SELECT * FROM `series` WHERE brand_id = '$brand_id' ORDER BY name ASC");
                        while($series_row = $series_qry->fetch_assoc()):
                    ?>
                    <option value="<?php echo $series_row['id'] ?>" <?php echo $series_id == $series_row['id'] ? 'selected' : '' ?>><?php echo $series_row['name'] ?></option>
                    <?php endwhile; ?>
                <?php endif; ?>
                </select>
			</div>
            <div class="form-group">
				<label for="model_id" class="control-label">Model</label>
                <select name="model_id" id="model_id" class="custom-select" required>
                <option value="" selected="" disabled="">Select Series First</option>
                <?php if(isset($series_id) && isset($model_id)): ?>
                    <?php
                        $model_qry = $conn->query("SELECT * FROM `models` WHERE series_id = '$series_id' ORDER BY name ASC");
                        while($model_row = $model_qry->fetch_assoc()):
                    ?>
                    <option value="<?php echo $model_row['id'] ?>" <?php echo $model_id == $model_row['id'] ? 'selected' : '' ?>><?php echo $model_row['name'] ?></option>
                    <?php endwhile; ?>
                <?php endif; ?>
                </select>
			</div>
			<div class="form-group">
				<label for="product_name" class="control-label">Product Name</label>
                <textarea name="product_name" id="product_name" cols="30" rows="2" class="form-control form no-resize" placeholder="اكتب اسم المنتج..."><?php echo isset($product_name) ? $product_name : ''; ?></textarea>
			</div>
            
            <div class="form-group">
                <label for="description" class="control-label">Description</label>
                <textarea name="description" id="" cols="30" rows="2" class="form-control form no-resize summernote"><?php echo isset($description) ? html_entity_decode($description) : ''; ?></textarea>
			</div>
            <div class="form-group">
				<label for="status" class="control-label">Status</label>
                <select name="status" id="status" class="custom-select selevt">
                <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
                <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
                </select>
			</div>

            <!-- معلومات المخزون والسعر -->
            <div class="card mt-3">
                <div class="card-header bg-info">
                    <h5 class="mb-0">معلومات المخزون والسعر</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price" class="control-label">السعر (ر.س) <small class="text-muted">(اختياري)</small></label>
                                <input type="number" step="0.01" class="form-control" name="price" id="price" placeholder="اتركه فارغاً إذا لم يكن محدد" value="<?php echo isset($price) ? $price : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantity" class="control-label">الكمية المتوفرة <small class="text-muted">(اختياري)</small></label>
                                <input type="number" class="form-control" name="quantity" id="quantity" placeholder="اتركه فارغاً إذا لم يكن محدد" value="<?php echo isset($quantity) ? $quantity : '' ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="unit" class="control-label">الوحدة</label>
                                <input type="text" class="form-control" name="unit" id="unit" placeholder="مثل: قطعة، علبة، كيلو" value="<?php echo isset($unit) ? $unit : 'قطعة' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- خيارات المنتج (الألوان) -->
            <div class="card mt-3">
                <div class="card-header bg-success">
                    <h5 class="mb-0">خيارات المنتج</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="has_colors" name="has_colors" value="1" <?php echo isset($has_colors) && $has_colors == 1 ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="has_colors">هذا المنتج متوفر بألوان مختلفة</label>
                        </div>
                    </div>

                    <div id="colors-section" style="<?php echo isset($has_colors) && $has_colors == 1 ? 'display: block;' : 'display: none;' ?>">
                        <div class="form-group">
                            <label for="colors" class="control-label">الألوان المتوفرة</label>
                            <div id="colors-container">
                                <?php if(isset($colors) && !empty($colors)): 
                                    $colors_array = json_decode($colors, true);
                                    if(is_array($colors_array)):
                                        foreach($colors_array as $color):
                                ?>
                                <div class="color-item mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="color_names[]" placeholder="اسم اللون (مثل: أزرق بحري)" value="<?php echo htmlspecialchars($color['name']) ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="color" class="form-control" name="color_codes[]" value="<?php echo htmlspecialchars($color['code']) ?>" title="اختر اللون">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-color">حذف</button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                                <?php else: ?>
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
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-sm btn-success" id="add-color">إضافة لون جديد</button>
                        </div>
                    </div>
                </div>
            </div>

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
            <?php 
                $file= scandir(base_app.$upload_path);
                foreach($file as $img):
                    if(in_array($img,array('.','..')))
                        continue;
            ?>
                <div class="d-flex w-100 align-items-center img-item">
                    <span><img src="<?php echo base_url.$upload_path.'/'.$img ?>" width="150px" height="100px" style="object-fit:cover;" class="img-thumbnail" alt=""></span>
                    <span class="ml-4"><button class="btn btn-sm btn-default text-danger rem_img" type="button" data-path="<?php echo base_app.$upload_path.'/'.$img ?>"><i class="fa fa-trash"></i></button></span>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>
            <?php endif; ?>
			
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="product-form">Save</button>
		<a class="btn btn-flat btn-default" href="?page=product">Cancel</a>
	</div>
</div>

<script>
$(document).ready(function(){
    // إعداد الـ select2 للبراند
    $('#brand_id').select2({
        placeholder: "اختر البراند",
        width: "100%"
    });

    // إذا كان في وضع التحديث وتم اختيار البراند مسبقاً، قم بإعداد السيريس والموديل
    <?php if(isset($brand_id)): ?>
    $('#series_id').select2({
        placeholder: "اختر السيريس",
        width: "100%"
    });
    
    <?php if(isset($series_id)): ?>
    $('#model_id').select2({
        placeholder: "اختر الموديل",
        width: "100%"
    });
    <?php endif; ?>
    <?php endif; ?>
    
    // تحديث السيريس عند تغيير البراند
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
                method: "POST",
                data: {brand_id: brand_id},
                dataType: "json",
                error:err=>{
                    console.log(err)
                    alert_toast("An error occured.",'error');
                    end_loader();
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        var options = '<option value="">اختر الفئة</option>';
                        $.each(resp.series, function(id, name) {
                            var selected = '<?php echo isset($series_id) ? $series_id : '' ?>' == id ? 'selected' : '';
                            options += '<option value="' + id + '" ' + selected + '>' + name + '</option>';
                        });
                        series_select.html(options);
                        series_select.select2({
                            placeholder: "اختر السيريس",
                            width: "100%"
                        });
                    } else {
                        series_select.html('<option value="">لا توجد فئات متاحة</option>');
                    }
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
                method: "POST",
                data: {series_id: series_id},
                dataType: "json",
                error:err=>{
                    console.log(err)
                    alert_toast("An error occured.",'error');
                    end_loader();
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        var options = '<option value="">اختر الموديل</option>';
                        $.each(resp.models, function(id, name) {
                            var selected = '<?php echo isset($model_id) ? $model_id : '' ?>' == id ? 'selected' : '';
                            options += '<option value="' + id + '" ' + selected + '>' + name + '</option>';
                        });
                        model_select.html(options);
                        model_select.select2({
                            placeholder: "اختر الموديل",
                            width: "100%"
                        });
                    } else {
                        model_select.html('<option value="">لا توجد موديلات متاحة</option>');
                    }
                }
            });
        } else {
            model_select.html('<option value="">اختر الفئة أولاً</option>');
        }
    });

    // معالج حذف الصور
    $(document).on('click', '.rem_img', function(){
        var path = $(this).data('path');
        if(confirm('هل أنت متأكد من حذف هذه الصورة؟')){
            delete_img(path);
        }
    });

    $('#product-form').submit(function(e){
        e.preventDefault();
        var _this = $(this)
        $('.err-msg').remove();
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master.php?f=save_product",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error:err=>{
                console.log(err)
                alert_toast("An error occured",'error');
                end_loader();
            },
            success:function(resp){
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

    $('.summernote').summernote({
        height: 220,
        lang: 'ar-AR',
        dialogsInBody: true,
        placeholder: 'اكتب وصف المنتج هنا... يمكن إضافة عناوين وقوائم وروابط',
        toolbar: [
            [ 'style', [ 'style' ] ],
            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
            [ 'fontname', [ 'fontname' ] ],
            [ 'fontsize', [ 'fontsize' ] ],
            [ 'color', [ 'color' ] ],
            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
            [ 'table', [ 'table' ] ],
            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
        ]
    });
    
    // فرض اتجاه عربي داخل المحرر
    $('.summernote').on('summernote.init', function() {
        $(this).next('.note-editor').find('.note-editable')
            .attr('dir','rtl')
            .css({ 'text-align':'right', 'line-height':'1.9' });
    });
    
    // في حال كان المحرر مهيأ مسبقاً
    $('.note-editor .note-editable').attr('dir','rtl').css({ 'text-align':'right', 'line-height':'1.9' });

    // إدارة الألوان
    $('#has_colors').change(function(){
        if($(this).is(':checked')){
            $('#colors-section').slideDown();
        } else {
            $('#colors-section').slideUp();
        }
    });

    $('#add-color').click(function(){
        var colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96c93d', '#feca57', '#ff9ff3', '#54a0ff', '#5f27cd'];
        var randomColor = colors[Math.floor(Math.random() * colors.length)];

        var colorItem = `
            <div class="color-item mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="color_names[]" placeholder="اسم اللون (مثل: أحمر قاني)">
                    </div>
                    <div class="col-md-4">
                        <input type="color" class="form-control" name="color_codes[]" value="${randomColor}" title="اختر اللون">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-color">حذف</button>
                    </div>
                </div>
            </div>
        `;
        $('#colors-container').append(colorItem);
    });

    $(document).on('click', '.remove-color', function(){
        if($('.color-item').length > 1) {
            $(this).closest('.color-item').remove();
        } else {
            alert('يجب أن يكون هناك لون واحد على الأقل');
        }
    });

    // جعل دالة displayImg متاحة عالمياً
    window.displayImg = displayImg;
});

function displayImg(input,_this) {
    console.log(input.files)
    var fnames = []
    var totalSize = 0
    var maxSize = 5 * 1024 * 1024 // 5MB

    Object.keys(input.files).map(k=>{
        var file = input.files[k]
        fnames.push(file.name)
        totalSize += file.size

        // فحص حجم الملف
        if(file.size > maxSize) {
            alert('الملف ' + file.name + ' كبير جداً. الحد الأقصى 5MB')
            input.value = ''
            return false
        }
    })

    if(fnames.length > 0) {
        _this.siblings('.custom-file-label').html(fnames.length + ' صورة محددة: ' + fnames.slice(0,2).join(', ') + (fnames.length > 2 ? '...' : ''))
    } else {
        _this.siblings('.custom-file-label').html('اختر الصور')
    }
}

function delete_img($path){
    start_loader()
    
    $.ajax({
        url: _base_url_+'classes/Master.php?f=delete_img',
        data:{path:$path},
        method:'POST',
        dataType:"json",
        error:err=>{
            console.log(err)
            alert_toast("An error occured while deleting an Image","error");
            end_loader()
        },
        success:function(resp){
            $('.modal').modal('hide')
            if(typeof resp =='object' && resp.status == 'success'){
                $('[data-path="'+$path+'"]').closest('.img-item').hide('slow',function(){
                    $('[data-path="'+$path+'"]').closest('.img-item').remove()
                })
                alert_toast("Image Successfully Deleted","success");
            }else{
                console.log(resp)
                alert_toast("An error occured while deleting an Image","error");
            }
            end_loader()
        }
    })
}
</script>