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
        echo "<div class='alert alert-success'>Order updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating order: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0">Edit Order</h1>
            <a href="order_list.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Orders List
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="customer_id">Customer ID</label>
                            <input type="text" name="customer_id" class="form-control" value="<?php echo $order['customer_id']; ?>" required readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="order_date">Order Date</label>
                            <input type="date" name="order_date" class="form-control" value="<?php echo $order['order_date']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="status">Order Status</label>
                            <select name="order_status" class="form-control" required>
                                <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                <option value="Packing" <?php if ($order['status'] == 'Packing') echo 'selected'; ?>>Packing</option>
                                <option value="Shipped" <?php if ($order['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                <option value="Delivered" <?php if ($order['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option> 
                                <option value="Cancelled" <?php if ($order['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="total_amount">Order Total</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">RS.</span>
                                </div>
                                <input type="text" name="total_amount" class="form-control" value="<?php echo number_format($order['total_amount'], 2); ?>" required readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Update Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include("footer.php");
?>