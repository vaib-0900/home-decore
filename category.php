<?php
include "header.php";
include "db_connection.php";

$mysqli = new mysqli("localhost", "root", "", "admin_home");
if ($mysqli->connect_errno) {
    die("Failed to connect: " . $mysqli->connect_error);
}

// Fetch categories
$categories = [];
$cat_result = $mysqli->query("SELECT category_id, category_name FROM tbl_category");
while ($cat = $cat_result->fetch_assoc()) {
    // Fetch products for each category
    $prod_result = $mysqli->query("SELECT product_id, product_name, product_image, product_price FROM tbl_product WHERE add_category = " . (int)$cat['category_id']);
    $products = [];
    while ($prod = $prod_result->fetch_assoc()) {
        $products[] = $prod;
    }
    $cat['tbl_product'] = $products;
    $categories[] = $cat;
}
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>nestfy home Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .category-title {
            color: var(--secondary-color);
            font-weight: 700;
            border-bottom: 3px solid var(--primary-color);
            display: inline-block;
            padding-bottom: 8px;
            margin: 2rem 0 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 0.75rem;
        }

        .price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-danger {
            background-color: var(--accent-color);
            border: none;
            padding: 0.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline-secondary {
            border-color: #ddd;
            padding: 0.5rem;
        }

        .badge-offer {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--accent-color);
            font-size: 0.8rem;
            padding: 0.35rem 0.6rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-top: auto;
        }

        .action-buttons .btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>

    <!--================Home Banner Area =================-->
    <!-- breadcrumb start-->
    <section class="breadcrumb breadcrumb_bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="breadcrumb_iner">
                        <div class="breadcrumb_iner_item">
                            <h2>Category</h2>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                                <li class="breadcrumb-item"><a href="shop.php" class="text-decoration-none">shop</a></li>
                                <li class="breadcrumb-item"><a href="category.php" class="text-decoration-none">Category</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- breadcrumb start-->

    <div class="container py-4 mt-5">
        <h2 class="text-center mb-5 mt">Our categories Selection</h2>

        <?php foreach ($categories as $category): ?>
            <div class="category-section mb-5">
                <h2 class="category-title"><?= htmlspecialchars($category['category_name']) ?></h2>
                <div class="row g-4">
                    <?php foreach ($category['tbl_product'] as $product): ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card h-100">
                                <a href="single-product.php?id=<?= htmlspecialchars($product['product_id']) ?>" class="stretched-link"></a>
                                <img src="admin/<?= htmlspecialchars($product['product_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['product_name']) ?>">

                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                                    <div class="price">Rs.<?= number_format(htmlspecialchars($product['product_price']), 2) ?></div>
                                    <div class="d-flex justify-content-center mt-3">
                                        <a href="addtocart.php?id=<?= htmlspecialchars($product['product_id']) ?>"
                                            class="btn btn-primary">Add to Cart</a>
                                        <a href="wishlist.php?id=<?= htmlspecialchars($product['product_id']) ?>" class="btn btn-outline-secondary ms-2">
                                            <i class="bi bi-heart"></i> Wishlist
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.0/dist/iconify-icon.min.js"></script>
</body>

</html>
<?php
include "footer.php";
?>