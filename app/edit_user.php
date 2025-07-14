<?php
session_start();
require '../db/connection.php';
require '../functions/logActivity.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: dashboard.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$userId = intval($_GET['id']);
$success = $error = '';
$emp_id = $name = $email = $role = $image = '';

// Fetch user data
$stmt = $conn->prepare("SELECT employee_id, name, email, role, image FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($emp_id, $name, $email, $role, $image);
if (!$stmt->fetch()) {
    $error = "User not found.";
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = isset($_POST['email']) ? trim($_POST['email']) : null;
    $role     = $_POST['role'];
    $password = isset($_POST['password']) && $_POST['password'] !== '' ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $newImage = $image;

    // Handle new image upload
    if (!empty($_FILES['image']['name'])) {
        $imgName = time() . '_' . basename($_FILES['image']['name']);
        $target = "../uploaded_file/" . $imgName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $newImage = $imgName;
        }
    }

    // Update query
    $query = "UPDATE users SET name=?, email=?, role=?, image=?";
    $params = [$name, $email, $role, $newImage];

    if ($password) {
        $query .= ", password=?";
        $params[] = $password;
    }

    $query .= " WHERE id=?";
    $params[] = $userId;

    $types = "ssssi" . ($password ? "s" : "");
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $success = "✅ User updated successfully.";
        logActivity($conn, $_SESSION['user_id'], 'update_user', "Updated user ($emp_id) to role $role");
    } else {
        $error = "❌ Update failed. Email may already exist.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .form-label.required::after { content: " *"; color: red; }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-body">
            <h4 class="mb-3">✏️ Edit User</h4>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="editForm">
                <div class="mb-3">
                    <label class="form-label required">Employee ID</label>
                    <input type="text" value="<?= htmlspecialchars($emp_id) ?>" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Full Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Role</label>
                    <select name="role" class="form-select" id="roleSelect" required>
                        <option value="department_admin" <?= $role === 'department_admin' ? 'selected' : '' ?>>Department Admin</option>
                        <option value="it_manager" <?= $role === 'it_manager' ? 'selected' : '' ?>>IT Manager</option>
                        <option value="viewer" <?= $role === 'viewer' ? 'selected' : '' ?>>Viewer</option>
                        <option value="do_not_login" <?= $role === 'do_not_login' ? 'selected' : '' ?>>Do Not Able to Login</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="emailField" value="<?= htmlspecialchars($email) ?>" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" id="passwordField" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload Image (optional)</label>
                    <input type="file" name="image" class="form-control">
                    <?php if ($image): ?>
                        <small class="text-muted">Current: <img src="../uploaded_file/<?= $image ?>" width="40"></small>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="users.php" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('roleSelect').addEventListener('change', function () {
    const selected = this.value;
    const email = document.getElementById('emailField');
    const password = document.getElementById('passwordField');

    if (selected === 'do_not_login') {
        email.disabled = true;
        password.disabled = true;
        email.required = false;
        password.required = false;
        email.value = '';
        password.value = '';
    } else {
        email.disabled = false;
        password.disabled = false;
    }
});
</script>
</body>
</html>
