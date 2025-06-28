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
                            <a href="account.php" class="nav-icon" title="Account">
                                <i class="ti-user"></i>
                            </a>
                            <a href="wishlist.php" class="nav-icon position-relative" title="Wishlist">
                                <i class="ti-heart"></i>
                                <?php if($wishlist_count > 0): ?>
                                    <span class="badge-count"><?= $wishlist_count ?></span>
                                <?php endif; ?>
                            </a>
                            <a href="cart_list.php" class="nav-icon position-relative" title="Cart"></a>
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