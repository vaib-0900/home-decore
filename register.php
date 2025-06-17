<?php
include "header.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .registration-container {
            max-width: 600px;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: 500;
        }
        .btn-register {
            background-color: #0d6efd;
            border: none;
            padding: 10px 0;
            font-weight: 500;
        }
        .btn-register:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5 mt-5">
    <div class="registration-container bg-white p-4 p-md-5 mt-5">
        <h2 class="text-center mb-4">Register Your Account</h2>
        <form action="register_out.php" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="name" required placeholder="Enter your full name">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" required placeholder="Enter your email">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required placeholder="Create a password">
                    <div class="form-text">At least 8 characters</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" name="phone" pattern="[0-9]{10}" required placeholder="10-digit number">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Full Address</label>
                <textarea class="form-control" name="address" rows="3" required placeholder="Street address, apartment, floor"></textarea>
            </div>
            
            <div class="mb-4">
                <label for="landmark" class="form-label">Landmark (Optional)</label>
                <input type="text" class="form-control" name="landmark" placeholder="Nearby famous location">
            </div>
            
            <button type="submit" class="btn btn-primary btn-register w-100">Register Now</button>
            
            <div class="text-center mt-3">
                <p class="mb-0">Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
include "footer.php";
?>