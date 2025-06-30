<?php
session_start();
include("db_connection.php");
$customer_id = $_SESSION['customer_id'];
$cart_id = $_POST['cart_id'];
$query2 = "SELECT * FROM tbl_cart where cart_id = $cart_id AND cart_customer_id = $customer_id";
$result2 = mysqli_query($conn,$query2);
$row = mysqli_fetch_array($result2);
if($row['cart_status'] == 'active')
{
    $customer_id = $_SESSION['customer_id'];
$cart_id = $_POST['cart_id'];
$query = "UPDATE tbl_cart SET cart_status='inactive' where cart_id = $cart_id AND cart_customer_id = $customer_id";
$result = mysqli_query($conn,$query);
 if($result)
 {
    echo"<script>window.location.href='cart_list.php'</script>";
 }
}
else 
{
    $customer_id = $_SESSION['customer_id'];
    $cart_id = $_POST['cart_id'];
$query = "UPDATE tbl_cart SET cart_status='active' where cart_id = $cart_id AND cart_customer_id = $customer_id";
$result = mysqli_query($conn,$query);
if($result)
 {
    echo"<script>window.location.href='cart_list.php'</script>";
}
}
?>