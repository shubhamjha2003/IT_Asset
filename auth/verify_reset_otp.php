<?php
session_start();
require '../db/connection.php';

$error = '';
$success = '';

if (!isset($_SESSION['reset_user_id'])) {
    header("Location: forgot_password.php");
    exit;
}

$userId = $_SESSION['reset_user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredOtp = trim($_POST['otp']);

    // Fetch the latest OTP for this user
    $stmt = $conn->prepare("SELECT otp_code, expires_at FROM user_otp_verification_logs WHERE user_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($otp_code, $expires_at);
    $stmt->fetch();

    if ($stmt->num_rows === 1) {
        if (strtotime($expires_at) < time()) {
            $error = "⏰ OTP expired. Please request again.";
        } elseif ($enteredOtp === $otp_code) {
            // Allow password reset
            $_SESSION['reset_verified'] = true;
            header("Location: reset_password.php");
            exit;
        } else {
            $error = "❌ Invalid OTP. Please try again.";
        }
    } else {
        $error = "❌ No OTP found. Try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP - IT Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto shadow" style="max-width: 500px;">
        <div class="card-body">
            <h4 class="mb-3">📧 Verify OTP for Password Reset</h4>

            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Enter OTP <span class="text-danger">*</span></label>
                    <input type="text" name="otp" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success w-100">Verify</button>
                <p class="text-center mt-3"><a href="forgot_password.php">Back</a></p>
            </form>
        </div>
    </div>
</div>
</body>
</html>
