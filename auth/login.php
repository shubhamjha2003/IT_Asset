<?php
session_start();
require '../vendor/autoload.php';
require '../db/connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // CAPTCHA verification
    $recaptcha = $_POST['g-recaptcha-response'];
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lf_umwrAAAAAA-1uQOEo9AJ-JV-sDqAUuHLPBPS&response=" . $recaptcha);
    $res = json_decode($response);

    if (!$res->success) {
        $error = "⚠️ CAPTCHA verification failed.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password, role, is_verified FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $hashed_password, $role, $is_verified);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                if ($is_verified == 1) {
                    // Generate OTP
                    $otp_code = rand(100000, 999999);
                    $conn->query("UPDATE users SET otp_code = '$otp_code' WHERE id = $id");

                    $_SESSION['pending_user_id'] = $id;
                    $_SESSION['pending_user_email'] = $email;
                    $_SESSION['pending_user_name'] = $name;
                    $_SESSION['pending_user_role'] = $role;

                    // Send OTP
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
                        $mail->Subject = 'Login OTP - IT Asset System';
                        $mail->Body    = "<p>Hello <strong>$name</strong>,</p>
                                          <p>Your login OTP is:</p>
                                          <h2>$otp_code</h2>
                                          <p>This OTP is valid for one time login.</p>
                                          <p>Regards,<br>IT Asset Team</p>";
                        $mail->send();

                        header("Location: verify_login_otp.php");
                        exit;
                    } catch (Exception $e) {
                        $error = "❌ OTP could not be sent. Try again.";
                    }
                } else {
                    $error = "❌ Please verify your account via email first.";
                }
            } else {
                $error = "❌ Incorrect password.";
            }
        } else {
            $error = "❌ No user found with this email.";
        }
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Login - IT Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto shadow" style="max-width: 500px;">
        <div class="card-body">
            <h3 class="card-title text-center">Login</h3>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <div class="g-recaptcha" data-sitekey="6Lf_umwrAAAAAIZxf97hPqgaCsRwm2iKtFZtv8s5"></div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <p class="text-center mt-2"><a href="forgot_password.php">Forgot Password?</a></p>
            </form>
        </div>
    </div>
</div>
</body>
</html>
