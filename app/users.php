<?php
session_start();
require '../db/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: dashboard.php");
    exit;
}
?>
<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>User List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Manage Users</h2>
            <a href="../auth/register.php" class="btn btn-primary mb-3">
                <i class="bi bi-person-plus-fill me-1"></i> Create User
            </a>
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Employee ID</th>
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
                <?php
                $result = $conn->query("SELECT * FROM users WHERE is_active = 1");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['employee_id']) . "</td>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td><span class='badge bg-info text-dark'>" . ucfirst($row['role']) . "</span></td>
                        <td><span class='badge bg-success'>Active</span></td>
                        <td>";
                            echo $row['is_verified'] ? "<span class='badge bg-success'>Yes</span>" : "<span class='badge bg-warning text-dark'>No</span>";
                        echo "</td>
                        <td>";
                            if (!empty($row['image'])) {
                                echo "<img src='../uploaded_file/{$row['image']}' width='50' height='50' class='rounded-circle' style='object-fit:cover;'>";
                            } else {
                                echo "<i class='bi bi-person-circle fs-4 text-muted'></i>";
                            }
                        echo "</td>
                        <td>" . date('d M Y', strtotime($row['created_at'])) . "</td>
                        <td>
                            <a href='edit_user.php?id={$row['id']}' class='btn btn-sm btn-warning'><i class='bi bi-pencil'></i></a>
                            <a href='delete_user.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure you want to delete this user?')\"><i class='bi bi-trash'></i></a>
                        </td>
                    </tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
