<?php
include('header.php');
include('sidebar.php');
include('db_connection.php');

if(!isset($_GET['id'])) {
    header("Location: order_list.php");
    exit();
}

$order_id = $_GET['id'];

// Get order details
$order_query = "SELECT * FROM orders WHERE order_id = $order_id";
$order_result = mysqli_query($conn, $order_query);
$order = mysqli_fetch_assoc($order_result);

// Get order items
$items_query = "SELECT * FROM order_items WHERE order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);
?>

  <div class="container">
        <div class="page-inner">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    Order #<?= $order['order_id'] ?>
                    <span class="badge bg-<?= 
                        $order['status'] == 'Completed' ? 'success' : 
                        ($order['status'] == 'Processing' ? 'warning' : 
                        ($order['status'] == 'Cancelled' ? 'danger' : 'info')) 
                    ?> ms-2 align-middle">
                        <?= $order['status'] ?>
                    </span>
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-fw fa-tachometer-alt"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="order_list.php"><i class="fas fa-list"></i> Orders</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-file-invoice"></i> Order Details</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-outline-primary me-2">
                    <i class="fas fa-print me-1"></i> Print Invoice
                </button>
                <a href="order_list.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Orders
                </a>
            </div>
        </div>

        <!-- Order Summary Cards -->
        <div class="row mb-4">
            <!-- Customer Information -->
            <div class="col-lg-6 mb-4">
                <div class="card border-start-primary shadow h-100">
                    <div class="card-header bg-primary text-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-user me-2"></i>Customer Information</h6>
                            <a href="#" class="text-white" data-bs-toggle="tooltip" title="View Customer Profile">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center mb-3 mb-md-0">
                                <div class="avatar-lg mx-auto bg-light-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fas fa-user fa-2x text-primary"></i>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-light text-dark">Customer ID: <?= $order['customer_id'] ?></span>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h5 class="mb-2"><?= htmlspecialchars($order['name']) ?></h5>
                                <p class="mb-1">
                                    <i class="fas fa-envelope me-2 text-muted"></i>
                                    <a href="mailto:<?= htmlspecialchars($order['email']) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($order['email']) ?>
                                    </a>
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-phone me-2 text-muted"></i>
                                    <?= htmlspecialchars($order['phone']) ?>
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                    <?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?>, 
                                    <?= htmlspecialchars($order['state']) ?> - <?= htmlspecialchars($order['zip_code']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Information -->
            <div class="col-lg-6 mb-4">
                <div class="card border-start-info shadow h-100">
                    <div class="card-header bg-info text-white py-3">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-shopping-cart me-2"></i>Order Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <h6 class="text-muted small mb-1">Order Date</h6>
                                <p class="mb-0">
                                    <i class="far fa-calendar-alt me-2"></i>
                                    <?= date('F j, Y', strtotime($order['order_date'])) ?>
                                </p>
                                <small class="text-muted"><?= date('g:i a', strtotime($order['order_date'])) ?></small>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <h6 class="text-muted small mb-1">Payment Method</h6>
                                <p class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>
                                    <?= htmlspecialchars($order['payment_method']) ?>
                                    <?php if($order['payment_method'] == 'Credit Card'): ?>
                                        <span class="text-muted small">(****-****-****-<?= substr($order['card_number'], -4) ?>)</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <h6 class="text-muted small mb-1">Order Status</h6>
                                <span class="badge bg-<?= 
                                    $order['status'] == 'peakging' ? 'success' : 
                                    ($order['status'] == 'Processing' ? 'warning' : 
                                    ($order['status'] == 'Cancelled' ? 'danger' : 'info')) 
                                ?>">
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </div>
                            <div class="col-sm-6">
                                <h6 class="text-muted small mb-1">Total Amount</h6>
                                <h4 class="mb-0 text-success">Rs. <?= number_format($order['total_amount'], 2) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="card shadow mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-ol me-2"></i>Order Items</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50px">#</th>
                                <th>Product</th>
                                <th class="text-end">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $counter = 1;
                            $subtotal = 0;
                            while($item = mysqli_fetch_assoc($items_result)): 
                                $item_total = $item['price'] * $item['quantity'];
                                $subtotal += $item_total;
                            ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <img src="admin/" alt="Product" width="40" class="rounded">
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?= htmlspecialchars($item['product_name']) ?></h6>
                                            <small class="text-muted">SKU: <?= $item['product_id'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">Rs. <?= number_format($item['price'], 2) ?></td>
                                <td class="text-center"><?= $item['quantity'] ?></td>
                                <td class="text-end">Rs. <?= number_format($item_total, 2) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                                <td class="text-end fw-bold">Rs. <?= number_format($subtotal, 2) ?></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">Shipping:</td>
                                <td class="text-end">Free</td>
                            </tr>
                            
                            <tr class="table-success">
                                <td colspan="4" class="text-end fw-bold">Grand Total:</td>
                                <td class="text-end fw-bold">Rs. <?= number_format($order['total_amount'], 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Notes & Actions -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-sticky-note me-2"></i>Order Notes</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-0">No notes have been added for this order.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-cog me-2"></i>Order Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="edit_order.php?id=<?= $order['order_id'] ?>" class="btn btn-outline-primary me-md-2">
                                <i class="fas fa-pencil-alt me-1"></i> Edit Order
                            </a>
                            <button class="btn btn-outline-success me-md-2">
                                <i class="fas fa-truck me-1"></i> Update Shipping
                            </button>
                            <button class="btn btn-outline-danger">
                                <i class="fas fa-times me-1"></i> Cancel Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thank You Message -->
        <div class="text-center py-4 bg-light rounded mb-4">
            <h5 class="mb-3">Thank you for your order!</h5>
            <p class="mb-0">We appreciate your business and hope you enjoy your purchase.</p>
        
            <div class="mt-2">
                <small class="text-muted">Order ID: <?= $order['order_id'] ?> | Placed on <?= date('F j, Y', strtotime($order['order_date'])) ?></small>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<style>
.avatar-lg {
    width: 80px;
    height: 80px;
}
.border-start-primary {
    border-left: 4px solid #4e73df !important;
}
.border-start-info {
    border-left: 4px solid #36b9cc !important;
}
</style>

<script>
// Enable tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>