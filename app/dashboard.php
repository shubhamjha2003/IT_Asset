<?php
session_start();
require_once '../db/connection.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../components/navbar.php';
include '../components/sidebar.php';

// Fetch logged-in user's info
$email = $_SESSION['email'];
$query = $conn->prepare("SELECT * FROM users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

$name = $user['name'];
$role = ucfirst(str_replace('_', ' ', $user['role']));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - IT Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .main-content {
            margin-left: 190px;
            padding: 20px;
            margin-top: 60px;
        }
        .card {
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .welcome-box {
            background: #f8f9fa;
            border-left: 5px solid #0d6efd;
            padding: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="main-content">
    <div class="welcome-box mb-4">
        <h4>Welcome, <?= htmlspecialchars($name) ?> (<?= $role ?>)</h4>
        <p>This is your IT Asset Management dashboard. Use the tools below to manage assets and users.</p>
    </div>

    <div class="row g-4">
        <?php if ($_SESSION['role'] === 'super_admin'): ?>
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="bi bi-person-lines-fill display-5 text-primary"></i>
                    <h5 class="mt-2">User Management</h5>
                    <p class="text-muted">Create, update, delete, assign roles</p>
                    <a href="users.php" class="btn btn-outline-primary btn-sm">Manage Users</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard-data display-5 text-success"></i>
                    <h5 class="mt-2">Reports</h5>
                    <p class="text-muted">View asset reports and summaries</p>
                    <a href="Reports.php" class="btn btn-outline-success btn-sm">View Reports</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-tools display-5 text-warning"></i>
                    <h5 class="mt-2">System Tools</h5>
                    <p class="text-muted">Configure categories, depreciation, etc.</p>
                    <a href="category.php" class="btn btn-outline-warning btn-sm">Configure Tools</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <i class="bi bi-archive-fill display-5 text-danger"></i>
                    <h5 class="mt-2">Archived Users</h5>
                    <p class="text-muted">Access deleted user records</p>
                    <a href="trash_users.php" class="btn btn-outline-danger btn-sm">View Archive</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
