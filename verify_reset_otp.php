<?php
session_start();

$entered_otp = $_POST['otp'];

if($entered_otp == $_SESSION['reset_otp']){
    
    $_SESSION['otp_verified'] = true;
    
}

header("Location: forgot_password.php");
exit();
?>