<?php
include 'header.php';
include 'db_connection.php'; // Make sure this includes the recommendation functions

// Recommendation system functions
function getRecommendedProducts($customer_id = null, $limit = 4)
{
    global $conn;

    $recommended = array();

    if ($customer_id) {
        // Collaborative filtering approach
        // Step 1: Find users with similar purchase history
        $similar_users_query = "SELECT DISTINCT customer_id FROM orders WHERE product_id IN 
                              (SELECT product_id FROM orders WHERE customer_id = $customer_id) 
                              AND customer_id != $customer_id LIMIT 5";

        $similar_users = $conn->query($similar_users_query);

        if ($similar_users->num_rows > 0) {
            $product_ids = array();
            while ($user = $similar_users->fetch_assoc()) {
                $products_query = "SELECT product_id FROM orders WHERE customer_id = {$user['customer_id']} 
                                 AND product_id NOT IN 
                                 (SELECT product_id FROM orders WHERE customer_id = $customer_id)";
                $products = $conn->query($products_query);

                while ($product = $products->fetch_assoc()) {
                    $product_ids[] = $product['product_id'];
                }
            }

            // Count occurrences and get most frequently purchased products
            if (!empty($product_ids)) {
                $counts = array_count_values($product_ids);
                arsort($counts);
                $top_products = array_slice(array_keys($counts), 0, $limit);

                if (!empty($top_products)) {
                    $ids = implode(",", $top_products);
                    $recommended_query = "SELECT * FROM tbl_product WHERE product_id IN ($ids)";
                    $recommended = $conn->query($recommended_query)->fetch_all(MYSQLI_ASSOC);
                }
            }
        }
    }

    // Fallback to popular products if no recommendations found
    if (empty($recommended)) {
        $popular_query = "SELECT p.*, COUNT(p.product_id) as purchase_count 
                         FROM tbl_product p
                         LEFT JOIN orders o ON p.product_id = p.product_id
                         GROUP BY p.product_id
                         ORDER BY purchase_count DESC, p.product_id DESC
                         LIMIT $limit";
        $recommended = $conn->query($popular_query)->fetch_all(MYSQLI_ASSOC);
    }

    return $recommended;
}

function getSimilarProducts($product_id, $limit = 4)
{
    global $conn;

    // Get current product's category
    $product_query = "SELECT add_category FROM tbl_product WHERE product_id = $product_id";
    $product_result = $conn->query($product_query);

    if ($product_result->num_rows > 0) {
        $product = $product_result->fetch_assoc();
        $category_id = $product['add_category'];

        // Get products from same category (excluding current product)
        $similar_query = "SELECT p.*, COUNT(p.product_id) as purchase_count 
                         FROM tbl_product p
                         LEFT JOIN orders o ON p.product_id = p.product_id
                         WHERE p.add_category = $category_id AND p.product_id != $product_id
                         GROUP BY p.product_id
                         ORDER BY purchase_count DESC, p.product_id DESC
                         LIMIT $limit";

        return $conn->query($similar_query)->fetch_all(MYSQLI_ASSOC);
    }

    return array();
}
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
                                <button class="btn btn-sm btn-outline-secondary mb-2 price-range-btn active" data-min="0" data-max="1000000">All Prices</button>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="text-muted">
                        Showing <span id="showing-count">0</span> of <span id="total-count">0</span> products
                    </div>
                    <div class="sorting-options">
                        <select id="sort-products" class="form-select form-select-sm">
                            <option value="default">Default Sorting</option>

                        </select>
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
                            $rating = rand(3, 5);
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
                                    <?= $discount ?>

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

                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div>
                                                <h5 class="text-primary mb-0">
                                                    ₹.<?= number_format($row['sell_price'], 2) ?>
                                                </h5>
                                                <?php if (isset($row['product_price']) && $row['product_price'] > $row['sell_price']): ?>
                                                    <small class="text-muted" style="text-decoration: line-through;">₹.<?= number_format($row['product_price'], 2) ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex align-items-center" style="gap: 0.4rem;">
                                                <a href="addtocart.php?id=<?= $row['product_id'] ?>"
                                                    class="btn btn-sm btn-outline-primary rounded-pill d-flex align-items-center"
                                                    title="Add to Cart">
                                                    <i class="fas fa-cart-plus me-1"></i>
                                                </a>
                                                <a href="wishlist-insert.php?product_id=<?= $row['product_id'] ?>"
                                                    class="btn btn-sm btn-outline-danger rounded-pill d-flex align-items-center"
                                                    title="Add to Wishlist">
                                                    <i class="far fa-heart"></i>
                                                </a>
                                            </div>
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
<section class="py-5 bg-white">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h3 class="position-relative d-inline-block fw-bold">Recommended For You</h3>
        </div>

        <div class="row g-4">
            <?php
            $customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;
            $recommend_query = "SELECT * FROM tbl_product ORDER BY view_count DESC LIMIT 4";
            $recommend_result = mysqli_query($conn, $recommend_query);

            if (mysqli_num_rows($recommend_result) > 0) {
                while ($row = mysqli_fetch_array($recommend_result)) {
                    $discount_percent = 0;
                    if ($row['product_price'] > $row['sell_price']) {
                        $discount_percent = round(($row['product_price'] - $row['sell_price']) / $row['product_price'] * 100);
                    }
            ?>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card h-100 border-0 rounded-3 shadow-sm overflow-hidden transition-all hover-shadow">

                            <div class="product-img-container position-relative overflow-hidden" style="height: 200px; background-color: #f8f9fa;">
                                <a href="single-product.php?product_id=<?= $row['product_id'] ?>" class="text-decoration-none">
                                    <img src="admin/<?= htmlspecialchars($row['product_image']) ?>"
                                        class="img-fluid p-3 h-100 w-100"
                                        style="object-fit: contain;"
                                        alt="<?= htmlspecialchars($row['product_name']) ?>">
                                </a>
                            </div>

                            <div class="card-body p-3 text-center">
                                <div class="rating small mb-2">
                                    <?php
                                    $rating = rand(3, 5); // Random rating for demo - replace with actual rating if available
                                    for ($i = 1; $i <= 5; $i++): ?>
                                        <?= $i <= $rating ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-warning"></i>' ?>
                                    <?php endfor; ?>
                                </div>

                                <a href="single-product.php?id=<?= $row['product_id'] ?>" class="text-decoration-none">
                                    <h5 class="card-title mb-2 text-dark hover-text-primary"><?= htmlspecialchars($row['product_name']) ?></h5>
                                </a>

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <h5 class="text-primary mb-0">
                                            ₹.<?= number_format($row['sell_price'], 2) ?>
                                        </h5>
                                        <?php if (isset($row['product_price']) && $row['product_price'] > $row['sell_price']): ?>
                                            <small class="text-muted" style="text-decoration: line-through;">₹.<?= number_format($row['product_price'], 2) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex align-items-center" style="gap: 0.4rem;">
                                        <a href="addtocart.php?id=<?= $row['product_id'] ?>"
                                            class="btn btn-sm btn-outline-primary rounded-pill d-flex align-items-center"
                                            title="Add to Cart">
                                            <i class="fas fa-cart-plus me-1"></i>
                                        </a>
                                        <a href="wishlist-insert.php?product_id=<?= $row['product_id'] ?>"
                                            class="btn btn-sm btn-outline-danger rounded-pill d-flex align-items-center"
                                            title="Add to Wishlist">
                                            <i class="far fa-heart"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<div class="col-12 text-center"><p>No recommendations available at the moment.</p></div>';
            }
            ?>
        </div>
    </div>
</section>
<!-- JavaScript for Filtering and Sorting -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize elements
        const productCards = document.querySelectorAll('.product-card');
        const totalProducts = productCards.length;
        const productsContainer = document.getElementById('products-container');
        const showingCountElement = document.getElementById('showing-count');
        const totalCountElement = document.getElementById('total-count');
        const applyFiltersBtn = document.getElementById('apply-filters');
        const resetFiltersBtn = document.getElementById('reset-filters');
        const sortSelect = document.getElementById('sort-products');
        const priceRangeButtons = document.querySelectorAll('.price-range-btn');
        const categoryCheckboxes = document.querySelectorAll('.category-filter');

        // Initialize counts
        totalCountElement.textContent = totalProducts;
        updateProductCount(totalProducts);

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Current filter state
        let currentFilters = {
            priceRange: {
                min: 0,
                max: 1000000
            },
            categories: [],
            sortBy: 'default'
        };

        // Price range filter
        priceRangeButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Update UI
                priceRangeButtons.forEach(btn => {
                    btn.classList.remove('active', 'btn-primary');
                    btn.classList.add('btn-outline-secondary');
                });
                this.classList.add('active', 'btn-primary');
                this.classList.remove('btn-outline-secondary');

                // Update filter state
                currentFilters.priceRange = {
                    min: parseInt(this.dataset.min),
                    max: parseInt(this.dataset.max)
                };

                applyFilters();
            });
        });

        // Category filter
        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                currentFilters.categories = Array.from(document.querySelectorAll('.category-filter:checked'))
                    .map(el => el.value);
                applyFilters();
            });
        });

        // Sorting
        sortSelect.addEventListener('change', function() {
            currentFilters.sortBy = this.value;
            applyFilters();
        });

        // Apply filters
        function applyFilters() {
            let visibleProducts = 0;
            const filteredProducts = [];

            // Filter products
            productCards.forEach(card => {
                const price = parseFloat(card.dataset.price);
                const category = card.dataset.category;
                const rating = parseInt(card.dataset.rating);
                const date = parseInt(card.dataset.date);

                // Price filter
                const priceMatch = price >= currentFilters.priceRange.min &&
                    price <= currentFilters.priceRange.max;

                // Category filter
                const categoryMatch = currentFilters.categories.length === 0 ||
                    currentFilters.categories.includes(category);

                // Combined filter
                if (priceMatch && categoryMatch) {
                    card.style.display = 'block';
                    visibleProducts++;
                    filteredProducts.push(card);
                } else {
                    card.style.display = 'none';
                }
            });

            // Sort products
            if (currentFilters.sortBy !== 'default') {
                sortProducts(filteredProducts, currentFilters.sortBy);
            }

            // Update count
            updateProductCount(visibleProducts);

            // Show/hide no products message
            toggleNoProductsMessage(visibleProducts);
        }

        // Sort products
        function sortProducts(products, sortBy) {
            const container = productsContainer;

            products.sort((a, b) => {
                switch (sortBy) {
                    case 'price-low':
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    case 'price-high':
                        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                    case 'rating':
                        return parseInt(b.dataset.rating) - parseInt(a.dataset.rating);
                    case 'newest':
                        return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                    default:
                        return 0;
                }
            });

            // Re-append sorted products
            products.forEach(product => {
                container.appendChild(product);
            });
        }

        // Reset all filters
        resetFiltersBtn.addEventListener('click', function() {
            // Reset price range
            currentFilters.priceRange = {
                min: 0,
                max: 1000000
            };
            priceRangeButtons[0].click(); // Click the "All Prices" button

            // Reset categories
            categoryCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            currentFilters.categories = [];

            // Reset sorting
            sortSelect.value = 'default';
            currentFilters.sortBy = 'default';

            // Apply reset
            applyFilters();
        });

        // Update product count display
        function updateProductCount(count) {
            showingCountElement.textContent = count;
        }

        // Show/hide no products message
        function toggleNoProductsMessage(visibleCount) {
            let noProductsMsg = productsContainer.querySelector('.no-products-message');

            if (visibleCount === 0 && !noProductsMsg) {
                noProductsMsg = document.createElement('div');
                noProductsMsg.className = 'col-12 text-center py-5 no-products-message';
                noProductsMsg.innerHTML = '<div class="alert alert-warning">No products match your filters. Try adjusting your criteria.</div>';
                productsContainer.appendChild(noProductsMsg);
            } else if (visibleCount > 0 && noProductsMsg) {
                noProductsMsg.remove();
            }
        }

        // Quick view functionality
        document.querySelectorAll('.quick-view').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                // Here you would typically fetch product details via AJAX
                console.log('Quick view for product ID:', productId);
                // Example: showQuickViewModal(productId);
            });
        });

        // Initialize with default filters
        applyFilters();
    });
</script>

<style>
    /* Add these styles to your existing CSS */
    .price-range-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .price-range-buttons .btn {
        text-align: left;
        transition: all 0.3s;
    }

    .price-range-buttons .btn.active {
        background-color: var(--bs-primary);
        color: white;
        border-color: var(--bs-primary);
    }

    .filter-scroll {
        max-height: 200px;
        overflow-y: auto;
        padding-right: 0.5rem;
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

    .no-products-message {
        grid-column: 1 / -1;
    }
</style>

<?php
include 'footer.php';
?>