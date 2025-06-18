<?php
include 'header.php';
include 'db_connection.php';
?>

<!-- Product Page Header -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Products</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Main Product Section -->
<section class="py-5">
    <div class="container">
                        <h1 class="display-6 fw-bold text-center mt-5 my-5">Our Products</h1>

        <div class="row">
            <!-- Sidebar Filters (Collapsible on Mobile) -->
            
            
            <!-- Product Grid -->
            <div class="col-lg-9 col-md-8">
                <!-- Sorting and Product Count -->
               
                
                <!-- Product Cards -->
                <div class="row g-4">
                    <?php
                    $products = $conn->query("SELECT p.*, c.category_name 
                                            FROM tbl_product p
                                            LEFT JOIN tbl_category c ON p.add_category = c.category_id
                                            ORDER BY p.product_id DESC
                                            LIMIT 12");
                    
                    if ($products->num_rows > 0):
                        while ($product = $products->fetch_assoc()): 
                            $rating = rand(3, 5); // Random rating for demo
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <!-- Product Badge -->
                            <?php if (isset($product['original_price']) && $product['original_price'] > $product['product_price']): 
                                $discount = round(($product['original_price'] - $product['product_price']) / $product['original_price'] * 100);
                            ?>
                                <span class="badge bg-danger position-absolute m-2">-<?= $discount ?>%</span>
                            <?php endif; ?>
                            
                            <!-- Product Image -->
                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                <img src="admin/<?= htmlspecialchars($product['product_image']) ?>" 
                                     class="card-img-top h-100 object-fit-cover" 
                                     alt="<?= htmlspecialchars($product['product_name']) ?>">
                                <div class="position-absolute top-0 end-0 m-2">
                                    <button class="btn btn-sm btn-light rounded-circle shadow-sm">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Product Body -->
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge bg-light text-dark"><?= $product['category_name'] ?></span>
                                    <div class="text-warning">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?= $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>' ?>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                
                                <h5 class="card-title">
                                    <a href="single-product.php?id=<?= $product['product_id'] ?>" class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($product['product_name']) ?>
                                    </a>
                                </h5>
                                
                                <p class="card-text text-muted small">
                                    <?= substr(htmlspecialchars($product['product_description']), 0, 80) ?>...
                                </p>
                                
                                <!-- Price -->
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <h5 class="text-primary mb-0">Rs. <?= number_format($product['product_price'], 2) ?></h5>
                                        <?php if (isset($product['original_price']) && $product['original_price'] > $product['product_price']): ?>
                                            <small class="text-muted text-decoration-line-through">Rs. <?= number_format($product['original_price'], 2) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <a href="addtocart.php?id=<?= $product['product_id'] ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-cart-plus"></i> Add
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">No products found.</div>
                        </div>
                    <?php endif; ?>
                </div>
                
               
            </div>
        </div>
    </div>
</section>

<!-- Custom CSS -->
<style>
    .object-fit-cover {
        object-fit: cover;
    }
    
    .card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .breadcrumb {
        padding: 60px 0;
        background-color: #f8f9fa;
    }
</style>

<?php
include 'footer.php';
?>