<?php
include 'header.php';
?>
    <!--================Home Banner Area =================-->
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
      <h3 class="text-center mb-4">Categories</h3>
      <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
          <?php
          $category_id = $row['category_id'];
          $category_name = htmlspecialchars($row['category_name']);
          $category_image = htmlspecialchars($row['category_image']);
          ?>
          <div class="col-md-3 mb-4">
            <a href="category.php?id=<?= $category_id ?>" class="text-decoration-none text-dark text-center">
              <img src="admin/upload/<?= $category_image ?>" alt="<?= $category_name ?>" class="img-fluid mb-2" style="height: 80px;">
              <h5 class="mb-0"><?= $category_name ?></h5>
            </a>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  <?php else: ?>
    <p class="text-center">No categories found.</p>
  <?php endif; ?>
</section>

    <!-- ================ category section end ================= -->

    <!-- ================ product section start ================= -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar Filters (optional) -->
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Filters</h5>
                        
                        <!-- Price Range Filter -->
                        <div class="mb-4">
                            <h6 class="mb-3">Price Range</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span id="price-min">Rs. 0</span>
                                <span id="price-max">Rs. 10000</span>
                            </div>
                            <input type="range" class="form-range" id="price-range" min="0" max="10000" step="100">
                        </div>
                        
                        <!-- Categories Filter -->
                        <div class="mb-4">
                            <h6 class="mb-3">Categories</h6>
                            <?php
                            include "db_connection.php";
                            $categories = $conn->query("SELECT * FROM tbl_category");
                            while($cat = $categories->fetch_assoc()) {
                                echo '<div class="form-check mb-2">
                                    <input class="form-check-input category-filter" type="checkbox" value="'.$cat['category_id'].'" id="cat-'.$cat['category_id'].'">
                                    <label class="form-check-label" for="cat-'.$cat['category_id'].'">
                                        '.$cat['category_name'].'
                                    </label>
                                </div>';
                            }
                            ?>
                        </div>
                        
                        <!-- Rating Filter -->
                        <div class="mb-3">
                            <h6 class="mb-3">Rating</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input rating-filter" type="checkbox" value="5" id="rating-5">
                                <label class="form-check-label" for="rating-5">
                                    <?php for($i=0; $i<5; $i++) echo '<iconify-icon icon="clarity:star-solid" class="text-warning"></iconify-icon>'; ?>
                                    & Up
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input rating-filter" type="checkbox" value="4" id="rating-4">
                                <label class="form-check-label" for="rating-4">
                                    <?php for($i=0; $i<4; $i++) echo '<iconify-icon icon="clarity:star-solid" class="text-warning"></iconify-icon>'; ?>
                                    & Up
                                </label>
                            </div>
                        </div>
                        
                        <button class="btn btn-primary w-100 mt-3" id="apply-filters">Apply Filters</button>
                        <button class="btn btn-outline-secondary w-100 mt-2" id="reset-filters">Reset</button>
                    </div>
                </div>
            </div>
            
            <!-- Product Listing -->
            <div class="col-md-9">
                <!-- Sorting Options -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <span class="me-2">Sort by:</span>
                        <select class="form-select form-select-sm w-auto" id="sort-products">
                            <option value="default">Default</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="rating">Rating</option>
                            <option value="newest">Newest</option>
                        </select>
                    </div>
                    <div class="text-muted">
                        Showing <span id="showing-count">0</span> of <span id="total-count">0</span> products
                    </div>
                </div>
                
                <!-- Products Grid -->
                <div class="row" id="products-container">
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
                    ?>
                            <div class="col-lg-4 col-md-6 mb-4 product-card"
                                data-price="<?= $row['product_price'] ?>"
                                data-category="<?= $row['add_category'] ?>"
                                data-rating="<?= $rating ?>"
                                data-date="<?= strtotime($row['created_at'] ?? date('Y-m-d')) ?>">
                                <div class="card h-100 border-0 rounded-3 shadow-sm overflow-hidden">
                                    <!-- Product Badge -->
                                   
                                    <!-- Product Image -->
                                    <a href="single-product.php?id=<?= $row['product_id'] ?>" class="text-decoration-none">
                                        <img src="admin/upload/product/<?= htmlspecialchars($row['product_image']) ?>" 
                                             class="card-img-top" alt="<?= htmlspecialchars($row['product_name']) ?>">
                                    </a>
                                    
                                    <!-- Product Body -->
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-light text-dark"><?= $row['category_name'] ?></span>
                                        </div>
                                        
                                        <a href="single-product.php?id=<?= $row['product_id'] ?>" class="text-decoration-none">
                                            <h5 class="card-title mb-2"><?= htmlspecialchars($row['product_name']) ?></h5>
                                        </a>
                                        
                                        <p class="card-text small text-muted mb-2">
                                            <?= substr(htmlspecialchars($row['product_description']), 0, 80) ?>...
                                        </p>
                                        
                                        <!-- Rating -->
                                        <div class="rating mb-2">
                                            <?php 
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $rating) {
                                                    echo '<iconify-icon icon="clarity:star-solid" class="text-warning"></iconify-icon>';
                                                } else {
                                                    echo '<iconify-icon icon="clarity:star-line" class="text-warning"></iconify-icon>';
                                                }
                                            }
                                            ?>
                                            <small class="text-muted ms-1">(<?= $rating ?>.0)</small>
                                        </div>
                                        
                                        <!-- Price -->
                                        <h5 class="text-primary mb-3">
                                            Rs. <?= number_format($row['product_price'], 2) ?>
                                            <?php if(isset($row['original_price']) && $row['original_price'] > $row['product_price']): ?>
                                                <small class="text-muted text-decoration-line-through ms-2">Rs. <?= number_format($row['original_price'], 2) ?></small>
                                            <?php endif; ?>
                                        </h5>
                                        
                                        <!-- Action Buttons -->
                                        <div class="d-flex justify-content-between gap-2">
                                            <a href="addtocart.php?id=<?= $row['product_id'] ?>" 
                                               class="btn btn-primary btn-sm flex-grow-1 d-flex align-items-center justify-content-center py-2">
                                                <iconify-icon icon="mdi:cart-plus" class="me-1"></iconify-icon>
                                                <span>Add to Cart</span>
                                            </a>
                                            <a href="wishlist.php?id=<?= $row['product_id'] ?>" 
                                               class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center px-3 py-2">
                                                <iconify-icon icon="fluent:heart-28-regular"></iconify-icon>
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
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
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
});
</script>







<?php
include 'footer.php';
?>