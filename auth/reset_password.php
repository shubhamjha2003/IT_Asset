<?php
session_start();
require '../db/connection.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'];
    $new_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $email = $_SESSION['reset_email'] ?? '';
    $expected_otp = $_SESSION['reset_otp'] ?? '';

    if ($otp == $expected_otp && $email) {
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_pass, $email);
        $stmt->execute();

        // Clear session data
        unset($_SESSION['reset_email'], $_SESSION['reset_otp']);
        $success = "✅ Password successfully reset. <a href='login.php'>Login now</a>";
    } else {
        $error = "❌ Invalid OTP or session expired.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - IT Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto shadow" style="max-width: 500px;">
        <div class="card-body">
            <h3 class="card-title text-center">Reset Password</h3>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">OTP Code</label>
                    <input type="text" name="otp" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Reset Password</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
