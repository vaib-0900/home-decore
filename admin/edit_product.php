<?php
include('header.php');
include('sidebar.php');
include('db_connection.php');

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $query = "SELECT * FROM tbl_product WHERE product_id = $product_id";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($result);

    if (!$product) {
        echo "Product not found.";
        exit;
    }
} else {
    echo "Invalid product ID.";
    exit;
}

// Fetch all categories
$category_query = "SELECT * FROM tbl_category";
$category_result = mysqli_query($conn, $category_query);

if (isset($_POST['update_product'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $discount_per = $_POST['discount_per'];
    $discount_value = $_POST['discount_value'];
    $sell_price = $_POST['sell_price'];
    $product_description = $_POST['product_description'];
    $category_id = $_POST['category_id'];

    // Handle image upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "upload/product/";
        $target_file = $target_dir . basename($_FILES["product_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["product_image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                $product_image = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit;
            }
        } else {
            echo "File is not an image.";
            exit;
        }
    } else {
        $product_image = $product['product_image'];
    }

    $update_query = "UPDATE tbl_product SET
                        product_name = '$product_name',
                        product_image = '$product_image',
                        product_price = '$product_price',
                        discount_per = '$discount_per',
                        discount_value = '$discount_value',
                        sell_price = '$sell_price',
                        product_description = '$product_description',
                        add_category = '$category_id'
                    WHERE product_id = $product_id";

    if (mysqli_query($conn, $update_query)) {
        echo "Product updated successfully.";
    } else {
        echo "Error updating product: " . mysqli_error($conn);
    }
}
?>
   <div class="container">
        <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-header">Update Product</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="product_name">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo $product['product_name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="product_image">Product Image</label>
                        <input type="file" class="form-control" id="product_image" name="product_image">
                        <img src="<?php echo $product['product_image']; ?>" alt="Product Image" style="width: 100px; height: auto;">
                    </div>
                    <div class="form-group">
                        <label for="product_price">Product Price</label>
                        <input type="number" class="form-control" id="product_price" name="product_price" value="<?php echo $product['product_price']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="discount_per">Discount Percentage</label>
                        <input type="number" class="form-control" id="discount_per" name="discount_per" value="<?php echo $product['discount_per']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="discount_value">Discount Value</label>
                        <input type="number" class="form-control" id="discount_value" name="discount_value" value="<?php echo $product['discount_value']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="sell_price">Sell Price</label>
                        <input type="number" class="form-control" id="sell_price" name="sell_price" value="<?php echo $product['sell_price']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php while ($category = mysqli_fetch_assoc($category_result)) { ?>
                                <option value="<?php echo $category['category_id']; ?>" <?php if ($category['category_id'] == $product['add_category']) echo 'selected'; ?>><?php echo $category['category_name']; ?></option>

                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="product_description">Product Description</label>
                        <textarea class="form-control" id="product_description" name="product_description" required><?php echo $product['product_description']; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" name="update_product">Update Product</button>


                </form>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>