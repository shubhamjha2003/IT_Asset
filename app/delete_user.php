<?php
session_start();
require '../db/connection.php';
require '../functions/logActivity.php'; // ✅ Include activity logging function

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: dashboard.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$userId = intval($_GET['id']);

// ✅ Prevent deleting yourself
if ($_SESSION['user_id'] == $userId) {
    $_SESSION['error'] = "❌ You cannot delete your own account.";
    header("Location: users.php");
    exit;
}

// ✅ Soft delete: Set is_active = 0
$stmt = $conn->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    // ✅ Log activity using function
    $adminId = $_SESSION['user_id'];
    logActivity($conn, $adminId, 'delete_user', "Soft-deleted user with ID $userId");

    $_SESSION['success'] = "✅ User deleted (archived) successfully.";
} else {
    $_SESSION['error'] = "❌ Failed to delete user.";
}

header("Location: users.php");
exit;
?>
