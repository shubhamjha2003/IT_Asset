<?php
session_start();
require '../db/connection.php';
require '../functions/logActivity.php'; // ✅ Activity logging function

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
$name = $email = $role = $image = '';

$stmt = $conn->prepare("SELECT name, email, role, image FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($name, $email, $role, $image);
if (!$stmt->fetch()) {
    $error = "User not found.";
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $newImage = $image;

    // ✅ Handle new image upload
    if (!empty($_FILES['image']['name'])) {
        $imgName = time() . '_' . basename($_FILES['image']['name']);
        $target = "../uploaded_file/" . $imgName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $newImage = $imgName;
        }
    }

    $update = $conn->prepare("UPDATE users SET name=?, email=?, role=?, image=? WHERE id=?");
    $update->bind_param("ssssi", $name, $email, $role, $newImage, $userId);

    if ($update->execute()) {
        $success = "✅ User updated successfully.";

        // ✅ Log the activity
        $adminId = $_SESSION['user_id'];
        logActivity($conn, $adminId, 'update_user', "Updated user ($email) to role $role");
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
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-body">
            <h4 class="mb-3">✏️ Edit User</h4>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role *</label>
                    <select name="role" class="form-select" required>
                        <option value="department_admin" <?= $role === 'department_admin' ? 'selected' : '' ?>>Department Admin</option>
                        <option value="it_manager" <?= $role === 'it_manager' ? 'selected' : '' ?>>IT Manager</option>
                        <option value="viewer" <?= $role === 'viewer' ? 'selected' : '' ?>>Viewer</option>
                    </select>
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
</body>
</html>
