<?php 
session_start();
include('../db/connection.php');

// Restrict access to admins and super admins only
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin')) {
    header("Location: index.php");
    exit;
}

include('../components/navbar.php');
include('../components/sidebar.php');

// Fetch users (excluding soft-deleted ones)
$users = $conn->query("SELECT * FROM users WHERE is_deleted = 0 ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .main-content {
            margin-left: 200px;
            padding: 20px;
            margin-top: 56px;
        }
        img.user-image {
            height: 40px;
            width: 40px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>
<div class="main-content">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>All Registered Users</h3>
        <div>
            <a href="../auth/register.php" class="btn btn-success me-2">➕ Add New User</a>
            <a href="trash_users.php" class="btn btn-secondary">🗑️ View Trash</a>
        </div>
    </div>

    <?php if ($users->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <form method="POST" action="change_role.php" class="d-flex align-items-center gap-1">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <select name="new_role" class="form-select form-select-sm">
                                        <option <?= $user['role'] === 'client' ? 'selected' : '' ?>>client</option>
                                        <option <?= $user['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                                        <option <?= $user['role'] === 'super_admin' ? 'selected' : '' ?>>super_admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                </form>
                            </td>
                            <td><?= $user['otp_verified'] ? '✅ Verified' : '❌ Not Verified' ?></td>
                            <td><?= date('d M Y', strtotime($user['created_at'] ?? 'now')) ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" title="Delete"
                                   onclick="return confirm('Are you sure you want to move this user to trash?')">
                                    <i class="bi bi-trash3-fill"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>
</div>
</body>
</html>
