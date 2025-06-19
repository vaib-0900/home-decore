
<?php
session_start();
include 'db_connection.php';
if(isset($_POST["add_category"])){
    
    $cart_id = $_POST["cart_id"];
    $cart_qty = $_POST["cart_qty"]+1;
    
     
    $query = "UPDATE tbl_cart SET cart_qty='$cart_qty' WHERE cart_id='$cart_id'";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $_SESSION["update"] = "update successful.";
        echo "<script>window.location.href='cart_list.php'</script>";
    } else {
        echo "connection error";
    }
}
if(isset($_POST["minus_category"])){

     
    $cart_id = $_POST["cart_id"];
    $cart_qty = $_POST["cart_qty"]-1;
    
     
    $query = "UPDATE tbl_cart SET cart_qty='$cart_qty' WHERE cart_id='$cart_id'";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $_SESSION["update"] = "update successful.";
        echo "<script>window.location.href='cart_list.php'</script>";
    } else {
        echo "connection error";
    }
}


?>