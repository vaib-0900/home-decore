<?php
include 'header.php';
include "db_connection.php";

// Fetch product details
$product_query = "SELECT p.*, c.category_name 
                  FROM tbl_product p
                  LEFT JOIN tbl_category c ON p.add_category = c.category_id
                  WHERE p.product_id = ?";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();


$product = $product_result->fetch_assoc();

// Fetch related products (from same category)
$related_query = "SELECT p.* FROM tbl_product p 
                  WHERE p.add_category = ? AND p.product_id != ? 
                  LIMIT 4";
$stmt = $conn->prepare($related_query);
$stmt->bind_param("ii", $product['add_category'], $product_id);
$stmt->execute();
$related_result = $stmt->get_result();
$related_products = $related_result->fetch_all(MYSQLI_ASSOC);
?>

<!--================Home Banner Area =================-->
<section class="breadcrumb breadcrumb_bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="breadcrumb_iner">
                    <div class="breadcrumb_iner_item">
                        <h2>Product Details</h2>
                        <p>Home <span>-</span> Shop <span>-</span> </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!--================Single Product Area =================-->
<section class="product_details section_padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-6">
                <div class="product_details_slider">
                    <!-- Main Product Image -->
                    <div class="main_product_img mb-4">
                        <img id="main_product_image" src="admin/<?= htmlspecialchars($product['product_image'] ?? '') ?>" 
                             alt="<?= htmlspecialchars($product['product_name'] ?? 'Product Image') ?>" 
                             class="img-fluid rounded-3 border shadow-sm w-100">
                    </div>
                    
                    <!-- Thumbnail Gallery -->
                    <div class="thumbnail_gallery">
                        <div class="row g-2">
                            <div class="col-3">
                                <a href="#" class="thumbnail-item active" data-image="admin/<?= htmlspecialchars($product['product_image'] ?? '') ?>">
                                    <img src="admin/<?= htmlspecialchars($product['product_image'] ?? '') ?>" 
                                         alt="Thumbnail" class="img-thumbnail">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5 col-md-6 offset-lg-1">
                <div class="product_details_text">
                    <h3><?= htmlspecialchars($product['product_name'] ?? 'Product Name') ?></h3>
                    
                    <div class="rating mb-3">
                        <?php
                        $rating = rand(3, 5);
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $rating ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-warning"></i>';
                        }
                        ?>
                        <span class="ms-2">(<?= rand(10, 100) ?> reviews)</span>
                    </div>
                    
                    <div class="price mb-4">
                        <h4 class="text-primary">Rs. <?= isset($product['product_price']) ? number_format($product['product_price'], 2) : '0.00' ?></h4>
                        <?php if (isset($product['product_price']) && $product['product_price'] > $product['product_price']): ?>
                            <del class="text-muted">Rs. <?= number_format($product['product_price'], 2) ?></del>
                            <span class="badge bg-danger ms-2">Save Rs. <?= number_format($product['product_price'] - $product['product_price'], 2) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="mb-4"><?= htmlspecialchars($product['product_description']) ?></p>
                    
                    <div class="product_details_meta mb-4">
                        <ul class="list-unstyled">
                            <li><span class="fw-bold">Category:</span> <?= htmlspecialchars($product['category_name']) ?></li>
                            <li><span class="fw-bold">Availability:</span> 
                                <span class="text-success">In Stock</span>
                            </li>
                            <li><span class="fw-bold">SKU:</span> <?= $product['product_id']?></li>
                        </ul>
                    </div>
                    
                    <div class="product_quantity_wrapper mb-4">
                        <div class="input-group quantity_selector" style="width: 150px;">
                            <button class="btn btn-outline-secondary minus-btn" type="button">-</button>
                            <input type="text" class="form-control text-center quantity-input" value="1" min="1">
                            <button class="btn btn-outline-secondary plus-btn" type="button">+</button>
                        </div>
                    </div>
                    
                    <div class="product_action_buttons d-flex flex-wrap gap-3">
                        <a href="addtocart.php?id=<?= $product['product_id'] ?? 0 ?>&quantity=1" 
                           class="btn btn-primary px-4 py-3">
                           <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                        </a>
                        <button class="btn btn-outline-secondary px-4 py-3">
                            <i class="far fa-heart me-2"></i>Add to Wishlist
                        </button>
                    </div>
                    
                    <div class="product_share mt-5">
                        <h6>Share this product:</h6>
                        <div class="social_icons">
                            <a href="#" class="text-muted me-3"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-muted me-3"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-muted me-3"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-muted me-3"><i class="fab fa-pinterest"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Description & Reviews Tabs -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="productTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="description-tab" data-bs-toggle="tab" 
                                        data-bs-target="#description" type="button" role="tab">
                                    Description
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" 
                                        data-bs-target="#reviews" type="button" role="tab">
                                    Reviews (<?= rand(5, 20) ?>)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" 
                                        data-bs-target="#shipping" type="button" role="tab">
                                    Shipping & Returns
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content p-4" id="productTabsContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel">
                                <h5 class="mb-4">Product Details</h5>
                                <p><?= htmlspecialchars($product['product_description'] ?? 'No description available') ?></p>
                                
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> High Quality Material</li>
                                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Eco Friendly</li>
                                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> 1 Year Warranty</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Free Shipping</li>
                                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Easy Returns</li>
                                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> 24/7 Support</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="reviews" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h5 class="mb-4">Customer Reviews</h5>
                                        
                                        <!-- Sample Reviews -->
                                        <div class="review-item mb-4">
                                            <div class="d-flex justify-content-between mb-2">
                                                <div class="rating">
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="far fa-star text-warning"></i>
                                                </div>
                                                <small class="text-muted">2 days ago</small>
                                            </div>
                                            <h6>Great Product!</h6>
                                            <p class="mb-2">This product exceeded my expectations. The quality is amazing and it looks even better in person.</p>
                                            <p class="text-muted small">By John D.</p>
                                        </div>
                                        
                                        <div class="review-item mb-4">
                                            <div class="d-flex justify-content-between mb-2">
                                                <div class="rating">
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                </div>
                                                <small class="text-muted">1 week ago</small>
                                            </div>
                                            <h6>Perfect!</h6>
                                            <p class="mb-2">I'm very satisfied with this purchase. Fast shipping and excellent customer service.</p>
                                            <p class="text-muted small">By Sarah M.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <h5 class="mb-4">Add a Review</h5>
                                        <form>
                                            <div class="mb-3">
                                                <label class="form-label">Your Rating</label>
                                                <div class="rating-input">
                                                    <i class="far fa-star" data-rating="1"></i>
                                                    <i class="far fa-star" data-rating="2"></i>
                                                    <i class="far fa-star" data-rating="3"></i>
                                                    <i class="far fa-star" data-rating="4"></i>
                                                    <i class="far fa-star" data-rating="5"></i>
                                                    <input type="hidden" name="rating" id="rating-value" value="0">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="review-title" class="form-label">Review Title</label>
                                                <input type="text" class="form-control" id="review-title">
                                            </div>
                                            <div class="mb-3">
                                                <label for="review-text" class="form-label">Your Review</label>
                                                <textarea class="form-control" id="review-text" rows="4"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit Review</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="shipping" role="tabpanel">
                                <h5 class="mb-4">Shipping Information</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <h6><i class="fas fa-truck me-2 text-primary"></i> Shipping Options</h6>
                                        <ul class="list-unstyled ps-4">
                                            <li class="mb-2">Standard Shipping: 3-5 business days</li>
                                            <li class="mb-2">Express Shipping: 1-2 business days</li>
                                            <li class="mb-2">Free Shipping on orders over Rs. 5000</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <h6><i class="fas fa-undo me-2 text-primary"></i> Return Policy</h6>
                                        <ul class="list-unstyled ps-4">
                                            <li class="mb-2">30-day return policy</li>
                                            <li class="mb-2">Items must be unused and in original packaging</li>
                                            <li class="mb-2">Customer pays return shipping</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <div class="section-title text-center mb-5">
                    <h3 class="position-relative d-inline-block">Related Products</h3>
                </div>
                
                <div class="row g-4">
                    <?php foreach ($related_products as $related): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 transition-all hover-shadow">
                            <!-- Product Badge -->
                            <?php if (isset($related['original_price']) && $related['original_price'] > $related['product_price']): ?>
                                <?php $discount_percent = round(($related['original_price'] - $related['product_price']) / $related['original_price'] * 100); ?>
                                <span class="product-badge bg-danger">-<?= $discount_percent ?>%</span>
                            <?php endif; ?>
                            
                            <!-- Product Image -->
                            <div class="product-img-container position-relative overflow-hidden" style="height: 200px;">
                                <a href="single-product.php?id=<?= $related['product_id'] ?>" class="text-decoration-none">
                                    <img src="admin/<?= htmlspecialchars($related['product_image'] ?? '') ?>" 
                                         alt="<?= htmlspecialchars($related['product_name'] ?? 'Product Image') ?>"
                                         class="img-fluid w-100 h-100 object-fit-cover">
                                </a>
                            </div>
                            
                            <!-- Product Body -->
                            <div class="card-body text-center">
                                <h5 class="card-title mb-1">
                                    <a href="single-product.php?id=<?= $related['product_id'] ?>" class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($related['product_name'] ?? 'Product Name') ?>
                                    </a>
                                </h5>
                                <div class="rating small mb-2">
                                    <?php
                                    $rating = rand(3, 5);
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rating ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-warning"></i>';
                                    }
                                    ?>
                                </div>
                                <div class="price">
                                    <span class="text-primary">Rs. <?= isset($related['product_price']) ? number_format($related['product_price'], 2) : '0.00' ?></span>
                                    <?php if (isset($related['original_price']) && $related['original_price'] > $related['product_price']): ?>
                                        <del class="text-muted small d-block">Rs. <?= number_format($related['original_price'], 2) ?></del>
                                    <?php endif; ?>
                                </div>
                                <a href="single-product.php?id=<?= $related['product_id'] ?>" class="btn btn-outline-primary btn-sm mt-3">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- JavaScript for Product Page -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Thumbnail image click handler
        document.querySelectorAll('.thumbnail-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const mainImg = document.getElementById('main_product_image');
                mainImg.src = this.getAttribute('data-image');
                
                // Update active thumbnail
                document.querySelectorAll('.thumbnail-item').forEach(thumb => {
                    thumb.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
        
        // Quantity selector
        const minusBtn = document.querySelector('.minus-btn');
        const plusBtn = document.querySelector('.plus-btn');
        const quantityInput = document.querySelector('.quantity-input');
        
        minusBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            if (value > 1) {
                quantityInput.value = value - 1;
            }
        });
        
        plusBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            quantityInput.value = value + 1;
        });
        
        // Rating stars for review form
        const stars = document.querySelectorAll('.rating-input i');
        const ratingValue = document.getElementById('rating-value');
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                ratingValue.value = rating;
                
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                    }
                });
            });
            
            star.addEventListener('mouseover', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                    }
                });
            });
            
            star.addEventListener('mouseout', function() {
                const currentRating = parseInt(ratingValue.value);
                
                stars.forEach((s, index) => {
                    if (index < currentRating) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                    }
                });
            });
        });
    });
</script>

<style>
    .breadcrumb {
        padding: 60px 0;
        position: relative;
        background-size: cover;
        background-position: center;
        background-image: url('https://via.placeholder.com/1920x400');
    }
    
    .breadcrumb:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }
    
    .breadcrumb_iner {
        position: relative;
        z-index: 1;
        color: white;
    }
    
    .breadcrumb_iner p {
        color: rgba(255, 255, 255, 0.8);
    }
    
    .product_details_slider {
        position: sticky;
        top: 20px;
    }
    
    .thumbnail-item {
        display: block;
        position: relative;
    }
    
    .thumbnail-item.active:after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border: 2px solid var(--bs-primary);
    }
    
    .product_quantity_wrapper .input-group {
        width: 150px;
    }
    
    .product_quantity_wrapper .btn {
        padding: 0.375rem 0.75rem;
    }
    
    .product_quantity_wrapper .form-control {
        text-align: center;
    }
    
    .rating-input i {
        cursor: pointer;
        font-size: 1.5rem;
        margin-right: 0.25rem;
    }
    
    .product-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 5px 10px;
        color: white;
        border-radius: 3px;
        font-size: 12px;
        z-index: 2;
    }
    
    .object-fit-cover {
        object-fit: cover;
    }
    
    .hover-shadow {
        transition: box-shadow 0.3s;
    }
    
    .hover-shadow:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
    
    .transition-all {
        transition: all 0.3s ease;
    }
</style>

<?php
include 'footer.php';
?>