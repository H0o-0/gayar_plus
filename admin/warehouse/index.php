<?php if(!defined('DB_SERVER')){ include("../../initialize.php"); } ?>
<div class="container-fluid">
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">إدارة المخزن المؤقت</h3>
		<div class="card-tools">
			<div class="btn-group">
				<a class="btn btn-sm btn-default btn-flat border-primary" href="./?page=warehouse/upload">
					<i class="fa fa-plus"></i> رفع ملف CSV
				</a>
				<a class="btn btn-sm btn-warning btn-flat" href="./?page=warehouse/split_existing_data">
					<i class="fa fa-cut"></i> تقسيم البيانات الموجودة
				</a>
			</div>
		</div>
	</div>
	<div class="card-body">
		<!-- إحصائيات سريعة -->
		<div class="row mb-4">
			<?php 
			// إصلاح تلقائي أدق لحقل الحالة:
			// اعتبر "مصنف" فقط إذا كان هناك category_id أو كانت suggested_brand تطابق فئة موجودة
			$conn->query("UPDATE temp_warehouse tw 
				LEFT JOIN categories c ON LOWER(tw.suggested_brand) = LOWER(c.category)
				SET tw.status = 'classified'
				WHERE tw.status = 'unclassified' 
				AND (
					tw.category_id IS NOT NULL
					OR c.id IS NOT NULL
				)");

			// تنظيف الحالات الخاطئة القديمة: أي عنصر ليس لديه category_id ولا تطابق علامة مع فئة يبقى غير مصنف (باستثناء المنشور)
			$conn->query("UPDATE temp_warehouse tw 
				LEFT JOIN categories c ON LOWER(tw.suggested_brand) = LOWER(c.category)
				SET tw.status = 'unclassified'
				WHERE tw.status = 'classified' 
				AND tw.category_id IS NULL 
				AND c.id IS NULL 
				AND tw.status <> 'published'");

			$total_products = $conn->query("SELECT COUNT(*) as count FROM temp_warehouse")->fetch_assoc()['count'];
			$unclassified = $conn->query("SELECT COUNT(*) as count FROM temp_warehouse WHERE status = 'unclassified'")->fetch_assoc()['count'];
			$classified = $conn->query("SELECT COUNT(*) as count FROM temp_warehouse WHERE status = 'classified'")->fetch_assoc()['count'];
			$published = $conn->query("SELECT COUNT(*) as count FROM temp_warehouse WHERE status = 'published'")->fetch_assoc()['count'];
			?>
			
			<div class="col-lg-3 col-6">
				<div class="small-box bg-info">
					<div class="inner">
						<h3><?php echo number_format($total_products) ?></h3>
						<p>إجمالي المنتجات</p>
					</div>
					<div class="icon">
						<i class="fas fa-boxes"></i>
					</div>
				</div>
			</div>
			
			<div class="col-lg-3 col-6">
				<div class="small-box bg-warning">
					<div class="inner">
						<h3><?php echo number_format($unclassified) ?></h3>
						<p>غير مصنف</p>
					</div>
					<div class="icon">
						<i class="fas fa-question-circle"></i>
					</div>
				</div>
			</div>
			
			<div class="col-lg-3 col-6">
				<div class="small-box bg-primary">
					<div class="inner">
						<h3><?php echo number_format($classified) ?></h3>
						<p>مصنف</p>
					</div>
					<div class="icon">
						<i class="fas fa-check-circle"></i>
					</div>
				</div>
			</div>
			
			<div class="col-lg-3 col-6">
				<div class="small-box bg-success">
					<div class="inner">
						<h3><?php echo number_format($published) ?></h3>
						<p>منشور</p>
					</div>
					<div class="icon">
						<i class="fas fa-globe"></i>
					</div>
				</div>
			</div>
		</div>

		<!-- إحصائيات العلامات التجارية مع اللوغو -->
		<div class="row mb-4">
			<div class="col-12">
				<h5><i class="fas fa-chart-bar"></i> إحصائيات العلامات التجارية</h5>
			</div>
			<?php 
			$brand_stats = $conn->query("
				SELECT suggested_brand, COUNT(*) as count 
				FROM temp_warehouse 
				WHERE suggested_brand IS NOT NULL 
				GROUP BY suggested_brand 
				ORDER BY count DESC
			");
			
			$brand_logos = [
				'Apple' => 'apple.png',
				'Samsung' => 'samsung.png', 
				'Huawei' => 'huawei.png',
				'Xiaomi' => 'xiaomi.png',
				'Oppo' => 'oppo.png',
				'Vivo' => 'vivo.png',
				'LG' => 'lg.png',
				'Sony' => 'sony.png',
				'OnePlus' => 'oneplus.png'
			];
			
			while($brand = $brand_stats->fetch_assoc()):
				$logo_file = $brand_logos[$brand['suggested_brand']] ?? 'default.png';
				$logo_path = 'warehouse/images/' . $logo_file;
			?>
			<div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
				<div class="card text-center h-100">
					<div class="card-body p-3">
						<?php if(file_exists($logo_path)): ?>
							<img src="<?php echo $logo_path ?>" alt="<?php echo $brand['suggested_brand'] ?>" 
								 style="width: 50px; height: 50px; object-fit: contain;" class="mb-2">
						<?php else: ?>
							<i class="fas fa-mobile-alt fa-3x mb-2 text-primary"></i>
						<?php endif; ?>
						<h6 class="card-title mb-2"><?php echo $brand['suggested_brand'] ?></h6>
						<span class="badge badge-primary badge-pill"><?php echo number_format($brand['count']) ?> منتج</span>
					</div>
				</div>
			</div>
			<?php endwhile; ?>
		</div>

		<!-- فلاتر البحث -->
		<div class="row mb-3">
			<div class="col-md-3">
				<select class="form-control" id="brand-filter">
					<option value="">جميع العلامات التجارية</option>
					<?php 
					$brands = $conn->query("SELECT DISTINCT suggested_brand FROM temp_warehouse WHERE suggested_brand IS NOT NULL ORDER BY suggested_brand");
					while($brand = $brands->fetch_assoc()):
					?>
					<option value="<?php echo $brand['suggested_brand'] ?>"><?php echo $brand['suggested_brand'] ?></option>
					<?php endwhile; ?>
				</select>
			</div>
			<div class="col-md-3">
				<select class="form-control" id="type-filter">
					<option value="">جميع أنواع المنتجات</option>
					<?php 
					$types = $conn->query("SELECT DISTINCT suggested_type FROM temp_warehouse WHERE suggested_type IS NOT NULL ORDER BY suggested_type");
					while($type = $types->fetch_assoc()):
					?>
					<option value="<?php echo $type['suggested_type'] ?>"><?php echo $type['suggested_type'] ?></option>
					<?php endwhile; ?>
				</select>
			</div>
			<div class="col-md-3">
				<select class="form-control" id="status-filter">
					<option value="">جميع الحالات</option>
					<option value="unclassified">غير مصنف</option>
					<option value="classified">مصنف</option>
					<option value="published">منشور</option>
				</select>
			</div>
			<div class="col-md-3">
				<input type="text" class="form-control" id="search-input" placeholder="البحث في أسماء المنتجات...">
			</div>
		</div>

		<!-- جدول المنتجات -->
		<div class="table-responsive">
			<table class="table table-hover table-striped" id="warehouse-table">
				<thead>
					<tr>
						<th width="5%">
							<input type="checkbox" id="select-all">
						</th>
						<th width="30%">اسم المنتج</th>
						<th width="10%">السعر</th>
						<th width="15%">العلامة التجارية</th>
						<th width="15%">الفئة</th>
						<th width="10%">الحالة</th>
						<th width="15%">الإجراءات</th>
					</tr>
				</thead>
				<tbody>
					<?php
					// Fix SQL query - check if tables exist and handle errors
					$sql = "SELECT tw.*, 
					        COALESCE(b.name, tw.suggested_brand) as brand_name,
					        COALESCE(s.name, '') as series_name
					        FROM temp_warehouse tw
					        LEFT JOIN brands b ON tw.category_id = b.id
					        LEFT JOIN series s ON tw.sub_category_id = s.id
					        ORDER BY tw.created_at DESC";
					
					$products = $conn->query($sql);
					
					// Check if query failed
					if (!$products) {
						echo "<tr><td colspan='6' class='text-center text-danger'>خطأ في قاعدة البيانات: " . $conn->error . "</td></tr>";
					} else {
						while($row = $products->fetch_assoc()):
					?>
					<tr data-id="<?php echo $row['id'] ?>" 
					    data-status="<?php echo htmlspecialchars($row['status']) ?>" 
					    data-brand="<?php echo strtolower($row['suggested_brand'] ?? '') ?>" 
					    data-type="<?php echo strtolower($row['suggested_type'] ?? '') ?>">
						<td>
							<input type="checkbox" class="product-checkbox" value="<?php echo $row['id'] ?>">
						</td>
						<td>
							<strong><?php echo htmlspecialchars($row['product_name']) ?></strong>
							<br><small class="text-muted">تاريخ الإضافة: <?php echo date('Y/m/d H:i', strtotime($row['created_at'])) ?></small>
						</td>
						<td>
							<?php if($row['original_price'] > 0): ?>
								<span class="badge badge-info"><?php echo number_format($row['original_price']) ?> د.ع</span>
							<?php else: ?>
								<span class="text-muted">غير محدد</span>
							<?php endif; ?>
						</td>
						<td>
							<?php if($row['suggested_brand']): ?>
								<span class="badge badge-primary"><?php echo htmlspecialchars($row['suggested_brand']) ?></span>
							<?php else: ?>
								<span class="text-muted">غير محدد</span>
							<?php endif; ?>
						</td>
						<td>
							<?php if($row['series_name']): ?>
								<span class="badge badge-secondary"><?php echo htmlspecialchars($row['series_name']) ?></span>
							<?php else: ?>
								<span class="text-muted">غير محدد</span>
							<?php endif; ?>
						</td>
						<td>
							<?php 
							$status_class = '';
							$status_text = '';
							switch($row['status']) {
								case 'unclassified':
									$status_class = 'warning';
									$status_text = 'غير مصنف';
									break;
								case 'classified':
									$status_class = 'primary';
									$status_text = 'مصنف';
									break;
								case 'published':
									$status_class = 'success';
									$status_text = 'منشور';
									break;
							}
							?>
							<span class="badge badge-<?php echo $status_class ?>"><?php echo $status_text ?></span>
						</td>
						<td>
							<div class="btn-group btn-group-sm">
								<?php if($row['status'] != 'published'): ?>
								<a href="./index.php?page=warehouse/edit_product&id=<?php echo $row['id'] ?>"
								   class="btn btn-primary btn-sm" title="تحرير ونشر">
									<i class="fas fa-edit"></i>
								</a>
								<button class="btn btn-success btn-sm quick-publish" data-id="<?php echo $row['id'] ?>" title="نشر سريع">
									<i class="fas fa-bolt"></i>
								</button>
								<?php 
								// التحقق مما إذا كان المنتج يحتوي على أجهزة متعددة
								$has_multiple_devices = (strpos($row['product_name'], '/') !== false || 
												   strpos($row['product_name'], '\\') !== false || 
												   strpos($row['product_name'], '|') !== false);
								if($has_multiple_devices): 
								?>
								<button class="btn btn-info btn-sm split-product" data-id="<?php echo $row['id'] ?>" title="تقسيم المنتج">
									<i class="fas fa-cut"></i>
								</button>
								<?php endif; ?>
								<?php endif; ?>
								<button class="btn btn-danger btn-sm delete-product" data-id="<?php echo $row['id'] ?>" title="حذف">
									<i class="fas fa-trash"></i>
								</button>
							</div>
						</td>
					</tr>
					<?php 
						endwhile; 
					} // End of else block for successful query
					?>
				</tbody>
			</table>
		</div>

		<!-- أزرار العمليات المجمعة -->
		<div class="row mt-3">
			<div class="col-md-6">
				<div class="btn-group">
					<button class="btn btn-secondary btn-sm" id="select-all-visible">
						<i class="fas fa-check-square"></i> تحديد الكل
					</button>
					<button class="btn btn-secondary btn-sm" id="deselect-all">
						<i class="fas fa-square"></i> إلغاء التحديد
					</button>
				</div>
			</div>
			<div class="col-md-6">
				<div class="btn-group">
					<button class="btn btn-success" id="bulk-edit" disabled>
						<i class="fas fa-edit"></i> تحرير المحدد
					</button>
					<button class="btn btn-info" id="bulk-quick-publish" disabled>
						<i class="fas fa-bolt"></i> نشر سريع للمحدد
					</button>
					<button class="btn btn-info" id="bulk-split" disabled>
						<i class="fas fa-cut"></i> تقسيم المحدد
					</button>
					<button class="btn btn-danger" id="bulk-delete" disabled>
						<i class="fas fa-trash"></i> حذف المحدد
					</button>
					<button class="btn btn-warning" id="delete-all-unclassified">
						<i class="fas fa-trash-alt"></i> حذف كل غير المصنف
					</button>
					<button class="btn btn-primary" id="auto-classify">
						<i class="fas fa-magic"></i> تصنيف تلقائي
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	console.log('🚀 تم تحميل صفحة المخزن - الإصدار المحسن');
	
	// دالة AJAX مع مسار صحيح
	function callAjax(action, data, successCallback, errorCallback) {
		console.log('📤 إرسال طلب:', action, data);
		
		$.ajax({
			url: 'warehouse_ajax.php',
			type: 'POST',
			data: $.extend({action: action}, data),
			dataType: 'json',
			timeout: 15000,
			success: function(response) {
				console.log('✅ رد الخادم:', response);
				if(response && response.status === 'success') {
					if(successCallback) successCallback(response);
				} else {
					alert('خطأ: ' + (response ? response.message : 'رد غير صحيح'));
					if(errorCallback) errorCallback(response);
				}
			},
			error: function(xhr, status, error) {
				console.error('❌ خطأ AJAX:', {
					status: status,
					error: error,
					responseText: xhr.responseText,
					statusCode: xhr.status
				});
				
				var msg = 'خطأ في الاتصال:\n';
				msg += 'Status: ' + status + '\n';
				msg += 'Error: ' + error + '\n';
				
				if(xhr.responseText && xhr.responseText.includes('<!DOCTYPE')) {
					msg += '\nالخادم أرجع صفحة HTML بدلاً من JSON.\nهذا يعني وجود خطأ في المسار أو PHP.';
				} else if(xhr.responseText) {
					msg += '\nرد الخادم: ' + xhr.responseText.substring(0, 200);
				}
				
				alert(msg);
				if(errorCallback) errorCallback();
			}
		});
	}
	
	// تفعيل/إلغاء تفعيل أزرار العمليات المجمعة
	function toggleBulkButtons() {
		var checkedCount = $('.product-checkbox:checked').length;
		$('#bulk-edit, #bulk-quick-publish, #bulk-split, #bulk-delete').prop('disabled', checkedCount === 0);
	}
	
	toggleBulkButtons();

	// تحديد/إلغاء تحديد الكل
	$('#select-all').change(function(){
		$('.product-checkbox').prop('checked', $(this).prop('checked'));
		toggleBulkButtons();
	});

	$(document).on('change', '.product-checkbox', function(){
		toggleBulkButtons();
		var totalCheckboxes = $('.product-checkbox').length;
		var checkedCheckboxes = $('.product-checkbox:checked').length;
		$('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
	});

	// فلترة محسنة
	$('#brand-filter, #type-filter, #status-filter').change(function(){
		filterTable();
	});

	$('#search-input').on('keyup', function(){
		filterTable();
	});

	function filterTable() {
		var brandFilter = $('#brand-filter').val();
		var typeFilter = $('#type-filter').val();
		var statusFilter = $('#status-filter').val();
		var searchText = $('#search-input').val();

		// Show a loading indicator
		$('#warehouse-table tbody').html('<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> جاري تحميل البيانات...</td></tr>');

		$.ajax({
			url: 'warehouse_ajax.php',
			type: 'POST',
			data: {
				action: 'filter_products',
				brand: brandFilter,
				type: typeFilter,
				status: statusFilter,
				search: searchText
			},
			dataType: 'json',
			success: function(response) {
				if (response && response.status === 'success') {
					$('#warehouse-table tbody').html(response.html);
				} else {
					$('#warehouse-table tbody').html('<tr><td colspan="7" class="text-center">حدث خطأ أثناء تحميل البيانات.</td></tr>');
					alert('خطأ: ' + (response ? response.message : 'رد غير صحيح من الخادم'));
				}
			},
			error: function() {
				$('#warehouse-table tbody').html('<tr><td colspan="7" class="text-center">فشل الاتصال بالخادم.</td></tr>');
			}
		});
	}

	// حذف منتج واحد
	$(document).on('click', '.delete-product', function(){
		console.log('🗑️ حذف منتج');
		var id = $(this).data('id');
		var row = $(this).closest('tr');

		if(confirm('هل أنت متأكد من حذف هذا المنتج؟')) {
			callAjax('delete_product', {id: id}, function(response) {
				row.fadeOut(function() { $(this).remove(); });
				alert_toast(response.message, 'success');
			});
		}
	});

	// نشر سريع
	$(document).on('click', '.quick-publish', function(){
		console.log('⚡ نشر سريع');
		var id = $(this).data('id');

		callAjax('quick_publish', {id: id}, function(response) {
			if(response.redirect) {
				window.location.href = response.redirect;
			}
		});
	});

	// حذف مجمع
	$('#bulk-delete').click(function(){
		console.log('🗑️ حذف مجمع');
		var selectedIds = [];
		$('.product-checkbox:checked').each(function(){
			selectedIds.push($(this).val());
		});

		if(selectedIds.length == 0) {
			alert('يرجى تحديد منتجات للحذف');
			return;
		}

		if(confirm('هل أنت متأكد من حذف ' + selectedIds.length + ' منتج؟')) {
			var $btn = $(this);
			$btn.prop('disabled', true).text('جاري الحذف...');

			callAjax('bulk_delete', {ids: selectedIds}, function(response) {
				alert_toast(response.message, 'success');
				location.reload();
			}, function() {
				$btn.prop('disabled', false).text('حذف المحدد');
			});
		}
	});

	// حذف كل غير المصنف
	$('#delete-all-unclassified').click(function(){
		console.log('🗑️ حذف كل غير المصنف');
		if(confirm('هل تريد حذف جميع المنتجات غير المصنفة؟')) {
			var $btn = $(this);
			$btn.prop('disabled', true).text('جاري الحذف...');
			
			callAjax('delete_unclassified', {}, function(response) {
				alert_toast(response.message, 'success');
				location.reload();
			}, function() {
				$btn.prop('disabled', false).text('حذف كل غير المصنف');
			});
		}
	});

	// تصنيف تلقائي
	$('#auto-classify').click(function(){
		console.log('🤖 تصنيف تلقائي');
		if(confirm('هل تريد تشغيل التصنيف التلقائي؟')) {
			var $btn = $(this);
			$btn.prop('disabled', true).text('جاري التصنيف...');
			
			callAjax('auto_classify', {}, function(response) {
				if(response.fixed_count > 0) {
					alert_toast(response.message, 'success');
					location.reload();
				} else {
					alert_toast('لا توجد منتجات تحتاج للتصنيف', 'info');
					$btn.prop('disabled', false).text('تصنيف تلقائي');
				}
			}, function() {
				$btn.prop('disabled', false).text('تصنيف تلقائي');
			});
		}
	});

	// تحديد الكل المرئي
	$('#select-all-visible').click(function(){
		$('#warehouse-table tbody tr:visible .product-checkbox').prop('checked', true);
		toggleBulkButtons();
	});

	// إلغاء تحديد الكل
	$('#deselect-all').click(function(){
		$('.product-checkbox').prop('checked', false);
		$('#select-all').prop('checked', false);
		toggleBulkButtons();
	});

	// تحرير مجمع
	$('#bulk-edit').click(function(){
		var selectedIds = [];
		$('.product-checkbox:checked').each(function(){
			selectedIds.push($(this).val());
		});

		if(selectedIds.length == 0) {
			alert('يرجى تحديد منتجات للتحرير');
			return;
		}

		var idsParam = selectedIds.join(',');
		window.location.href = 'index.php?page=warehouse/bulk_edit&ids=' + idsParam;
	});

	$('#bulk-quick-publish').click(function(){
		var selectedIds = [];
		$('.product-checkbox:checked').each(function(){
			selectedIds.push($(this).val());
		});

		if(selectedIds.length == 0) {
			alert('يرجى تحديد منتجات للنشر');
			return;
		}

		if(selectedIds.length == 1) {
			window.location.href = 'index.php?page=warehouse/edit_product&id=' + selectedIds[0];
			return;
		}

		var idsParam = selectedIds.join(',');
		window.location.href = 'index.php?page=warehouse/bulk_edit&ids=' + idsParam;
	});
	
	// تقسيم منتج واحد
	$(document).on('click', '.split-product', function(){
		console.log('✂️ تقسيم منتج');
		var id = $(this).data('id');
		var row = $(this).closest('tr');
		var productName = row.find('td:nth-child(2) strong').text();
		
		if(confirm('هل تريد تقسيم المنتج: ' + productName + '؟\n\nسيتم إنشاء منتجات منفصلة لكل جهاز.')) {
			var $btn = $(this);
			$btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
			
			callAjax('split_product', {product_id: id}, function(response) {
				if(response.count > 0) {
					var msg = 'تم تقسيم المنتج بنجاح إلى ' + response.count + ' منتج:';
					response.new_products.forEach(function(product) {
						msg += '\n- ' + product.name;
					});
					alert(msg);
					location.reload();
				} else {
					alert('لم يتم تقسيم المنتج. ' + response.message);
					$btn.prop('disabled', false).html('<i class="fas fa-cut"></i>');
				}
			}, function() {
				$btn.prop('disabled', false).html('<i class="fas fa-cut"></i>');
			});
		}
	});
	
	// تقسيم مجموعة منتجات
	$('#bulk-split').click(function(){
		console.log('✂️ تقسيم مجموعة منتجات');
		var selectedIds = [];
		$('.product-checkbox:checked').each(function(){
			selectedIds.push($(this).val());
		});

		if(selectedIds.length == 0) {
			alert('يرجى تحديد منتجات للتقسيم');
			return;
		}

		if(confirm('هل تريد تقسيم ' + selectedIds.length + ' منتج؟\n\nسيتم إنشاء منتجات منفصلة لكل جهاز في كل منتج محدد.')) {
			var $btn = $(this);
			$btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري التقسيم...');
			
			callAjax('bulk_split', {product_ids: selectedIds}, function(response) {
				var msg = 'تمت معالجة ' + response.processed + ' منتج.';
				msg += '\nتم تقسيم ' + response.split_count + ' منتج.';
				msg += '\nتم إنشاء ' + response.new_products_count + ' منتج جديد.';
				
				if(response.errors && response.errors.length > 0) {
					msg += '\n\nحدثت بعض الأخطاء:';
					response.errors.forEach(function(error) {
						msg += '\n- ' + error;
					});
				}
				
				alert(msg);
				location.reload();
			}, function() {
				$btn.prop('disabled', false).html('<i class="fas fa-cut"></i> تقسيم المحدد');
			});
		}
	});
});
</script>
