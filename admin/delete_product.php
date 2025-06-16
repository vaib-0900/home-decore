<?php
include('db_connection.php');

if(isset($_GET['id'])){
    $product_id = $_GET['id'];

    $query = "DELETE FROM products WHERE product_id='$product_id'";

    if(mysqli_query($conn, $query)){
        header("Location: product_list.php");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
?>