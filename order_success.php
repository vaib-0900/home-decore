<?php

include "header.php";
include "db_connection.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$order_id = mysqli_real_escape_string($conn, $_GET['id']);
$customer_id = $_SESSION['customer_id'];


$order_query = "SELECT * FROM orders WHERE order_id = '$order_id' AND customer_id = '$customer_id'";
$order_result = mysqli_query($conn, $order_query);

if (mysqli_num_rows($order_result) == 0) {
    header("Location: index.php");
    exit();
}

$order = mysqli_fetch_assoc($order_result);

$items_query = "SELECT * FROM order_items WHERE order_id = '$order_id'";
$items_result = mysqli_query($conn, $items_query);
?>

<div class="container py-5">
    <div class="text-center">
        <div class="mb-4">
            <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
        </div>
        <h1 class="mb-3">Order Confirmed!</h1>
        <p class="lead mb-4">Thank you for your purchase. Your order has been received.</p>
        
        <div class="card shadow-sm mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <div class="alert alert-success">
                    <h5 class="alert-heading">Order #<?php echo $order_id; ?></h5>
                    <p>We've sent a confirmation email to <?php echo htmlspecialchars($order['email']); ?></p>
                </div>
                
                <h5 class="mt-4">Order Details</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>Rs.<?php echo number_format($item['price'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
                                <th>Rs.<?php echo number_format($order['total_amount'], 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <h5 class="mt-4">Shipping Information</h5>
                <address>
                    <strong><?php echo htmlspecialchars($order['name']); ?></strong><br>
                    <?php echo htmlspecialchars($order['address']); ?><br>
                    <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> <?php echo htmlspecialchars($order['zip_code']); ?><br>
                    <abbr title="Phone">P:</abbr> <?php echo htmlspecialchars($order['phone']); ?>
                </address>
                
                <div class="d-grid gap-2">
                    <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                    <a href="order_history.php" class="btn btn-outline-secondary">View Order History</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>