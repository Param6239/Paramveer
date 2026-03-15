<?php

include('config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userId = $_SESSION['user'];

if (!$userId) {
    die("User not logged in");
}

/* FETCH USER EMAIL */
$sql = "SELECT * FROM tbl_registration WHERE user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found");
}

$row = $result->fetch_assoc();
$email = $row['email'];

/* GENERATE OTP */
$otp = rand(100000, 999999);

/* STORE OTP IN SESSION */
$_SESSION['otp'] = $otp;

/* LOAD PHPMailer */
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'unknownsender456.private@gmail.com';
    $mail->Password   = 'asoq ygna dslk bvle';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    /* Sender */
    $mail->setFrom('unknownsender456.private@gmail.com', 'OMTBS - OTP Verification');

    /* Receiver */
    $mail->addAddress($email);

    /* Email content */
    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code';

    $mail->Body = "
<table width='100%' cellpadding='0' cellspacing='0' style='background:#f4f6fb;padding:30px 0;font-family:Arial,Helvetica,sans-serif;'>
<tr>
<td align='center'>

<table width='100%' cellpadding='0' cellspacing='0' style='max-width:520px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 10px 25px rgba(0,0,0,0.08);'>

<tr>
<td style='background:linear-gradient(135deg,#4f46e5,#6366f1);padding:28px;text-align:center;color:#ffffff;'>
<h1 style='margin:0;font-size:24px;font-weight:600;'>Verification Code</h1>
<p style='margin-top:6px;font-size:14px;opacity:0.9;'>Secure account verification</p>
</td>
</tr>

<tr>
<td style='padding:35px 30px;text-align:center;color:#333;'>

<h2 style='margin-top:0;font-size:20px;'>Your OTP Code</h2>

<p style='font-size:15px;color:#555;margin-bottom:24px;'>
Use the One-Time Password below to complete your verification.
</p>

<div style='display:inline-block;background:#f1f5ff;border:2px dashed #6366f1;padding:18px 36px;border-radius:10px;font-size:32px;font-weight:bold;letter-spacing:6px;color:#4f46e5;'>
$otp
</div>

<p style='margin-top:24px;font-size:14px;color:#666;'>
This code will expire shortly. Do not share it with anyone.
</p>

</td>
</tr>

<tr>
<td style='padding:0 30px;'>
<hr style='border:none;border-top:1px solid #eee;'>
</td>
</tr>

<tr>
<td style='padding:18px 30px;text-align:center;font-size:13px;color:#888;'>
If you did not request this code, you can safely ignore this email.
</td>
</tr>

</table>

</td>
</tr>
</table>
    ";

    $mail->AltBody = "Your OTP is: $otp";

    $mail->send();

    // echo "OTP sent successfully";

} catch (Exception $e) {

    echo "Mailer Error: {$mail->ErrorInfo}";
}
?>
