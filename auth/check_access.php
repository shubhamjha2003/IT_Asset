<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /auth/login.php");
    exit;
}

// Role Restriction (optional)
function requireRole($role) {
    if ($_SESSION['role'] !== $role) {
        echo "<script>alert('Access denied!'); window.location.href='/app/index.php';</script>";
        exit;
    }
}
