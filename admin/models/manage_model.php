<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
<<<<<<< HEAD
    $qry = $conn->query("SELECT m.*, s.brand_id FROM `models` m 
                         LEFT JOIN `series` s ON s.id = m.series_id 
                         WHERE m.id = '{$_GET['id']}' ");
    if($qry && $qry->num_rows > 0){
=======
    $qry = $conn->query("SELECT * from `models` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
>>>>>>> cebc63a3bc4f7e2f5ae4119daff21338fea35eb8
        foreach($qry->fetch_assoc() as $k => $v){
            $$k = $v;
        }
    }
}
?>

<div class="card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title"><?php echo isset($id) ? "تحديث " : "إنشاء جديد " ?> موديل</h3>
    </div>
    <div class="card-body">
        <form action="" id="model-form">
            <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
            
            <div class="form-group">
                <label for="brand_id" class="control-label">البراند <span class="text-danger">*</span></label>
<<<<<<< HEAD
                <select id="brand_id" class="form-control select2" required>
                    <option value="">اختر البراند</option>
                    <?php 
                    $brands = $conn->query("SELECT * FROM `brands` WHERE status = 1 ORDER BY name ASC");
                    if($brands && $brands->num_rows > 0):
                        while($row = $brands->fetch_assoc()):
=======
                <select name="brand_id" id="brand_id" class="form-control select2" required>
                    <option value="">اختر البراند</option>
                    <?php 
                    $brands = $conn->query("SELECT * FROM `brands` WHERE status = 1 ORDER BY name ASC");
                    while($row = $brands->fetch_assoc()):
>>>>>>> cebc63a3bc4f7e2f5ae4119daff21338fea35eb8
                    ?>
                        <option value="<?php echo $row['id'] ?>" <?php echo isset($brand_id) && $brand_id == $row['id'] ? 'selected' : '' ?>>
                            <?php echo $row['name'] ?>
                        </option>
<<<<<<< HEAD
                    <?php 
                        endwhile;
                    endif;
                    ?>
=======
                    <?php endwhile; ?>
>>>>>>> cebc63a3bc4f7e2f5ae4119daff21338fea35eb8
                </select>
            </div>
            
            <div class="form-group">
                <label for="series_id" class="control-label">السيريس <span class="text-danger">*</span></label>
                <select name="series_id" id="series_id" class="form-control select2" required>
                    <option value="">اختر السيريس</option>
                    <?php 
                    if(isset($brand_id) && $brand_id > 0):
                        $series = $conn->query("SELECT * FROM `series` WHERE brand_id = '{$brand_id}' AND status = 1 ORDER BY name ASC");
<<<<<<< HEAD
                        if($series && $series->num_rows > 0):
                            while($row = $series->fetch_assoc()):
=======
                        while($row = $series->fetch_assoc()):
>>>>>>> cebc63a3bc4f7e2f5ae4119daff21338fea35eb8
                    ?>
                        <option value="<?php echo $row['id'] ?>" <?php echo isset($series_id) && $series_id == $row['id'] ? 'selected' : '' ?>>
                            <?php echo $row['name'] ?>
                        </option>
                    <?php 
<<<<<<< HEAD
                            endwhile;
                        endif;
=======
                        endwhile;
>>>>>>> cebc63a3bc4f7e2f5ae4119daff21338fea35eb8
                    endif;
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="name" class="control-label">اسم الموديل <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($name) ? $name : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description" class="control-label">الوصف</label>
                <textarea name="description" id="description" cols="30" rows="3" class="form-control"><?php echo isset($description) ? $description : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="status" class="control-label">الحالة</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>فعال</option>
                    <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>غير فعال</option>
                </select>
            </div>
        </form>
    </div>
    <div class="card-footer">
        <button class="btn btn-flat btn-primary" form="model-form">حفظ</button>
        <a class="btn btn-flat btn-default" href="?page=models">إلغاء</a>
    </div>
</div>

<script>
$(document).ready(function(){
    $('.select2').select2({
        placeholder: "اختر من القائمة",
        width: "100%"
    });
    
    // عند تغيير البراند، حدث السيريس
    $('#brand_id').change(function(){
        var brand_id = $(this).val();
        var series_select = $('#series_id');
        
        // إعادة تعيين السيريس
        series_select.html('<option value="">جاري التحميل...</option>');
        
        if(brand_id != ''){
            // طلب AJAX لجلب السيريس
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=get_series_by_brand",
                method: 'POST',
                data: {brand_id: brand_id},
                dataType: 'text',
                success: function(resp){
                    series_select.html('<option value="">اختر السيريس</option>' + resp);
                    series_select.trigger('change.select2');
                },
                error: function(){
                    series_select.html('<option value="">خطأ في التحميل</option>');
                }
            });
        } else {
            series_select.html('<option value="">اختر البراند أولاً</option>');
        }
    });
    
    // حفظ النموذج
    $('#model-form').submit(function(e){
        e.preventDefault();
        var _this = $(this);
        $('.err-msg').remove();
        
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=save_model",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            dataType: 'json',
            error: function(xhr, status, error){
                console.log('AJAX Error:', xhr.responseText);
                alert_toast("حدث خطأ في الاتصال: " + error, 'error');
                end_loader();
            },
            success: function(resp){
                if(typeof resp == 'object' && resp.status == 'success'){
                    alert_toast("تم الحفظ بنجاح", 'success');
                    setTimeout(function(){
                        location.href = "./?page=models";
                    }, 1500);
                } else if(resp.status == 'failed' && resp.msg){
                    var el = $('<div>').addClass("alert alert-danger err-msg").text(resp.msg);
                    _this.prepend(el);
                    el.show('slow');
                    $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                } else {
                    alert_toast("حدث خطأ غير متوقع", 'error');
                }
                end_loader();
            }
        });
    });
});
</script>