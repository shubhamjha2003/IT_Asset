<?php
session_start();
require '../vendor/autoload.php';
require '../db/connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];
    $otp_code = rand(100000, 999999); // 6-digit OTP

    // ✅ Verify Google reCAPTCHA
    $recaptcha = $_POST['g-recaptcha-response'];
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lf_umwrAAAAAA-1uQOEo9AJ-JV-sDqAUuHLPBPS&response=" . $recaptcha);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        $error = "⚠️ CAPTCHA verification failed. Please try again.";
    } else {
        // ✅ Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "❌ This email is already registered.";
        } else {
            // ✅ Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, otp_code) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $password, $role, $otp_code);
            $stmt->execute();

            // ✅ Send OTP via PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'shubhamkjc58@gmail.com';      // Your Gmail
                $mail->Password   = 'mzoq cmkn rhnh hxix';          // Your App Password
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('shubhamkjc58@gmail.com', 'IT Asset System');
                $mail->addAddress($email, $name);

                $mail->isHTML(true);
                $mail->Subject = 'OTP Verification - IT Asset System';
                $mail->Body    = "<p>Hello <strong>$name</strong>,</p>
                                  <p>Your OTP for verifying your account is:</p>
                                  <h2>$otp_code</h2>
                                  <p>Regards,<br>IT Asset Team</p>";

                $mail->send();

                $_SESSION['email'] = $email;
                header('Location: verify_otp.php');
                exit;
            } catch (Exception $e) {
                $error = "❌ Email could not be sent. Mailer Error: " . $mail->ErrorInfo;
            }
        }
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Register - IT Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto shadow" style="max-width: 500px;">
        <div class="card-body">
            <h3 class="card-title text-center">Create an Account</h3>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" required />
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required />
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required />
                </div>
                <div class="mb-3">
                    <label class="form-label">User Role</label>
                    <select name="role" class="form-select" required>
                        <option value="client">Client</option>
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                <div class="mb-3">
                    <div class="g-recaptcha" data-sitekey="6Lf_umwrAAAAAIZxf97hPqgaCsRwm2iKtFZtv8s5"></div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
                <p class="text-center mt-2">Already registered? <a href="login.php">Login here</a></p>
            </form>
        </div>
    </div>
</div>
</body>
</html>
