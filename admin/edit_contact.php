<?php
include('header.php');
include('sidebar.php');
include('db_connection.php');

if (isset($_GET['id'])) {
    $contact_id = $_GET['id'];
    $query = "SELECT * FROM tbl_contact WHERE contact_id = $contact_id";
    $result = mysqli_query($conn, $query);
    $contact = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact_name = mysqli_real_escape_string($conn, $_POST['contact_name']);
    $contact_email = mysqli_real_escape_string($conn, $_POST['contact_email']);
    $contact_sub = mysqli_real_escape_string($conn, $_POST['contact_sub']);
    $contact_msg = mysqli_real_escape_string($conn, $_POST['contact_msg']);

    $query = "UPDATE tbl_contact SET 
              contact_name='$contact_name', 
              contact_email='$contact_email', 
              contact_sub='$contact_sub', 
              contact_msg='$contact_msg' 
              WHERE contact_id=$contact_id";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Contact updated successfully!";
        header("Location: contact_list.php");
        exit();
    } else {
        $error_message = "Error updating contact: " . mysqli_error($conn);
    }
}
?>

   <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h4 class="page-title">Edit Contact</h4>
               
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <a href="contact_list.php" class="btn btn-primary btn-round ml-auto">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                            <?php endif; ?>
                            
                            <form action="" method="POST">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact_name">Name</label>
                                            <input type="text" class="form-control" id="contact_name" name="contact_name" 
                                                   value="<?php echo htmlspecialchars($contact['contact_name']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact_email">Email</label>
                                            <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                                   value="<?php echo htmlspecialchars($contact['contact_email']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="contact_sub">Subject</label>
                                            <input type="text" class="form-control" id="contact_sub" name="contact_sub" 
                                                   value="<?php echo htmlspecialchars($contact['contact_sub']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="contact_msg">Message</label>
                                            <textarea class="form-control" id="contact_msg" name="contact_msg" 
                                                      rows="5" required><?php echo htmlspecialchars($contact['contact_msg']); ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="form-group text-right">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Update Contact
                                            </button>
                                            <a href="contact_list.php" class="btn btn-danger">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
include 'footer.php';
?>