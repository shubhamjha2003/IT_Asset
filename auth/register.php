<?php
session_start();
require '../db/connection.php';
require '../functions/logActivity.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: ../app/dashboard.php");
    exit;
}

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_id   = trim($_POST['emp_id']);
    $name     = trim($_POST['name']);
    $role     = $_POST['role'];
    $email    = isset($_POST['email']) ? trim($_POST['email']) : null;
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $image    = '';

    if (!empty($_FILES['image']['name'])) {
        $imgName = time() . '_' . basename($_FILES['image']['name']);
        $target = "../uploaded_file/" . $imgName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image = $imgName;
        }
    }

    $stmt = $conn->prepare("INSERT INTO users (employee_id, name, email, password, role, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $emp_id, $name, $email, $password, $role, $image);

    if ($stmt->execute()) {
        $userId = $stmt->insert_id;
        logActivity($conn, $_SESSION['user_id'], 'register_user', "Registered new user ($email) with role $role");

        if (in_array($role, ['department_admin', 'it_manager', 'viewer']) && $email) {
            $otp = rand(100000, 999999);
            $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));
            $conn->query("INSERT INTO user_otp_verification_logs (user_id, otp_code, expires_at) VALUES ($userId, '$otp', '$expires')");

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
                $mail->Subject = "Verify Your Account - OTP";
                $mail->Body = "<p>Hello <strong>$name</strong>,</p>
                            <p>Your OTP is:</p>
                            <h2>$otp</h2>
                            <p>This OTP is valid for 10 minutes.</p>
                            <p>Regards,<br>IT Asset Admin</p>";
                $mail->send();

                $success = "✅ User registered. OTP sent to $email";
            } catch (Exception $e) {
                $error = "❌ OTP email failed to send.";
            }
        } else {
            $success = "✅ User registered (Login disabled).";
        }
    } else {
        $error = "❌ Registration failed. Duplicate email or emp ID?";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .form-label.required::after { content: " *"; color: red; }
    </style>
</head>
<body>
<div class="container my-4">
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="registerForm">
        <div class="mb-3">
            <label class="form-label required">Employee ID</label>
            <input type="text" name="emp_id" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label required">Full Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label required">Role</label>
            <select name="role" class="form-select" id="roleSelect" required>
                <option value="">-- Select Role --</option>
                <option value="department_admin">Department Admin</option>
                <option value="it_manager">IT Manager</option>
                <option value="viewer">Viewer</option>
                <option value="disabled">Do Not Able to Login</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" id="emailField" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" id="passwordField" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Upload Image (optional)</label>
            <input type="file" name="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100">Register User</button>
    </form>
</div>

<script>
    document.getElementById('roleSelect').addEventListener('change', function () {
        const selected = this.value;
        const email = document.getElementById('emailField');
        const password = document.getElementById('passwordField');

        if (selected === 'disabled') {
            email.disabled = true;
            password.disabled = true;
            email.required = false;
            password.required = false;
            email.value = '';
            password.value = '';
        } else {
            email.disabled = false;
            password.disabled = false;
            email.required = true;
            password.required = true;
        }
    });
</script>
</body>
</html>
