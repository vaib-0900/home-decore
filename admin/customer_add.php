<?php
include('header.php');
include('sidebar.php');
include('db_connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Customer</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fc;
        }
        #page-wrapper {
            padding: 20px;
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
    </style>
</head>
<body>
  <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        <i class="fas fa-user-plus"></i> Add New Customer
                    </h1>
                    <ol class="breadcrumb">
                        <li><i class="fa fa-dashboard"></i> <a href="index.php">Dashboard</a></li>
                        <li><i class="fas fa-users"></i> <a href="customer_list.php">Customers</a></li>
                        <li class="active"><i class="fas fa-user-plus"></i> Add Customer</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 text-center mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                        </div>
                        <div class="card-body">
                            <?php
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                // Validate and sanitize inputs
                                $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
                                $customer_email = mysqli_real_escape_string($conn, $_POST['customer_email']);
                                $customer_password = mysqli_real_escape_string($conn, $_POST['customer_password']);
                                $customer_phone = mysqli_real_escape_string($conn, $_POST['customer_phone']);
                                $customer_address = mysqli_real_escape_string($conn, $_POST['customer_address']);
                                $customer_landmark = mysqli_real_escape_string($conn, $_POST['customer_landmark']);
                                $customer_status = intval($_POST['customer_status']);

                                // Check if email already exists
                                $check_email = "SELECT customer_id FROM tbl_customer WHERE customer_email = '$customer_email'";
                                $result = mysqli_query($conn, $check_email);
                                
                                if (mysqli_num_rows($result) > 0) {
                                    echo '<div class="alert alert-danger">Email already exists!</div>';
                                } else {
                                    $sql = "INSERT INTO tbl_customer (customer_name, customer_email, customer_password, customer_phone, customer_address, customer_landmark, customer_status) 
                                            VALUES (?, ?, ?, ?, ?, ?, ?)";
                                    
                                    $stmt = mysqli_prepare($conn, $sql);
                                    mysqli_stmt_bind_param($stmt, "ssssssi", $customer_name, $customer_email, $customer_password, $customer_phone, $customer_address, $customer_landmark, $customer_status);
                                    
                                    if (mysqli_stmt_execute($stmt)) {
                                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                                Customer added successfully!
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                              </div>';
                                    } else {
                                        echo '<div class="alert alert-danger">Error: ' . mysqli_error($conn) . '</div>';
                                    }
                                    mysqli_stmt_close($stmt);
                                }
                            }
                            ?>

                            <form action="customer_add.php" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_name" class="form-label">Customer Name *</label>
                                            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_email" class="form-label">Email *</label>
                                            <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_password" class="form-label">Password *</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="customer_password" name="customer_password" required>
                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">Minimum 8 characters</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_phone" class="form-label">Phone *</label>
                                            <input type="tel" class="form-control" id="customer_phone" name="customer_phone" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="customer_address" class="form-label">Address *</label>
                                    <textarea class="form-control" id="customer_address" name="customer_address" rows="2" required></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_landmark" class="form-label">Landmark</label>
                                            <input type="text" class="form-control" id="customer_landmark" name="customer_landmark">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_status" class="form-label">Status *</label>
                                            <select class="form-select" id="customer_status" name="customer_status" required>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-save"></i> Save Customer
                                    </button>
                                    <a href="customer_list.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to List
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
        document.getElementById('togglePassword').addEventListener('click', function() {
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