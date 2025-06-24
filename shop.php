<?php
include 'header.php';
?>
<!--================Home Banner Area =================-->
<section class="breadcrumb breadcrumb_bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="breadcrumb_iner">
                    <div class="breadcrumb_iner_item">
                        <h2>Shop Products</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================ category section start ================= -->
<section class="mt-5 mb-5" id="categories">
    <?php
    include "db_connection.php";
    // Fetch all categories from the database
    $query = "SELECT * FROM tbl_category";
    $result = $conn->query($query);
    ?>

    <?php if ($result->num_rows > 0): ?>
        <div class="container">
            <div class="section-title text-center mb-5">
                <h3 class="position-relative d-inline-block">Our Categories</h3>
            </div>
            <div class="row g-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                    $category_id = $row['category_id'];
                    $category_name = htmlspecialchars($row['category_name']);
                    $category_image = htmlspecialchars($row['category_image']);
                    ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="category-card card border-0 shadow-sm h-100 transition-all hover-shadow">
                            <a href="category.php?id=<?= $category_id ?>" class="text-decoration-none text-dark">
                                <div class="category-img-container position-relative overflow-hidden" style="height: 200px;">
                                    <img src="admin/upload/<?= $category_image ?>" alt="<?= $category_name ?>"
                                        class="img-fluid w-100 h-100 object-fit-cover transition-all hover-scale">
                                </div>
                                <div class="card-body text-center">
                                    <h5 class="mb-0"><?= $category_name ?></h5>
                                    <small class="text-muted">Shop Now <i class="fas fa-arrow-right ms-1"></i></small>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="container">
            <div class="alert alert-info text-center">No categories found.</div>
        </div>
    <?php endif; ?>
</section>
<!-- ================ category section end ================= -->

<!-- ================ product section start ================= -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h5 class="card-title mb-4 d-flex align-items-center">
                            <i class="fas fa-filter me-2 text-primary"></i> Filters
                        </h5>

                        <!-- Price Range Filter -->
                        <div class="mb-4">
                            <h6 class="mb-3 d-flex align-items-center">
                                <i class="fas fa-tag me-2 text-muted"></i> Price Range
                            </h6>
                            <div class="price-range-buttons">
                                <button class="btn btn-sm btn-outline-secondary mb-2 price-range-btn" data-min="0" data-max="1000000">All Prices</button>
                                <button class="btn btn-sm btn-outline-secondary mb-2 price-range-btn" data-min="1000" data-max="3000">Rs. 1,000 - 3,000</button>
                                <button class="btn btn-sm btn-outline-secondary mb-2 price-range-btn" data-min="3000" data-max="6000">Rs. 3,000 - 6,000</button>
                                <button class="btn btn-sm btn-outline-secondary mb-2 price-range-btn" data-min="6000" data-max="10000">Rs. 6,000 - 10,000</button>
                                <button class="btn btn-sm btn-outline-secondary mb-2 price-range-btn" data-min="10000" data-max="13000">Rs. 10,000 - 13,000</button>
                                <button class="btn btn-sm btn-outline-secondary mb-2 price-range-btn" data-min="13000" data-max="20000">Rs. 13,000 - 20,000</button>
                                <button class="btn btn-sm btn-outline-secondary mb-2 price-range-btn" data-min="20000" data-max="25000">Rs. 20,000 - 25,000</button>
                                <button class="btn btn-sm btn-outline-secondary mb-2 price-range-btn" data-min="25000" data-max="30000">Rs. 25,000 - 30,000</button>
                            </div>
                        </div>

                        <!-- Categories Filter -->
                        <div class="mb-4">
                            <h6 class="mb-3 d-flex align-items-center">
                                <i class="fas fa-list me-2 text-muted"></i> Categories
                            </h6>
                            <div class="filter-scroll" style="max-height: 200px; overflow-y: auto;">
                                <?php
                                $categories = $conn->query("SELECT * FROM tbl_category");
                                while ($cat = $categories->fetch_assoc()) {
                                    echo '<div class="form-check mb-2">
                                        <input class="form-check-input category-filter" type="checkbox" value="' . $cat['category_id'] . '" id="cat-' . $cat['category_id'] . '">
                                        <label class="form-check-label d-flex align-items-center" for="cat-' . $cat['category_id'] . '">
                                            <span class="d-inline-block me-2" style="width: 20px; height: 20px; background-color: #' . substr(md5($cat['category_name']), 0, 6) . '; opacity: 0.7;"></span>
                                            ' . $cat['category_name'] . '
                                        </label>
                                    </div>';
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Rating Filter
                        <div class="mb-3">
                            <h6 class="mb-3 d-flex align-items-center">
                                <i class="fas fa-star me-2 text-muted"></i> Rating
                            </h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input rating-filter" type="checkbox" value="5" id="rating-5">
                                <label class="form-check-label" for="rating-5">
                                    <?php for ($i = 0; $i < 5; $i++) echo '<i class="fas fa-star text-warning"></i>'; ?>
                                    <span class="ms-1">& Up</span>
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input rating-filter" type="checkbox" value="4" id="rating-4">
                                <label class="form-check-label" for="rating-4">
                                    <?php for ($i = 0; $i < 4; $i++) echo '<i class="fas fa-star text-warning"></i>'; ?>
                                    <i class="far fa-star text-warning"></i>
                                    <span class="ms-1">& Up</span>
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input rating-filter" type="checkbox" value="3" id="rating-3">
                                <label class="form-check-label" for="rating-3">
                                    <?php for ($i = 0; $i < 3; $i++) echo '<i class="fas fa-star text-warning"></i>'; ?>
                                    <?php for ($i = 0; $i < 2; $i++) echo '<i class="far fa-star text-warning"></i>'; ?>
                                    <span class="ms-1">& Up</span>
                                </label>
                            </div>
                        </div> -->

                        <button class="btn btn-primary w-100 mt-3" id="apply-filters">
                            <i class="fas fa-check-circle me-2"></i> Apply Filters
                        </button>
                        <button class="btn btn-outline-secondary w-100 mt-2" id="reset-filters">
                            <i class="fas fa-sync-alt me-2"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Listing -->
            <div class="col-lg-9 col-md-8">
                <!-- Sorting Options -->
                <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-3 shadow-sm">
                    <div class="text-muted">
                        <span id="showing-count"><?= $total_products ?></span> of <span id="total-count"><?= $total_products ?></span> products
                    </div>
                    <!--<div class="sort-options">
                        <select class="form-select form-select-sm" id="sort-products">
                            <option value="default">Default Sorting</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="rating">Highest Rating</option>
                            <option value="newest">Newest Arrivals</option>
                        </select>
                    </div>-->
                </div>

                <!-- Products Grid -->
                <div class="row g-4" id="products-container">
                    <?php
                    $query = "SELECT p.*, c.category_name 
                              FROM tbl_product p
                              LEFT JOIN tbl_category c ON p.add_category = c.category_id
                              ORDER BY p.product_id DESC";
                    $result = $conn->query($query);
                    $total_products = $result->num_rows;

                    if ($total_products > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Calculate random rating for demo (replace with actual rating from DB if available)
                            $rating = rand(3, 5);
                            // Calculate discount if original price exists
                            $discount = '';
                            if (isset($row['original_price']) && $row['original_price'] > $row['product_price']) {
                                $discount_percent = round(($row['original_price'] - $row['product_price']) / $row['original_price'] * 100);
                                $discount = '<span class="product-badge bg-danger">-' . $discount_percent . '%</span>';
                            }
                    ?>
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 mb-4 product-card"
                                data-price="<?= $row['product_price'] ?>"
                                data-category="<?= $row['add_category'] ?>"
                                data-rating="<?= $rating ?>"
                                data-date="<?= strtotime($row['created_at'] ?? date('Y-m-d')) ?>">
                                <div class="card h-100 border-0 rounded-3 shadow-sm overflow-hidden transition-all hover-shadow">
                                    <!-- Product Badge -->
                                    <?= $discount ?>

                                    <!-- Product Image -->
                                    <div class="product-img-container position-relative overflow-hidden" style="height: 250px;">
                                        <a href="single-product.php?id=<?= $row['product_id'] ?>" class="text-decoration-none">
                                            <img src="admin/<?= htmlspecialchars($row['product_image']) ?>" class="img-thumbnail" alt="<?= htmlspecialchars($row['product_image']) ?>">
                                            <div class="product-actions position-absolute top-0 end-0 m-2">                         
                                                <button class="btn btn-sm btn-light rounded-circle shadow-sm quick-view" data-id="<?= $row['product_id'] ?>" data-bs-toggle="tooltip" title="Quick View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Product Body -->
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-light text-dark"><?= $row['category_name'] ?></span>
                                            <div class="rating small">
                                                <?php
                                                for ($i = 1; $i <= 5; $i++) {
                                                    echo $i <= $rating ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-warning"></i>';
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <a href="single-product.php?id=<?= $row['product_id'] ?>" class="text-decoration-none">
                                            <h5 class="card-title mb-2 text-dark hover-text-primary"><?= htmlspecialchars($row['product_name']) ?></h5>
                                        </a>

                                        <p class="card-text small text-muted mb-2">
                                            <?= substr(htmlspecialchars($row['product_description']), 0, 80) ?>...
                                        </p>

                                        <!-- Price -->
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div>
                                                <h5 class="text-primary mb-0">
                                                    Rs. <?= number_format($row['product_price'], 2) ?>
                                                </h5>
                                                <?php if (isset($row['original_price']) && $row['original_price'] > $row['product_price']): ?>
                                                    <small class="text-muted text-decoration-line-through">Rs. <?= number_format($row['original_price'], 2) ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <a href="addtocart.php?id=<?= $row['product_id'] ?>"
                                                class="btn btn-primary btn-sm rounded-pill px-3 py-2 d-flex align-items-center">
                                                <i class="fas fa-cart-plus me-1"></i>
                                                <span>Add</span>
                                            </a>
                                            <a href="wishlist-insert.php?product_id=<?= $row['product_id'] ?>"
                                                class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-flex align-items-center">
                                                <i class="far fa-heart"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo '<div class="col-12 text-center py-5">
                                <div class="alert alert-info">No products found. Please check back later.</div>
                              </div>';
                    }
                    $conn->close();
                    ?>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for Filtering and Sorting -->
<!-- JavaScript for Filtering and Sorting -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize counts
        const totalProducts = <?= $total_products ?>;
        document.getElementById('total-count').textContent = totalProducts;
        document.getElementById('showing-count').textContent = totalProducts;

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Price range buttons functionality
        let currentPriceRange = { min: 0, max: 1000000 };
        const priceRangeButtons = document.querySelectorAll('.price-range-btn');
        
        // Set "All Prices" as active by default
        priceRangeButtons[0].classList.add('active', 'btn-primary');
        priceRangeButtons[0].classList.remove('btn-outline-secondary');
        
        priceRangeButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                priceRangeButtons.forEach(btn => {
                    btn.classList.remove('active', 'btn-primary');
                    btn.classList.add('btn-outline-secondary');
                });
                
                // Add active class to clicked button
                this.classList.add('active', 'btn-primary');
                this.classList.remove('btn-outline-secondary');
                
                // Set current price range
                currentPriceRange = {
                    min: parseInt(this.getAttribute('data-min')),
                    max: parseInt(this.getAttribute('data-max'))
                };
                
                // Immediately apply filter when price range is clicked
                applyFilters();
            });
        });

        // Function to apply all filters
        function applyFilters() {
            const selectedCategories = Array.from(document.querySelectorAll('.category-filter:checked')).map(el => el.value);
            const selectedRatings = Array.from(document.querySelectorAll('.rating-filter:checked')).map(el => parseInt(el.value));

            const productCards = document.querySelectorAll('.product-card');
            let visibleCount = 0;

            productCards.forEach(card => {
                const price = parseFloat(card.dataset.price);
                const category = card.dataset.category;
                const rating = parseInt(card.dataset.rating);

                const priceMatch = price >= currentPriceRange.min && price <= currentPriceRange.max;
                const categoryMatch = selectedCategories.length === 0 || selectedCategories.includes(category);
                const ratingMatch = selectedRatings.length === 0 || selectedRatings.some(r => rating >= r);

                if (priceMatch && categoryMatch && ratingMatch) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('showing-count').textContent = visibleCount;
            
            // Show message if no products match filters
            const container = document.getElementById('products-container');
            let noProductsMsg = container.querySelector('.no-products-message');
            
            if (visibleCount === 0) {
                if (!noProductsMsg) {
                    noProductsMsg = document.createElement('div');
                    noProductsMsg.className = 'col-12 text-center py-5 no-products-message';
                    noProductsMsg.innerHTML = '<div class="alert alert-warning">No products match your filters. Try adjusting your criteria.</div>';
                    container.appendChild(noProductsMsg);
                }
            } else if (noProductsMsg) {
                noProductsMsg.remove();
            }
        }

        // Apply filters when button clicked
        document.getElementById('apply-filters').addEventListener('click', applyFilters);

        // Reset all filters
        document.getElementById('reset-filters').addEventListener('click', function() {
            // Reset price range
            currentPriceRange = { min: 0, max: 1000000 };
            priceRangeButtons.forEach(btn => {
                btn.classList.remove('active', 'btn-primary');
                btn.classList.add('btn-outline-secondary');
            });
            
            // Set "All Prices" as active
            priceRangeButtons[0].classList.add('active', 'btn-primary');
            priceRangeButtons[0].classList.remove('btn-outline-secondary');
            
            // Reset category and rating filters
            document.querySelectorAll('.category-filter, .rating-filter').forEach(el => el.checked = false);
            
            // Show all products
            document.querySelectorAll('.product-card').forEach(card => card.style.display = 'block');
            document.getElementById('showing-count').textContent = totalProducts;
            
            // Remove any no products message
            const noProductsMsg = document.querySelector('.no-products-message');
            if (noProductsMsg) {
                noProductsMsg.remove();
            }
        });

        // Sort products
        document.getElementById('sort-products').addEventListener('change', function() {
            const container = document.getElementById('products-container');
            const cards = Array.from(container.querySelectorAll('.product-card'));
            
            // Filter out hidden cards
            const visibleCards = cards.filter(card => card.style.display !== 'none');

            visibleCards.sort((a, b) => {
                const sortBy = this.value;

                if (sortBy === 'price-low') {
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                } else if (sortBy === 'price-high') {
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                } else if (sortBy === 'rating') {
                    return parseInt(b.dataset.rating) - parseInt(a.dataset.rating);
                } else if (sortBy === 'newest') {
                    return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                } else {
                    return 0; // Default sorting (keep original order)
                }
            });

            // Re-append sorted cards
            visibleCards.forEach(card => container.appendChild(card));
        });

        // Quick view functionality
        document.querySelectorAll('.quick-view').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                // Here you would typically fetch product details via AJAX and show in a modal
                alert('Quick view for product ID: ' + productId);
            });
        });

        // Add hover effects
        document.querySelectorAll('.hover-text-primary').forEach(el => {
            el.addEventListener('mouseover', () => {
                el.classList.add('text-primary');
                el.classList.remove('text-dark');
            });
            el.addEventListener('mouseout', () => {
                el.classList.remove('text-primary');
                el.classList.add('text-dark');
            });
        });
    });
</script>

<style>
    /* Your existing CSS styles remain the same */
    .price-range-buttons .btn {
        width: 100%;
        text-align: left;
        transition: all 0.3s;
        margin-bottom: 0.5rem;
    }
    
    .price-range-buttons .btn.active {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    
    .price-range-buttons .btn:hover {
        transform: translateX(5px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .price-range-buttons .btn:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>

<?php
include 'footer.php';
?>