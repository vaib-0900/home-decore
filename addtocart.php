<?php
include "db_connection.php";
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $product_id = mysqli_real_escape_string($conn, $_GET['id']);
    $customer_id = $_SESSION['customer_id'];
    
    // Check if product already exists in cart
    $check_query = "SELECT * FROM tbl_cart 
                   WHERE cart_product_id = '$product_id' 
                   AND cart_customer_id = '$customer_id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update quantity if exists
        $update_query = "UPDATE tbl_cart 
                        SET cart_qty = cart_qty + 1 
                        WHERE cart_product_id = '$product_id' 
                        AND cart_customer_id = '$customer_id'";
        mysqli_query($conn, $update_query);
    } else {
        // Insert new item if not exists
        $insert_query = "INSERT INTO tbl_cart (cart_product_id, cart_customer_id, cart_qty) 
                        VALUES ('$product_id', '$customer_id', 1)";
        mysqli_query($conn, $insert_query);
    }
    
    // Redirect to cart page with success message
    header("Location: cart_list.php?added=true");
    exit();
} else {
    // No product ID provided
    header("Location: shop.php");
    exit();
}
?>