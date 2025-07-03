<?php
session_start();
require '../db/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: dashboard.php");
    exit;
}

$result = $conn->query("SELECT al.*, u.name FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.timestamp DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Activity Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../components/navbar.php'; ?>
<?php include '../components/sidebar.php'; ?>

<div class="container mt-5 pt-4">
    <h4><i class="bi bi-clipboard-data me-2"></i>Activity Logs</h4>
    <div class="table-responsive mt-3">
        <table class="table table-bordered table-striped bg-white">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['name'] ?? 'Unknown' ?></td>
                    <td><?= htmlspecialchars($row['action']) ?></td>
                    <td><?= date('d M Y, h:i A', strtotime($row['timestamp'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
