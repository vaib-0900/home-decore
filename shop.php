<?php
include 'header.php';
?>
<!--================Home Banner Area =================-->
<!-- breadcrumb start-->
<!-- breadcrumb start-->
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

<!-- breadcrumb start-->
<!-- breadcrumb start-->

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
                            <div class="d-flex justify-content-between mb-2">
                                <span id="price-min" class="badge bg-light text-dark">Rs. 0</span>
                                <span id="price-max" class="badge bg-primary">Rs. 10000</span>
                            </div>
                            <input type="range" class="form-range" id="price-range" min="0" max="10000" step="100">
                        </div>

                        <!-- Categories Filter -->
                        <div class="mb-4">
                            <h6 class="mb-3 d-flex align-items-center">
                                <i class="fas fa-list me-2 text-muted"></i> Categories
                            </h6>
                            <div class="filter-scroll" style="max-height: 200px; overflow-y: auto;">
                                <?php
                                include "db_connection.php";
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

                        <!-- Rating Filter -->
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
                        </div>

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

        // Filter products based on selected criteria
        document.getElementById('apply-filters').addEventListener('click', function() {
            const priceRange = parseInt(document.getElementById('price-range').value);
            const selectedCategories = Array.from(document.querySelectorAll('.category-filter:checked')).map(el => el.value);
            const selectedRatings = Array.from(document.querySelectorAll('.rating-filter:checked')).map(el => parseInt(el.value));

            const productCards = document.querySelectorAll('.product-card');
            let visibleCount = 0;

            productCards.forEach(card => {
                const price = parseFloat(card.dataset.price);
                const category = card.dataset.category;
                const rating = parseInt(card.dataset.rating);

                const priceMatch = price <= priceRange;
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
        });

        // Reset all filters
        document.getElementById('reset-filters').addEventListener('click', function() {
            document.getElementById('price-range').value = 10000;
            document.getElementById('price-max').textContent = 'Rs. 10000';
            document.querySelectorAll('.category-filter, .rating-filter').forEach(el => el.checked = false);
            document.querySelectorAll('.product-card').forEach(card => card.style.display = 'block');
            document.getElementById('showing-count').textContent = totalProducts;
        });

        // Sort products
        document.getElementById('sort-products').addEventListener('change', function() {
            const container = document.getElementById('products-container');
            const cards = Array.from(container.querySelectorAll('.product-card'));

            cards.sort((a, b) => {
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
                    return parseInt(a.dataset.id) - parseInt(b.dataset.id);
                }
            });

            // Re-append sorted cards
            cards.forEach(card => container.appendChild(card));
        });

        // Update price range display
        document.getElementById('price-range').addEventListener('input', function() {
            document.getElementById('price-max').textContent = 'Rs. ' + this.value;
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
    .breadcrumb {
        padding: 60px 0;
        position: relative;
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
    }

    .category-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .category-card:hover {
        transform: translateY(-5px);
    }

    .hover-shadow {
        transition: box-shadow 0.3s;
    }

    .hover-shadow:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .hover-scale {
        transition: transform 0.5s;
    }

    .hover-scale:hover {
        transform: scale(1.05);
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

    .product-img-container {
        background-color: #f8f9fa;
    }

    .product-actions button {
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s;
    }

    .card:hover .product-actions button {
        opacity: 1;
        transform: translateY(0);
    }

    .filter-scroll::-webkit-scrollbar {
        width: 5px;
    }

    .filter-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .filter-scroll::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .filter-scroll::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .object-fit-cover {
        object-fit: cover;
    }
</style>

<?php
include 'footer.php';
?>