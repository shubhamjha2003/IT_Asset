<?php
session_start();
require '../db/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Optional: Delete user image from server
    $imgRes = $conn->query("SELECT image FROM users WHERE id = $id");
    if ($imgRes && $imgRes->num_rows > 0) {
        $imgRow = $imgRes->fetch_assoc();
        if (!empty($imgRow['image']) && file_exists("../uploaded_file/" . $imgRow['image'])) {
            unlink("../uploaded_file/" . $imgRow['image']);
        }
    }

    // Delete from users table
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: archived_users.php");
exit;
