<?php
include('header.php');
include('sidebar.php');
include('db_connection.php');
?>
     <div class="container">
        <div class="page-inner">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Order Management</h1>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
            </a>
        </div>

        <!-- Content Row -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Order List</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" 
                                 aria-labelledby="dropdownMenuLink">
                                <div class="dropdown-header">Actions:</div>
                                <a class="dropdown-item" href="#"><i class="fas fa-file-export mr-2"></i>Export</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-filter mr-2"></i>Filter</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#"><i class="fas fa-sync-alt mr-2"></i>Refresh</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM orders ORDER BY order_date DESC";
                                    $result = mysqli_query($conn, $query);
                                    while ($row = mysqli_fetch_assoc($result)) :
                                        // Determine status badge color
                                        $status_class = '';
                                        switch(strtolower($row['status'])) {
                                            case 'packing': $status_class = 'info'; break;
                                            case 'pending': $status_class = 'warning'; break;
                                            case 'processing': $status_class = 'primary'; break;
                                            case 'cancelled': $status_class = 'danger'; break;
                                            case 'shipped': $status_class = 'secondary'; break;
                                            case 'delivered': $status_class = 'success'; break;
                                            default: $status_class = 'secondary';
                                        }
                                    ?>
                                        <tr>
                                            <td class="font-weight-bold">#<?= $row['order_id']; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-3">
                                                        <div class="icon-circle bg-light">
                                                            <i class="fas fa-user text-dark"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold"><?= $row['name']; ?></div>
                                                        <div class="small text-gray-500"><?= $row['email']; ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="font-weight-bold text-primary">RS.<?= number_format($row['total_amount'], 2); ?></td>
                                            <td>
                                                <div class="small"><?= date('M j, Y', strtotime($row['order_date'])); ?></div>
                                                <div class="small text-gray-500"><?= date('h:i A', strtotime($row['order_date'])); ?></div>
                                            </td>
                                            <td><?= ucfirst($row['payment_method']); ?></td>
                                            <td>
                                                <span class="badge badge-<?= $status_class; ?>"><?= ucfirst($row['status']); ?></span>
                                            </td>
                                            <td>
                                            <div class="btn-group" role="group" aria-label="Order Actions">
                                                <a href="view_order.php?id=<?= urlencode($row['order_id']); ?>" class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit_order.php?id=<?= urlencode($row['order_id']); ?>" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete_order.php?id=<?= urlencode($row['order_id']); ?>" class="btn btn-sm btn-danger"
                                                   title="Delete" onclick="return confirm('Are you sure you want to delete this order?');">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                                 
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom styles for this page -->
<link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

<!-- Page level plugins -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page level custom scripts -->
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "order": [[ 0, "desc" ]],
        "responsive": true,
        "columnDefs": [
            { "responsivePriority": 1, "targets": 0 },
            { "responsivePriority": 2, "targets": -1 },
            { "responsivePriority": 3, "targets": 2 }
        ]
    });
});
</script>

<?php include('footer.php'); ?>