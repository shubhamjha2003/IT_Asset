<?php
session_start();
require '../db/connection.php';
require '../vendor/autoload.php';
require '../functions/logActivity.php';

use PHPMailer\PHPMailer\PHPMailer;

// reCAPTCHA
$recaptchaSecret = '6Lf_umwrAAAAAA-1uQOEo9AJ-JV-sDqAUuHLPBPS';

$error = '';
$showToast = false;
$toastMessage = '';

// Redirect to setup page if no users exist
$checkUsers = $conn->query("SELECT COUNT(*) AS total FROM users");
$row = $checkUsers->fetch_assoc();
if ($row['total'] == 0) {
    header("Location: setup_super_admin.php");
    exit;
}

// Auto-login with cookies
if (isset($_COOKIE['remember_email']) && isset($_COOKIE['remember_token'])) {
    $email = $_COOKIE['remember_email'];
    $token = $_COOKIE['remember_token'];

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $hashed_password, $role);
    $stmt->fetch();

    if (hash('sha256', $email . $hashed_password) === $token) {
        $_SESSION['user_id'] = $id;
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $name;
        $_SESSION['role'] = $role;
        logActivity($conn, $id, 'auto_login', 'Auto login via cookies');
        header("Location: ../app/dashboard.php");
        exit;
    }
}

// Login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $captcha = $_POST['g-recaptcha-response'] ?? '';
    $captchaResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$captcha}");
    $captchaSuccess = json_decode($captchaResponse)->success;

    if (!$captchaSuccess) {
        $toastMessage = "⚠️ CAPTCHA verification failed.";
        $showToast = true;
    } else {
        $stmt = $conn->prepare("SELECT id, name, password, role, is_verified FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $hashed_password, $role, $is_verified);
            $stmt->fetch();

            // 🛑 Block login if user is not allowed
            if ($role === 'do_not_login') {
                $toastMessage = "🚫 You are not authorized to login. If you need help, contact the Admin.";
                $showToast = true;
            } elseif (!$hashed_password || !$email) {
                $toastMessage = "⚠️ Login not available for this user. Contact Admin.";
                $showToast = true;
            } elseif (password_verify($password, $hashed_password)) {
                if ($is_verified == 1) {
                    $_SESSION['user_id'] = $id;
                    $_SESSION['email'] = $email;
                    $_SESSION['name'] = $name;
                    $_SESSION['role'] = $role;

                    if ($remember) {
                        $token = hash('sha256', $email . $hashed_password);
                        setcookie('remember_email', $email, time() + (86400 * 7), "/");
                        setcookie('remember_token', $token, time() + (86400 * 7), "/");
                    }

                    logActivity($conn, $id, 'login', 'User logged in');
                    header("Location: ../app/dashboard.php");
                    exit;
                } else {
                    $_SESSION['pending_user_id'] = $id;
                    $_SESSION['pending_user_email'] = $email;
                    $_SESSION['pending_user_name'] = $name;
                    logActivity($conn, $id, 'otp_redirect', 'User redirected to OTP verification');
                    header("Location: verify_otp.php");
                    exit;
                }
            } else {
                $toastMessage = "❌ Incorrect password.";
                $showToast = true;
            }
        } else {
            $toastMessage = "❌ No user found with this email.";
            $showToast = true;
        }
    }
}

// Toast for logout
if (isset($_SESSION['logout_success'])) {
    $toastMessage = $_SESSION['logout_success'];
    $showToast = true;
    unset($_SESSION['logout_success']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - IT Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-light">

<!-- ✅ Toast -->
<?php if ($showToast): ?>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
    <div class="toast text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"><?= htmlspecialchars($toastMessage) ?></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ✅ Login Form -->
<div class="container mt-5">
    <div class="card mx-auto shadow" style="max-width: 500px;">
        <div class="card-body">
            <h3 class="card-title text-center mb-3">🔐 Login</h3>

            <form method="POST">
                <div class="mb-3">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="rememberCheck">
                    <label class="form-check-label" for="rememberCheck">Remember Me</label>
                </div>

                <div class="mb-3">
                    <div class="g-recaptcha" data-sitekey="6Lf_umwrAAAAAIZxf97hPqgaCsRwm2iKtFZtv8s5"></div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
                <p class="text-center mt-3"><a href="forgot_password.php">Forgot Password?</a></p>
            </form>
        </div>
    </div>
</div>

</body>
</html>
