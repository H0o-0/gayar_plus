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
				<label for="category_id" class="control-label">Category</label>
                <select name="category_id" id="category_id" class="custom-select select2" required>
                <option value=""></option>
                <?php
                    $qry = $conn->query("SELECT * FROM `categories` order by category asc");
                    while($row= $qry->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($category_id) && $category_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['category'] ?></option>
                <?php endwhile; ?>
                </select>
			</div>
            <div class="form-group">
				<label for="sub_category_id" class="control-label">Sub Category</label>
                <select name="sub_category_id" id="sub_category_id" class="custom-select" required>
                <option value="" selected="" disabled="">Select Category First</option>
                <?php
                    $qry = $conn->query("SELECT * FROM `sub_categories` order by sub_category asc");
                    $sub_categories = array();
                    while($row= $qry->fetch_assoc()):
                    $sub_categories[$row['parent_id']][] = $row;
                    endwhile; 
                ?>
                </select>
			</div>
			<div class="form-group">
				<label for="product_name" class="control-label">Product Name</label>
                <textarea name="product_name" id="" cols="30" rows="2" class="form-control form no-resize"><?php echo isset($product_name) ? $product_name : ''; ?></textarea>
			</div>
            <div class="form-group">
				<label for="description" class="control-label">Description</label>
                <textarea name="description" id="" cols="30" rows="2" class="form-control form no-resize summernote"><?php echo isset($description) ? $description : ''; ?></textarea>
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
            <div class="card mt-3">
                <div class="card-header bg-success">
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
    var sub_categories = $.parseJSON('<?php echo json_encode($sub_categories) ?>');
	$(document).ready(function(){
        $('.rem_img').click(function(){
            _conf("Are sure to delete this image permanently?",'delete_img',["'"+$(this).attr('data-path')+"'"])
        })
       
        $('#category_id').change(function(){
            var cid = $(this).val()
            var opt = "<option></option>";
            Object.keys(sub_categories).map(k=>{
                if(k == cid){
                    Object.keys(sub_categories[k]).map(i=>{
                        if('<?php echo isset($sub_category_id) ? $sub_category_id : 0 ?>' == sub_categories[k][i].id){
                            opt += "<option value='"+sub_categories[k][i].id+"' selected>"+sub_categories[k][i].sub_category+"</option>";
                        }else{
                            opt += "<option value='"+sub_categories[k][i].id+"'>"+sub_categories[k][i].sub_category+"</option>";
                        }
                    })
                }
            })
            $('#sub_category_id').html(opt)
            $('#sub_category_id').select2({placeholder:"Please Select here",width:"relative"})
        })
        $('.select2').select2({placeholder:"Please Select here",width:"relative"})
        if(parseInt("<?php echo isset($category_id) ? $category_id : 0 ?>") > 0){
            console.log('test')
            start_loader()
            setTimeout(() => {
                $('#category_id').trigger("change");
                end_loader()
            }, 750);
        }
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
		        height: 200,
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
		    })

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
	})
</script>