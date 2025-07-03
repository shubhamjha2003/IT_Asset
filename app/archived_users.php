<?php
session_start();
require '../db/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: dashboard.php");
    exit;
}

$result = $conn->query("SELECT * FROM users WHERE is_active = 0");

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Archived Users - IT Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

<?php include '../components/navbar.php'; ?>
<?php include '../components/sidebar.php'; ?>

<?php if ($success || $error): ?>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div class="toast align-items-center text-bg-<?= $success ? 'success' : 'danger' ?> border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body"><?= $success ?: $error ?></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="container mt-5 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="bi bi-archive me-2"></i>Archived Users</h4>
        <a href="users.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left-circle"></i> Back to Active Users
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-secondary">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Verified</th>
                    <th>Image</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><span class="badge bg-info"><?= $row['role'] ?></span></td>
                            <td>
                                <?= $row['is_verified'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-warning text-dark">No</span>' ?>
                            </td>
                            <td>
                                <?php if ($row['image']): ?>
                                    <img src="../uploaded_file/<?= $row['image'] ?>" width="40" height="40" class="rounded-circle" alt="User Image">
                                <?php else: ?>
                                    <i class="bi bi-person-circle fs-4 text-muted"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= date("d M Y", strtotime($row['created_at'])) ?></td>
                            <td>
                                <a href="restore_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Restore this user?')">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                                <a href="permanent_delete_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Permanently delete this user? This cannot be undone.')">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center text-muted">No archived users.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
