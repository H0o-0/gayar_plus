<?php
// صفحة رفع ملف Excel/CSV
?>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">رفع ملف CSV
		<div class="card-tools">
			<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=warehouse">
				<i class="fa fa-arrow-left"></i> العودة للمخزن
			</a>
		</div>
	</div>
	<div class="card-body">
		
		<!-- تعليمات الرفع -->
		<div class="alert alert-info">
			<h5><i class="icon fas fa-info"></i> تعليمات مهمة:</h5>
			<ul class="mb-0">
				<li><strong>نوع الملف المدعوم:</strong> CSV فقط</li>
				<li><strong>للحصول على أفضل النتائج:</strong> احفظ ملف Excel كـ CSV (UTF-8)</li>
				<li>يجب أن يحتوي الملف على عمود اسم المنتج وعمود السعر كحد أدنى</li>
				<li>سيتم تصنيف المنتجات تلقائياً حسب أسمائها (يدعم العربية والإنجليزية)</li>
				<li>يمكنك مراجعة وتعديل التصنيف بعد الرفع</li>
			</ul>
		</div>

		<div class="alert alert-warning">
			<h5><i class="icon fas fa-exclamation-triangle"></i> كيفية تحويل Excel إلى CSV:</h5>
			<p><strong>الطريقة السريعة:</strong></p>
			<ol>
				<li>افتح ملف Excel</li>
				<li>حدد البيانات (اسم المنتج والسعر فقط)</li>
				<li>انسخها (Ctrl+C)</li>
				<li>افتح Notepad أو أي محرر نصوص</li>
				<li>الصق البيانات</li>
				<li>احفظ الملف باسم "products.csv" (اختر UTF-8)</li>
				<li>ارفع الملف الجديد</li>
			</ol>
		</div>

		<!-- نموذج رفع الملف -->
		<form id="upload-form" enctype="multipart/form-data">
			<div class="row">
				<div class="col-md-8">
					<div class="form-group">
						<label for="csv-file">اختر ملف CSV:</label>
						<div class="custom-file">
							<input type="file" class="custom-file-input" id="csv-file" name="csv_file" accept=".csv" required>
							<label class="custom-file-label" for="csv-file">اختر الملف...</label>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="import-batch">اسم الدفعة (اختياري):</label>
						<input type="text" class="form-control" id="import-batch" name="import_batch" placeholder="مثل: دفعة_يناير_2024">
					</div>
				</div>
			</div>

			<!-- خيارات متقدمة -->
			<div class="card card-secondary collapsed-card">
				<div class="card-header">
					<h3 class="card-title">خيارات متقدمة</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse">
							<i class="fas fa-plus"></i>
						</button>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="name-column">عمود اسم المنتج:</label>
								<select class="form-control" id="name-column" name="name_column">
									<option value="0">العمود الأول (A)</option>
									<option value="1" selected>العمود الثاني (B)</option>
									<option value="2">العمود الثالث (C)</option>
									<option value="3">العمود الرابع (D)</option>
									<option value="4">العمود الخامس (E)</option>
								</select>
								<small class="text-muted">عادة العمود الثاني (B) يحتوي على أسماء المنتجات</small>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="price-column">عمود السعر:</label>
								<select class="form-control" id="price-column" name="price_column">
									<option value="1">العمود الثاني (B)</option>
									<option value="0">العمود الأول (A)</option>
									<option value="2" selected>العمود الثالث (C)</option>
									<option value="3">العمود الرابع (D)</option>
									<option value="4">العمود الخامس (E)</option>
								</select>
								<small class="text-muted">عادة العمود الثالث (C) يحتوي على الأسعار</small>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="start-row">البدء من السطر:</label>
								<input type="number" class="form-control" id="start-row" name="start_row" value="2" min="1">
								<small class="text-muted">السطر الأول عادة يحتوي على العناوين</small>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group mt-3">
				<button type="submit" class="btn btn-primary btn-lg">
					<i class="fas fa-upload"></i> رفع وتحليل الملف
				</button>
			</div>
		</form>

		<!-- منطقة عرض النتائج -->
		<div id="upload-results" style="display: none;">
			<hr>
			<h4>نتائج الرفع والتصنيف:</h4>
			<div id="results-content"></div>
		</div>

		<!-- شريط التقدم -->
		<div id="upload-progress" style="display: none;">
			<hr>
			<h5>جاري المعالجة...</h5>
			<div class="progress">
				<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
			</div>
			<div id="progress-text" class="mt-2"></div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	// تحديث اسم الملف المختار
	$('#excel-file').on('change', function(){
		var fileName = $(this).val().split('\\').pop();
		$(this).next('.custom-file-label').html(fileName);
	});

	// معالجة رفع الملف
	$('#upload-form').on('submit', function(e){
		e.preventDefault();
		
		var formData = new FormData(this);
		
		// إظهار شريط التقدم
		$('#upload-progress').show();
		$('#upload-results').hide();
		updateProgress(0, 'بدء رفع الملف...');
		
		$.ajax({
			url: 'warehouse/process_upload.php',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			xhr: function() {
				var xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener("progress", function(evt) {
					if (evt.lengthComputable) {
						var percentComplete = evt.loaded / evt.total * 100;
						updateProgress(percentComplete, 'جاري رفع الملف...');
					}
				}, false);
				return xhr;
			},
			success: function(response) {
				updateProgress(100, 'تم الرفع بنجاح!');
				
				setTimeout(function() {
					$('#upload-progress').hide();
					$('#upload-results').show();
					$('#results-content').html(response);
				}, 1000);
			},
			error: function(xhr, status, error) {
				updateProgress(0, 'حدث خطأ: ' + error);
				setTimeout(function() {
					$('#upload-progress').hide();
				}, 3000);
			}
		});
	});

	function updateProgress(percent, text) {
		$('#upload-progress .progress-bar').css('width', percent + '%');
		$('#progress-text').text(text);
	}
});
</script>

<style>
.upload-stats {
	background: #f8f9fa;
	border-radius: 10px;
	padding: 20px;
	margin: 20px 0;
}

.upload-stats .stat-item {
	text-align: center;
	padding: 15px;
}

.upload-stats .stat-number {
	font-size: 2rem;
	font-weight: bold;
	color: #007bff;
}

.upload-stats .stat-label {
	color: #6c757d;
	font-size: 0.9rem;
}

.classification-preview {
	max-height: 400px;
	overflow-y: auto;
	border: 1px solid #dee2e6;
	border-radius: 5px;
}

.classification-item {
	padding: 10px;
	border-bottom: 1px solid #f8f9fa;
}

.classification-item:last-child {
	border-bottom: none;
}
</style>
