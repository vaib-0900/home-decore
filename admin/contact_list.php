<?php
include 'header.php';
include 'sidebar.php';
?>
   <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h4 class="page-title">Contact List</h4>
               
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Contact List</h4>
                               
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="contactTable" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Subject</th>
                                            <th>Message</th>
                                            <th style="width: 10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include('db_connection.php');
                                        $query = "SELECT * FROM tbl_contact"; 
                                        $result = mysqli_query($conn, $query);

                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<tr>";
                                                echo "<td>" . $row['contact_id'] . "</td>";
                                                echo "<td>" . htmlspecialchars($row['contact_name']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['contact_email']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['contact_sub']) . "</td>";
                                                echo "<td>" . htmlspecialchars(substr($row['contact_msg'], 0, 50)) . (strlen($row['contact_msg']) > 50 ? '...' : '') . "</td>";
                                                echo "<td>
                                                        <div class='form-button-action'>
                                                          
                                                            <a href='delete_contact.php?id=" . $row['contact_id'] . "' class='btn btn-link btn-danger' data-toggle='tooltip' title='Delete' onclick='return confirm(\"Are you sure you want to delete this contact?\")'>
                                                                <i class='fa fa-times'></i>
                                                            </a>
                                                        </div>
                                                    </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6' class='text-center'>No contacts found.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<!-- Add Contact Modal -->
<!--<div class="modal fade" id="addRowModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header no-bd">
                <h5 class="modal-title">
                    <span class="fw-mediumbold">New</span> 
                    <span class="fw-light">Contact</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="add_contact.php" method="POST">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" class="form-control" name="subject" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Message</label>
                                <textarea class="form-control" name="message" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer no-bd">
                        <button type="submit" class="btn btn-primary">Add</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>-->

<?php
include 'footer.php'; 
?>