<?php
session_start();
require '../db/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: dashboard.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$userId = intval($_GET['id']);

// Prevent deleting yourself
if ($_SESSION['user_id'] == $userId) {
    $_SESSION['error'] = "❌ You cannot delete your own account.";
    header("Location: users.php");
    exit;
}

// Soft delete: Set is_active = 0
$stmt = $conn->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
$stmt->bind_param("i", $userId);
if ($stmt->execute()) {
    // Log activity
    $log = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $adminId = $_SESSION['user_id'];
    $action = "Soft-deleted user with ID $userId";
    $log->bind_param("is", $adminId, $action);
    $log->execute();

    $_SESSION['success'] = "✅ User deleted (archived) successfully.";
} else {
    $_SESSION['error'] = "❌ Failed to delete user.";
}

header("Location: users.php");
exit;
?>
