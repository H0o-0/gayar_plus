<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<script>
var _base_url_ = '<?php echo $_settings->base_url ?>';
</script>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Brands</h3>
		<div class="card-tools">
			<a href="?page=brands/manage_brand" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        	<div class="container-fluid">
			<table class="table table-bordered table-stripped">
				<colgroup>
					<col width="5%">
					<col width="35%">
					<col width="35%">
					<col width="10%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Brand</th>
						<th>Description</th>
                        <th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `brands` order by `name` asc ");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo $row['name'] ?></td>
							<td><p class="m-0 truncate-1"><?php echo $row['description'] ?></p></td>
                            <td class="text-center">
                                <?php if($row['status'] == 1): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item" href="?page=brands/manage_brand&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
    console.log("Script loaded");
    
    $('.delete_data').click(function(){
        console.log("Delete button clicked");
        var id = $(this).attr('data-id');
        console.log("Brand ID:", id);
        
        _conf = confirm("Are you sure to delete this brand permanently?");
        if(_conf == true){
            delete_brand(id);
        }
    });
    
    $('.table').DataTable({
        columnDefs: [
            { orderable: false, targets: [4] }
        ],
        order: [[0, "asc"]]
    });
});

function delete_brand(id){
    console.log("delete_brand function called with ID:", id);
    start_loader();
    
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=delete_brand",
        method: "POST",
        data: {id: id},
        dataType: "json",
        beforeSend: function(){
            console.log("Sending AJAX request to:", _base_url_ + "classes/Master.php?f=delete_brand");
        },
        error: function(err) {
            console.log("AJAX Error:", err);
            alert_toast("An error occurred", 'error');
            end_loader();
        },
        success: function(resp){
            console.log("AJAX Response:", resp);
            if(typeof resp == 'object' && resp.status == 'success'){
                alert_toast("Brand deleted successfully", 'success');
                setTimeout(function(){
                    location.reload();
                }, 1500);
            } else if(resp.status == 'failed' && !!resp.error){
                alert_toast("Error: " + resp.error, 'error');
                end_loader();
            } else {
                alert_toast("An error occurred", 'error');
                end_loader();
                console.log("Unexpected response:", resp);
            }
        }
    });
}
</script>