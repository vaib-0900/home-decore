<?php
session_start();
if (!isset($_SESSION["login"])) {
  echo "<script> window.location.href='login.php'</script>";
}
?>
<?php
include "db_connection.php";

$product_id = $_POST['product_id'];
$customer_id = $_SESSION['customer_id'];
$cart_qty = $_POST['cart_qty'];

$query = "INSERT INTO tbl_cart (cart_product_id,cart_customer_id,qty)VALUE('$cart_product_id','$cart_customer_id','$cart_qty')";
$result = mysqli_query($conn,$query);
if($result)
{
  echo"<script>window.location.href='index.php'</script>";
}
?>