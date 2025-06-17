<?php
session_start();
include "db_connection.php";

$email = $_POST["email"];
$password = $_POST["password"];

$query = "SELECT * FROM `tbl_customer` WHERE `customer_email` = '$email' AND `customer_password` = '$password'";
$result = mysqli_query($conn, $query);

if ($result->num_rows > 0) {
    $row = mysqli_fetch_array($result);
    $_SESSION["login"] = true;
    $_SESSION["customer_id"] = $row["customer_id"];
    $_SESSION["customer_name"] = $row["customer_name"];
    echo "<script>alert('Login successful!');</script>";
    echo "<script>window.location.href='index.php';</script>";
} else {
    echo "<script>alert('Logout successful!');</script>";
    echo "<script>window.location.href='login.php';</script>";
}
?>



