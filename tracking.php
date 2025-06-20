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
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .breadcrumb-area {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 80px 0;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .breadcrumb-area::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('img/dots-pattern.png');
            opacity: 0.1;
        }
        
        .breadcrumb-content {
            position: relative;
            z-index: 1;
        }
        
        .tracking-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        
        .tracking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .tracking-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
        }
        
        .order-timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .order-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #4cc9f0, #4361ee, #3a0ca3);
            border-radius: 3px;
        }
        
        .timeline-step {
            position: relative;
            margin-bottom: 25px;
            padding: 15px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
        }
        
        .timeline-step::before {
            content: '';
            position: absolute;
            left: -36px;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #fff;
            border: 4px solid #dee2e6;
            z-index: 2;
        }
        
        .timeline-step.active::before {
            border-color: var(--primary-color);
            background: var(--primary-color);
            box-shadow: 0 0 0 5px rgba(67, 97, 238, 0.2);
        }
        
        .timeline-step.completed::before {
            border-color: var(--success-color);
            background: var(--success-color);
        }
        
        .timeline-step.completed .timeline-content {
            opacity: 0.8;
        }
        
        .timeline-content h6 {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .timeline-content p {
            font-size: 0.85rem;
            margin-bottom: 0;
        }
        
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #eee;
        }
        
        .product-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 15px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease;
        }
        
        .product-item:hover {
            transform: translateY(-3px);
        }
        
        .product-info {
            flex: 1;
            padding-left: 15px;
        }
        
        .product-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .product-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
        }
        
        .product-price {
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.5em 1em;
            border-radius: 50px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
            border-color: var(--primary-color);
        }
        
        .input-group-text {
            background-color: white;
            border-right: none;
            border-radius: 10px 0 0 10px !important;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0 !important;
        }
        
        @media (max-width: 768px) {
            .status-container {
                flex-direction: column;
            }
            
            .timeline-section, .products-section {
                width: 100%;
            }
            
            .order-timeline::before {
                left: 12px;
            }
            
            .timeline-step::before {
                left: -30px;
            }
        }
    </style>
</head>
<body>
  <!-- breadcrumb start-->

     <section class="breadcrumb breadcrumb_bg">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="breadcrumb_iner">
           
          <div class="breadcrumb_iner_item">
            <h1 class="mb-3">Order Tracking</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="index.php">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Track Your Order</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- breadcrumb end-->

  <!--================Tracking Box Area =================-->
  <section class="tracking_box_area py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="tracking-card mb-5">
            <div class="card-body p-4">
              <h3 class="mb-4"><i class="fas fa-truck me-2"></i> Track Your Order</h3>
              <p class="text-muted mb-4">Enter your Order ID and billing email address to view your order status and tracking information.</p>
              
              <form class="row g-3 tracking_form" method="post">
                <div class="col-md-6">
                  <label for="order_id" class="form-label">Order ID</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-receipt"></i></span>
                    <input type="text" class="form-control" id="order_id" name="order_id" placeholder="e.g. ORD123456" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label">Billing Email</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Your billing email" required>
                  </div>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i> Track Order
                  </button>
                </div>
              </form>
            </div>
          </div>
            
          <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <div class="tracking-result">
              <?php if ($tracking_result && mysqli_num_rows($tracking_result) > 0): 
                $order = mysqli_fetch_assoc($tracking_result);
                $order_date = date('M d, Y', strtotime($order['order_date']));
                $order_time = date('h:i A', strtotime($order['order_date']));
                
                // Determine status class
                $status_class = '';
                switch (strtolower($order['status'])) {
                    case 'pending':
                        $status_class = 'bg-warning';
                        break;
                    case 'processing':
                        $status_class = 'bg-primary';
                        break;
                    case 'packing':
                        $status_class = 'bg-primary';
                        break;
                    case 'shipped':
                        $status_class = 'bg-info';
                        break;
                    case 'delivered':
                        $status_class = 'bg-success';
                        break;
                    case 'cancelled':
                        $status_class = 'bg-danger';
                        break;
                    default:
                        $status_class = 'bg-secondary';
                        break;
                }
              ?>
                <div class="tracking-card mb-4">
                  <div class="tracking-header">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                      <div class="mb-3 mb-md-0">
                        <h4 class="mb-1">Order #<?php echo $order['order_id']; ?></h4>
                        <p class="mb-0">
                          <i class="far fa-calendar-alt me-1"></i> <?php echo $order_date; ?> at <?php echo $order_time; ?>
                        </p>
                      </div>
                      <div>
                        <span class="status-badge <?php echo $status_class; ?>">
                          <?php echo $order['status']; ?>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="card-body p-4">
                    <div class="row">
                      <div class="col-md-6 mb-3 mb-md-0">
                        <div class="d-flex align-items-center mb-3">
                          <div class="bg-light p-3 rounded-circle me-3">
                            <i class="fas fa-box-open text-primary"></i>
                          </div>
                          <div>
                            <h6 class="mb-0"><?php echo $order['item_count']; ?> Item<?php echo $order['item_count'] != 1 ? 's' : ''; ?></h6>
                            <p class="text-muted small mb-0">Total Items</p>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                          <div class="bg-light p-3 rounded-circle me-3">
                            <i class="fas fa-money-bill-wave text-primary"></i>
                          </div>
                          <div>
                            <h6 class="mb-0">Rs.<?php echo number_format($order['total_amount'], 2); ?></h6>
                            <p class="text-muted small mb-0">Total Amount</p>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="mt-4">
                      <h5 class="mb-3"><i class="fas fa-map-marker-alt me-2 text-primary"></i> Delivery Address</h5>
                      <div class="bg-light p-3 rounded">
                        <p class="mb-0"><?php echo $order['address']; ?></p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <h4 class="mb-4"><i class="fas fa-clipboard-list me-2 text-primary"></i> Order Status & Products</h4>
                <div class="row">
                  <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="tracking-card h-100">
                      <div class="card-body p-4">
                        <h5 class="mb-3"><i class="fas fa-tasks me-2 text-primary"></i> Order Status</h5>
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
                              <div class="timeline-content">
                                <h6><?php echo $label; ?></h6>
                                <?php if ($step_class == 'completed'): ?>
                                  <p class="text-muted mb-0">Completed on <?php echo date('M d, Y', strtotime($order['order_date'] . ' + ' . array_search($status, array_keys($status_steps)) . ' days')); ?></p>
                                <?php elseif ($step_class == 'active'): ?>
                                  <p class="text-primary mb-0">Currently at this step</p>
                                <?php else: ?>
                                  <p class="text-muted mb-0">Pending</p>
                                <?php endif; ?>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-lg-6">
                    <div class="tracking-card h-100">
                      <div class="card-body p-4">
                        <h5 class="mb-3"><i class="fas fa-shopping-bag me-2 text-primary"></i> Ordered Products</h5>
                        <?php if ($order_items && mysqli_num_rows($order_items) > 0): ?>
                          <div class="product-list">
                            <?php while ($item = mysqli_fetch_assoc($order_items)): ?>
                              <div class="product-item">
                                <img src="admin/<?php echo $item['product_image']; ?>" alt="<?php echo $item['product_name']; ?>" class="product-img">
                                <div class="product-info">
                                  <h6 class="product-title"><?php echo $item['product_name']; ?></h6>
                                  <div class="product-meta">
                                    <span class="text-muted">Qty: <?php echo $item['quantity']; ?></span>
                                    <span class="product-price">Rs.<?php echo number_format($item['price'], 2); ?></span>
                                  </div>
                                  <?php if ($item['quantity'] > 1): ?>
                                    <div class="small text-muted mt-1">
                                      Rs.<?php echo number_format($item['price'] * $item['quantity'], 2); ?> total
                                    </div>
                                  <?php endif; ?>
                                </div>
                              </div>
                            <?php endwhile; ?>
                          </div>
                        <?php else: ?>
                          <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No products found for this order.</p>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="text-center mt-4">
                  <a href="order_details.php?id=<?php echo $order['order_id']; ?>" class="btn btn-outline-primary me-2">
                    <i class="fas fa-file-alt me-1"></i> View Order Details
                  </a>
                  <?php if (strtolower($order['status']) == 'delivered'): ?>
                    <button class="btn btn-primary reorder-btn" data-order-id="<?php echo $order['order_id']; ?>">
                      <i class="fas fa-redo me-1"></i> Reorder Items
                    </button>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <div class="tracking-card">
                  <div class="card-body p-4 text-center">
                    <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                    <h4 class="mb-3">Order Not Found</h4>
                    <p class="text-muted mb-4">No order found with the provided details. Please check your Order ID and email address.</p>
                    <button class="btn btn-primary" onclick="window.history.back();">
                      <i class="fas fa-arrow-left me-1"></i> Try Again
                    </button>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
  <!--================End Tracking Box Area =================-->

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
</body>
</html>

<?php
include 'footer.php';
?>