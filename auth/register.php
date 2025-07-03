<?php
session_start();
require '../db/connection.php';
require '../vendor/autoload.php';
require '../functions/logActivity.php'; // ✅ Log activity

use PHPMailer\PHPMailer\PHPMailer;

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: ../app/dashboard.php");
    exit;
}

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];
    $image    = '';

    // ✅ Upload image
    if (!empty($_FILES['image']['name'])) {
        $imgName = time() . '_' . basename($_FILES['image']['name']);
        $target = "../uploaded_file/" . $imgName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image = $imgName;
        }
    }

    // ✅ Insert user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $password, $role, $image);

    if ($stmt->execute()) {
        $userId = $stmt->insert_id;

        // ✅ Log activity
        logActivity($conn, $_SESSION['user_id'], 'register_user', "Registered new user ($email) with role $role");

        // ✅ Generate OTP
        $otp = rand(100000, 999999);
        $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));
        $conn->query("INSERT INTO user_otp_verification_logs (user_id, otp_code, expires_at) VALUES ($userId, '$otp', '$expires')");

        // ✅ Send OTP
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'shubhamkjc58@gmail.com';
            $mail->Password   = 'mzoq cmkn rhnh hxix'; // ⚠️ Secure this via env file ideally
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('shubhamkjc58@gmail.com', 'IT Asset System');
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = "Verify Your Account - OTP";
            $mail->Body    = "<p>Hello <strong>$name</strong>,</p>
                              <p>Your verification OTP is:</p>
                              <h2>$otp</h2>
                              <p>This OTP is valid for 10 minutes.</p>
                              <p>Regards,<br>IT Asset Admin</p>";
            $mail->send();

            $success = "✅ User registered successfully. OTP sent to $email";
        } catch (Exception $e) {
            $error = "❌ Failed to send OTP. Check mail config.";
        }
    } else {
        $error = "❌ Registration failed. Email may already exist.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register User - IT Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .container { max-width: 600px; margin-top: 80px; }
        .form-label::after { content: ' *'; color: red; }
    </style>
</head>
<body class="bg-light">
<div class="container bg-white p-4 shadow rounded">
    <h4><i class="bi bi-person-plus-fill me-2"></i>Register New User</h4>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
                <option value="">-- Select Role --</option>
                <option value="department_admin">Department Admin</option>
                <option value="it_manager">IT Manager</option>
                <option value="viewer">Viewer</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Upload Image (optional)</label>
            <input type="file" name="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100">Register User</button>
    </form>
</div>
</body>
</html>
