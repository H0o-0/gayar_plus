<?php if(isset($_GET['view'])): 
require_once('../../config.php');
endif;?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<?php 
if(!isset($_GET['id'])){
    $_settings->set_flashdata('error','No order ID Provided.');
    redirect('admin/?page=orders');
}
// Changed from INNER JOIN to LEFT JOIN to show orders even if client is missing
$order = $conn->query("SELECT o.*,IFNULL(concat(c.firstname,' ',c.lastname), c.firstname) as client, c.contact as phone FROM `orders` o left join clients c on c.id = o.client_id where o.id = '{$_GET['id']}' ");
if($order->num_rows > 0){
    $order_data = $order->fetch_assoc();
    foreach($order_data as $k => $v){
        $$k = $v;
    }
    
    // Extract client info from delivery_address for guest orders (client_id = 0)
    if($client_id == 0) {
        // Parse customer info from delivery_address
        $delivery_info = $delivery_address;
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
        
        $client = $client_name;
        $phone = $client_phone;
    }
}else{
    $_settings->set_flashdata('error','Order ID provided is Unknown');
    redirect('admin/?page=orders');
}
?>
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="conitaner-fluid">
            <p><b>Client Name: <?php echo $client ?></b></p>
            <p><b>Phone Number: <?php echo $phone ?></b></p>
            <p><b>Delivery Address: <?php echo $delivery_address ?></b></p>
            <table class="table-striped table table-bordered">
                <colgroup>
                    <col width="15%">
                    <col width="15%">
                    <col width="30%">
                    <col width="20%">
                    <col width="20%">
                </colgroup>
                <thead>
                    <tr>
                        <th>QTY</th>
                        <th>Unit</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $olist = $conn->query("SELECT o.*,p.product_name FROM order_list o inner join products p on o.product_id = p.id where o.order_id = '{$id}' ");
                        while($row = $olist->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo $row['quantity'] ?></td>
                        <td><?php echo $row['unit'] ?></td>
                        <td><?php echo $row['product_name'] ." ({$row['size']}) " ?></td>
                        <td class="text-right"><?php echo number_format($row['price']) ?></td>
                        <td class="text-right"><?php echo number_format($row['price'] * $row['quantity']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan='4'  class="text-right">Total</th>
                        <th class="text-right"><?php echo number_format($amount) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="row">
            <div class="col-6">
                <p>Payment Method: <?php echo $payment_method ?></p>
                <p>Payment Status: <?php echo $paid == 0 ? '<span class="badge badge-light">Unpaid</span>' : '<span class="badge badge-success">Paid</span>' ?></p>
            </div>
            <div class="col-6 row row-cols-2">
                <div class="col-3">Order Status:</div>
                <div class="col-9">
                <?php 
                    switch($status){
                        case '0':
                            echo '<span class="badge badge-light text-dark">Pending</span>';
	                    break;
                        case '1':
                            echo '<span class="badge badge-primary">Packed</span>';
	                    break;
                        case '2':
                            echo '<span class="badge badge-warning">Out for Delivery</span>';
	                    break;
                        case '3':
                            echo '<span class="badge badge-success">Delivered</span>';
	                    break;
                        default:
                            echo '<span class="badge badge-danger">Cancelled</span>';
	                    break;
                    }
                ?>
                </div>
                <?php if(!isset($_GET['view'])): ?>
                <div class="col-3"></div>
                <div class="col">
                    <button type="button" id="update_status" class="btn btn-sm btn-flat btn-primary">Update Status</button>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
<?php if(isset($_GET['view'])): ?>
<style>
    #uni_modal>.modal-dialog>.modal-content>.modal-footer{
        display:none;
    }
</style>
<?php endif; ?>
<script>
    $(function(){
        $('#update_status').click(function(){
            uni_modal("Update Status", "./orders/update_status.php?oid=<?php echo $id ?>&status=<?php echo $status ?>")
        })
    })
</script>