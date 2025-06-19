<?php
include('db_connection.php');

if(!isset($_GET['id'])) {
    header("Location: order_list.php");
    exit();
}

$order_id = $_GET['id'];

// Delete order items first (due to foreign key constraint)
$delete_items = "DELETE FROM order_items WHERE order_id = $order_id";
mysqli_query($conn, $delete_items);

// Then delete the order
$delete_order = "DELETE FROM orders WHERE order_id = $order_id";
mysqli_query($conn, $delete_order);

$_SESSION['message'] = "Order deleted successfully";
header("Location: order_list.php");
exit();
?>