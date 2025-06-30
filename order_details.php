<?php
include "header.php";
include "db_connection.php";

// Check if order ID exists and customer is logged in
if (!isset($_GET['id']) || !isset($_SESSION['customer_id'])) {
    header("Location: order_history.php");
    exit();
}

$order_id = mysqli_real_escape_string($conn, $_GET['id']);
$customer_id = $_SESSION['customer_id'];

// Get order details with prepared statement
$order_query = "SELECT * FROM orders WHERE order_id = ? AND customer_id = ?";
$stmt = mysqli_prepare($conn, $order_query);
mysqli_stmt_bind_param($stmt, "si", $order_id, $customer_id);
mysqli_stmt_execute($stmt);
$order_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($order_result) == 0) {
    header("Location: order_history.php");
    exit();
}

$order = mysqli_fetch_assoc($order_result);

// Get order items
$items_query = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = mysqli_prepare($conn, $items_query);
mysqli_stmt_bind_param($stmt, "s", $order_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);

// Calculate item count and subtotal
$item_count = mysqli_num_rows($items_result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $order_id; ?> - Your Store</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f5f7fb;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e3e6f0;
            padding: 1.25rem 1.5rem;
        }

        .order-status-badge {
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 0.5rem 1rem;
            border-radius: 0.35rem;
        }

        /* Progress Tracker */
        .progress-tracker {
            position: relative;
            padding: 0;
            margin: 2rem auto;
            max-width: 100%;
            overflow-x: auto;
        }

        .step-progress {
            display: flex;
            justify-content: space-between;
            list-style: none;
            padding: 0;
            margin: 0 0 1rem;
            position: relative;
        }

        .step-progress::before {
            content: "";
            position: absolute;
            top: 24px;
            left: 0;
            width: 100%;
            height: 4px;
            background-color: #e9ecef;
            z-index: 1;
            border-radius: 2px;
        }

        .step-progress__step {
            position: relative;
            flex: 1;
            text-align: center;
            z-index: 2;
            padding: 0 5px;
        }

        .step-progress__step:last-child {
            flex: 0;
        }

        .step-progress__step-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            margin: 0 auto 8px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            font-size: 1.25rem;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .step-progress__step-title {
            display: block;
            font-size: 0.875rem;
            color: #6c757d;
            white-space: nowrap;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        /* Active step */
        .step-progress__step.is-active .step-progress__step-icon {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 0 0 6px rgba(78, 115, 223, 0.2);
        }

        .step-progress__step.is-active .step-progress__step-title {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Completed steps */
        .step-progress__step.is-complete .step-progress__step-icon {
            background-color: var(--success-color);
            color: white;
        }

        .step-progress__step.is-complete .step-progress__step-title {
            color: var(--success-color);
        }

        /* Cancelled state */
        .step-progress__step.is-cancelled .step-progress__step-icon {
            background-color: var(--danger-color);
            color: white;
        }

        .step-progress__step.is-cancelled .step-progress__step-title {
            color: var(--danger-color);
        }

        /* Progress bar */
        .step-progress__bar {
            position: absolute;
            top: 24px;
            left: 0;
            height: 4px;
            background-color: var(--success-color);
            z-index: 1;
            transition: width 0.3s ease;
            border-radius: 2px;
        }

        /* Table styles */
        .table thead th {
            background-color: #f8f9fc;
            color: #4e73df;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e3e6f0;
        }

        .table td {
            vertical-align: middle;
            border-top: 1px solid #e3e6f0;
        }

        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.35rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .step-progress {
                min-width: 500px;
            }

            .step-progress__step-title {
                font-size: 0.75rem;
            }

            .step-progress__step-icon {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }

            .step-progress::before,
            .step-progress__bar {
                top: 18px;
            }

            .card-body {
                padding: 1rem;
            }
        }

        .btn-rounded {
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
        }

        .bg-light-custom {
            background-color: #f8f9fc;
        }

        .text-primary-custom {
            color: var(--primary-color);
        }

        .border-left-primary {
            border-left: 0.25rem solid var(--primary-color) !important;
        }

        .border-left-success {
            border-left: 0.25rem solid var(--success-color) !important;
        }
    </style>
</head>

<body>
    <div class="container py-5 mt-5">
        <div class="row mb-4 mt-5">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item"><a href="order_history.php" class="text-decoration-none">My Orders</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Order #<?php echo $order_id; ?></li>
                    </ol>
                </nav>
                <h2 class="h3 mb-0 text-gray-800">Order Details</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- Order Summary Card -->
                <div class="card border-left-primary shadow-sm mt-3">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="h4 mb-1"><i class="fas fa-receipt text-primary me-2"></i> Order #<?php echo $order_id; ?></h3>
                                <p class="text-muted small mb-0">Placed on <?php echo date('F j, Y \a\t g:i A', strtotime($order['order_date'])); ?></p>
                            </div>
                            <span class="order-status-badge bg-<?php
                                                                switch ($order['status']) {
                                                                    case 'Pending':
                                                                        echo 'warning';
                                                                        break;
                                                                    case 'Packing':  // Changed from 'Completed'
                                                                        echo 'info';  // Changed color to info (blue)
                                                                        break;
                                                                    case 'Processing':
                                                                        echo 'primary';
                                                                        break;
                                                                    case 'Shipped':
                                                                        echo 'info';
                                                                        break;
                                                                    case 'Delivered':
                                                                        echo 'success';
                                                                        break;
                                                                    case 'Cancelled':
                                                                        echo 'danger';
                                                                        break;
                                                                    default:
                                                                        echo 'secondary';
                                                                }
                                                                ?> text-white">
                                <?php echo $order['status']; ?>
                            </span>

                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Enhanced Progress Tracker -->
                        <!-- Enhanced Progress Tracker -->
                       <div class="progress-tracker">
    <h6 class="text-uppercase small fw-bold mb-3 text-gray-600">Order Status</h6>
    <ul class="step-progress">
        <!-- Progress line -->
        <div class="step-progress__bar" style="width: <?php 
            if ($order['status'] == 'Pending') echo '20%';
            elseif ($order['status'] == 'Processing') echo '40%';
            elseif ($order['status'] == 'Packing') echo '60%';
            elseif ($order['status'] == 'Shipped') echo '80%';
            elseif ($order['status'] == 'Delivered') echo '100%';
            elseif ($order['status'] == 'Cancelled') echo '120%';
            else echo '0%';
        ?>;"></div>
        
        <!-- Steps -->
        <li class="step-progress__step <?php echo (($order['status'] == 'Pending') ? 'is-active' : (in_array($order['status'], ['Processing', 'Packing', 'Shipped', 'Delivered', 'Cancelled']))) ? 'is-complete' : ''; ?>">
            <span class="step-progress__step-title">Pending</span>
            <span class="step-progress__step-icon">
                <i class="fas fa-hourglass-start"></i>
            </span>
        </li>
        <li class="step-progress__step <?php echo (($order['status'] == 'Processing') ? 'is-active' : (in_array($order['status'], ['Packing', 'Shipped', 'Delivered', 'Cancelled']))) ? 'is-complete' : ''; ?>">
            <span class="step-progress__step-title">Processing</span>
            <span class="step-progress__step-icon">
                <i class="fas fa-clipboard-check"></i>
            </span>
        </li>
        <li class="step-progress__step <?php echo (($order['status'] == 'Packing') ? 'is-active' : (in_array($order['status'], ['Shipped', 'Delivered', 'Cancelled']))) ? 'is-complete' : ''; ?>">
            <span class="step-progress__step-title">Packing</span>
            <span class="step-progress__step-icon">
                <i class="fas fa-box"></i>
            </span>
        </li>
        <li class="step-progress__step <?php echo (($order['status'] == 'Shipped') ? 'is-active' : (in_array($order['status'], ['Delivered', 'Cancelled']))) ? 'is-complete' : ''; ?>">
            <span class="step-progress__step-title">Shipped</span>
            <span class="step-progress__step-icon">
                <i class="fas fa-shipping-fast"></i>
            </span>
        </li>
        <li class="step-progress__step <?php echo ((($order['status'] == 'Delivered') ? 'is-active' : ($order['status'] == 'Cancelled'))) ? 'is-complete' : ''; ?>">
            <span class="step-progress__step-title">Delivered</span>
            <span class="step-progress__step-icon">
                <i class="fas fa-box-open"></i>
            </span>
        </li>
        <li class="step-progress__step <?php echo ($order['status'] == 'Cancelled') ? 'is-active is-cancelled' : ''; ?>">
            <span class="step-progress__step-title">Cancelled</span>
            <span class="step-progress__step-icon">
                <i class="fas fa-times-circle"></i>
            </span>
        </li>
    </ul>
</div>

<style>
    .progress-tracker {
        position: relative;
        padding: 20px 0;
    }
    
    .step-progress {
        display: flex;
        justify-content: space-between;
        list-style: none;
        padding: 0;
        margin: 0 0 1rem;
        position: relative;
    }
    
    .step-progress::before {
        content: "";
        position: absolute;
        top: 24px;
        left: 0;
        width: 100%;
        height: 4px;
        background-color: #e9ecef;
        z-index: 1;
        border-radius: 2px;
    }
    
    .step-progress__bar {
        position: absolute;
        top: 24px;
        left: 0;
        height: 4px;
        background-color: #4e73df;
        z-index: 2;
        transition: width 0.5s ease;
        border-radius: 2px;
    }
    
    .step-progress__step {
        position: relative;
        flex: 1;
        text-align: center;
        z-index: 2;
    }
    
    .step-progress__step:last-child {
        flex: 0;
    }
    
    .step-progress__step-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        margin: 0 auto 8px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        font-size: 1.25rem;
        position: relative;
        z-index: 2;
        transition: all 0.3s ease;
    }
    
    .step-progress__step-title {
        display: block;
        font-size: 0.875rem;
        color: #6c757d;
        white-space: nowrap;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    /* Active step */
    .step-progress__step.is-active .step-progress__step-icon {
        background-color: #4e73df;
        color: white;
        box-shadow: 0 0 0 6px rgba(78, 115, 223, 0.2);
    }
    
    .step-progress__step.is-active .step-progress__step-title {
        color: #4e73df;
        font-weight: 600;
    }
    
    /* Completed steps */
    .step-progress__step.is-complete .step-progress__step-icon {
        background-color: #1cc88a;
        color: white;
    }
    
    .step-progress__step.is-complete .step-progress__step-title {
        color: #1cc88a;
    }
    
    /* Cancelled state */
    .step-progress__step.is-cancelled .step-progress__step-icon {
        background-color: #e74a3b;
        color: white;
    }
    
    .step-progress__step.is-cancelled .step-progress__step-title {
        color: #e74a3b;
    }
</style>

<script>
    // Optional: Add smooth animation to the progress bar
    document.addEventListener('DOMContentLoaded', function() {
        const progressBar = document.querySelector('.step-progress__bar');
        // Trigger the animation by resetting the width
        setTimeout(() => {
            progressBar.style.width = progressBar.style.width;
        }, 100);
    });
</script>


                        <!-- Order Items -->
                        <div class="mb-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0"><i class="fas fa-box-open text-gray-500 me-2"></i> Order Items (<?php echo $item_count; ?>)</h5>
                                <span class="badge bg-light text-dark">Order Total: ₹.<?php echo number_format($order['total_amount'], 2); ?></span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="bg-light-custom">
                                        <tr>
                                            <th style="width: 80px">Item</th>
                                            <th>Product</th>
                                            <th class="text-end">Price</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                                            <tr>
                                                <td>
                                                    <?php if (!empty($item['image'])): ?>
                                                        <img src="admin/upload/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-img">
                                                    <?php else: ?>
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center product-img">
                                                            <i class="fas fa-box-open text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                                    <p class="text-muted small mb-0">SKU: <?php echo htmlspecialchars($item['product_id']); ?></p>
                                                </td>
                                                <td class="text-end">₹.<?php echo number_format($item['price'], 2); ?></td>
                                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                                <td class="text-end fw-bold">₹.<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="row mt-4">
                            <div class="col-lg-6 mb-4">
                                <div class="card border-left-success h-100">
                                    <div class="card-body">
                                        <h5 class="card-title text-gray-800"><i class="fas fa-truck text-gray-500 me-2"></i> Shipping Information</h5>
                                        <hr>
                                        <address class="mb-0">
                                            <strong><?php echo htmlspecialchars($order['name']); ?></strong><br>
                                            <?php echo htmlspecialchars($order['address']); ?><br>
                                            <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> <?php echo htmlspecialchars($order['zip_code']); ?><br>
                                            <abbr title="Phone" class="text-gray-600">Phone:</abbr> <?php echo htmlspecialchars($order['phone']); ?><br>
                                            <abbr title="Email" class="text-gray-600">Email:</abbr> <?php echo htmlspecialchars($order['email']); ?>
                                        </address>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card border-left-primary h-100">
                                    <div class="card-body">
                                        <h5 class="card-title text-gray-800"><i class="fas fa-file-invoice-dollar text-gray-500 me-2"></i> Payment Summary</h5>
                                        <hr>
                                        <table class="table table-sm mb-0">
                                            <tr>
                                                <td class="text-gray-600">Subtotal (<?php echo $item_count; ?> items)</td>
                                                <td class="text-end">₹.<?php echo number_format($order['total_amount'], 2); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-600">Shipping Fee</td>
                                                <td class="text-end">Free</td>
                                            </tr>
                                            <tr class="fw-bold">
                                                <td class="text-gray-800">Total</td>
                                                <td class="text-end text-primary">₹.<?php echo number_format($order['total_amount'], 2); ?></td>
                                            </tr>
                                        </table>
                                        <hr>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-info-circle text-gray-500 me-2"></i>
                                            <p class="small text-muted mb-0">
                                                Payment Method: <span class="fw-bold"><?php echo htmlspecialchars($order['payment_method']); ?></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-5">
                            <a href="confirmation.php" class="btn btn-outline-secondary btn-rounded">
                                <i class="fas fa-arrow-left me-1"></i> Back to Orders
                            </a>
                            <div>
                                <?php if ($order['status'] == 'Completed'): ?>
                                    <button class="btn btn-success btn-rounded me-2">
                                        <i class="fas fa-redo me-1"></i> Reorder
                                    </button>
                                <?php endif; ?>
                                <?php if (in_array($order['status'], ['Completed', 'Shipped', 'Delivered'])): ?>
                                    <form action="print_invoice.php" method="get" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($order_id); ?>">
                                        <button type="submit" class="btn btn-primary btn-rounded">
                                            <i class="fas fa-file-download me-1"></i> View Invoice
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    <?php include "footer.php"; ?>
</body>

</html>