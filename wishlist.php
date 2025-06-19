<?php
include 'header.php';
include("db_connection.php");
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toastEl = document.querySelector('.toast');
        if (toastEl) {
            const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
        }
    });
</script>
<?php if (isset($_SESSION['success'])): ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;text-align: center;">
        <div class="toast text-center align-items-center text-white bg-success border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['delete'])): ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;text-align: center;">
        <div class="toast text-center align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars($_SESSION['delete']) ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['delete']); ?>
<?php endif; ?>
<div class="container">
    <div class="text-center mb-5 mt-4">
        <h1 class="display-5 fw-bold text-uppercase">Your Wishlist</h1>
        <p class="text-muted">Browse and manage your saved items</p>
    </div>
    <div class="row g-4">
        <?php
        // Check if customer is logged in
        if (!isset($_SESSION["customer_id"])) {
            echo "<div class='col-12 text-center'><p class='text-muted fs-5'>Please login to view your wishlist.</p></div>";
        } else {
            $customer = $_SESSION["customer_id"];
            // Modified query to get wishlist items with proper join
            $query = "SELECT tbl_wishlist.wishlist_id, tbl_product.* 
                      FROM tbl_wishlist 
                      INNER JOIN tbl_product ON tbl_product.product_id = tbl_wishlist.wishlist_product_id 
                      WHERE tbl_wishlist.wishlist_customer = $customer";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    ?>
                    <div class="col-md-6 col-lg-6 col-xl-6">
                        <div class="card product-card h-100 border-0 shadow-sm hover-top">
                            <?php if ($row['discount_per'] > 0): ?>
                                <div class="badge bg-success position-absolute top-0 end-0 m-2">
                                    <?= $row['discount_per'] ?>% OFF
                                </div>
                            <?php endif; ?>
                            <div class="product-image">
                                <a href="single_productview.php?product_id=<?= $row['product_id'] ?>">
                                    <img src="admin/<?= htmlspecialchars($row['product_image']) ?>" class="card-img-top"
                                        style="height: 200px; object-fit: contain;" alt="<?= htmlspecialchars($row['product_name']) ?>">
                                </a>
                            </div>
                            <div class="card-body d-flex flex-column shadow-sm">
                                <div class="mb-2">
                                    <a href="single_productview.php?product_id=<?= $row['product_id'] ?>" class="text-decoration-none">
                                        <h5 class="card-title mb-1 text-center"><?= htmlspecialchars($row['product_name']) ?></h5>
                                    </a>
                                    <div class="d-flex text-center mb-2 ms-3">
                                        <div class="text-warning small me-2">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <span class="text-muted small">(400 reviews)</span>
                                    </div>
                                </div>
                                <div class="mt-auto">
                                    <div class="d-flex align-items-center mb-3 justify-content-center">
                                        <span class="text-dark fw-bold fs-5 me-2"><?= number_format($row['product_sell_price'], 2) ?> Rs</span>
                                        <?php if ($row['product_mrp'] > $row['product_sell_price']): ?>
                                            <span class="text-muted text-decoration-line-through"><?= number_format($row['product_mrp'], 2) ?> Rs</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <form action="addtocart.php" method="post" class="flex-grow-1">
                                            <input type="hidden" name="id" value="<?= $row['product_id'] ?>">
                                            <input type="hidden" name="cart_qty" value="1">
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                            </button>
                                        </form>
                                        <a href="wishlist_delete.php?wishlist_id=<?= $row['wishlist_id'] ?>" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to remove this item from your wishlist?');">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div class='col-12 text-center'><p class='text-muted fs-5'>Your wishlist is empty.</p></div>";
            }
        }
        ?>
    </div>
</div>
<?php
include 'footer.php';
?>