<?php
include('header.php');
include('sidebar.php');
include('db_connection.php');

$alert_message = '';
$alert_type = '';

if (isset($_GET['id'])) {
    $category_id = $_GET['id'];
    $query = "SELECT * FROM tbl_category WHERE category_id = $category_id";
    $result = mysqli_query($conn, $query);
    $category = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $category_description = mysqli_real_escape_string($conn, $_POST['category_description']);
    
    // Handle image upload if a new image is provided
    if (!empty($_FILES['category_image']['name'])) {
        $category_image = $_FILES['category_image']['name'];
        $target = "upload/" . basename($category_image);
        
        if (!move_uploaded_file($_FILES['category_image']['tmp_name'], $target)) {
            $alert_message = "Failed to upload image.";
            $alert_type = "danger";
        }
    } else {
        // Keep the existing image if no new image is uploaded
        $category_image = $category['category_image'];
    }
    
    if (empty($alert_message)) {
        $query = "UPDATE tbl_category SET 
                 category_name='$category_name', 
                 category_description='$category_description', 
                 category_image='$category_image' 
                 WHERE category_id=$category_id";
        
        if (mysqli_query($conn, $query)) {
            $alert_message = "Category updated successfully!";
            $alert_type = "success";
            
            // Refresh category data after update
            $query = "SELECT * FROM tbl_category WHERE category_id = $category_id";
            $result = mysqli_query($conn, $query);
            $category = mysqli_fetch_assoc($result);
        } else {
            $alert_message = "Error updating category: " . mysqli_error($conn);
            $alert_type = "danger";
        }
    }
}
?>
    
<div class="container">
  <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Edit Category</h4>
                            <a href="category_list.php" class="btn btn-primary btn-round ml-auto">
                                <i class="fas fa-arrow-left"></i> Back to Categories
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($alert_message)): ?>
                            <div class="alert alert-<?php echo $alert_type; ?> alert-dismissible fade show" role="alert">
                                <?php echo $alert_message; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="category_name">Category Name</label>
                                <input type="text" class="form-control" id="category_name" name="category_name" 
                                       value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="category_description">Category Description</label>
                                <textarea class="form-control" id="category_description" name="category_description" 
                                          rows="3" required><?php echo htmlspecialchars($category['category_description']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="category_image">Category Image</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="category_image" name="category_image">
                                    <label class="custom-file-label" for="category_image">Choose file</label>
                                </div>
                                <small class="form-text text-muted">Leave blank to keep current image</small>
                                
                                <?php if (!empty($category['category_image'])): ?>
                                    <div class="mt-3">
                                        <p>Current Image:</p>
                                        <img src="upload/<?php echo htmlspecialchars($category['category_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($category['category_name']); ?>" 
                                             class="img-thumbnail" width="150">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Category
                                </button>
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