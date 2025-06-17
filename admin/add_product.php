<?php
include 'header.php';
include 'sidebar.php';
include 'db_connection.php';

// Initialize variables
$product_name = $product_price = $discount_per = $product_description = '';
$error = '';

// Product save code with security improvements
if (isset($_POST['submit'])) {
    // Sanitize inputs
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_price = floatval($_POST['product_price']);
    $discount_per = floatval($_POST['discount_per']);
    $discount_value = floatval($_POST['discount_value']);
    $sell_price = floatval($_POST['sell_price']);
    $product_description = mysqli_real_escape_string($conn, $_POST['product_description']);
    $category_id = intval($_POST['category_id']);

    // File upload handling with validation
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file_info, $_FILES['product_image']['tmp_name']);
        
        if (in_array($mime_type, $allowed_types)) {
            $file_ext = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_ext;
            $upload_path = "upload/product/" . $file_name;
            
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                $product_img = $upload_path;
            } else {
                $error = "Error uploading file.";
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.";
        }
    } else {
        $error = "Please select a product image.";
    }

    if (empty($error)) {
        // Prepared statement for security
        $query = "INSERT INTO tbl_product (product_name, product_image, product_price, discount_per, discount_value, sell_price, product_description, add_category) VALUES ('$product_name','$product_img','$product_price', '$discount_per', '$discount_value', '$sell_price', '$product_description', '$category_id')";
        $stmt = mysqli_prepare($conn, $query);
       
        
        if (mysqli_stmt_execute($stmt)) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Product added successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            // Reset form values
            $product_name = $product_price = $discount_per = $product_description = '';
        } else {
            $error = "Error adding product: " . mysqli_error($conn);
        }
    }
    
    if (!empty($error)) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                '.$error.'
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}
?>

 <div class="container">
        <div class="page-inner">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h4 class="m-0 font-weight-bold text-primary">Add New Product</h4>
      <a href="product_list.php" class="btn btn-primary btn-sm">
        <i class="fas fa-list"></i> View Product List
      </a>
    </div>
    <div class="card-body">
      <form action="add_product.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="row">
          <div class="col-md-8">
            <div class="mb-3">
              <label for="product_name" class="form-label">Product Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="product_name" name="product_name" value="<?= htmlspecialchars($product_name) ?>" required>
              <div class="invalid-feedback">Please provide a product name.</div>
            </div>
            
            <div class="mb-3">
              <label for="product_description" class="form-label">Product Description</label>
              <textarea class="form-control" id="product_description" name="product_description" rows="3"><?= htmlspecialchars($product_description) ?></textarea>
            </div>
            
            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="product_price" class="form-label">Price (RS.) <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">RS.</span>
                  <input type="number" class="form-control" id="product_price" name="product_price" step="0.01" min="0" value="<?= $product_price ?>" required>
                  <div class="invalid-feedback">Please provide a valid price.</div>
                </div>
              </div>
              
              <div class="col-md-4 mb-3">
                <label for="discount_per" class="form-label">Discount (%)</label>
                <div class="input-group">
                  <input type="number" class="form-control" id="discount_per" name="discount_per" step="0.01" min="0" max="100" value="<?= $discount_per ?>">
                  <span class="input-group-text">%</span>
                </div>
              </div>
              
              <div class="col-md-4 mb-3">
                <label for="discount_value" class="form-label">Discount Value</label>
                <div class="input-group">
                  <span class="input-group-text">RS.</span>
                  <input type="number" class="form-control" id="discount_value" name="discount_value" readonly>
                </div>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="sell_price" class="form-label">Sell Price</label>
              <div class="input-group">
                <span class="input-group-text">RS.</span>
                <input type="number" class="form-control" id="sell_price" name="sell_price" readonly>
              </div>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="mb-3">
              <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
              <select name="category_id" id="category_id" class="form-select" required>
                <option value="">Select Category</option>
                <?php
                $query = "SELECT * FROM tbl_category";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                  echo '<option value="'.$row['category_id'].'">'.htmlspecialchars($row['category_name']).'</option>';
                }
                ?>
              </select>
              <div class="invalid-feedback">Please select a category.</div>
            </div>
            
            <div class="mb-3">
              <label for="product_image" class="form-label">Product Image <span class="text-danger">*</span></label>
              <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*" required>
              <div class="invalid-feedback">Please upload a product image.</div>
              <small class="text-muted">Allowed formats: JPG, PNG, GIF, WEBP</small>
            </div>
            
            <div class="card mb-3">
              <div class="card-body text-center">
                <img id="imagePreview" src="https://via.placeholder.com/300x200?text=Product+Image" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
              </div>
            </div>
          </div>
        </div>
        
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
          <button type="reset" class="btn btn-outline-secondary me-md-2">
            <i class="fas fa-undo"></i> Reset
          </button>
          <button type="submit" name="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Product
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Price calculation
document.addEventListener('DOMContentLoaded', function() {
  const calculatePrices = () => {
    const productPrice = parseFloat(document.getElementById('product_price').value) || 0;
    const discountPercentage = parseFloat(document.getElementById('discount_per').value) || 0;
    const discountValue = (productPrice * discountPercentage) / 100;
    document.getElementById('discount_value').value = discountValue.toFixed(2);
    document.getElementById('sell_price').value = (productPrice - discountValue).toFixed(2);
  };

  document.getElementById('product_price').addEventListener('input', calculatePrices);
  document.getElementById('discount_per').addEventListener('input', calculatePrices);

  // Image preview
  document.getElementById('product_image').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('imagePreview').src = e.target.result;
      };
      reader.readAsDataURL(this.files[0]);
    }
  });

  // Form validation
  const forms = document.querySelector('.needs-validation');
  forms.addEventListener('submit', function(event) {
    if (!forms.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }
    forms.classList.add('was-validated');
  }, false);
});
</script>

<?php
include "footer.php";
?>