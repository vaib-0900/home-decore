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
    
    if (mysqli_num_rows($result) > 0) {
        $category = mysqli_fetch_assoc($result);
    } else {
        $alert_message = "Category not found!";
        $alert_type = "danger";
    }
} else {
    $alert_message = "No category ID specified!";
    $alert_type = "danger";
}
?>
    
<div class="container">
  <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">View Category Details</h4>
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
                        
                        <?php if (isset($category)): ?>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Category Name</label>
                                    <p class="form-control-static"><?php echo htmlspecialchars($category['category_name']); ?></p>
                                </div>
                                
                                <div class="form-group">
                                    <label>Category Description</label>
                                    <p class="form-control-static"><?php echo htmlspecialchars($category['category_description']); ?></p>
                                </div>
                                
                              
                            </div>
                            
                            <div class="col-md-4">
                                <?php if (!empty($category['category_image'])): ?>
                                    <div class="text-center">
                                        <label>Category Image</label>
                                        <div class="mt-2">
                                            <img src="upload/<?php echo htmlspecialchars($category['category_image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($category['category_name']); ?>" 
                                                 class="img-fluid rounded" style="max-height: 200px;">
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center">
                                        <label>Category Image</label>
                                        <div class="mt-2">
                                            <span class="text-muted">No image available</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <a href="edit_category.php?id=<?php echo $category_id; ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Category
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>