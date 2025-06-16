<?php
include('db_connection.php');

if (isset($_GET['id'])) {
    $category_id = $_GET['id'];
    $query = "DELETE FROM tbl_category WHERE category_id = $category_id";
    if (mysqli_query($conn, $query)) {
        header("Location: category_list.php");
    } else {
        echo "Error deleting category: " . mysqli_error($conn);
    }
}
?>