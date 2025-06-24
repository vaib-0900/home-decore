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
if (isset($_POST['wishlist_id'])) {
    $wishlist_id = mysqli_real_escape_string($conn, $_POST['wishlist_id']);
    $customer_id = $_SESSION['customer_id'];
    
    // Delete the item from cart
    $delete_query = "DELETE FROM tbl_wishlist 
                     WHERE wishlist_id = '$wishlist_id' AND wishlist_customer = '$customer_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        // Redirect back to cart with success message
        header("Location: wishlist.php?removed=true");
        exit();
    } else {
        // Handle database error
        header("Location: wishlist.php?error=remove_failed");
        exit();
    }
} else {
    // No cart_id provided
    header("Location: wishlist.php?error=invalid_request");
    exit();
}
?>