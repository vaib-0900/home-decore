<?php
include "header.php";

// Initialize variables
$errors = [];
$success = false;

// Fetch current customer data
include "db_connection.php";
$customer_id = $_SESSION['customer_id'];
$query = "SELECT * FROM tbl_customer WHERE customer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors['current_password'] = 'Current password is required to change password';
        } elseif (!password_verify($current_password, $customer['customer_password'])) {
            $errors['current_password'] = 'Current password is incorrect';
        }

        if ($new_password !== $confirm_password) {
            $errors['confirm_password'] = 'Passwords do not match';
        } elseif (strlen($new_password) < 8) {
            $errors['new_password'] = 'Password must be at least 8 characters';
        }
    }

    // Update if no errors
    if (empty($errors)) {
        try {
            $conn->begin_transaction();
            
            // Prepare base query
            $query = "UPDATE tbl_customer SET customer_name = ?, customer_email = ?, customer_phone = ?";
            $params = [$name, $email, $phone];
            $types = "sss";
            
            // Add password if changing
            if (!empty($new_password)) {
                $query .= ", customer_password = ?";
                $params[] = password_hash($new_password, PASSWORD_DEFAULT);
                $types .= "s";
            }
            
            $query .= " WHERE customer_id = ?";
            $params[] = $customer_id;
            $types .= "i";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            
            $conn->commit();
            $success = true;
            
            // Refresh customer data
            $stmt = $conn->prepare("SELECT * FROM tbl_customer WHERE customer_id = ?");
            $stmt->bind_param("i", $customer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $customer = $result->fetch_assoc();
            
        } catch (Exception $e) {
            $conn->rollback();
            $errors['database'] = 'Error updating profile: ' . $e->getMessage();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .profile-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1.5rem;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
        }
        .form-control {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #764ba2;
            box-shadow: 0 0 0 0.25rem rgba(118, 75, 162, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd1 0%, #6a4199 100%);
        }
        .btn-outline-secondary {
            border-radius: 8px;
            padding: 10px 25px;
        }
        .password-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        .alert {
            border-radius: 8px;
        }
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.85rem;
        }
        .is-invalid {
            border-color: #dc3545;
        }
    </style>
</head>
<body>
<div class="container py-5 mt-5">
    <div class="row justify-content-center mt-5">
        <div class="col-lg-8">
            <div class="profile-card">
                <div class="card-header text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Profile</h3>
                        <a href="account.php" class="btn btn-sm btn-outline-light">
                            <i class="fas fa-arrow-left me-1"></i> Back to Profile
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <div>Profile updated successfully!</div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errors['database'])): ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div><?= htmlspecialchars($errors['database']) ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                       id="name" name="name" value="<?= htmlspecialchars($customer['customer_name'] ?? '') ?>">
                                <?php if (isset($errors['name'])): ?>
                                    <div class="invalid-feedback d-flex align-items-center">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <?= htmlspecialchars($errors['name']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                       id="email" name="email" value="<?= htmlspecialchars($customer['customer_email'] ?? '') ?>">
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback d-flex align-items-center">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <?= htmlspecialchars($errors['email']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?= htmlspecialchars($customer['customer_phone'] ?? '') ?>">
                        </div>
                        
                        <div class="password-section">
                            <h5 class="mb-4"><i class="fas fa-lock me-2"></i>Change Password</h5>
                            <p class="text-muted mb-4">Leave blank if you don't want to change your password</p>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>" 
                                           id="current_password" name="current_password">
                                    <?php if (isset($errors['current_password'])): ?>
                                        <div class="invalid-feedback d-flex align-items-center">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            <?= htmlspecialchars($errors['current_password']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>" 
                                           id="new_password" name="new_password">
                                    <?php if (isset($errors['new_password'])): ?>
                                        <div class="invalid-feedback d-flex align-items-center">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            <?= htmlspecialchars($errors['new_password']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                                           id="confirm_password" name="confirm_password">
                                    <?php if (isset($errors['confirm_password'])): ?>
                                        <div class="invalid-feedback d-flex align-items-center">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            <?= htmlspecialchars($errors['confirm_password']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-5">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-2"></i> Reset
                            </button>
                            <div>
                                <a href="account.php" class="btn btn-outline-secondary me-3">
                                    <i class="fas fa-times me-2"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include "footer.php"; ?>