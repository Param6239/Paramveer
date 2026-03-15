<?php 
include('header.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

</div>
<div class="content">
<div class="wrap">
<div class="content-top" style="min-height:300px;padding:50px">

<div class="col-md-4 col-md-offset-4">
<div class="panel panel-default">
<div class="panel-heading">Forgot Password</div>

<div class="panel-body">

<?php include('msgbox.php'); ?>

<?php
/* STEP 1 : ASK EMAIL */
if(!isset($_SESSION['otp_sent'])) {
?>

<p class="login-box-msg">Enter your email to receive OTP</p>

<form action="send_reset_otp.php" method="post">

<div class="form-group has-feedback">
<input name="email" type="email" class="form-control" placeholder="Email" required>
<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
</div>

<div class="form-group">
<button type="submit" class="btn btn-primary">Send OTP</button>
</div>

</form>

<?php
}

/* STEP 2 : VERIFY OTP */
elseif(isset($_SESSION['otp_sent']) && !isset($_SESSION['otp_verified'])) {
?>

<p class="login-box-msg">Enter OTP sent to your email</p>

<form action="verify_reset_otp.php" method="post">

<div class="form-group has-feedback">
<input name="otp" type="text" class="form-control" placeholder="Enter OTP" required>
<span class="glyphicon glyphicon-lock form-control-feedback"></span>
</div>

<div class="form-group">
<button type="submit" class="btn btn-primary">Verify OTP</button>
</div>

</form>

<p style="margin-top:10px">
<a href="send_reset_otp.php">Resend OTP</a>
</p>

<?php
}

/* STEP 3 : CHANGE PASSWORD */
elseif(isset($_SESSION['otp_verified'])) {
?>

<p class="login-box-msg">Enter new password</p>

<form action="update_password.php" method="post">

<div class="form-group has-feedback">
<input name="password" type="password" class="form-control" placeholder="New Password" required>
<span class="glyphicon glyphicon-lock form-control-feedback"></span>
</div>

<div class="form-group has-feedback">
<input name="confirm_password" type="password" class="form-control" placeholder="Confirm Password" required>
<span class="glyphicon glyphicon-lock form-control-feedback"></span>
</div>

<div class="form-group">
<button type="submit" class="btn btn-success">Update Password</button>
</div>

</form>

<?php
}
?>

</div>
</div>
</div>

</div>
<div class="clear"></div>

</div>

<?php include('footer.php'); ?>
</div>