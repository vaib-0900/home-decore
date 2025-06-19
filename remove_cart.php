<?php
include "db_connection.php";

// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

// Check if cart_id is provided
if (isset($_POST['cart_id'])) {
    $cart_id = mysqli_real_escape_string($conn, $_POST['cart_id']);
    $customer_id = $_SESSION['customer_id'];
    
    // Delete the item from cart
    $delete_query = "DELETE FROM tbl_cart 
                    WHERE cart_id = '$cart_id' AND cart_customer_id = '$customer_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        // Redirect back to cart with success message
        header("Location: cart_list.php?removed=true");
        exit();
    } else {
        // Handle database error
        header("Location: cart_list.php?error=remove_failed");
        exit();
    }
} else {
    // No cart_id provided
    header("Location: cart_list.php?error=invalid_request");
    exit();
}
?>