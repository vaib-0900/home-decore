<?php
include("db_connection.php");
include 'header.php';
if (!isset($_SESSION["login"])) {
    echo "<script> window.location.href='SignUp_LogIn_Form.php'</script>";
}
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

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-uppercase text-gradient">Your Wishlist</h1>
        <p class="text-muted fs-5">The products you love, all in one place.</p>
    </div>

    <div class="row g-5">
        <?php
        $customer = $_SESSION["customer_id"];
        $query = "SELECT * FROM tbl_wishlist 
                  INNER JOIN tbl_product ON tbl_product.product_id = tbl_wishlist.wishlist_product_id 
                  WHERE tbl_wishlist.wishlist_customer = $customer";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-lg rounded-4 h-100 position-relative animate_animated animate_fadeInUp">
                        <span class="badge bg-danger position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill">
                            <?= $row['discount_per'] ?>% OFF
                        </span>
                        <a href="single_product.php?product_id=<?= $row['product_id'] ?>" class="text-decoration-none">
                            <img src="admin/<?= $row['product_image'] ?>" class="card-img-top p-4 rounded-top"
                                style="height: 260px; object-fit: contain;">
                        </a>
                        <div class="card-body d-flex flex-column text-center px-4">
                            <h5 class="fw-bold mb-2 text-dark"><?= $row['product_name'] ?></h5>
                            <div class="text-warning mb-2">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i>
                                <i class="fas fa-star"></i><i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <small class="text-muted ms-1">(400+ reviews)</small>
                            </div>
                            <div class="mb-3">
                                <span class="fs-5 text-success fw-bold"><?= $row['sell_price'] ?> Rs</span>
                               
                            </div>
                            <div class="d-flex gap-2 mt-auto">
                               <a href="addtocart.php?id=<?= $row['product_id'] ?>"
                                                class="btn btn-primary btn-sm rounded-pill px-3 py-2 d-flex align-items-center">
                                                <i class="fas fa-cart-plus me-1"></i>
                                                <span>Add</span>
                                            </a>
                                <a href="wish_list_delete.php?product_id=<?= $row['product_id'] ?>"
                                    onclick="return confirm('Remove this item from your wishlist?');"
                                    class="btn btn-outline-danger rounded-pill">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div class='col-12 text-center'><p class='fs-4 text-muted'>Oops! Your wishlist is empty.</p></div>";
        }
        ?>
    </div>
</div>

<?php
include 'footer.php';
?>