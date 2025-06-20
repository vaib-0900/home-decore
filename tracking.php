<?php
include 'header.php';
include 'db_connection.php';

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Handle form submission
$tracking_result = null;
$order_items = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $tracking_query = "SELECT o.*, COUNT(oi.item_id) as item_count 
                      FROM orders o
                      LEFT JOIN order_items oi ON o.order_id = oi.order_id
                      WHERE o.order_id = '$order_id' 
                      AND o.customer_id = '$customer_id'
                      AND o.email = '$email'
                      GROUP BY o.order_id";
    $tracking_result = mysqli_query($conn, $tracking_query);
    
    // Get order items if order exists
    if ($tracking_result && mysqli_num_rows($tracking_result) > 0) {
        $items_query = "SELECT oi.*, p.product_name, p.product_image 
                       FROM order_items oi
                       JOIN tbl_product p ON oi.product_id = p.product_id
                       WHERE oi.order_id = '$order_id'";
        $order_items = mysqli_query($conn, $items_query);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tracking</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .breadcrumb_iner {
            background-color: rgba(255,255,255,0.8);
            padding: 20px;
            border-radius: 10px;
        }
        .tracking_box_inner {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .order-timeline {
            position: relative;
            padding-left: 30px;
            margin-top: 20px;
        }
        .order-timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline-step {
            position: relative;
            margin-bottom: 30px;
        }
        .timeline-step::before {
            content: '';
            position: absolute;
            left: -30px;
            top: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #dee2e6;
            z-index: 2;
        }
        .timeline-step.active::before {
            border-color: #0d6efd;
            background: #0d6efd;
        }
        .timeline-step.completed::before {
            border-color: #198754;
            background: #198754;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        .tracking-card {
            border-left: 4px solid #0d6efd;
        }
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .product-item {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .product-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .status-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .timeline-section {
            flex: 1;
            min-width: 300px;
        }
        .products-section {
            flex: 1;
            min-width: 300px;
        }
        .products-card {
            height: 100%;
        }
    </style>
</head>
<body>
  <!-- breadcrumb start-->
  <section class="breadcrumb breadcrumb_bg" style="background-image: url('img/breadcrumb.png');">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="breadcrumb_iner">
            <div class="breadcrumb_iner_item text-center">
              <h2>Order Tracking</h2>
              <p>Home <span>-</span> Track Your Order</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- breadcrumb end-->

  <!--================Tracking Box Area =================-->
  <section class="tracking_box_area padding_top">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="tracking_box_inner">
            <h3 class="mb-4"><i class="fas fa-truck me-2 text-primary"></i> Track Your Order</h3>
            <p class="mb-4">Enter your Order ID and billing email address to view your order status and tracking information.</p>
            
            <form class="row g-3 tracking_form" method="post">
              <div class="col-md-6">
                <label for="order_id" class="form-label">Order ID</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="fas fa-receipt"></i></span>
                  <input type="text" class="form-control" id="order_id" name="order_id" placeholder="e.g. ORD123456" required>
                </div>
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Billing Email</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope"></i></span>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Your billing email" required>
                </div>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill">
                  <i class="fas fa-search me-1"></i> Track Order
                </button>
              </div>
            </form>
            
            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
              <div class="mt-5">
                <?php if ($tracking_result && mysqli_num_rows($tracking_result) > 0): 
                  $order = mysqli_fetch_assoc($tracking_result);
                  $order_date = date('M d, Y', strtotime($order['order_date']));
                  $order_time = date('h:i A', strtotime($order['order_date']));
                  
                  // Determine status class
                  $status_class = '';
                  switch (strtolower($order['status'])) {
                      case 'pending':
                          $status_class = 'bg-warning text-dark';
                          break;
                      case 'processing':
                          $status_class = 'bg-primary text-white';
                          break;
                      case 'packing':
                          $status_class = 'bg-primary text-white';
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
                  <div class="card tracking-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                        <div class="mb-3 mb-md-0">
                          <h5 class="card-title mb-1">Order #<?php echo $order['order_id']; ?></h5>
                          <p class="text-muted small mb-0">
                            <i class="far fa-calendar-alt me-1"></i> <?php echo $order_date; ?> at <?php echo $order_time; ?>
                          </p>
                        </div>
                        <div>
                          <span class="badge rounded-pill status-badge <?php echo $status_class; ?>">
                            <?php echo $order['status']; ?>
                          </span>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                          <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-box-open text-muted me-2"></i>
                            <span><?php echo $order['item_count']; ?> Item<?php echo $order['item_count'] != 1 ? 's' : ''; ?></span>
                          </div>
                          <div class="d-flex align-items-center">
                            <i class="fas fa-money-bill-wave text-muted me-2"></i>
                            <span>Total: Rs.<?php echo number_format($order['total_amount'], 2); ?></span>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <h6 class="mb-2">Delivery Address</h6>
                          <address class="small text-muted mb-0">
                            <?php echo $order['address']; ?>
                          </address>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <h5 class="mb-3">Order Status & Products</h5>
                  <div class="status-container">
                    <div class="timeline-section">
                      <div class="order-timeline">
                        <?php 
                        // Define all possible status steps
                        $status_steps = [
                            'pending' => 'Order Placed',
                            'processing' => 'Processing',
                            'packing' => 'Packing',
                            'shipped' => 'Shipped',
                            'delivered' => 'Delivered'
                        ];
                        
                        $current_status = strtolower($order['status']);
                        $found_current = false;
                        
                        foreach ($status_steps as $status => $label): 
                            $step_class = '';
                            if ($status == $current_status) {
                                $step_class = 'active';
                                $found_current = true;
                            } elseif ($found_current) {
                                $step_class = '';
                            } else {
                                $step_class = 'completed';
                            }
                        ?>
                          <div class="timeline-step <?php echo $step_class; ?>">
                            <h6><?php echo $label; ?></h6>
                            <?php if ($step_class == 'completed'): ?>
                              <p class="small text-muted mb-0">Completed on <?php echo date('M d, Y', strtotime($order['order_date'] . ' + ' . array_search($status, array_keys($status_steps)) . ' days')); ?></p>
                            <?php elseif ($step_class == 'active'): ?>
                              <p class="small text-primary mb-0">Currently at this step</p>
                            <?php else: ?>
                              <p class="small text-muted mb-0">Pending</p>
                            <?php endif; ?>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                    
                    <div class="products-section">
                      <div class="card products-card border-0 shadow-sm">
                        <div class="card-body">
                          <h6 class="card-title mb-3"><i class="fas fa-shopping-bag me-2"></i>Ordered Products</h6>
                          <?php if ($order_items && mysqli_num_rows($order_items) > 0): ?>
                            <div class="product-list">
                              <?php while ($item = mysqli_fetch_assoc($order_items)): ?>
                                <div class="product-item d-flex">
                                  <div class="flex-shrink-0">
                                    <img src="admin/<?php echo $item['product_image']; ?>" alt="<?php echo $item['product_name']; ?>" class="product-img">
                                  </div>
                                  <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1"><?php echo $item['product_name']; ?></h6>
                                    <div class="d-flex justify-content-between">
                                      <span class="text-muted">Qty: <?php echo $item['quantity']; ?></span>
                                      <span class="fw-bold">Rs.<?php echo number_format($item['price'], 2); ?></span>
                                    </div>
                                    <?php if ($item['quantity'] > 1): ?>
                                      <div class="small text-muted">
                                        Rs.<?php echo number_format($item['price'] * $item['quantity'], 2); ?> total
                                      </div>
                                    <?php endif; ?>
                                  </div>
                                </div>
                              <?php endwhile; ?>
                            </div>
                          <?php else: ?>
                            <p class="text-muted">No products found for this order.</p>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="mt-4">
                    <a href="order_details.php?id=<?php echo $order['order_id']; ?>" class="btn btn-outline-primary px-4 rounded-pill">
                      <i class="fas fa-file-alt me-1"></i> View Order Details
                    </a>
                  </div>
                <?php else: ?>
                  <div class="alert alert-danger mt-4">
                    <i class="fas fa-exclamation-circle me-2"></i> No order found with the provided details. Please check your Order ID and email address.
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--================End Tracking Box Area =================-->

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
include 'footer.php';
?>