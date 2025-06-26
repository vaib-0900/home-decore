<?php
include "header.php";
include "db_connection.php";

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // Fetch product details
    $query = "SELECT * FROM tbl_product WHERE product_id = ?";

    // Example: Update product view count (optional, adjust as needed)
    $updateQuery = "UPDATE tbl_product SET view_count = view_count + 1 WHERE product_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("i", $product_id);
    $updateStmt->execute();
    $updateStmt->close();
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "<div class='container my-5'><div class='alert alert-danger text-center'>Product not found.</div></div>";
        include "footer.php";
        exit();
    }

    $stmt->close();
} else {
    echo "<div class='container my-5'><div class='alert alert-warning text-center'>Invalid product.</div></div>";
    include "footer.php";
    exit();
}
?>
<section class="breadcrumb breadcrumb_bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="breadcrumb_iner">
                    <div class="breadcrumb_iner_item">
                        <h2>single view Products</h2>
                         <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="shop.php" class="text-decoration-none">Shop</a></li>
            </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - Pet Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .product-gallery-thumb {
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        .product-gallery-thumb:hover {
            border-color: var(--bs-primary);
            transform: scale(1.05);
        }
        .product-gallery-thumb.active {
            border-color: var(--bs-primary);
        }
        .main-product-image {
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        .product-badge {
            top: 1rem;
            left: 1rem;
            z-index: 1;
            font-size: 0.9rem;
            padding: 0.35rem 0.75rem;
        }
        .quantity-control {
            width: 120px;
        }
        .product-info-card {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1.5rem;
        }
        .product-tabs .nav-link {
            font-weight: 500;
            color: #495057;
        }
        .product-tabs .nav-link.active {
            color: var(--bs-primary);
            background-color: #f8f9fa;
            border-bottom-color: #f8f9fa;
        }
        .related-product-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .related-product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        .rating-stars {
            color: #ffc107;
        }
    </style>
</head>

<body>
<!-- Product Detail Section -->
<section class="py-5 ">
    <div class="container ">
        <!-- Breadcrumb -->

        <div class="row g-4">
            <!-- Product Images -->
            <div class="col-lg-6">
                <div class="position-relative mb-4">
                    <span class="badge bg-danger product-badge position-absolute">Hot</span>
                    <img src="admin/<?php echo $product['product_image']; ?>" class="img-fluid main-product-image w-100" alt="<?php echo htmlspecialchars($product['product_name']); ?>" id="mainProductImage">
                </div>
                <div class="row g-2">
                    <?php
                    // Assuming we have multiple images (in a real scenario, you would fetch these from database)
                    $thumbnails = [$product['product_image'], $product['product_image'], $product['product_image'], $product['product_image']];
                    foreach ($thumbnails as $index => $thumb) {
                    ?>
                    <div class="col-3">
                        <img src="admin/<?php echo $thumb; ?>" class="img-fluid rounded product-gallery-thumb <?php echo $index === 0 ? 'active' : ''; ?>" alt="Thumbnail <?php echo $index + 1; ?>" data-image="admin/<?php echo $thumb; ?>">
                    </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="product-info-card h-100">
                    <h1 class="mb-3 fw-bold"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="rating-stars me-2">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </div>
                        <span class="text-muted small">(24 reviews)</span>
                        <span class="ms-3 badge bg-success">In Stock</span>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-primary fw-bold">Rs. <?php echo number_format($product['product_price'], 2); ?></h3>
                        <?php if (isset($product['original_price']) && $product['original_price'] > $product['product_price']): ?>
                            <span class="text-muted text-decoration-line-through">Rs. <?php echo number_format($product['original_price'], 2); ?></span>
                            <span class="badge bg-danger ms-2">Save Rs. <?php echo number_format($product['original_price'] - $product['product_price'], 2); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="mb-4"><?php echo nl2br(htmlspecialchars($product['product_description'])); ?></p>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-4">
                            <label class="form-label fw-semibold">Quantity:</label>
                            <div class="input-group quantity-control">
                                <button class="btn btn-outline-secondary" type="button" id="decrement">-</button>
                                <input type="text" class="form-control text-center" value="1" id="quantity">
                                <button class="btn btn-outline-secondary" type="button" id="increment">+</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="addtocart.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary px-4 py-2 flex-grow-1">
                            <i class="bi bi-cart-plus me-2"></i> Add to Cart
                        </a>
                        <a href="wishlist-insert.php?product_id=<?= $product['product_id'] ?>" class="btn btn-outline-danger px-4 py-2">
                            <i class="bi bi-heart me-2"></i> Add to Wishlist
                        </a>
                    </div>
                    
                    <div class="border-top pt-3">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <span class="fw-semibold">SKU:</span> <?php echo $product['product_id']; ?>
                            </div>
                            <div class="col-md-6 mb-2">
                                <span class="fw-semibold">Category:</span> <?php echo $product['add_category']; ?>
                            </div>
                            <div class="col-12">
                                <span class="fw-semibold">Share:</span>
                                <div class="d-inline-flex gap-2 ms-2">
                                    <a href="#" class="text-decoration-none text-dark"><i class="bi bi-facebook"></i></a>
                                    <a href="#" class="text-decoration-none text-dark"><i class="bi bi-twitter"></i></a>
                                    <a href="#" class="text-decoration-none text-dark"><i class="bi bi-instagram"></i></a>
                                    <a href="#" class="text-decoration-none text-dark"><i class="bi bi-pinterest"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Tabs -->
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs product-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">Description</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">Reviews (24)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab">Shipping & Returns</button>
                    </li>
                </ul>
                <div class="tab-content p-4 border border-top-0 rounded-bottom bg-white" id="productTabsContent">
                    <div class="tab-pane fade show active" id="description" role="tabpanel">
                        <h4 class="mb-4">Product Details</h4>
                        <p><?php echo nl2br(htmlspecialchars($product['product_description'])); ?></p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item border-0 ps-0"><i class="bi bi-check-circle-fill text-success me-2"></i> High-quality ingredients for your pet's health</li>
                            <li class="list-group-item border-0 ps-0"><i class="bi bi-check-circle-fill text-success me-2"></i> Veterinarian recommended</li>
                            <li class="list-group-item border-0 ps-0"><i class="bi bi-check-circle-fill text-success me-2"></i> 100% satisfaction guarantee</li>
                            <li class="list-group-item border-0 ps-0"><i class="bi bi-check-circle-fill text-success me-2"></i> Made with natural ingredients</li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <div class="mb-4">
                            <h4 class="mb-3">Customer Reviews</h4>
                            <div class="d-flex align-items-center mb-3">
                                <div class="rating-stars me-2">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                </div>
                                <span class="fw-semibold">4.5 out of 5</span>
                                <span class="ms-3 text-muted">Based on 24 reviews</span>
                            </div>
                        </div>
                        
                        <div class="review mb-4 pb-3 border-bottom">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class="mb-0">Great Product!</h5>
                                <small class="text-muted">2 days ago</small>
                            </div>
                            <div class="rating-stars mb-2">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <p class="mb-1">Excellent quality and fast delivery. Highly recommended!</p>
                            <p class="text-muted small">- Alex P.</p>
                        </div>
                        
                        <div class="review mb-4 pb-3 border-bottom">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class="mb-0">Good quality</h5>
                                <small class="text-muted">1 week ago</small>
                            </div>
                            <div class="rating-stars mb-2">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star"></i>
                            </div>
                            <p class="mb-1">Good product but a bit expensive.</p>
                            <p class="text-muted small">- Sarah M.</p>
                        </div>
                        
                        <button class="btn btn-outline-primary mt-3">Write a Review</button>
                    </div>
                    <div class="tab-pane fade" id="shipping" role="tabpanel">
                        <h4 class="mb-4">Shipping Information</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Shipping Method</th>
                                        <th>Delivery Time</th>
                                        <th>Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Standard Shipping</td>
                                        <td>3-5 business days</td>
                                        <td>Rs. 150</td>
                                    </tr>
                                    <tr>
                                        <td>Express Shipping</td>
                                        <td>1-2 business days</td>
                                        <td>Rs. 300</td>
                                    </tr>
                                    <tr>
                                        <td>Free Shipping</td>
                                        <td>3-5 business days</td>
                                        <td>Free on orders over Rs. 2000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <h4 class="mt-5 mb-3">Returns Policy</h4>
                        <div class="alert alert-info">
                            <p class="mb-0">We offer a 30-day return policy. Items must be unopened and in original packaging.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4 fw-bold">You May Also Like</h3>
                <div class="row">
                    <?php
                    $query = "SELECT * FROM tbl_product WHERE product_id != ? ORDER BY RAND() LIMIT 4";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $product_id);
                    $stmt->execute();
                    $related = $stmt->get_result();
                    
                    if ($related->num_rows > 0) {
                        while ($item = $related->fetch_assoc()) {
                    ?>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card h-100 related-product-card border-0">
                            <div class="position-relative">
                                <img src="admin/<?php echo $item['product_image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                <div class="card-img-overlay d-flex justify-content-end align-items-start p-2">
                                    <button class="btn btn-sm btn-light rounded-circle shadow-sm">
                                        <i class="bi bi-heart"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="text-primary fw-bold">Rs. <?php echo number_format($item['product_price'], 2); ?></span>
                                    <a href="single-product.php?id=<?php echo $item['product_id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    }
                    $stmt->close();
                    $conn->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Quantity increment/decrement
    document.getElementById('increment').addEventListener('click', function() {
        const quantityInput = document.getElementById('quantity');
        quantityInput.value = parseInt(quantityInput.value) + 1;
    });
    
    document.getElementById('decrement').addEventListener('click', function() {
        const quantityInput = document.getElementById('quantity');
        if (parseInt(quantityInput.value) > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
        }
    });
    
    // Image gallery functionality
    const thumbnails = document.querySelectorAll('.product-gallery-thumb');
    const mainImage = document.getElementById('mainProductImage');
    
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            // Remove active class from all thumbnails
            thumbnails.forEach(t => t.classList.remove('active'));
            // Add active class to clicked thumbnail
            this.classList.add('active');
            // Update main image
            mainImage.src = this.dataset.image;
        });
    });
</script>

<?php
include "footer.php";
?>