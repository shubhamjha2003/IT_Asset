<?php
session_start();
require '../db/connection.php';

$error = '';

if (!isset($_SESSION['pending_user_email'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = trim($_POST['otp']);
    $email = $_SESSION['pending_user_email'];

    $stmt = $conn->prepare("SELECT id, name, role, otp_code FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $role, $stored_otp);
    $stmt->fetch();

    if ($entered_otp == $stored_otp) {
        // Finalize login
        $_SESSION['user_id'] = $id;
        $_SESSION['name'] = $name;
        $_SESSION['role'] = $role;

        // Clear pending session vars and OTP
        unset($_SESSION['pending_user_email'], $_SESSION['pending_user_name'], $_SESSION['pending_user_role'], $_SESSION['pending_user_id']);
        $conn->query("UPDATE users SET otp_code = NULL WHERE id = $id");

        header("Location: ../app/index.php");
        exit;
    } else {
        $error = "❌ Invalid OTP. Please check your email again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto shadow" style="max-width: 500px;">
        <div class="card-body">
            <h3 class="card-title text-center">OTP Verification</h3>
            <p class="text-muted text-center">Check your email for a 6-digit code</p>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Enter OTP</label>
                    <input type="number" name="otp" class="form-control" required maxlength="6">
                </div>
                <button type="submit" class="btn btn-success w-100">Verify & Login</button>
            </form>
            <p class="text-center mt-3">
                 Didn't get OTP? <a href="resend_otp.php">Resend OTP</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
