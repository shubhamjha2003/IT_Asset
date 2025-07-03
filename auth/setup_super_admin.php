<?php
session_start();
require '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Double check no users exist
    $check = $conn->query("SELECT COUNT(*) AS total FROM users");
    $row = $check->fetch_assoc();
    if ($row['total'] == 0) {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, is_verified, is_active) VALUES (?, ?, ?, 'super_admin', 1, 1)");
        $stmt->bind_param("sss", $name, $email, $password);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = 'super_admin';
            header("Location: ../app/dashboard.php");
            exit;
        } else {
            $error = "Failed to create super admin.";
        }
    } else {
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Setup Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 500px; margin-top: 100px; }
    </style>
</head>
<body class="bg-light">
<div class="container bg-white p-4 shadow rounded">
    <h4 class="mb-4 text-center text-primary">🚀 First-Time Setup: Super Admin</h4>
    <?php if (!empty($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Full Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Create Super Admin</button>
    </form>
</div>
</body>
</html>
