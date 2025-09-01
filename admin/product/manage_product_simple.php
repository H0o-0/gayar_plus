<?php
require_once('../../initialize.php');

// التحقق من وجود الجداول الجديدة
$tables_check = $conn->query("SHOW TABLES LIKE 'brands'");
if ($tables_check->num_rows == 0) {
    echo "<div style='color: red; padding: 20px; text-align: center;'>";
    echo "<h3>يجب إنشاء الهيكل الجديد أولاً!</h3>";
    echo "<p><a href='../../quick_setup.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>إنشاء الهيكل الجديد</a></p>";
    echo "</div>";
    exit;
}

$id = isset($_GET['id']) ? $_GET['id'] : '';
$selected_brand_id = '';
$selected_series_id = '';
$selected_model_id = '';

// جلب بيانات المنتج للتعديل
if ($id) {
    $product_query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($product_query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product_data = $stmt->get_result()->fetch_assoc();
    
    if ($product_data) {
        $selected_model_id = $product_data['model_id'];
        
        // جلب معلومات الشركة والفئة من الموديل
        if ($selected_model_id) {
            $model_query = "SELECT m.*, s.brand_id, s.id as series_id FROM models m 
                           LEFT JOIN series s ON m.series_id = s.id 
                           WHERE m.id = ?";
            $stmt = $conn->prepare($model_query);
            $stmt->bind_param("i", $selected_model_id);
            $stmt->execute();
            $model_data = $stmt->get_result()->fetch_assoc();
            
            if ($model_data) {
                $selected_brand_id = $model_data['brand_id'];
                $selected_series_id = $model_data['series_id'];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المنتجات - النظام الجديد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-plus-circle"></i>
                        <?php echo $id ? 'تعديل المنتج' : 'إضافة منتج جديد'; ?>
                        <span class="badge bg-warning text-dark">النظام الثلاثي الجديد</span>
                    </h3>
                </div>
                
                <div class="card-body">
                    <form action="" id="product-form-new" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $id ?>">
                        
                        <!-- معلومات أساسية -->
                        <div class="card mb-3">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">المعلومات الأساسية</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="name" class="form-label">اسم المنتج <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" id="name" 
                                                   value="<?php echo isset($product_data) ? htmlspecialchars($product_data['name']) : '' ?>" 
                                                   placeholder="أدخل اسم المنتج" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="price" class="form-label">السعر (د.ع)</label>
                                            <input type="number" step="0.01" class="form-control" name="price" id="price" 
                                                   value="<?php echo isset($product_data) ? $product_data['price'] : '' ?>"
                                                   placeholder="السعر بالدينار العراقي">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="description" class="form-label">وصف المنتج</label>
                                    <textarea class="form-control" name="description" id="description" rows="3" 
                                              placeholder="وصف تفصيلي للمنتج"><?php echo isset($product_data) ? htmlspecialchars($product_data['description']) : '' ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- التصنيف الثلاثي الجديد -->
                        <div class="card mb-3">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-sitemap"></i> التصنيف (الشركة → الفئة → الموديل)</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- الشركة -->
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="brand_id" class="form-label">الشركة <span class="text-danger">*</span></label>
                                            <select class="form-select select2" name="brand_id" id="brand_id" required>
                                                <option value="">اختر الشركة</option>
                                                <?php
                                                $brands = $conn->query("SELECT * FROM brands WHERE status = 1 ORDER BY name");
                                                while ($brand = $brands->fetch_assoc()):
                                                    $selected = ($brand['id'] == $selected_brand_id) ? 'selected' : '';
                                                ?>
                                                <option value="<?php echo $brand['id'] ?>" <?php echo $selected ?>>
                                                    <?php echo htmlspecialchars($brand['name_ar'] ?: $brand['name']) ?>
                                                </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- الفئة -->
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="series_id" class="form-label">الفئة <span class="text-danger">*</span></label>
                                            <select class="form-select select2" name="series_id" id="series_id" required>
                                                <option value="">اختر الشركة أولاً</option>
                                                <?php if ($selected_brand_id): ?>
                                                    <?php
                                                    $series = $conn->query("SELECT * FROM series WHERE brand_id = $selected_brand_id AND status = 1 ORDER BY name");
                                                    while ($serie = $series->fetch_assoc()):
                                                        $selected = ($serie['id'] == $selected_series_id) ? 'selected' : '';
                                                    ?>
                                                    <option value="<?php echo $serie['id'] ?>" <?php echo $selected ?>>
                                                        <?php echo htmlspecialchars($serie['name_ar'] ?: $serie['name']) ?>
                                                    </option>
                                                    <?php endwhile; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- الموديل -->
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="model_id" class="form-label">الموديل <span class="text-danger">*</span></label>
                                            <select class="form-select select2" name="model_id" id="model_id" required>
                                                <option value="">اختر الفئة أولاً</option>
                                                <?php if ($selected_series_id): ?>
                                                    <?php
                                                    $models = $conn->query("SELECT * FROM models WHERE series_id = $selected_series_id AND status = 1 ORDER BY name");
                                                    while ($model = $models->fetch_assoc()):
                                                        $selected = ($model['id'] == $selected_model_id) ? 'selected' : '';
                                                    ?>
                                                    <option value="<?php echo $model['id'] ?>" <?php echo $selected ?>>
                                                        <?php echo htmlspecialchars($model['name_ar'] ?: $model['name']) ?>
                                                    </option>
                                                    <?php endwhile; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <small><i class="fas fa-info-circle"></i> 
                                    يجب اختيار الشركة أولاً، ثم الفئة، ثم الموديل بالترتيب
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- معلومات إضافية -->
                        <div class="card mb-3">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">معلومات إضافية</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="quantity" class="form-label">الكمية المتوفرة</label>
                                            <input type="number" class="form-control" name="quantity" id="quantity" 
                                                   value="<?php echo isset($product_data) ? $product_data['quantity'] : '' ?>"
                                                   placeholder="الكمية في المخزن">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="unit" class="form-label">الوحدة</label>
                                            <input type="text" class="form-control" name="unit" id="unit" 
                                                   value="<?php echo isset($product_data) ? $product_data['unit'] : 'قطعة' ?>"
                                                   placeholder="مثل: قطعة، علبة، كيلو">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="img" class="form-label">صور المنتج</label>
                                    <input type="file" class="form-control" name="img[]" id="img" multiple accept="image/*">
                                    <small class="text-muted">يمكنك اختيار عدة صور للمنتج</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary me-md-2">
                                <i class="fas fa-save"></i> حفظ المنتج
                            </button>
                            <a href="../../admin/?page=product" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> رجوع
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
                url: 'ajax_get_series.php',
                type: 'POST',
                data: {brand_id: brand_id},
                dataType: 'json',
                success: function(response) {
                    if(response.status == 'success') {
                        var options = '<option value="">اختر الفئة</option>';
                        $.each(response.series, function(id, name) {
                            options += '<option value="' + id + '">' + name + '</option>';
                        });
                        series_select.html(options);
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
                url: 'ajax_get_models.php',
                type: 'POST',
                data: {series_id: series_id},
                dataType: 'json',
                success: function(response) {
                    if(response.status == 'success') {
                        var options = '<option value="">اختر الموديل</option>';
                        $.each(response.models, function(id, name) {
                            options += '<option value="' + id + '">' + name + '</option>';
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

    // معالجة إرسال النموذج
    $('#product-form-new').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('action', 'save_product_new');
        
        $.ajax({
            url: '../../classes/Master.php?f=save_product_new',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    var result = JSON.parse(response);
                    if(result.status == 'success') {
                        alert('تم حفظ المنتج بنجاح!');
                        if(!$('#id').val()) {
                            // إذا كان منتج جديد، إعادة تحميل الصفحة
                            window.location.reload();
                        }
                    } else {
                        alert('خطأ: ' + result.msg);
                    }
                } catch(e) {
                    alert('خطأ في معالجة الاستجابة');
                    console.log(response);
                }
            },
            error: function() {
                alert('خطأ في الاتصال بالخادم');
            }
        });
    });
});
</script>

</body>
</html>
