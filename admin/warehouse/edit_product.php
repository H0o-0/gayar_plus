<?php
if(!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">معرف المنتج مطلوب</div>';
    exit;
}

$product_id = intval($_GET['id']);

// الحصول على بيانات المنتج
$product_query = $conn->query("SELECT * FROM temp_warehouse WHERE id = $product_id");
if($product_query->num_rows == 0) {
    echo '<div class="alert alert-danger">المنتج غير موجود</div>';
    exit;
}

$product = $product_query->fetch_assoc();

// الحصول على التصنيفات
$categories = $conn->query("SELECT * FROM categories WHERE status = 1");
$sub_categories = $conn->query("SELECT * FROM sub_categories WHERE status = 1");
?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">تحرير المنتج قبل النشر</h3>
        <div class="card-tools">
            <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=warehouse">
                <i class="fa fa-arrow-left"></i> العودة للمخزن
            </a>
        </div>
    </div>
    <div class="card-body">
        
        <form id="edit-product-form" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $product['id'] ?>">
            
            <!-- معلومات المنتج الأساسية -->
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="product_name">اسم المنتج:</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" 
                               value="<?php echo htmlspecialchars($product['product_name']) ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="price">السعر (د.ع):</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="price" name="price" 
                                   value="<?php echo $product['original_price'] ?>" min="0" step="0.01" required>
                            <div class="input-group-append">
                                <span class="input-group-text">د.ع</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- التصنيف -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category_id">الفئة الرئيسية:</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">اختر الفئة</option>
                            <?php while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id'] ?>" 
                                    <?php echo ($product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($cat['category']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="sub_category_id">الفئة الفرعية:</label>
                        <select class="form-control" id="sub_category_id" name="sub_category_id" required>
                            <option value="">اختر الفئة الفرعية</option>
                            <?php 
                            $sub_categories->data_seek(0); // إعادة تعيين المؤشر
                            while($sub_cat = $sub_categories->fetch_assoc()): 
                            ?>
                            <option value="<?php echo $sub_cat['id'] ?>" 
                                    data-parent="<?php echo $sub_cat['parent_id'] ?>"
                                    <?php echo ($product['sub_category_id'] == $sub_cat['id']) ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($sub_cat['sub_category']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- العلامة التجارية والوصف -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="brand">العلامة التجارية:</label>
                        <input type="text" class="form-control" id="brand" name="brand" 
                               value="<?php echo htmlspecialchars($product['suggested_brand']) ?>">
                        <small class="text-muted">مثل: iPhone, Samsung, Huawei</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="quantity">الكمية:</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" 
                               value="100" min="1" required>
                        <small class="text-muted">الكمية المتاحة في المخزن</small>
                    </div>
                </div>
            </div>

            <!-- الوصف -->
            <div class="form-group">
                <label for="description">الوصف:</label>
                <textarea class="form-control" id="description" name="description" rows="3" 
                          placeholder="وصف تفصيلي للمنتج...">منتج مستورد من المخزن المؤقت - <?php echo htmlspecialchars($product['product_name']) ?></textarea>
            </div>

            <!-- صورة المنتج -->
            <div class="form-group">
                <label for="product_img">صورة المنتج:</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="product_img" name="product_img" accept="image/*" onchange="displayImgName(this)">
                    <label class="custom-file-label" for="product_img">اختر صورة</label>
                </div>
                <small class="text-muted">الصيغ المدعومة: JPG, PNG, GIF. الحد الأقصى: 5MB</small>
            </div>

            <!-- نظام الألوان المحسن -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">إدارة الألوان</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="has_colors" name="has_colors" value="1">
                            <label class="custom-control-label" for="has_colors">هذا المنتج متوفر بألوان مختلفة</label>
                        </div>
                    </div>
                    
                    <div id="colors_container" style="display: none;">
                        <div class="form-group">
                            <label>إضافة لون جديد:</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="color_name" placeholder="اسم اللون (مثل: أحمر، أزرق)">
                                </div>
                                <div class="col-md-4">
                                    <input type="color" class="form-control" id="color_code" value="#000000" title="اختر لون">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary btn-block" id="add_color">
                                        <i class="fas fa-plus"></i> إضافة
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>الألوان المضافة:</label>
                            <div id="colors_list" class="mt-2">
                                <!-- هنا ستظهر الألوان المضافة -->
                            </div>
                            <small class="text-muted">يمكنك إضافة عدة ألوان وسيتم عرضها في بطاقة المنتج</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات إضافية -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">معلومات إضافية</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="size">الحجم:</label>
                                <select class="form-control" id="size" name="size">
                                    <option value="عادي">عادي</option>
                                    <option value="صغير">صغير</option>
                                    <option value="متوسط">متوسط</option>
                                    <option value="كبير">كبير</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unit">الوحدة:</label>
                                <select class="form-control" id="unit" name="unit">
                                    <option value="قطعة">قطعة</option>
                                    <option value="مجموعة">مجموعة</option>
                                    <option value="صندوق">صندوق</option>
                                    <option value="حزمة">حزمة</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">حالة المنتج:</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1">نشط</option>
                                    <option value="0">غير نشط</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-globe"></i> نشر المنتج
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
        $('#sub_category_id option').hide();
        $('#sub_category_id').val('');
        
        if(selectedCategory) {
            $('#sub_category_id option[data-parent="' + selectedCategory + '"]').show();
            $('#sub_category_id option[value=""]').show();
        } else {
            $('#sub_category_id option').show();
        }
    });

    // تطبيق الفلترة عند تحميل الصفحة
    $('#category_id').trigger('change');

    // إظهار/إخفاء قسم الألوان
    $('#has_colors').change(function(){
        if($(this).is(':checked')) {
            $('#colors_container').slideDown();
        } else {
            $('#colors_container').slideUp();
            $('#colors_list').empty(); // مسح الألوان عند إلغاء التفعيل
        }
    });
    
    // إضافة لون جديد
    $('#add_color').click(function(){
        var color_name = $('#color_name').val().trim();
        var color_code = $('#color_code').val();
        
        if(color_name) {
            // التحقق من عدم تكرار اللون
            var exists = false;
            $('#colors_list input[name="color_names[]"]').each(function(){
                if($(this).val() === color_name) {
                    exists = true;
                    return false;
                }
            });
            
            if(exists) {
                alert('هذا اللون موجود بالفعل');
                return;
            }
            
            var color_html = '<div class="color-item badge p-3 mr-2 mb-2" style="background-color: ' + color_code + '; color: ' + getContrastColor(color_code) + ';">' +
                             '<span>' + color_name + '</span>' +
                             '<input type="hidden" name="color_names[]" value="' + color_name + '">' +
                             '<input type="hidden" name="color_codes[]" value="' + color_code + '">' +
                             '<button type="button" class="close ml-2 text-white remove-color" aria-label="Close" style="color: inherit !important;">' +
                             '<span aria-hidden="true">&times;</span>' +
                             '</button>' +
                             '</div>';
            $('#colors_list').append(color_html);
            $('#color_name').val('').focus();
            $('#color_code').val('#000000');
        } else {
            alert('يرجى إدخال اسم اللون');
        }
    });
    
    // حذف لون
    $(document).on('click', '.remove-color', function(){
        $(this).parent().remove();
    });
    
    // إضافة اللون بالضغط على Enter
    $('#color_name').keypress(function(e){
        if(e.which == 13) {
            e.preventDefault();
            $('#add_color').click();
        }
    });
    
    // دالة لحساب لون النص المناسب للخلفية
    function getContrastColor(hexcolor) {
        // تحويل hex إلى RGB
        var r = parseInt(hexcolor.substr(1,2),16);
        var g = parseInt(hexcolor.substr(3,2),16);
        var b = parseInt(hexcolor.substr(5,2),16);
        
        // حساب السطوع
        var brightness = ((r * 299) + (g * 587) + (b * 114)) / 1000;
        
        return brightness > 128 ? '#000000' : '#FFFFFF';
    }
    
    // عرض اسم الصورة المختارة
    window.displayImgName = function(input) {
        if (input.files && input.files[0]) {
            var fileName = input.files[0].name;
            $(input).next('.custom-file-label').html(fileName);
        }
    };

    // معالجة نشر المنتج
    $('#edit-product-form').on('submit', function(e){
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('action', 'publish');
        
        $.ajax({
            url: 'warehouse/publish_product.php',
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
                    $('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-globe"></i> نشر المنتج');
                }
            },
            error: function() {
                alert_toast('حدث خطأ أثناء معالجة الطلب', 'error');
                $('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-globe"></i> نشر المنتج');
            }
        });
    });

    // حفظ كمسودة
    $('#save-draft').click(function(){
        var formData = new FormData($('#edit-product-form')[0]);
        formData.append('action', 'save_draft');
        
        $.ajax({
            url: 'warehouse/publish_product.php',
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