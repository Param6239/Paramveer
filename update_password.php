<?php
include("config.php");
session_start();

$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

if($password == $confirm){

$email = $_SESSION['reset_email'];


$sql = "UPDATE tbl_login SET password='$password' WHERE username='$email'";
$con->query($sql);

session_destroy();

header("Location: login.php");
exit();

}
?>