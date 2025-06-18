<?php
include('header.php');
include('sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fc;
        }

        #page-wrapper {
            padding: 20px;
            min-height: calc(100vh - 60px);
        }

        .card {
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.35rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table th {
            white-space: nowrap;
            background-color: #f8f9fc;
        }

        .img-thumbnail {
            max-width: 100px;
            height: auto;
        }

        .action-btns .btn {
            margin-right: 5px;
        }

        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="page-inner">
            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Product Management
                    </h1>
                    <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard"></i> <a href="index.php">Dashboard</a>
                        </li>
                        <li class="active">
                            <i class="fa fa-box"></i> Products
                        </li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Product List</h6>
                            <a href="add_product.php" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Add New Product
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">ID</th>
                                            <th>Product Name</th>
                                            <th width="15%">Image</th>
                                            <th>Price</th>
                                            <th>Discount (%)</th>
                                            <th>Discount Value</th>
                                            <th>Sell Price</th>
                                            <th>Category</th>
                                            <th>Featured</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include('db_connection.php');

                                        try {
                                            $query = "SELECT p.*, c.category_name 
                                                     FROM tbl_product p
                                                     LEFT JOIN tbl_category c ON p.add_category = c.category_id";
                                            $result = mysqli_query($conn, $query);

                                            if (!$result) {
                                                throw new Exception(mysqli_error($conn));
                                            }

                                            if (mysqli_num_rows($result) > 0) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['product_id']) ?></td>
                                                        <td><?= htmlspecialchars($row['product_name']) ?></td>

                                                        <td>
                                                            <?php if (!empty($row['product_image'])): ?>
                                                                <img src="<?= htmlspecialchars($row['product_image']) ?>" class="img-thumbnail" alt="Product Image">
                                                            <?php else: ?>
                                                                <img src="images/no-image.png" class="img-thumbnail" alt="No Image">
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($row['product_price']) ?></td>
                                                        <td><?= htmlspecialchars($row['discount_per']) ?></td>
                                                        <td><?= htmlspecialchars($row['discount_value']) ?></td>
                                                        <td><?= htmlspecialchars($row['sell_price']) ?></td>
                                                        <td><?= !empty($row['category_name']) ? htmlspecialchars($row['category_name']) : 'Uncategorized' ?></td>
                                                        <td>
                                                            <a href="Featured-insert.php?product_id=<?= $row["product_id"] ?>"
                                                                class="text-warning fs-5">
                                                                <i class="fa fa-star"></i>
                                                            </a>
                                                        </td>
                                                        <td class="action-btns">
                                                            <a href="view_product.php?id=<?= $row['product_id'] ?>" class="btn btn-info btn-sm" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="edit_product.php?id=<?= $row['product_id'] ?>" class="btn btn-primary btn-sm" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="delete_product.php?id=<?= $row['product_id'] ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this product?')">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="9" class="text-center">
                                                        No products found. <a href="add_product.php">Add your first product</a>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                        } catch (Exception $e) {
                                            ?>
                                            <tr>
                                                <td colspan="9" class="text-center text-danger">
                                                    Error: <?= htmlspecialchars($e->getMessage()) ?>
                                                </td>
                                            </tr>
                                        <?php
                                        }

                                        mysqli_close($conn);
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer small text-muted">
                            Updated at <?= date("Y-m-d H:i:s") ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
include('footer.php');
