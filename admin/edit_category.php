<?php
include('header.php');
include('sidebar.php');
include('db_connection.php');

if (isset($_GET['id'])) {
    $category_id = $_GET['id'];
    $query = "SELECT * FROM tbl_category WHERE category_id = $category_id";
    $result = mysqli_query($conn, $query);
    $category = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = $_POST['category_name'];
    $category_description = $_POST['category_description'];
    $category_image = $_FILES['category_image']['name'];
    $target = "upload/" . basename($category_image);

    if (move_uploaded_file($_FILES['category_image']['tmp_name'], $target)) {
        $query = "UPDATE tbl_category SET category_name='$category_name', category_discription='$category_description', category_image='$category_image' WHERE category_id=$category_id";
        if (mysqli_query($conn, $query)) {
            echo "Category updated successfully.";
        } else {
            echo "Error updating category: " . mysqli_error($conn);
        }
    } else {
        echo "Failed to upload image.";
    }
}
?>
    
<div id="page-wrapper">
<div id="page-inner">
    <h1>Edit Category</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="category_name">Category Name:</label>
            <input type="text" name="category_name" class="form-control" value="<?php echo $category['category_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="category_description">Category Description:</label>
            <textarea name="category_description" class="form-control" required><?php echo $category['category_discription']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="category_image">Category Image:</label>
            <input type="file" name="category_image" class="form-control" required>
            <img src="upload/<?php echo $category['category_image']; ?>" alt="<?php echo $category['category_name']; ?>" width="100">
        </div>
        
        <button type="submit" class="btn btn-primary">Update Category</button>
    </form>
</div>
</div>

<?php
include 'footer.php';
?>