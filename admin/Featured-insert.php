<?php
session_start();
include("db_connection.php");
$id = $_GET["product_id"];
$sel = "SELECT * FROM tbl_product WHERE product_id = $id";
$res = mysqli_query($conn, $sel);
while ($row = mysqli_fetch_array($res)) {
    $product_id = $row["product_id"];
}

$sel = "INSERT INTO tbl_feature(product_id) VALUES('$product_id')";
$result = mysqli_query($conn, $sel);
if ($result) {

    echo "<script>window.location.href='feature_list.php'</script>";
}
?>