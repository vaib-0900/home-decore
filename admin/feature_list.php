<?php
include("../db-connection/db connection.php");
include "header.php";
include "sidebar.php";
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
<?php if (isset($_SESSION['delete'])): ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;text-align: center; margin-top: 100px;">
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
<!-- delete -->
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Featured List</h3>
            </div>
        </div>
        <hr>
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-info text-white text-left fw-bold fs-4 rounded-top-4">
                Featured List
            </div>
            <div class="card-body bg-light rounded-bottom-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Featured Product Name</th>
                                <th>Featured Image</th>
                                <th>MRP</th>
                                <th>Discount %</th>
                                <th>Discount Value</th>
                                <th>Sell Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 0;
                            $query = "SELECT * FROM tbl_feature INNER JOIN tbl_product ON tbl_product.product_id = tbl_feature.product_id";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_array($result)) {
                                ?>
                                <tr>
                                    <td><?= ++$count; ?></td>
                                    <td class="fw-semibold"><?= $row["product_name"] ?></td>
                                    <td>
                                        <img src="uplodes/image/<?= $row["product_img"] ?>" class="rounded-circle shadow-sm"
                                            style="width: 50px; height: 50px;" alt="Product Image">
                                    </td>
                                    <td>₹<?= $row["product_mrp"] ?></td>
                                    <td><span class="badge bg-info"><?= $row["product_discount_percentage"] ?>%</span></td>
                                    <td>₹<?= $row["product_discount_value"] ?></td>
                                    <td><span class="badge bg-success fs-6">₹<?= $row["product_sell_price"] ?></span></td>
                                    <td>
                                        <a href="Featured-delete.php?feature_id=<?= $row["feature_id"] ?>"
                                            class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this item?');">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                            if ($count == 0) {
                                echo '<tr><td colspan="8"><div class="text-danger fw-bold">No Data Found</div></td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include "footer.php";
?>1