<?php
include "header.php";
include "sidebar.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fc;
        }

        .page-inner {
            padding: 20px;
            min-height: calc(100vh - 60px);
        }

        .card {
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 20px;
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

        .action-btns .btn {
            margin-right: 5px;
        }

        .status-active {
            color: #28a745;
            font-weight: bold;
        }

        .status-inactive {
            color: #dc3545;
            font-weight: bold;
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
                        Customer Management
                    </h1>
                    <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard"></i> <a href="index.php">Dashboard</a>
                        </li>
                        <li class="active">
                            <i class="fa fa-users"></i> Customers
                        </li>
                    </ol>
                </div>
            </div>
            <!-- /.row -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Customer List</h6>
                            <a href="customer_add.php" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Add New Customer
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Landmark</th>
                                            <th width="10%">Status</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include "db_connection.php";
                                        $query = "SELECT * FROM tbl_customer"; 
                                        $result = mysqli_query($conn, $query);

                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row['customer_id']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['customer_email']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['customer_phone']) . "</td>";
                                                echo "<td>" . htmlspecialchars(substr($row['customer_address'], 0, 30)) . (strlen($row['customer_address']) > 30 ? '...' : '') . "</td>";
                                                echo "<td>" . htmlspecialchars($row['customer_landmark']) . "</td>";
                                                echo "<td class='" . ($row['customer_status'] ? 'status-active' : 'status-inactive') . "'>" . 
                                                     ($row['customer_status'] ? 'Active' : 'Inactive') . "</td>";
                                                echo "<td class='action-btns'>";
                                                echo "<a href='edit_customer.php?id=" . $row['customer_id'] . "' class='btn btn-primary btn-sm' title='Edit'>";
                                                echo "<i class='fas fa-edit'></i></a> ";
                                                echo "<a href='delete_customer.php?id=" . $row['customer_id'] . "' class='btn btn-danger btn-sm' title='Delete' ";
                                                echo "onclick='return confirm(\"Are you sure you want to delete this customer?\")'>";
                                                echo "<i class='fas fa-trash-alt'></i></a>";
                                                echo "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='8' class='text-center'>No customers found</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer small text-muted">
                            Last updated at <?= date("Y-m-d H:i:s") ?>
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
include "footer.php";
?>