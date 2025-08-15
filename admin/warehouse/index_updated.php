<?php
// التحقق من وجود جلسة مسجلة
if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['login_type'] != 1) {
    echo '<div class="alert alert-danger">غير مصرح لك بالوصول</div>';
    exit;
}

// الاتصال بقاعدة البيانات
// require_once('../../config.php'); // المسار خاطئ
// config.php سبق تحميله من admin/index.php

// استعلام للحصول على إحصائيات المخزن
$stats_query = $conn->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'processed' THEN 1 ELSE 0 END) as processed,
        SUM(CASE WHEN status = 'classified' THEN 1 ELSE 0 END) as classified,
        SUM(CASE WHEN status = 'unclassified' THEN 1 ELSE 0 END) as unclassified,
        SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as error_count
    FROM temp_warehouse
");

$stats = $stats_query->fetch_assoc();

// استعلام للحصول على المنتجات غير المصنفة حسب العلامة التجارية
$brands_query = $conn->query("
    SELECT 
        COALESCE(suggested_brand, 'Unclassified') as brand,
        COUNT(*) as count
    FROM temp_warehouse
    WHERE status = 'unclassified'
    GROUP BY suggested_brand
    ORDER BY count DESC
");

$brands_stats = [];
while($row = $brands_query->fetch_assoc()) {
    $brands_stats[] = $row;
}

// استعلام للحصول على المنتجات
$products_query = $conn->query("
    SELECT * FROM temp_warehouse
    ORDER BY 
        CASE 
            WHEN status = 'unclassified' THEN 1
            WHEN status = 'classified' THEN 2
            WHEN status = 'processed' THEN 3
            WHEN status = 'published' THEN 4
            ELSE 5
        END,
        id DESC
");
?>

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
        <!-- إحصائيات المخزن -->
        <div class="row">
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?php echo $stats['total'] ?></h3>
                        <p>إجمالي المنتجات</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?php echo $stats['processed'] ?></h3>
                        <p>منتج تم معالجته</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3><?php echo $stats['classified'] ?></h3>
                        <p>منتج تم تصنيفه</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tag"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?php echo $stats['unclassified'] ?></h3>
                        <p>منتج غير مصنف</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- إحصائيات العلامات التجارية غير المصنفة -->
        <?php if(!empty($brands_stats)): ?>
        <div class="card card-outline card-warning mb-3">
            <div class="card-header">
                <h3 class="card-title">إحصائيات التصنيف</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach($brands_stats as $brand): ?>
                    <div class="col-md-3 mb-2">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-tag"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text"><?php echo $brand['brand'] ?></span>
                                <span class="info-box-number">Unclassified: <?php echo $brand['count'] ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- أدوات البحث والفلترة -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="search-input" placeholder="بحث...">
                    <div class="input-group-append">
                        <button class="btn btn-primary" id="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="status-filter">
                    <option value="">جميع الحالات</option>
                    <option value="unclassified">غير مصنف</option>
                    <option value="classified">مصنف</option>
                    <option value="processed">تمت المعالجة</option>
                    <option value="published">منشور</option>
                    <option value="error">خطأ</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="brand-filter">
                    <option value="">جميع العلامات التجارية</option>
                    <?php foreach($brands_stats as $brand): ?>
                    <option value="<?php echo $brand['brand'] ?>"><?php echo $brand['brand'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-secondary btn-block" id="reset-filters">
                    <i class="fas fa-undo"></i> إعادة ضبط
                </button>
            </div>
        </div>
        
        <!-- جدول المنتجات -->
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="warehouse-table">
                <thead>
                    <tr>
                        <th width="5%">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="select-all">
                                <label class="custom-control-label" for="select-all"></label>
                            </div>
                        </th>
                        <th width="5%">#</th>
                        <th width="25%">اسم المنتج</th>
                        <th width="15%">العلامة التجارية</th>
                        <th width="10%">السعر</th>
                        <th width="10%">الحالة</th>
                        <th width="15%">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $products_query->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input product-checkbox" id="product-<?php echo $row['id'] ?>" value="<?php echo $row['id'] ?>">
                                <label class="custom-control-label" for="product-<?php echo $row['id'] ?>"></label>
                            </div>
                        </td>
                        <td><?php echo $row['id'] ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']) ?></td>
                        <td><?php echo htmlspecialchars($row['suggested_brand'] ?? 'غير محدد') ?></td>
                        <td><?php echo number_format($row['original_price'], 0) ?> د.ع</td>
                        <td>
                            <?php 
                            $status_class = 'warning';
                            $status_text = 'غير مصنف';
                            
                            switch($row['status']) {
                                case 'processed':
                                    $status_class = 'secondary';
                                    $status_text = 'تمت المعالجة';
                                    break;
                                case 'error':
                                    $status_class = 'danger';
                                    $status_text = 'خطأ';
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
                                <a href="./index.php?page=warehouse/edit_product&id=<?php echo $row['id'] ?>"
                                   class="btn btn-primary btn-sm" title="تحرير">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <?php if($row['status'] != 'published'): ?>
                                <button class="btn btn-success btn-sm quick-publish" data-id="<?php echo $row['id'] ?>" title="نشر سريع">
                                    <i class="fas fa-bolt"></i>
                                </button>
                                <?php else: ?>
                                <button class="btn btn-info btn-sm republish-product" data-id="<?php echo $row['id'] ?>" title="إعادة نشر">
                                    <i class="fas fa-sync-alt"></i>
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
            <div class="col-md-6 text-right">
                <div class="btn-group">
                    <button class="btn btn-primary" id="bulk-edit" disabled>
                        <i class="fas fa-edit"></i> تحرير المحدد
                    </button>
                    <button class="btn btn-info" id="bulk-quick-publish" disabled>
                        <i class="fas fa-bolt"></i> نشر المحدد
                    </button>
                    <button class="btn btn-danger" id="bulk-delete" disabled>
                        <i class="fas fa-trash"></i> حذف المحدد
                    </button>
                </div>
                <button class="btn btn-warning ml-2" id="delete-all-unclassified" disabled>
                    <i class="fas fa-trash-alt"></i> حذف جميع غير المصنفة
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        // تفعيل/تعطيل أزرار العمليات المجمعة
        function toggleBulkButtons() {
            var checkedCount = $('.product-checkbox:checked').length;
            $('#bulk-edit, #bulk-quick-publish, #bulk-delete, #delete-all-unclassified').prop('disabled', checkedCount === 0);
        }
        
        // تحديد/إلغاء تحديد الكل
        $('#select-all').change(function(){
            $('.product-checkbox').prop('checked', $(this).prop('checked'));
            toggleBulkButtons();
        });
        
        // تحديث حالة زر تحديد الكل عند تغيير حالة أي صندوق
        $(document).on('change', '.product-checkbox', function(){
            toggleBulkButtons();
        });
        
        // البحث والفلترة
        $('#search-btn, #status-filter, #brand-filter').on('click change', function(){
            var searchText = $('#search-input').val().toLowerCase();
            var statusFilter = $('#status-filter').val();
            var brandFilter = $('#brand-filter').val();
            
            $('#warehouse-table tbody tr').each(function(){
                var row = $(this);
                var productName = row.find('td:eq(2)').text().toLowerCase();
                var brand = row.find('td:eq(3)').text().toLowerCase();
                var statusText = row.find('td:eq(5) .badge').text().toLowerCase();
                var statusClass = row.find('td:eq(5) .badge').attr('class');
                
                var statusMatch = true;
                if(statusFilter) {
                    switch(statusFilter) {
                        case 'unclassified':
                            statusMatch = statusClass.includes('warning');
                            break;
                        case 'classified':
                            statusMatch = statusClass.includes('primary');
                            break;
                        case 'processed':
                            statusMatch = statusClass.includes('secondary');
                            break;
                        case 'published':
                            statusMatch = statusClass.includes('success');
                            break;
                        case 'error':
                            statusMatch = statusClass.includes('danger');
                            break;
                    }
                }
                
                var brandMatch = !brandFilter || brand.includes(brandFilter.toLowerCase());
                var searchMatch = !searchText || productName.includes(searchText) || brand.includes(searchText);
                
                if(statusMatch && brandMatch && searchMatch) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        });
        
        // إعادة ضبط الفلاتر
        $('#reset-filters').click(function(){
            $('#search-input').val('');
            $('#status-filter, #brand-filter').val('');
            $('#warehouse-table tbody tr').show();
        });
        
        // حذف منتج
        $(document).on('click', '.delete-product', function(){
            var id = $(this).data('id');
            var row = $(this).closest('tr');
            
            if(confirm('هل أنت متأكد من حذف هذا المنتج؟')) {
                $.post('warehouse/ajax_actions.php', {
                    action: 'delete_product',
                    id: id
                }, function(response) {
                    if(response.status == 'success') {
                        row.fadeOut(function(){
                            $(this).remove();
                        });
                        alert(response.message);
                    } else {
                        alert('خطأ: ' + response.message);
                    }
                }, 'json');
            }
        });
        
        // النشر السريع
        $(document).on('click', '.quick-publish', function(){
            var id = $(this).data('id');
            var row = $(this).closest('tr');

            // استخدام النشر السريع المعدل الذي يأخذ المستخدم إلى صفحة التحرير
            $.post('warehouse/ajax_actions.php', {
                action: 'quick_publish',
                id: id
            }, function(response) {
                if(response.status == 'success') {
                    // التحويل إلى صفحة التحرير
                    window.location.href = response.redirect;
                } else {
                    alert('خطأ: ' + response.message);
                }
            }, 'json');
        });
        
        // إعادة نشر منتج
        $(document).on('click', '.republish-product', function(){
            var id = $(this).data('id');
            
            if(confirm('هل تريد إعادة نشر هذا المنتج؟')) {
                $.post('warehouse/ajax_actions.php', {
                    action: 'republish_product',
                    id: id
                }, function(response) {
                    if(response.status == 'success') {
                        alert(response.message);
                        window.location.reload();
                    } else {
                        alert('خطأ: ' + response.message);
                    }
                }, 'json');
            }
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
                $.post('warehouse/ajax_actions.php', {
                    action: 'bulk_delete',
                    ids: selectedIds
                }, function(response) {
                    if(response.status == 'success') {
                        location.reload();
                    } else {
                        alert('خطأ: ' + response.message);
                    }
                }, 'json');
            }
        });
        
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
            toggleBulkButtons();
        });
        
        // حذف جميع المنتجات غير المصنفة
        $('#delete-all-unclassified').click(function(){
            if(confirm('هل أنت متأكد من حذف جميع المنتجات غير المصنفة؟')) {
                $.post('warehouse/ajax_actions.php', {
                    action: 'delete_unclassified'
                }, function(response) {
                    if(response.status == 'success') {
                        location.reload();
                    } else {
                        alert('خطأ: ' + response.message);
                    }
                }, 'json');
            }
        });
    });
</script>