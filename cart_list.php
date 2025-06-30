<?php
include "header.php";
include "db_connection.php";

// Handle cart updates
if (isset($_POST['update_cart'])) {
  $customer_id = $_SESSION['customer_id'];
  foreach ($_POST['quantity'] as $cart_id => $qty) {
    $cart_id = mysqli_real_escape_string($conn, $cart_id);
    $qty = intval($qty);
    if ($qty > 0) {
      $update_query = "UPDATE tbl_cart SET cart_qty = $qty 
                            WHERE cart_id = $cart_id AND cart_customer_id = '$customer_id'";
      mysqli_query($conn, $update_query);
    } else {
      // Remove item if quantity is 0
      $delete_query = "DELETE FROM tbl_cart   
                           WHERE cart_id = $cart_id AND cart_customer_id = '$customer_id'";
      mysqli_query($conn, $delete_query);
    }
  }
  header("Location: cart_list.php?updated=true");
  exit();
}

// Handle individual quantity updates (plus/minus)
if (isset($_POST['update_qty'])) {
  $customer_id = $_SESSION['customer_id'];
  $cart_id = mysqli_real_escape_string($conn, $_POST['cart_id']);
  $action = $_POST['action'];

  // Get current quantity
  $current_qty_query = "SELECT cart_qty FROM tbl_cart 
                         WHERE cart_id = $cart_id AND cart_customer_id = '$customer_id'";
  $current_qty_result = mysqli_query($conn, $current_qty_query);
  $current_qty = mysqli_fetch_assoc($current_qty_result)['cart_qty'];

  // Calculate new quantity
  $new_qty = $current_qty;
  if ($action == 'increase') {
    $new_qty = $current_qty + 1;
  } elseif ($action == 'decrease' && $current_qty > 1) {
    $new_qty = $current_qty - 1;
  }

  // Update quantity
  $update_query = "UPDATE tbl_cart SET cart_qty = $new_qty 
                    WHERE cart_id = $cart_id AND cart_customer_id = '$customer_id'";
  mysqli_query($conn, $update_query);
}

// Get cart items and calculate totals for active items only
$customer_id = $_SESSION['customer_id'];
$query = "SELECT * FROM tbl_cart 
         INNER JOIN tbl_product ON tbl_product.product_id = tbl_cart.cart_product_id 
         WHERE tbl_cart.cart_customer_id = '$customer_id'";
$result = mysqli_query($conn, $query);

$total = 0;
$active_items_count = 0;
$cart_items = array();

while ($row = mysqli_fetch_array($result)) {
  $cart_items[] = $row;
  if ($row['cart_status'] == 'active') {
    $subtotal = $row['product_price'] * $row['cart_qty'];
    $total += $subtotal;
    $active_items_count++;
  }
}

// Reset the result pointer for display
mysqli_data_seek($result, 0);
?>
<!-- breadcrumb start-->
<section class="breadcrumb breadcrumb_bg">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="breadcrumb_iner">
          <div class="breadcrumb_iner_item">
            <h2>Cart List</h2>
            <p>Home <span>-</span>Cart List</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- breadcrumb start-->
<style>
    .toggle-btn {
        font-size: 10px;
        padding: 5px 10px;
        cursor: pointer;
        border: none;
        border-radius: 5px;
        background-color: #4CAF50;
        color: white;
    }

    .toggle-btn.cross {
        background-color: #f44336;
    }
    
    .inactive-row {
        opacity: 0.6;
        background-color: #f8f9fa;
    }
</style>
<div class="container py-5 mt-3">
  <div class="row">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0">
          <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-shopping-cart me-2 text-primary"></i>Your Shopping Cart</h4>
            <span class="badge bg-primary rounded-pill">
              <?php echo mysqli_num_rows($result); ?> item(s)
            </span>
          </div>
        </div>
        <div class="card-body p-0">
          <?php if (isset($_GET['added'])): ?>
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
              <i class="fas fa-check-circle me-2"></i>Product added to cart successfully!
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
              <i class="fas fa-check-circle me-2"></i>Cart updated successfully!
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <form method="post" action="cart_list.php">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th width="40%" class="ps-4">Product</th>
                    <th width="15%">Price</th>
                    <th width="15%">Quantity</th>
                    <th width="15%">Subtotal</th>
                    <th width="10%">Status</th>
                    <th width="15%" class="pe-4">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (count($cart_items) > 0): ?>
                    <?php foreach ($cart_items as $row): ?>
                      <?php
                      $subtotal = $row['product_price'] * $row['cart_qty'];
                      $row_class = ($row['cart_status'] == 'active') ? '' : 'inactive-row';
                      ?>
                      <tr class="align-middle <?php echo $row_class; ?>">
                        <td class="ps-4">
                          <div class="d-flex align-items-center">
                            <img src="admin/<?php echo htmlspecialchars($row['product_image']); ?>"
                              class="img-thumbnail rounded me-3"
                              width="80"
                              style="object-fit: cover; height: 80px;"
                              alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                            <div>
                              <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($row['product_name']); ?></h6>
                              <small class="text-muted"><?php echo substr(htmlspecialchars($row['product_description']), 0, 50); ?>...</small>
                            </div>
                          </div>
                        </td>
                        <td>
                          <span class="fw-bold text-primary">₹.<?php echo number_format($row['product_price'], 2); ?></span>
                        </td>
                        <td>
                          <form method="post" action="cart_list.php" class="d-inline">
                            <input type="hidden" name="cart_id" value="<?= $row['cart_id'] ?>">
                            <input type="hidden" name="update_qty" value="1">
                            <div class="input-group quantity" style="width: 120px;">
                              <button type="submit" name="action" value="decrease" class="btn btn-sm btn-outline-secondary <?= ($row['cart_qty'] <= 1) ? 'disabled' : '' ?>">
                                <i class="fas fa-minus"></i>
                              </button>
                              <input type="text" readonly class="form-control form-control-sm text-center border-secondary"
                                value="<?= $row['cart_qty'] ?>">
                              <button type="submit" name="action" value="increase" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-plus"></i>
                              </button>
                            </div>
                          </form>
                        </td>
                        <td class="subtotal fw-bold" data-id="<?php echo $row['cart_id']; ?>">
                          ₹.<?php echo number_format($subtotal, 2); ?>
                        </td>
                        <td>
                          <form method="post" action="updateCartStatus.php">
                            <input type="hidden" name="cart_id" value="<?= $row['cart_id'] ?>">
                            <input type="hidden" name="cart_status"
                              value="<?= $row['cart_status'] == 'active' ? 'inactive' : 'active' ?>">

                            <button type="submit"
                              class="toggle-btn <?= $row['cart_status'] == 'active' ? '' : 'cross' ?>">
                              <?= $row['cart_status'] == 'active' ? '✓' : '✕' ?>
                            </button>
                          </form>
                        </td>
                        <td class="pe-4">
                          <form action="remove_cart.php" method="post" class="d-inline">
                            <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle"
                              data-bs-toggle="tooltip" title="Remove item">
                              <i class="fas fa-trash-alt"></i>
                            </button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="6" class="text-center py-5">
                        <div class="py-5">
                          <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                          <h5 class="text-muted">Your cart is empty</h5>
                          <a href="shop.php" class="btn btn-primary mt-3">
                            <i class="fas fa-store me-2"></i>Start Shopping
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

            <?php if (count($cart_items) > 0): ?>
              <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between">
                  <div></div>
                  <a href="shop.php" class="btn btn-outline-primary rounded-pill px-4 ms-auto">
                    <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                  </a>
                </div>
              </div>
            <?php endif; ?>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
        <div class="card-header bg-white py-3 border-0">
          <h4 class="mb-0"><i class="fas fa-receipt me-2 text-success"></i>Order Summary</h4>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Subtotal (<?php echo $active_items_count; ?> items):</span>
            <span id="cart-subtotal" class="fw-bold">₹.<?php echo number_format($total, 2); ?></span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Shipping:</span>
            <span class="text-success">Free</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Tax:</span>
            <span>Rs.0.00</span>
          </div>
          <hr>
          <div class="d-flex justify-content-between mb-3">
            <span class="fw-bold">Total:</span>
            <span id="cart-total" class="fw-bold text-primary fs-5">₹.<?php echo number_format($total, 2); ?></span>
          </div>
        
            <a href="checkout.php" class="btn btn-success w-100 rounded-pill py-2">
              <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
            </a>
         
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>

<script>
  // Initialize Bootstrap tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  });

  // AJAX for status toggle to update totals without page reload
  document.querySelectorAll('form[action="updateCartStatus.php"]').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      fetch(form.action, {
        method: 'POST',
        body: new FormData(form)
      })
      .then(response => response.text())
      .then(() => {
        location.reload(); // Reload to update totals
      });
    });
  });
</script>