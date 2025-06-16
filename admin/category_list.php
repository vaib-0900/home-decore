<?php
include('header.php');
include('sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
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
    </style>
</head>

<body>
    <div class="container">
        <div class="page-inner">
            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Category Management List
                    </h1>
                    <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard"></i> <a href="index.php">Dashboard</a>
                        </li>
                        <li class="active">
                            <i class="fa fa-list"></i> Categories
                        </li>
                    </ol>
                </div>
            </div>
            <!-- /.row -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Categories</h6>
                            <a href="add_category.php" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Add New Category
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">ID</th>
                                            <th>Category Name</th>
                                            <th width="15%">Image</th>
                                            <th>Description</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include('db_connection.php');
                                        $conn = mysqli_connect("localhost", "root", "", "admin_home");
                                        if (!$conn) {
                                            die("<div class='alert alert-danger'>Connection failed: " . mysqli_connect_error() . "</div>");
                                        }

                                        $query = "SELECT * FROM tbl_category";
                                        $result = mysqli_query($conn, $query);
                                        ?>

                                        <?php if (mysqli_num_rows($result) > 0): ?>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['category_id']) ?></td>
                                                    <td><?= htmlspecialchars($row['category_name']) ?></td>

                                                    <td>
                                                        <?php if (!empty($row['category_image'])): ?>
                                                            <img src="upload/<?= htmlspecialchars($row['category_image']) ?>" alt="<?= htmlspecialchars($row['category_name']) ?>" class="img-thumbnail">
                                                        <?php else: ?>
                                                            <span class="text-muted">No image</span>
                                                        <?php endif; ?>
                                                    </td>

                                                    
                                                    <td>
                                                        <?= htmlspecialchars(substr($row['category_description'], 0, 50)) ?>
                                                        <?= strlen($row['category_description']) > 50 ? '...' : '' ?>
                                                    </td>
                                                    <td class=" action-btns">
                                                         <a href="view_category.php?id=<?= $row['category_id'] ?>"
                                                            class="btn btn-info btn-sm"
                                                            title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="edit_category.php?id=<?= $row['category_id'] ?>"
                                                            class="btn btn-primary btn-sm"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="delete_category.php?id=<?= $row['category_id'] ?>"
                                                            class="btn btn-danger btn-sm"
                                                            title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this category?')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                       
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    No categories found. <a href="add_category.php">Add your first category</a>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php mysqli_close($conn); ?>
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
?>