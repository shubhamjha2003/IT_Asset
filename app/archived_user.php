<?php
session_start();
require '../db/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: dashboard.php");
    exit;
}

$result = $conn->query("SELECT * FROM users WHERE is_active = 0 ORDER BY created_at DESC");

$success = $_SESSION['restore_success'] ?? '';
unset($_SESSION['restore_success']);
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

<?php if ($success): ?>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div class="toast align-items-center text-bg-success border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body"><?= $success ?></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="container mt-5 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="bi bi-archive-fill me-2"></i>Archived Users</h4>
        <a href="users.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Users</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Verified</th>
                    <th>Image</th>
                    <th>Deleted At</th>
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
                            <td><span class="badge bg-secondary"><?= $row['role'] ?></span></td>
                            <td>
                                <?= $row['is_verified'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-warning text-dark">No</span>' ?>
                            </td>
                            <td>
                                <?php if ($row['image']): ?>
                                    <img src="../uploaded_file/<?= $row['image'] ?>" width="40" height="40" class="rounded-circle">
                                <?php else: ?>
                                    <i class="bi bi-person-circle fs-4 text-muted"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= date("d M Y", strtotime($row['updated_at'] ?? $row['created_at'])) ?></td>
                            <td>
                                <a href="restore_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success mb-1"
                                   onclick="return confirm('Restore this user?')">
                                   <i class="bi bi-arrow-clockwise"></i> Restore
                                </a>
                                <form method="POST" action="permanent_delete_user.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('This will permanently delete the user. Continue?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center text-muted">No archived users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
