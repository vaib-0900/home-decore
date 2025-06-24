<?php
include "header.php";
include "db_connection.php";

// Database connection
$mysqli = new mysqli("localhost", "root", "", "admin_home");
if ($mysqli->connect_errno) {
    die("Failed to connect: " . $mysqli->connect_error);
}

// Fetch categories with products
$categories = [];
$cat_result = $mysqli->query("SELECT category_id, category_name FROM tbl_category");
while ($cat = $cat_result->fetch_assoc()) {
    // Fetch products for each category using prepared statement
    $stmt = $mysqli->prepare("SELECT product_id, product_name, product_image, product_price FROM tbl_product WHERE add_category = ?");
    $stmt->bind_param("i", $cat['category_id']);
    $stmt->execute();
    $prod_result = $stmt->get_result();
    
    $products = [];
    while ($prod = $prod_result->fetch_assoc()) {
        $products[] = $prod;
    }
    $cat['products'] = $products;
    $categories[] = $cat;
}
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nestify Home - Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --accent: #e74c3c;
            --light: #f8f9fa;
        }

        .category-header {
            position: relative;
            padding: 60px 0;
            background-size: cover;
            background-position: center;
        }

        .category-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .breadcrumb-item a {
            color: #fff;
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: rgba(255, 255, 255, 0.7);
        }

        .category-title {
            color: var(--secondary);
            font-weight: 700;
            border-bottom: 3px solid var(--primary);
            display: inline-block;
            padding-bottom: 8px;
            margin: 2rem 0 1.5rem;
        }

        .product-card {
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .product-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--accent);
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
        }

        .product-price {
            font-weight: 700;
            color: var(--accent);
        }

        .original-price {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .action-buttons .btn {
            border-radius: 50px;
            padding: 8px 15px;
            font-size: 0.9rem;
        }

        .btn-wishlist {
            border: 1px solid #dee2e6;
        }
    </style>
</head>

<body>
  <!--================Home Banner Area =================-->
<section class="breadcrumb breadcrumb_bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="breadcrumb_iner">
                    <div class="breadcrumb_iner_item">
                        <h2>Category Products</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================ category section start ================= -->

    <!-- Categories Section -->
    <div class="container py-5">
        <h2 class="text-center mb-5">Browse Our Categories</h2>

        <?php foreach ($categories as $category): ?>
            <?php if (!empty($category['products'])): ?>
                <div class="category-section mb-5">
                    <h2 class="category-title"><?= htmlspecialchars($category['category_name']) ?></h2>
                    <div class="row">
                        <?php foreach ($category['products'] as $product): ?>
                            <div class="col-md-3 col-sm-6 mb-4">
                                <div class="product-card">
                                    <div class="position-relative">
                                        <a href="single-product.php?id=<?= $product['product_id'] ?>">
                                            <img src="admin/<?= htmlspecialchars($product['product_image']) ?>" 
                                                 class="product-img" 
                                                 alt="<?= htmlspecialchars($product['product_name']) ?>">
                                        </a>
                                        <?php if (isset($product['original_price']) && $product['original_price'] > $product['product_price']): ?>
                                            <?php $discount = round(($product['original_price'] - $product['product_price']) / $product['original_price'] * 100); ?>
                                            <span class="product-badge">-<?= $discount ?>%</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="single-product.php?id=<?= $product['product_id'] ?>" class="text-decoration-none text-dark">
                                                <?= htmlspecialchars($product['product_name']) ?>
                                            </a>
                                        </h5>
                                        <div class="mb-2">
                                            <span class="product-price">Rs. <?= number_format($product['product_price'], 2) ?></span>
                                            <?php if (isset($product['original_price']) && $product['original_price'] > $product['product_price']): ?>
                                                <small class="original-price ms-2">Rs. <?= number_format($product['original_price'], 2) ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="action-buttons d-flex">
                                            <a href="addtocart.php?id=<?= $product['product_id'] ?>" 
                                               class="btn btn-primary me-2">
                                               <i class="fas fa-cart-plus me-1"></i> Add
                                            </a>
                                            <a href="wishlist-insert.php?product_id=<?= $product['product_id'] ?>" 
                                               class="btn btn-outline-secondary btn-wishlist">
                                               <i class="far fa-heart me-1"></i> Wishlist
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include "footer.php"; ?>
</body>
</html>