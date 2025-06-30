<?php
session_start();
require '../db/connection.php';

if (!isset($_SESSION['email'])) {
    // If accessed directly without register
    header("Location: register.php");
    exit;
}

$email = $_SESSION['email'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = trim($_POST['otp']);

    $stmt = $conn->prepare("SELECT otp_code, is_verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($db_otp, $is_verified);
    $stmt->fetch();
    $stmt->close();

    if ($is_verified) {
        $message = "✅ You are already verified. Please log in.";
    } elseif ($entered_otp === $db_otp) {
        // Mark user as verified
        $update = $conn->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
        $update->bind_param("s", $email);
        $update->execute();
        $update->close();

        $message = "✅ OTP verified successfully! You can now log in.";
        unset($_SESSION['email']); // Clear session
        header("refresh:2;url=login.php"); // Redirect after 2 seconds
    } else {
        $message = "❌ Incorrect OTP. Please try again.";
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
            <h4 class="card-title text-center">OTP Verification</h4>
            <p class="text-center text-muted">Enter the OTP sent to your email</p>

            <?php if (!empty($message)): ?>
                <div class="alert alert-info text-center"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">OTP Code</label>
                    <input type="text" name="otp" class="form-control" maxlength="6" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Verify OTP</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
