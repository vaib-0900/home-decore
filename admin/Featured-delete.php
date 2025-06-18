<?php
include('db_connection.php');

if (isset($_GET['feature_id'])) {
    $feature_id = $_GET['feature_id'];
    $query = "DELETE FROM tbl_feature WHERE feature_id = $feature_id";
    if (mysqli_query($conn, $query)) {
        header("Location: feature_list.php");
    } else {
        echo "Error deleting feature: " . mysqli_error($conn);
    }
}
?>