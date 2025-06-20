<?php
include "header.php";
include "db_connection.php";

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Get all orders for this customer, newest first
$orders_query = "SELECT * FROM orders 
                WHERE customer_id = '$customer_id' 
                ORDER BY order_date DESC";
$orders_result = mysqli_query($conn, $orders_query);

// Count total orders
$total_orders = mysqli_num_rows($orders_result);

// Get order statistics for the stats cards - UPDATED to remove 'Completed' and add 'Packing'
$stats_query = "SELECT 
                SUM(CASE WHEN status = 'Packing' THEN 1 ELSE 0 END) as packing,
                SUM(CASE WHEN status = 'Shipped' THEN 1 ELSE 0 END) as shipped,
                SUM(CASE WHEN status IN ('Pending', 'Processing') THEN 1 ELSE 0 END) as processing
                FROM orders WHERE customer_id = '$customer_id'";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        .order-card {
            border-left: 4px solid;
        }
        .order-card.pending {
            border-left-color: #ffc107;
        }
        .order-card.processing {
            border-left-color: #0d6efd;
        }
        .order-card.shipped {
            border-left-color: #0dcaf0;
        }
        .order-card.packing {
            border-left-color:rgb(90, 23, 216); /* Purple color for packing */
        }
        .order-card.cancelled {
            border-left-color: #dc3545;
        }
    </style>
</head>
<body>
<div class="container py-4 mt-5">
    <div class="row mb-4 mt-5">
        <div class="col-12 mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0"><i class="fas fa-history me-2 text-primary"></i> Order History</h2>
                <span class="badge bg-primary rounded-pill fs-6 px-3 py-2">
                    <i class="fas fa-shopping-bag me-1"></i> <?php echo $total_orders; ?> orders
                </span>
            </div>
            
            <!-- Stats Cards - UPDATED to show Packing instead of Completed -->
            <div class="row g-4 mb-4 ">
                <div class="col-md-4">
                    <div class="card bg-primary bg-opacity-10 border-0 rounded-3 h-100 card-hover">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-25 p-3 rounded-circle me-3">
                                    <i class="fas fa-box fa-lg text-dark"></i>
                                </div>
                                <div>
                                    <h6 class="card-title text-uppercase text-muted small mb-1">Packing</h6>
                                    <h3 class="mb-0"><?php echo $stats['packing']; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info bg-opacity-10 border-0 rounded-3 h-100 card-hover">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-info bg-opacity-25 p-3 rounded-circle me-3">
                                    <i class="fas fa-truck fa-lg text-info"></i>
                                </div>
                                <div>
                                    <h6 class="card-title text-uppercase text-muted small mb-1">Shipped</h6>
                                    <h3 class="mb-0"><?php echo $stats['shipped']; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning bg-opacity-10 border-0 rounded-3 h-100 card-hover">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-25 p-3 rounded-circle me-3">
                                    <i class="fas fa-clock fa-lg text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="card-title text-uppercase text-muted small mb-1">Processing</h6>
                                    <h3 class="mb-0"><?php echo $stats['processing']; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($total_orders > 0): ?>
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mt-5">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="text-uppercase small">
                                        <th scope="col" class="fw-500 ps-4">Order #</th>
                                        <th scope="col" class="fw-500">Date</th>
                                        <th scope="col" class="fw-500">Items</th>
                                        <th scope="col" class="fw-500 text-end">Total</th>
                                        <th scope="col" class="fw-500">Status</th>
                                        <th scope="col" class="fw-500 text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = mysqli_fetch_assoc($orders_result)):
                                        // Count items in this order
                                        $items_query = "SELECT COUNT(*) as item_count FROM order_items 
                                                       WHERE order_id = '{$order['order_id']}'";
                                        $items_result = mysqli_query($conn, $items_query);
                                        $item_count = mysqli_fetch_assoc($items_result)['item_count'];
                                        
                                        // Format order date
                                        $order_date = date('M d, Y', strtotime($order['order_date']));
                                        $order_time = date('h:i A', strtotime($order['order_date']));
                                    ?>
                                        <tr class="border-top">
                                            <td class="ps-4 fw-bold text-primary">#<?php echo $order['order_id']; ?></td>
                                            <td>
                                                <div><?php echo $order_date; ?></div>
                                                <div class="small text-muted"><?php echo $order_time; ?></div>
                                            </td>
                                            <td>
                                                <span class="d-flex align-items-center">
                                                    <i class="fas fa-box-open me-2 text-muted"></i>
                                                    <?php echo $item_count; ?> item<?php echo $item_count != 1 ? 's' : ''; ?>
                                                </span>
                                            </td>
                                            <td class="fw-bold text-end">Rs.<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                switch (strtolower($order['status'])) {
                                                    case 'pending':
                                                        $status_class = 'bg-warning text-dark';
                                                        break;
                                                    case 'processing':
                                                        $status_class = 'bg-primary text-white';
                                                        break;
                                                    case 'packing': // NEW packing status
                                                        $status_class = 'bg-primary text-dark';
                                                        break;
                                                    case 'shipped':
                                                        $status_class = 'bg-info text-dark';
                                                        break;
                                                    case 'delivered':
                                                        $status_class = 'bg-success text-white';
                                                        break;
                                                    case 'cancelled':
                                                        $status_class = 'bg-danger text-white';
                                                        break;
                                                    default:
                                                        $status_class = 'bg-secondary text-white';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge rounded-pill status-badge <?php echo $status_class; ?>">
                                                    <?php echo $order['status']; ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="order_details.php?id=<?php echo $order['order_id']; ?>"
                                                    class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                                <?php if (strtolower($order['status']) == 'delivered'): ?>
                                                    <button class="btn btn-sm btn-outline-success px-3 rounded-pill ms-2 reorder-btn" 
                                                            data-order-id="<?php echo $order['order_id']; ?>">
                                                        <i class="fas fa-redo me-1"></i> Reorder
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    <div class="row g-3">
                        <?php 
                        // Reset pointer for mobile view
                        mysqli_data_seek($orders_result, 0);
                        while ($order = mysqli_fetch_assoc($orders_result)):
                            $items_query = "SELECT COUNT(*) as item_count FROM order_items 
                                           WHERE order_id = '{$order['order_id']}'";
                            $items_result = mysqli_query($conn, $items_query);
                            $item_count = mysqli_fetch_assoc($items_result)['item_count'];
                            
                            $order_date = date('M d, Y', strtotime($order['order_date']));
                            $order_time = date('h:i A', strtotime($order['order_date']));
                            
                            // Status class for mobile cards
                            $status_class = strtolower($order['status']);
                        ?>
                        <div class="col-12">
                            <div class="card order-card <?php echo $status_class; ?> shadow-sm rounded-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-bold text-primary">#<?php echo $order['order_id']; ?></span>
                                        <span class="text-muted small"><?php echo $order_date; ?></span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <span class="d-flex align-items-center text-muted">
                                                <i class="fas fa-box-open me-2"></i>
                                                <?php echo $item_count; ?> item<?php echo $item_count != 1 ? 's' : ''; ?>
                                            </span>
                                        </div>
                                        <div class="fw-bold">Rs.<?php echo number_format($order['total_amount'], 2); ?></div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge rounded-pill status-badge <?php echo $status_class; ?>">
                                            <?php echo $order['status']; ?>
                                        </span>
                                        <div>
                                            <a href="order_details.php?id=<?php echo $order['order_id']; ?>"
                                               class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                            <?php if (strtolower($order['status']) == 'delivered'): ?>
                                                <button class="btn btn-sm btn-outline-success px-3 rounded-pill ms-2 reorder-btn" 
                                                        data-order-id="<?php echo $order['order_id']; ?>">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Order pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php else: ?>
                <!-- No Orders Placeholder -->
                <div class="text-center py-5 my-5">
                    <div class="mb-4">
                        <i class="fas fa-shopping-bag fa-4x text-muted opacity-25"></i>
                    </div>
                    <h3 class="mb-3">No orders yet</h3>
                    <p class="text-muted mb-4">You haven't placed any orders. Start shopping to see your order history here.</p>
                    <a href="shop.php" class="btn btn-primary px-4 py-2 rounded-pill">
                        <i class="fas fa-store me-2"></i> Start Shopping
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Add hover effect to reorder buttons
    document.querySelectorAll('.reorder-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            // You can implement reorder functionality here
            alert('Reorder order #' + orderId);
        });
    });
</script>

<?php include "footer.php"; ?>