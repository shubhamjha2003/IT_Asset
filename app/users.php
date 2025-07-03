<?php
session_start();
require '../db/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: dashboard.php");
    exit;
}

$filterRole = $_GET['role'] ?? '';
$roles = ['department_admin', 'it_manager', 'viewer'];

$query = "SELECT * FROM users WHERE is_active = 1";
if (in_array($filterRole, $roles)) {
    $query .= " AND role = '$filterRole'";
}
$result = $conn->query($query);

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - IT Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .role-badge {
            text-transform: capitalize;
        }
    </style>
</head>
<body class="bg-light">

<?php include '../components/navbar.php'; ?>
<?php include '../components/sidebar.php'; ?>

<!-- Toasts -->
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
        <h4><i class="bi bi-people-fill me-2"></i>Manage Users</h4>
        <div>
            <a href="../auth/register.php" class="btn btn-primary me-2">
                <i class="bi bi-person-plus-fill me-1"></i> Create User
            </a>
            <a href="archived_users.php" class="btn btn-outline-secondary">
                <i class="bi bi-archive me-1"></i> Archived Users
            </a>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">
            <select name="role" class="form-select" onchange="this.form.submit()">
                <option value="">-- Filter by Role --</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r ?>" <?= $filterRole === $r ? 'selected' : '' ?>>
                        <?= ucfirst(str_replace('_', ' ', $r)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-secondary">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
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
                            <td><span class="badge bg-info role-badge"><?= $row['role'] ?></span></td>
                            <td><span class="badge bg-success">Active</span></td>
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
                                <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')" title="Delete"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center text-muted">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
