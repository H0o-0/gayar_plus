<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">إدارة المخزن المؤقت</h3>
		<div class="card-tools">
			<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=warehouse/upload">
				<i class="fa fa-plus"></i> رفع ملف CSV
			</a>
		</div>
	</div>
	<div class="card-body">
		<!-- إحصائيات سريعة -->
		<div class="row mb-4">
			<?php 
			$total_products = $conn->query("SELECT COUNT(*) as count FROM temp_warehouse")->fetch_assoc()['count'];
			$unclassified = $conn->query("SELECT COUNT(*) as count FROM temp_warehouse WHERE status = 'unclassified'")->fetch_assoc()['count'];
			$classified = $conn->query("SELECT COUNT(*) as count FROM temp_warehouse WHERE status = 'classified'")->fetch_assoc()['count'];
			$published = $conn->query("SELECT COUNT(*) as count FROM temp_warehouse WHERE status = 'published'")->fetch_assoc()['count'];
			?>
			
			<div class="col-lg-3 col-6">
				<div class="small-box bg-info">
					<div class="inner">
						<h3><?php echo $total_products ?></h3>
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
						<h3><?php echo $unclassified ?></h3>
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
						<h3><?php echo $classified ?></h3>
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
						<h3><?php echo $published ?></h3>
						<p>منشور</p>
					</div>
					<div class="icon">
						<i class="fas fa-globe"></i>
					</div>
				</div>
			</div>
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
					$products = $conn->query("
						SELECT tw.*, c.category, sc.sub_category
						FROM temp_warehouse tw
						LEFT JOIN categories c ON tw.category_id = c.id
						LEFT JOIN sub_categories sc ON tw.sub_category_id = sc.id
						ORDER BY tw.created_at DESC
					");
					while($row = $products->fetch_assoc()):
					?>
					<tr data-id="<?php echo $row['id'] ?>">
						<td>
							<input type="checkbox" class="product-checkbox" value="<?php echo $row['id'] ?>">
						</td>
						<td>
							<strong><?php echo $row['product_name'] ?></strong>
							<br><small class="text-muted">تاريخ الإضافة: <?php echo date('Y/m/d H:i', strtotime($row['created_at'])) ?></small>
						</td>
						<td>
							<span class="badge badge-info"><?php echo number_format($row['original_price']) ?> د.ع</span>
						</td>
						<td>
							<?php if($row['suggested_brand']): ?>
								<span class="badge badge-primary"><?php echo $row['suggested_brand'] ?></span>
							<?php else: ?>
								<span class="text-muted">غير محدد</span>
							<?php endif; ?>
						</td>
						<td>
							<?php if($row['category']): ?>
								<span class="badge badge-secondary"><?php echo $row['category'] ?></span>
								<?php if($row['sub_category']): ?>
									<br><small class="text-muted"><?php echo $row['sub_category'] ?></small>
								<?php endif; ?>
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
								<?php endif; ?>
								<button class="btn btn-danger btn-sm delete-product" data-id="<?php echo $row['id'] ?>" title="حذف">
									<i class="fas fa-trash"></i>
								</button>
							</div>
						</td>
					</tr>
					<?php endwhile; ?>
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
					<button class="btn btn-danger" id="bulk-delete" disabled>
						<i class="fas fa-trash"></i> حذف المحدد
					</button>
					<button class="btn btn-warning" id="delete-all-unclassified">
						<i class="fas fa-trash-alt"></i> حذف كل غير المصنف
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	// تفعيل/إلغاء تفعيل أزرار العمليات المجمعة
	function toggleBulkButtons() {
		var checkedCount = $('.product-checkbox:checked').length;
		$('#bulk-edit, #bulk-quick-publish, #bulk-delete').prop('disabled', checkedCount === 0);
	}
	
	// تفعيل الأزرار عند تحميل الصفحة
	toggleBulkButtons();

	// تحديد/إلغاء تحديد الكل
	$('#select-all').change(function(){
		$('.product-checkbox').prop('checked', $(this).prop('checked'));
		toggleBulkButtons();
	});

	// تحديد منتج واحد
	$(document).on('change', '.product-checkbox', function(){
		toggleBulkButtons();
		
		// تحديث حالة "تحديد الكل"
		var totalCheckboxes = $('.product-checkbox').length;
		var checkedCheckboxes = $('.product-checkbox:checked').length;
		$('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
	});

	// فلترة حسب العلامة التجارية
	$('#brand-filter, #type-filter, #status-filter').change(function(){
		filterTable();
	});

	// البحث في النص
	$('#search-input').on('keyup', function(){
		filterTable();
	});

	function filterTable() {
		var brandFilter = $('#brand-filter').val().toLowerCase();
		var typeFilter = $('#type-filter').val().toLowerCase();
		var statusFilter = $('#status-filter').val().toLowerCase();
		var searchText = $('#search-input').val().toLowerCase();

		$('#warehouse-table tbody tr').each(function(){
			var row = $(this);
			var productName = row.find('td:eq(1)').text().toLowerCase();
			var brand = row.find('td:eq(3) .badge').text().toLowerCase();
			var type = row.find('td:eq(4) .badge').text().toLowerCase();
			var status = row.find('td:eq(5) .badge').text().toLowerCase();

			var showRow = true;

			if(brandFilter && brand.indexOf(brandFilter) === -1) showRow = false;
			if(typeFilter && type.indexOf(typeFilter) === -1) showRow = false;
			if(statusFilter && status.indexOf(statusFilter) === -1) showRow = false;
			if(searchText && productName.indexOf(searchText) === -1) showRow = false;

			row.toggle(showRow);
		});
	}

	// معالجة أزرار الحذف والنشر
	$(document).on('click', '.delete-product', function(){
		var id = $(this).data('id');
		var row = $(this).closest('tr');

		if(confirm('هل أنت متأكد من حذف هذا المنتج؟')) {
			$.post('ajax_actions.php', {
				action: 'delete_product',
				id: id
			}, function(response) {
				if(response.status == 'success') {
					row.fadeOut(function() {
						$(this).remove();
					});
					alert(response.message);
				} else {
					alert('خطأ: ' + response.message);
				}
			}, 'json').fail(function(xhr, status, error) {
				console.error('AJAX Error:', status, error);
				console.error('Response Text:', xhr.responseText);
				alert('خطأ في الاتصال: ' + error);
			});
		}
	});

	$(document).on('click', '.quick-publish', function(){
		var id = $(this).data('id');
		var row = $(this).closest('tr');

		// استخدام النشر السريع المعدل الذي يأخذ المستخدم إلى صفحة التحرير
		$.post('ajax_actions.php', {
			action: 'quick_publish',
			id: id
		}, function(response) {
			if(response.status == 'success') {
				// التحويل إلى صفحة التحرير
				window.location.href = response.redirect;
			} else {
				alert('خطأ: ' + response.message);
			}
		}, 'json').fail(function(xhr, status, error) {
			console.error('AJAX Error:', status, error);
			console.error('Response Text:', xhr.responseText);
			alert('خطأ في الاتصال: ' + error);
		});
	});

	// العمليات المجمعة
	$('#bulk-delete').click(function(){
		var selectedIds = [];
		$('.product-checkbox:checked').each(function(){
			selectedIds.push($(this).val());
		});

		if(selectedIds.length == 0) {
			alert('يرجى تحديد منتجات للحذف');
			return;
		}

		if(confirm('هل أنت متأكد من حذف ' + selectedIds.length + ' منتج؟')) {
			// إظهار مؤشر التحميل
			$(this).prop('disabled', true).text('جاري الحذف...');

			$.post('ajax_actions.php', {
				action: 'bulk_delete',
				ids: selectedIds
			}, function(response) {
				if(response.status == 'success') {
					alert(response.message);
					location.reload();
				} else {
					alert('خطأ: ' + response.message);
					$('#bulk-delete').prop('disabled', false).text('حذف المحدد');
				}
			}, 'json').fail(function(xhr, status, error) {
				console.error('AJAX Error:', status, error);
				console.error('Response Text:', xhr.responseText);
				alert('خطأ في الاتصال: ' + error);
				$('#bulk-delete').prop('disabled', false).text('حذف المحدد');
			});
		}
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

		// الذهاب لصفحة التحرير المجمع
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

		// إذا تم تحديد منتج واحد فقط، نذهب مباشرة إلى صفحة التحرير
		if(selectedIds.length == 1) {
			window.location.href = 'index.php?page=warehouse/edit_product&id=' + selectedIds[0];
			return;
		}

		// إذا تم تحديد أكثر من منتج، نذهب إلى صفحة التحرير المجمع
		var idsParam = selectedIds.join(',');
		window.location.href = 'index.php?page=warehouse/bulk_edit&ids=' + idsParam;
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

	// حذف كل غير المصنف
	$('#delete-all-unclassified').click(function(){
		if(confirm('هل تريد حذف جميع المنتجات غير المصنفة؟ هذا الإجراء لا يمكن التراجع عنه!')) {
			$.post('ajax_actions.php', {
				action: 'delete_unclassified'
			}, function(response) {
				if(response.status == 'success') {
					alert(response.message);
					location.reload();
				} else {
					alert('خطأ: ' + response.message);
				}
			}, 'json').fail(function(xhr, status, error) {
				console.error('AJAX Error:', status, error);
				console.error('Response Text:', xhr.responseText);
				alert('خطأ في الاتصال: ' + error);
			});
		}
	});
});
</script>
