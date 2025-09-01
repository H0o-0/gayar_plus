<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<?php if($_settings->chk_flashdata('error')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('error') ?>",'error')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Orders</h3>
		<!-- <div class="card-tools">
			<a href="?page=order/manage_order" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div> -->
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-stripped">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="20%">
					<col width="15%">
					<col width="15%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Order</th>
						<th>Client</th>
						<th>Phone</th>
						<th>Total Amount</th>
						<th>Paid</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						// Changed from INNER JOIN to LEFT JOIN to show orders even if client is missing
						$qry = $conn->query("SELECT o.*,IFNULL(concat(c.firstname,' ',c.lastname), IFNULL(c.firstname, 'Guest')) as client, IFNULL(c.contact, 'N/A') as phone from `orders` o left join clients c on c.id = o.client_id order by unix_timestamp(o.date_created) desc ");
						while($row = $qry->fetch_assoc()):
							// Extract client info from delivery_address for guest orders (client_id = 0)
							if($row['client_id'] == 0) {
								// Parse customer info from delivery_address
								$delivery_info = $row['delivery_address'];
								$client_name = 'Guest Customer';
								$client_phone = 'N/A';
								
								// Extract name - updated to properly handle Arabic text
								if(preg_match('/الاسم:\s*(.+?)(?=\n|$)/u', $delivery_info, $name_matches)) {
									$client_name = trim($name_matches[1]);
								}
								
								// Extract phone - updated to properly handle Arabic text
								if(preg_match('/الهاتف:\s*(.+?)(?=\n|$)/u', $delivery_info, $phone_matches)) {
									$client_phone = trim($phone_matches[1]);
								}
								
								$row['client'] = $client_name;
								$row['phone'] = $client_phone;
							}
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo $row['client'] ?></td>
							<td><?php echo $row['phone'] ?></td>
							<td class="text-right"><?php echo number_format($row['amount']) ?></td>
							<td class="text-center">
                                <?php if($row['paid'] == 0): ?>
                                    <span class="badge badge-light">No</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Yes</span>
                                <?php endif; ?>
                            </td>
							<td class="text-center">
                                <?php if($row['status'] == 0): ?>
                                    <span class="badge badge-light">Pending</span>
                                <?php elseif($row['status'] == 1): ?>
                                    <span class="badge badge-primary">Packed</span>
							<?php elseif($row['status'] == 2): ?>
                                    <span class="badge badge-warning">Out for Delivery</span>
							<?php elseif($row['status'] == 3): ?>
                                    <span class="badge badge-success">Delivered</span>
							<?php else: ?>
                                    <span class="badge badge-danger">Cancelled</span>
                                <?php endif; ?>
                            </td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item" href="?page=orders/view_order&id=<?php echo $row['id'] ?>">View Order</a>
									<?php if($row['paid'] == 0 && $row['status'] != 4): ?>
				                    <a class="dropdown-item pay_order" href="javascript:void(0)"  data-id="<?php echo $row['id'] ?>">Mark as Paid</a>
									<?php endif; ?>
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
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this order permanently?","delete_order",[$(this).attr('data-id')])
		})
		$('.pay_order').click(function(){
			_conf("Are you sure to mark this order as paid?","pay_order",[$(this).attr('data-id')])
		})
		$('.table').dataTable();
	})
	function pay_order($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=pay_order",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
	function delete_order($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_order",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>