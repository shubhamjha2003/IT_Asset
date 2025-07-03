<?php
session_start();
require '../db/connection.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = $success = '';
$user_id = $_SESSION['pending_user_id'] ?? null;
$user_email = $_SESSION['pending_user_email'] ?? null;
$user_name = $_SESSION['pending_user_name'] ?? null;

if (!$user_id || !$user_email) {
    header("Location: login.php");
    exit;
}

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $otp = trim($_POST['otp']);

    $stmt = $conn->prepare("SELECT id, otp_code, expires_at, verified FROM user_otp_verification_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        $error = "❌ No OTP found. Please ask the admin to resend.";
    } elseif ($row['verified']) {
        $error = "✅ Already verified.";
    } elseif ($row['otp_code'] === $otp && strtotime($row['expires_at']) > time()) {
        $conn->query("UPDATE users SET is_verified = 1 WHERE id = $user_id");
        $conn->query("UPDATE user_otp_verification_logs SET verified = 1 WHERE id = {$row['id']}");

        unset($_SESSION['pending_user_id']);
        $success = "✅ Email verified successfully! You can now login.";
    } else {
        $error = "❌ Invalid or expired OTP.";
    }
}

// Handle resend OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_otp'])) {
    $otp_code = rand(100000, 999999);
    $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    // Save new OTP
    $stmt = $conn->prepare("INSERT INTO user_otp_verification_logs (user_id, otp_code, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $otp_code, $expires_at);
    $stmt->execute();

    // Send via email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'shubhamkjc58@gmail.com';
        $mail->Password = 'mzoq cmkn rhnh hxix'; // Use App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('shubhamkjc58@gmail.com', 'IT Asset System');
        $mail->addAddress($user_email, $user_name);
        $mail->isHTML(true);
        $mail->Subject = 'Resent OTP - IT Asset System';
        $mail->Body = "<p>Hello <strong>$user_name</strong>,</p>
                       <p>Your new OTP is:</p>
                       <h2>$otp_code</h2>
                       <p>This OTP is valid for 10 minutes.</p>
                       <p>Regards,<br>IT Asset Team</p>";

        $mail->send();
        $success = "✅ New OTP sent to <strong>$user_email</strong>";
    } catch (Exception $e) {
        $error = "❌ Failed to send OTP. Try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP - IT Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 500px; margin-top: 100px; }
    </style>
</head>
<body class="bg-light">
<div class="container bg-white p-4 shadow rounded">
    <h4><i class="bi bi-shield-lock-fill me-2"></i>OTP Verification</h4>

    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Enter OTP Code</label>
            <input type="text" name="otp" maxlength="6" class="form-control" required>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" name="verify_otp" class="btn btn-primary">✅ Verify OTP</button>
            <button type="submit" name="resend_otp" class="btn btn-outline-secondary">🔄 Resend OTP</button>
        </div>
    </form>
    <?php else: ?>
        <div class="text-center mt-3"><a href="login.php" class="btn btn-success">Login Now</a></div>
    <?php endif; ?>
</div>
</body>
</html>
