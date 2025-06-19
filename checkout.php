<?php
include "header.php";
include "db_connection.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$cart_query = "SELECT p.*, c.cart_qty, c.cart_id 
              FROM tbl_cart c
              JOIN tbl_product p ON c.cart_product_id = p.product_id
              WHERE c.cart_customer_id = '$customer_id'";
$cart_result = mysqli_query($conn, $cart_query);

$total = 0;
$cart_items = [];
while ($item = mysqli_fetch_assoc($cart_result)) {
    $subtotal = $item['product_price'] * $item['cart_qty'];
    $total += $subtotal;
    $cart_items[] = $item;
}

$customer_query = "SELECT * FROM tbl_customer WHERE customer_id = '$customer_id'";
$customer_result = mysqli_query($conn, $customer_query);
$customer = mysqli_fetch_assoc($customer_result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $zip = mysqli_real_escape_string($conn, $_POST['zip']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

    mysqli_begin_transaction($conn);

    try {
        $order_query = "INSERT INTO orders (
                        customer_id, total_amount, name, email, phone, 
                        address, city, state, zip_code, payment_method, order_date, status
                      ) VALUES (
                        '$customer_id', '$total', '$name', '$email', '$phone',
                        '$address', '$city', '$state', '$zip', '$payment_method', NOW(), 'Pending'
                      )";

        if (!mysqli_query($conn, $order_query)) {
            throw new Exception("Error creating order");
        }

        $order_id = mysqli_insert_id($conn);

        foreach ($cart_items as $item) {
            $item_query = "INSERT INTO order_items (
                            order_id, product_id, quantity, price, product_name
                          ) VALUES (
                            '$order_id', '{$item['product_id']}', '{$item['cart_qty']}', 
                            '{$item['product_price']}', '{$item['product_name']}'
                          )";

            if (!mysqli_query($conn, $item_query)) {
                throw new Exception("Error adding order items");
            }
        }

        $clear_cart = "DELETE FROM tbl_cart WHERE cart_customer_id = '$customer_id'";
        if (!mysqli_query($conn, $clear_cart)) {
            throw new Exception("Error clearing cart");
        }

        mysqli_commit($conn);
        
        // Redirect to success page after successful order
        header("Location: order_success.php?order_id=$order_id");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = "Error processing your order: " . $e->getMessage();
    }
}
?>

<div class="container py-5 mt-5">
    <div class="row mt-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Checkout Details</h4>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" id="checkout-form" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <h5 class="mb-3 text-primary"><i class="fas fa-user-circle me-2"></i>Billing Information</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="<?php echo htmlspecialchars($customer['customer_name'] ?? ''); ?>" required>
                                    <div class="invalid-feedback">
                                        Please enter your full name.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?php echo htmlspecialchars($customer['customer_email'] ?? ''); ?>" required>
                                    <div class="invalid-feedback">
                                        Please enter a valid email address.
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="<?php echo htmlspecialchars($customer['customer_phone'] ?? ''); ?>" required>
                                    <div class="invalid-feedback">
                                        Please enter your phone number.
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        value="<?php echo htmlspecialchars($customer['customer_address'] ?? ''); ?>" required>
                                    <div class="invalid-feedback">
                                        Please enter your shipping address.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        value="<?php echo htmlspecialchars($customer['customer_city'] ?? ''); ?>" required>
                                    <div class="invalid-feedback">
                                        Please enter your city.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="state" name="state"
                                        value="<?php echo htmlspecialchars($customer['customer_state'] ?? ''); ?>" required>
                                    <div class="invalid-feedback">
                                        Please enter your state.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="zip" class="form-label">ZIP Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="zip" name="zip"
                                        value="<?php echo htmlspecialchars($customer['customer_zip'] ?? ''); ?>" required>
                                    <div class="invalid-feedback">
                                        Please enter your ZIP code.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="mb-3 text-primary"><i class="fas fa-credit-card me-2"></i>Payment Method</h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-check card p-3 mb-2 border">
                                        <input id="cod" name="payment_method" type="radio" class="form-check-input align-middle mt-1" value="COD" checked required>
                                        <label class="form-check-label d-flex align-items-center" for="cod">
                                            <i class="fas fa-money-bill-wave fs-4 me-3 text-success"></i>
                                            <div>
                                                <span class="d-block fw-bold">Cash on Delivery (COD)</span>
                                                <small class="text-muted">Pay with cash upon delivery</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check card p-3 mb-2 border">
                                        <input id="credit" name="payment_method" type="radio" class="form-check-input align-middle mt-1" value="Credit Card">
                                        <label class="form-check-label d-flex align-items-center" for="credit">
                                            <i class="fab fa-cc-visa fs-4 me-3 text-primary"></i>
                                            <div>
                                                <span class="d-block fw-bold">Credit/Debit Card</span>
                                                <small class="text-muted">Pay using your credit or debit card</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check card p-3 border">
                                        <input id="paypal" name="payment_method" type="radio" class="form-check-input align-middle mt-1" value="PayPal">
                                        <label class="form-check-label d-flex align-items-center" for="paypal">
                                            <i class="fab fa-paypal fs-4 me-3 text-info"></i>
                                            <div>
                                                <span class="d-block fw-bold">PayPal</span>
                                                <small class="text-muted">Pay securely with your PayPal account</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg py-3" type="submit">
                                <i class="fas fa-lock me-2"></i>Place Order Securely
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-success text-white py-3">
                    <h4 class="mb-0"><i class="fas fa-receipt me-2"></i>Order Summary</h4>
                </div>
                <div class="card-body p-4">
                    <h6 class="mb-3 fw-bold">Your Products</h6>
                    <ul class="list-group mb-4">
                        <?php foreach ($cart_items as $item): ?>
                            <li class="list-group-item border-0 px-0 py-3 my-1">
                                <div class="d-flex align-items-center mx-2">
                                    <img src="admin/<?php echo htmlspecialchars($item['product_image'] ?? 'placeholder.jpg'); ?>" 
                                         class="rounded me-3" width="60" height="60" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                    <div class="flex-grow-1">
                                        <h6 class="my-0"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                        <small class="text-muted">Qty: <?php echo $item['cart_qty']; ?></small>
                                    </div>
                                    <span class="fw-bold">Rs.<?php echo number_format($item['product_price'] * $item['cart_qty'], 2); ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <strong>Rs.<?php echo number_format($total, 2); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <strong class="text-success">FREE</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax</span>
                            <strong>%.5.00</strong>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-5 mt-3 pt-2 border-top">
                            <span>Total</span>
                            <strong class="text-primary">Rs.<?php echo number_format($total, 2); ?></strong>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Your personal data will be used to process your order and support your experience throughout this website.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Enhanced form validation
(function () {
    'use strict'
    
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')
    
    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                
                form.classList.add('was-validated')
            }, false)
        })
})();

// Add smooth scrolling and better UX
document.addEventListener('DOMContentLoaded', function() {
    // Highlight empty fields on attempted submission
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        const inputs = this.querySelectorAll('input[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            // Scroll to first invalid field
            const firstInvalid = this.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
        }
    });
    
    // Add animation to payment method selection
    const paymentOptions = document.querySelectorAll('.form-check.card');
    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            paymentOptions.forEach(opt => opt.classList.remove('border-primary'));
            this.classList.add('border-primary');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        });
    });
});
</script>

<?php include "footer.php"; ?>