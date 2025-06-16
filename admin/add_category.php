<?php
include('header.php');
include('sidebar.php');
include('db_connection.php');
?>
<div id="page-wrapper">
    <div id="page-inner">
        <form action="add_category.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" class="form-control" id="category_name" name="category_name" required>
            </div>
            <div class="form-group">
                <label for="category_image">Category Image:</label>
                <input type="file" class="form-control" id="category_image" name="category_image" required>
            </div>
            <div class="form-group">
                <label for="category_description">Category Description:</label>
                <textarea class="form-control" id="category_description" name="category_description" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary" href="category_list.php">Add Category</button>
            <a href="category_list.php" class="btn btn-primary">Back to Category List</a>
        </form>


        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $category_name = $_POST['category_name'];
            $category_description = $_POST['category_description'];

            if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
                $target_dir = "upload/";
                $target_file = $target_dir . basename($_FILES["category_image"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($imageFileType, $allowed_types)) {
                    if (move_uploaded_file($_FILES["category_image"]["tmp_name"], $target_file)) {
                        $sql = "INSERT INTO tbl_category (`category_name`, `category_image`, `category_discription`) VALUES ('$category_name', '$target_file', '$category_description')";
                        if (mysqli_query($conn, $sql)) {
                            echo "<div class='alert alert-success'>Category added successfully!</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Failed to upload image.</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Please upload a valid image file.</div>";
            }
        }
        ?>

    </div>
</div>

<?php
include "footer.php";
?>