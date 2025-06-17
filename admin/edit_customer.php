<?php
include('header.php');
include('sidebar.php');
include('db_connection.php');

// Initialize variables
$customer = [];
$errors = [];

// Get customer data if ID is provided
if (isset($_GET['id'])) {
    $customer_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM tbl_customer WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
    $stmt->close();
    
    if (!$customer) {
        die("<div class='alert alert-danger'>Customer not found</div>");
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $customer_id = intval($_POST['customer_id']);
    $customer_name = htmlspecialchars(trim($_POST['customer_name']));
    $customer_email = filter_var(trim($_POST['customer_email']), FILTER_SANITIZE_EMAIL);
    $customer_phone = htmlspecialchars(trim($_POST['customer_phone']));
    $customer_address = htmlspecialchars(trim($_POST['customer_address']));
    $customer_landmark = htmlspecialchars(trim($_POST['customer_landmark']));
    $customer_status = intval($_POST['customer_status']);
    
    // Basic validation
    if (empty($customer_name)) {
        $errors[] = "Customer name is required";
    }
    if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Only update password if it was changed
    $password_update = "";
    if (!empty($_POST['customer_password'])) {
        $customer_password = password_hash($_POST['customer_password'], PASSWORD_DEFAULT);
        $password_update = ", customer_password = ?";
    }
    
    if (empty($errors)) {
        // Check if email already exists for another customer
        $check_stmt = $conn->prepare("SELECT customer_id FROM tbl_customer WHERE customer_email = ? AND customer_id != ?");
        $check_stmt->bind_param("si", $customer_email, $customer_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $errors[] = "Email already exists for another customer";
        } else {
            // Prepare the update query
            if (empty($password_update)) {
                $query = "UPDATE tbl_customer SET 
                          customer_name = ?, 
                          customer_email = ?, 
                          customer_phone = ?, 
                          customer_address = ?, 
                          customer_landmark = ?, 
                          customer_status = ? 
                          WHERE customer_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssssii", 
                    $customer_name, 
                    $customer_email, 
                    $customer_phone, 
                    $customer_address, 
                    $customer_landmark, 
                    $customer_status, 
                    $customer_id
                );
            } else {
                $query = "UPDATE tbl_customer SET 
                          customer_name = ?, 
                          customer_email = ?, 
                          customer_password = ?, 
                          customer_phone = ?, 
                          customer_address = ?, 
                          customer_landmark = ?, 
                          customer_status = ? 
                          WHERE customer_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssssii", 
                    $customer_name, 
                    $customer_email, 
                    $customer_password, 
                    $customer_phone, 
                    $customer_address, 
                    $customer_landmark, 
                    $customer_status, 
                    $customer_id
                );
            }
            
         
            $stmt->close();
        }
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fc;
        }
        .card {
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.35rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .password-toggle {
            cursor: pointer;
        }
    </style>
</head>
<body>
     <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        <i class="fas fa-user-edit"></i> Edit Customer
                    </h1>
                    <ol class="breadcrumb">
                        <li><i class="fa fa-dashboard"></i> <a href="index.php">Dashboard</a></li>
                        <li><i class="fas fa-users"></i> <a href="customer_list.php">Customers</a></li>
                        <li class="active"><i class="fas fa-user-edit"></i> Edit Customer</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 text-center mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Customer Details</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul>
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo $error; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form action="" method="POST">
                                <input type="hidden" name="customer_id" value="<?php echo $customer['customer_id']; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_name" class="form-label">Customer Name *</label>
                                            <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                                   value="<?php echo htmlspecialchars($customer['customer_name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_email" class="form-label">Email *</label>
                                            <input type="email" class="form-control" id="customer_email" name="customer_email" 
                                                   value="<?php echo htmlspecialchars($customer['customer_email']); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_password" class="form-label">Password (leave blank to keep current)</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="customer_password" name="customer_password">
                                                <button class="btn btn-outline-secondary password-toggle" type="button">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">Minimum 8 characters</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_phone" class="form-label">Phone *</label>
                                            <input type="tel" class="form-control" id="customer_phone" name="customer_phone" 
                                                   value="<?php echo htmlspecialchars($customer['customer_phone']); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="customer_address" class="form-label">Address *</label>
                                    <textarea class="form-control" id="customer_address" name="customer_address" rows="2" required><?php 
                                        echo htmlspecialchars($customer['customer_address']); ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_landmark" class="form-label">Landmark</label>
                                            <input type="text" class="form-control" id="customer_landmark" name="customer_landmark" 
                                                   value="<?php echo htmlspecialchars($customer['customer_landmark']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_status" class="form-label">Status *</label>
                                            <select class="form-select" id="customer_status" name="customer_status" required>
                                                <option value="1" <?php echo ($customer['customer_status'] == 1) ? 'selected' : ''; ?>>Active</option>
                                                <option value="0" <?php echo ($customer['customer_status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-save"></i> Update Customer
                                    </button>
                                    <a href="customer_list.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.querySelector('.password-toggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('customer_password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>

<?php
include('footer.php');
?>