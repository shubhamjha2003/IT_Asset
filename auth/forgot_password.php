<?php
session_start();
require '../vendor/autoload.php';
require '../db/connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // CAPTCHA Validation
    $recaptcha = $_POST['g-recaptcha-response'];
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lf_umwrAAAAAA-1uQOEo9AJ-JV-sDqAUuHLPBPS&response=" . $recaptcha);
    $responseData = json_decode($response);
    if (!$responseData->success) {
        $error = "⚠️ CAPTCHA verification failed.";
    } else {
        $check = $conn->prepare("SELECT name FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $check->bind_result($name);
            $check->fetch();

            $otp_code = rand(100000, 999999);
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_otp'] = $otp_code;

            // Send OTP Email
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
                $mail->Subject = 'Password Reset OTP - IT Asset';
                $mail->Body = "<p>Hello <strong>$name</strong>,</p>
                               <p>Your OTP to reset your password is:</p>
                               <h2>$otp_code</h2>
                               <p>If you didn't request this, please ignore.</p>";

                $mail->send();
                header('Location: reset_password.php');
                exit;
            } catch (Exception $e) {
                $error = "❌ Email couldn't be sent. " . $mail->ErrorInfo;
            }
        } else {
            $error = "❌ Email not found in system.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - IT Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto shadow" style="max-width: 500px;">
        <div class="card-body">
            <h3 class="card-title text-center">Forgot Password</h3>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Registered Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <div class="g-recaptcha" data-sitekey="6Lf_umwrAAAAAIZxf97hPqgaCsRwm2iKtFZtv8s5"></div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Send OTP</button>
                <p class="text-center mt-3"><a href="login.php">Back to Login</a></p>
            </form>
        </div>
    </div>
</div>
</body>
</html>
