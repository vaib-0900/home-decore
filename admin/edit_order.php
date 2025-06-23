<?php
include('header.php');
include('sidebar.php');
include('db_connection.php');
?>

<?php
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $query = "SELECT * FROM orders WHERE order_id = $order_id";
    $result = mysqli_query($conn, $query);
    $order = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_status = $_POST['order_status']; 
    $order_date = $_POST['order_date'];

    $query = "UPDATE orders SET status='$order_status', order_date='$order_date' WHERE order_id=$order_id";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Order updated successfully!";
        header("Location: order_list.php");
        exit();
    } else {
        $error_message = "Error updating order: " . mysqli_error($conn);
    }
}
?>

<div class="container">
        <div class="page-inner">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Edit Order #<?php echo $order_id; ?></h1>
            </div>
        </div>
        
        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade in">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Order Information
                    </div>
                    <div class="panel-body">
                        <form action="order_list.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="customer_id">Customer ID</label>
                                <input type="text" name="customer_id" class="form-control" 
                                       value="<?php echo htmlspecialchars($order['customer_id']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="order_status">Order Status</label>
                                <select name="order_status" class="form-control" required>
                                    <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="Packing" <?php if ($order['status'] == 'Packing') echo 'selected'; ?>>Packing</option>
                                    <option value="Shipped" <?php if ($order['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                    <option value="Delivered" <?php if ($order['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option> 
                                    <option value="Cancelled" <?php if ($order['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="total_amount">Order Total</label>
                                <div class="input-group">
                                    <span class="input-group-addon">RS.</span>
                                    <input type="text" readonly name="total_amount" class="form-control" 
                                           value="<?php echo number_format($order['total_amount'], 2); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="order_date">Order Date</label>
                                <input type="date" name="order_date" class="form-control" 
                                       value="<?php echo htmlspecialchars($order['order_date']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Update Order
                                </button>
                                <a href="order_list.php" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Orders
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Quick Actions
                    </div>
                    <div class="panel-body">
                        <div class="list-group">
                            <a href="order_list.php" class="list-group-item">
                                <i class="fa fa-list"></i> View All Orders
                            </a>
                            <a href="view_order.php" class="list-group-item" data-toggle="modal" data-target="#orderHistoryModal">
                                <i class="fa fa-history"></i> View Order History
                            </a>
                            <a href="#" class="list-group-item">
                                <i class="fa fa-envelope"></i> Contact Customer
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Order Statistics
                    </div>
                    <div class="panel-body">
                        <div class="text-center">
                            <div class="huge">#<?php echo $order_id; ?></div>
                            <div>Order Number</div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <div class="huge">RS<?php echo number_format($order['total_amount'], 2); ?></div>
                            <div>Total Amount</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order History Modal -->
<div class="modal fade" id="orderHistoryModal" tabindex="-1" role="dialog" aria-labelledby="orderHistoryModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="orderHistoryModalLabel">Order History</h4>
            </div>
            <div class="modal-body">
                <p>Order history would be displayed here...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>