<?php
session_start();
require '../db/connection.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($userId, $name);
        $stmt->fetch();

        // Generate OTP
        $otp = rand(100000, 999999);
        $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $conn->query("INSERT INTO user_otp_verification_logs (user_id, otp_code, expires_at) VALUES ($userId, '$otp', '$expires')");

        // Send OTP Email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'shubhamkjc58@gmail.com';
            $mail->Password   = 'mzoq cmkn rhnh hxix';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('shubhamkjc58@gmail.com', 'IT Asset System');
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = "Reset Your Password - OTP";
            $mail->Body    = "<p>Hello <strong>$name</strong>,</p>
                              <p>Your password reset OTP is:</p>
                              <h2>$otp</h2>
                              <p>This OTP is valid for 10 minutes.</p>
                              <p>Regards,<br>IT Asset Admin</p>";
            $mail->send();

            $_SESSION['reset_user_id'] = $userId;
            $_SESSION['reset_user_email'] = $email;
            $_SESSION['reset_user_name'] = $name;

            header("Location: verify_reset_otp.php");
            exit;
        } catch (Exception $e) {
            $error = "❌ Failed to send OTP. Please try again.";
        }
    } else {
        $error = "❌ No user found with this email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - IT Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto shadow" style="max-width: 500px;">
        <div class="card-body">
            <h4 class="mb-3">🔐 Forgot Password</h4>

            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Enter Registered Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Send OTP</button>
                <p class="text-center mt-3"><a href="login.php">Back to Login</a></p>
            </form>
        </div>
    </div>
</div>
</body>
</html>
