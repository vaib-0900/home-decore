<?php
include('header.php');
include('sidebar.php');
include('db_connection.php');

$alert_message = '';
$alert_type = '';

if (isset($_GET['id'])) {
    $category_id = intval($_GET['id']); // Sanitize input
    $query = "SELECT * FROM tbl_category WHERE category_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $category_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $category = mysqli_fetch_assoc($result);
    } else {
        $alert_message = "Category not found!";
        $alert_type = "danger";
    }
    mysqli_stmt_close($stmt);
} else {
    $alert_message = "No category ID specified!";
    $alert_type = "danger";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Category - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #2e59d9;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 0.5rem 0.5rem 0 0 !important;
            padding: 1.25rem 1.5rem;
        }
        
        .form-control-static {
            padding: 0.75rem;
            background-color: var(--secondary-color);
            border-radius: 0.35rem;
            min-height: 44px;
            display: flex;
            align-items: center;
        }
        
        .img-thumbnail {
            border: 1px solid #ddd;
            border-radius: 0.35rem;
            padding: 0.25rem;
            background-color: white;
            max-height: 250px;
            object-fit: contain;
        }
        
        .detail-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .back-btn {
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            transform: translateX(-3px);
        }
        
        .action-btn {
            transition: all 0.2s;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 0.75rem 0;
        }
        
        .breadcrumb-item.active {
            color: var(--primary-color);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-inner">
            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="category_list.php"><i class="fas fa-list"></i> Categories</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-eye"></i> View Category</li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-lg-12">
                    <!-- Alert Messages -->
                    <?php if (!empty($alert_message)): ?>
                    <div class="alert alert-<?php echo $alert_type; ?> alert-dismissible fade show" role="alert">
                        <i class="<?php echo $alert_type == 'danger' ? 'fas fa-exclamation-circle' : 'fas fa-check-circle'; ?>"></i>
                        <?php echo $alert_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Main Card -->
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="card-title mb-0"><i class="fas fa-folder-open me-2"></i>Category Details</h4>
                                <a href="category_list.php" class="btn btn-light back-btn">
                                    <i class="fas fa-arrow-left me-1"></i> Back to Categories
                                </a>
                            </div>
                        </div>
                        
                        <?php if (isset($category)): ?>
                        <div class="card-body">
                            <div class="row">
                                <!-- Left Column - Text Details -->
                                <div class="col-lg-8">
                                    <div class="mb-4">
                                        <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="detail-label">Category ID</label>
                                                <div class="form-control-static">
                                                    <span class="badge bg-primary">#<?php echo htmlspecialchars($category['category_id']); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                               
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="detail-label">Category Name</label>
                                            <div class="form-control-static">
                                                <i class="fas fa-tag me-2 text-muted"></i>
                                                <?php echo htmlspecialchars($category['category_name']); ?>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="detail-label">Description</label>
                                            <div class="form-control-static" style="min-height: 100px;">
                                                <?php 
                                                echo !empty($category['category_description']) 
                                                    ? nl2br(htmlspecialchars($category['category_description'])) 
                                                    : '<span class="text-muted">No description provided</span>';
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Additional Information Section -->
                                  
                                </div>
                                
                                <!-- Right Column - Image -->
                                <div class="col-lg-4">
                                    <div class="card h-100">
                                        <div class="card-header bg-white">
                                            <h5 class="mb-0"><i class="fas fa-image me-2"></i>Category Image</h5>
                                        </div>
                                        <div class="card-body text-center d-flex flex-column justify-content-center">
                                            <?php if (!empty($category['category_image']) && file_exists('upload/' . $category['category_image'])): ?>
                                                <img src="upload/<?php echo htmlspecialchars($category['category_image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($category['category_name']); ?>" 
                                                     class="img-thumbnail mb-3">
                                                <a href="upload/<?php echo htmlspecialchars($category['category_image']); ?>" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   target="_blank" 
                                                   download="<?php echo htmlspecialchars($category['category_name']); ?>">
                                                    <i class="fas fa-download me-1"></i> Download Image
                                                </a>
                                            <?php else: ?>
                                                <div class="text-center py-4">
                                                    <i class="fas fa-image fa-4x text-muted mb-3"></i>
                                                    <p class="text-muted">No image available</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-md-12 d-flex justify-content-between">
                                    <div>
                                        <a href="category_list.php" class="btn btn-outline-secondary action-btn">
                                            <i class="fas fa-list me-1"></i> View All Categories
                                        </a>
                                    </div>
                                    <div>
                                        <a href="edit_category.php?id=<?php echo $category_id; ?>" class="btn btn-primary action-btn me-2">
                                            <i class="fas fa-edit me-1"></i> Edit Category
                                        </a>
                                        <button class="btn btn-danger action-btn" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="fas fa-trash-alt me-1"></i> Delete Category
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the category "<strong><?php echo isset($category) ? htmlspecialchars($category['category_name']) : ''; ?></strong>"?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>This action cannot be undone!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="delete_category.php?id=<?php echo $category_id; ?>" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Delete Permanently
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script>
        // Enable Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Smooth scroll to top when clicking the back button
            document.querySelector('.back-btn').addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                setTimeout(function() {
                    window.location.href = e.target.closest('a').href;
                }, 500);
            });
        });
    </script>
</body>
</html>

<?php
include('footer.php');
?>      