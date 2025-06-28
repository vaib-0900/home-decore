<?php
include "header.php";

// Fetch customer account details from the database
include "db_connection.php";

$customer_id = $_SESSION['customer_id'];
$query = "SELECT * FROM tbl_customer WHERE customer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$customer = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .profile-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .profile-card:hover {
            transform: translateY(-5px);
        }
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem 1rem;
            color: white;
            text-align: center;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .info-item {
            border-left: 3px solid #764ba2;
            padding-left: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .info-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        .btn-edit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        .btn-edit:hover {
            background: linear-gradient(135deg, #5a6fd1 0%, #6a4199 100%);
            color: white;
        }
        .last-updated {
            background-color: #f8f9fa;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <section class="breadcrumb breadcrumb_bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="breadcrumb_iner">
                    <div class="breadcrumb_iner_item">
                        <h2>My Account</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="profile-card">
                <div class="profile-header">
                    <h2><i class="fas fa-user-circle me-2"></i>My Account</h2>
                </div>

                <div class="card-body p-4">
                    <?php if ($customer) : ?>
                        <div class="text-center mb-4">
                        
                            <h3 class="mb-1"><?= htmlspecialchars($customer['customer_name']) ?></h3>
                            <p class="text-muted">Member since <?= date("F Y", strtotime($customer['created_at'] ?? 'now')) ?></p>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="info-item">
                                    <h5 class="text-primary"><i class="fas fa-envelope me-2"></i>Email</h5>
                                    <p class="mb-0"><?= htmlspecialchars($customer['customer_email']) ?></p>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="info-item">
                                    <h5 class="text-primary"><i class="fas fa-phone me-2"></i>Phone</h5>
                                    <p class="mb-0"><?= htmlspecialchars($customer['customer_phone']) ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center mt-4 gap-3">
                            <a href="edit_profile.php" class="btn btn-edit btn-lg px-4">
                                <i class="fas fa-user-edit me-2"></i> Edit Profile
                            </a>
                            <a href="login_out.php" class="btn btn-danger btn-lg px-4">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </div>
                    <?php else : ?>
                        <div class="alert alert-warning text-center">Account details not found.</div>
                    <?php endif; ?>
                </div>

                <div class="card-footer last-updated text-center py-3">
                    <small><i class="fas fa-sync-alt me-2"></i>Last updated: <?= date("F j, Y, g:i a") ?></small>
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