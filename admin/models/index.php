<?php if($_settings->chk_flashdata('success')): ?>
<script>
    alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">قائمة الموديلات</h3>
        <div class="card-tools">
            <a href="?page=models/manage_model" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> إنشاء جديد</a>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-hover table-striped" id="list">
                <colgroup>
                    <col width="5%">
                    <col width="15%">
                    <col width="20%">
                    <col width="20%">
                    <col width="25%">
                    <col width="10%">
                    <col width="5%">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>البراند</th>
                        <th>السيريس</th>
                        <th>الموديل</th>
                        <th>الوصف</th>
                        <th>الحالة</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    $qry = $conn->query("SELECT m.*, b.name as brand_name, COALESCE(NULLIF(s.name_ar, ''), s.name) as series_name 
                                        FROM `models` m 
                                        INNER JOIN `series` s ON s.id = m.series_id 
                                        INNER JOIN `brands` b ON b.id = s.brand_id 
                                        ORDER BY b.name, s.name, m.name ASC");
                    if($qry && $qry->num_rows > 0):
                        while($row = $qry->fetch_assoc()):
                    ?>
                        <tr>
                            <td class="text-center"><?php echo $i++; ?></td>
                            <td><?php echo $row['brand_name'] ?></td>
                            <td><?php echo $row['series_name'] ?></td>
                            <td><b><?php echo $row['name'] ?></b></td>
                            <td><p class="m-0 truncate-1"><?php echo $row['description'] ?></p></td>
                            <td class="text-center">
                                <?php if($row['status'] == 1): ?>
                                    <span class="badge badge-success">فعال</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">غير فعال</span>
                                <?php endif; ?>
                            </td>
                            <td align="center">
                                <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                    العمليات
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item" href="?page=models/manage_model&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> تعديل</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> حذف</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">لا توجد بيانات</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // تأخير تحميل DataTable للتأكد من تحميل DOM كاملاً
    setTimeout(function() {
        try {
            // التحقق من وجود الجدول وأنه يحتوي على صفوف
            var table = $('#list');
            if (table.length === 0) {
                console.log('Table #list not found');
                return;
            }
            
            // التحقق من وجود thead و tbody
            if (table.find('thead').length === 0 || table.find('tbody').length === 0) {
                console.log('Table structure incomplete');
                return;
            }
            
            // تدمير DataTable إذا كان موجوداً
            if ($.fn.DataTable.isDataTable('#list')) {
                $('#list').DataTable().destroy();
            }
            
            // تهيئة DataTable
            $('#list').DataTable({
                columnDefs: [
                    { orderable: false, targets: [6] }
                ],
                order: [[1, 'asc']],
                responsive: true,
                autoWidth: false,
                language: {
                    "sProcessing": "جارٍ التحميل...",
                    "sLengthMenu": "أظهر _MENU_ مدخلات",
                    "sZeroRecords": "لم يعثر على أية سجلات",
                    "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                    "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
                    "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
                    "sInfoPostFix": "",
                    "sSearch": "ابحث:",
                    "sUrl": "",
                    "oPaginate": {
                        "sFirst": "الأول",
                        "sPrevious": "السابق",
                        "sNext": "التالي",
                        "sLast": "الأخير"
                    }
                }
            });
            
            console.log('DataTable initialized successfully');
            
        } catch (error) {
            console.error('Error initializing DataTable:', error);
        }
    }, 200);
    
    $('.delete_data').click(function(){
        _conf("هل أنت متأكد من حذف هذا الموديل؟","delete_model",[$(this).attr('data-id')])
    });
});

function delete_model($id){
    start_loader();
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=delete_model",
        method: "POST",
        data: {id: $id},
        dataType: "json",
        error: function(err){
            console.log(err);
            alert_toast("حدث خطأ.",'error');
            end_loader();
        },
        success: function(resp){
            if(typeof resp == 'object' && resp.status == 'success'){
                location.reload();
            } else if(resp.status == 'failed' && resp.msg){
                alert_toast(resp.msg,'error');
                end_loader();
            } else {
                alert_toast("حدث خطأ غير متوقع",'error');
                end_loader();
                console.log(resp);
            }
        }
    });
}
</script>