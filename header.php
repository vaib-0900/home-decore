<?php
session_start();
if(!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}

// Initialize counts
$cart_count = 0;
$wishlist_count = 0;

if(isset($_SESSION['customer_id'])) {
    include "db_connection.php";
    $customer_id = $_SESSION['customer_id'];
    
    // Get cart count (sum of quantities)
    $cart_query = "SELECT SUM(cart_qty) as count FROM tbl_cart WHERE cart_customer_id = ?";
    $stmt = mysqli_prepare($conn, $cart_query);
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    $cart_result = mysqli_stmt_get_result($stmt);
    $cart_data = mysqli_fetch_assoc($cart_result);
    $cart_count = $cart_data['count'] ?? 0;
    
    // Get wishlist count
    $wishlist_query = "SELECT COUNT(*) as count FROM tbl_wishlist WHERE wishlist_customer = ?";
    $stmt = mysqli_prepare($conn, $wishlist_query);
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    $wishlist_result = mysqli_stmt_get_result($stmt);
    $wishlist_data = mysqli_fetch_assoc($wishlist_result);
    $wishlist_count = $wishlist_data['count'] ?? 0;
    
    mysqli_close($conn);
}
?>
<!doctype html>
<html lang="zxx">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Nestify Home</title>
    <link rel="icon" href="img/favcon1.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- animate CSS -->
    <link rel="stylesheet" href="css/animate.css">
    <!-- owl carousel CSS -->
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="css/all.css">
    <!-- flaticon CSS -->
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/themify-icons.css">
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="css/magnific-popup.css">
    <!-- swiper CSS -->
    <link rel="stylesheet" href="css/slick.css">
    <!-- style CSS -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .badge-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff3368;
            color: white;
            border-radius: 50%;
            min-width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }
        .nav-icon {
            position: relative;
            font-size: 1.25rem;
            color: #495057;
            transition: all 0.3s;
        }
        .nav-icon:hover {
            color: #ff3368;
        }
        .hearer_icon {
            gap: 1rem;
        }
        
        /* Account Modal Styles */
        .account-modal {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background: white;
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
            z-index: 1050;
            transition: all 0.3s ease-out;
            overflow-y: auto;
        }
        .account-modal.show {
            right: 0;
        }
        .account-modal-header {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .account-modal-body {
            padding: 1.5rem;
        }
        .account-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }
        .account-modal-backdrop.show {
            opacity: 1;
            visibility: visible;
        }
        .account-close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }
        .account-user-info {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .account-user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
            color: #777;
        }
        .account-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .account-links li {
            margin-bottom: 0.5rem;
        }
        .account-links a {
            display: block;
            padding: 0.75rem 1rem;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .account-links a:hover {
            background: #f8f9fa;
            color: #ff3368;
        }
        .account-links i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }
        
        @media (max-width: 576px) {
            .account-modal {
                width: 100%;
                right: -100%;
            }
        }
    </style>
</head>

<body>
    <!--::header part start::-->
    <header class="main_menu home_menu">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <a class="navbar-brand" href="index.php" style="font-weight: bold; font-size: 2rem;">Nestify Home</a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="menu_icon"><i class="fas fa-bars"></i></span>
                        </button>

                        <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="index.php">Home</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="blog.php" id="navbarDropdown_1"
                                        role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Shop
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown_1">
                                        <a class="dropdown-item" href="shop.php">Shop</a>
                                        <a class="dropdown-item" href="category.php">Categories</a>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="about.php">About Us</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="blog.php" id="navbarDropdown_3"
                                        role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Pages
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown_2">
                                        <a class="dropdown-item" href="wishlist.php">Wishlist</a>
                                        <a class="dropdown-item" href="tracking.php">Order Tracking</a>
                                        <a class="dropdown-item" href="checkout.php">Checkout</a>
                                        <a class="dropdown-item" href="cart_list.php">Shopping Cart</a>
                                        <a class="dropdown-item" href="confirmation.php">Confirmation</a>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="contact.php">Contact</a>
                                </li>
                                <?php if(!isset($_SESSION["login"])): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="register.php">Register</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="login.php">Login</a>
                                    </li>
                                <?php else: ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="login_out.php">Logout</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="hearer_icon d-flex align-items-center">
                            <a id="search_1" href="javascript:void(0)" class="nav-icon" title="Search">
                                <i class="ti-search"></i>
                            </a>
                            <a href="javascript:void(0)" class="nav-icon account-trigger" title="Account">
                                <i class="ti-user"></i>
                            </a>
                            <a href="wishlist.php" class="nav-icon position-relative" title="Wishlist">
                                <i class="ti-heart"></i>
                                <?php if($wishlist_count > 0): ?>
                                    <span class="badge-count"><?= $wishlist_count ?></span>
                                <?php endif; ?>
                            </a>
                            <a href="cart_list.php" class="nav-icon position-relative" title="Cart">
                                <i class="ti-shopping-cart"></i>
                                <?php if($cart_count > 0): ?>
                                    <span class="badge-count"><?= $cart_count ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- Header part end-->

    <!-- Account Modal -->
    <div class="account-modal-backdrop"></div>
    <div class="account-modal">
        <div class="account-modal-header">
            <h5>My Account</h5>
            <button class="account-close-btn">&times;</button>
        </div>
        <div class="account-modal-body">
            <div class="account-user-info">
                <div class="account-user-avatar">
                    <i class="ti-user"></i>
                </div>
                <div>
                    <h6><?= isset($_SESSION['customer_name']) ? htmlspecialchars($_SESSION['customer_name']) : 'Guest' ?></h6>
                    <small><?= isset($_SESSION['customer_email']) ? htmlspecialchars($_SESSION['customer_email']) : '' ?></small>
                </div>
            </div>
            
            <ul class="account-links">
                <li><a href="account.php"><i class="ti-user"></i> Account Dashboard</a></li>
                <li><a href="confirmation.php"><i class="ti-package"></i> My Orders</a></li>
                <li><a href="cart_list.php"><i class="ti-shopping-cart"></i> My Cart</a></li>
                <li><a href="wishlist.php"><i class="ti-heart"></i> My Wishlist</a></li>
                <li><a href="login_out.php"><i class="ti-power-off"></i> Logout</a></li>
            </ul>
        </div>
    </div>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="js/jquery-1.12.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <!-- magnific popup js -->
    <script src="js/jquery.magnific-popup.js"></script>
    <!-- carousel js -->
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <!-- slick js -->
    <script src="js/slick.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/waypoints.min.js"></script>
    <script src="js/contact.js"></script>
    <script src="js/jquery.ajaxchimp.min.js"></script>
    <script src="js/jquery.form.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/mail-script.js"></script>
    <script src="js/custom.js"></script>
    
    <script>
        // Account Modal Toggle
        $(document).ready(function() {
            // Open modal
            $('.account-trigger').click(function() {
                $('.account-modal').addClass('show');
                $('.account-modal-backdrop').addClass('show');
                $('body').css('overflow', 'hidden');
            });
            
            // Close modal
            $('.account-close-btn, .account-modal-backdrop').click(function() {
                $('.account-modal').removeClass('show');
                $('.account-modal-backdrop').removeClass('show');
                $('body').css('overflow', 'auto');
            });
            
            // Prevent modal from closing when clicking inside
            $('.account-modal').click(function(e) {
                e.stopPropagation();
            });
        });
    </script>
</body>
</html>