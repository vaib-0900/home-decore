<?php
include('header.php');
include('sidebar.php');
?>
<div id="page-wrapper">
<div id="page-inner">
    <h1>Category List</h1>
    <a href="add_category.php" class="btn btn-success">Add New Category</a>
    <br><br>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Category image</th>
                <th>Category Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include('db_connection.php');
            $query = "SELECT * FROM tbl_category"; 
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['category_id'] . "</td>";
                    echo "<td>" . $row['category_name'] . "</td>";
                    echo "<td><img src='" . $row['category_image'] . "' alt='upload/" . $row['category_image'] . "' style='width: 100px; height: auto;'></td>";
                    echo "<td>" . $row['category_discription'] . "</td>";
                    echo "<td>
                            <a href='edit_category.php?id=" . $row['category_id'] . "' class='btn btn-primary btn-sm'>Edit</a>
                            <a href='delete_category.php?id=" . $row['category_id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No categories found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</div>








<?php
include 'footer.php';
?>