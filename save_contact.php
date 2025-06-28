<?php
include "db_connection.php";

$name = $_POST['name'];
$email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];
$query = "INSERT INTO tbl_contact (`contact_name`, `contact_email`, `contact_sub`, `contact_msg`) VALUES ('$name', '$email', '$subject', '$message')";
$result = mysqli_query($conn, $query);
session_start();
if ($result) {
    $_SESSION['message'] = 'Contact saved successfully!';
} else {
    $_SESSION['message'] = 'Error saving contact. Please try again.';
}
header('Location: contact.php');
exit;
?>