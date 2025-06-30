<?php
session_start();
require '../vendor/autoload.php';
require '../db/connection.php';

use PHPMailer\PHPMailer\PHPMailer;

if (!isset($_SESSION['pending_user_email'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['pending_user_email'];
$name = $_SESSION['pending_user_name'];
$otp_code = rand(100000, 999999);

// Update new OTP
$conn->query("UPDATE users SET otp_code = '$otp_code' WHERE email = '$email'");

// Send email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'shubhamkjc58@gmail.com';
    $mail->Password = 'mzoq cmkn rhnh hxix';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('shubhamkjc58@gmail.com', 'IT Asset System');
    $mail->addAddress($email, $name);

    $mail->isHTML(true);
    $mail->Subject = 'New OTP - IT Asset System';
    $mail->Body    = "<p>Hello <strong>$name</strong>,</p>
                      <p>Your new login OTP is:</p>
                      <h2>$otp_code</h2>
                      <p>Regards,<br>IT Asset Team</p>";

    $mail->send();
    header("Location: verify_login_otp.php");
    exit;
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}
?>
