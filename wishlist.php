<?php
include "header.php";
include "db_connection.php";

// Get wishlist items
$customer_id = $_SESSION['customer_id'];
$query = "SELECT tbl_wishlist.*, tbl_product.* 
          FROM tbl_wishlist 
          INNER JOIN tbl_product ON tbl_product.product_id = tbl_wishlist.wishlist_product_id 
          WHERE tbl_wishlist.wishlist_customer = '$customer_id'";
$result = mysqli_query($conn, $query);
?>
<!-- breadcrumb start-->
<section class="breadcrumb breadcrumb_bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="breadcrumb_iner">
                    <div class="breadcrumb_iner_item">
                        <h2>My Wishlist</h2>
                        <p>Home <span>-</span>My Wishlist</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- breadcrumb start-->
<div class="container py-5 mt-3">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-heart me-2 text-danger"></i>My Wishlist</h4>
                        <span class="badge bg-danger rounded-pill">
                            <?php echo mysqli_num_rows($result); ?> item(s)
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (isset($_GET['added'])): ?>
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i>Product added to wishlist!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['removed'])): ?>
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i>Product removed from wishlist!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50%" class="ps-4">Product</th>
                                    <th width="20%">Price</th>
                                    <th width="10%" class="pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr class="align-middle">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="admin/<?php echo htmlspecialchars($row['product_image']); ?>"
                                                        class="img-thumbnail rounded me-3"
                                                        width="80"
                                                        style="object-fit: cover; height: 80px;"
                                                        alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                                                    <div>
                                                        <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($row['product_name']); ?></h6>
                                                        <small class="text-muted"><?php echo substr(htmlspecialchars($row['product_description']), 0, 50); ?>...</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary">Rs.<?php echo number_format($row['product_price'], 2); ?></span>
                                            </td>
                                           
                                            <td class="pe-4">
                                                <div class="d-flex">
                                                    <form action="addtocart.php" method="get" class="me-2">
                                                        <input type="hidden" name="id" value="<?php echo $row['product_id']; ?>">
                                                        <button type="submit" class="btn btn-primary btn-sm me-2"
                                                                data-bs-toggle="tooltip" title="Add to Cart">
                                                            <i class="fas fa-shopping-cart"></i>
                                                        </button>
                                                    </form>  
                                                    <form action="remove_from_wishlist.php" method="post">
                                                        <input type="hidden" name="wishlist_id" value="<?php echo $row['wishlist_id']; ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" 
                                                                data-bs-toggle="tooltip" title="Remove item">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="py-5">
                                                <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">Your wishlist is empty</h5>
                                                <a href="shop.php" class="btn btn-primary mt-3">
                                                    <i class="fas fa-store me-2"></i>Start Shopping
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="card-footer bg-white border-0 py-3">
                        <div class="d-flex justify-content-between">
                            <form action="clear_wishlist.php" method="post">
                                <button type="submit" class="btn btn-outline-danger rounded-pill px-4">
                                    <i class="fas fa-trash me-2"></i>Clear Wishlist
                                </button>
                            </form>
                            <a href="shop.php" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-store me-2"></i>Continue Shopping
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>

<script>
// Initialize Bootstrap tooltips 
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>