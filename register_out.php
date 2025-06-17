<?php
include "db_connection.php";
$name = $_POST["name"];
$email = $_POST["email"];
$password = $_POST["password"];
$phone = $_POST["phone"];
$address = $_POST["address"];
$landmark = $_POST["landmark"];

$query = "INSERT INTO tbl_customer(`customer_name`,`customer_email`,`customer_password`,`customer_phone`,`customer_address`,`customer_landmark`) VALUES('$name','$email','$password','$phone','$address','$landmark')";
$result = mysqli_query($conn, $query);

if($result){
    echo "Registration Successfully!";
}  else{
    echo "Registratioin failed!";
}
?>