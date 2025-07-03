<?php
session_start();

// Clear session
$_SESSION = [];
session_unset();
session_destroy();

// Remove "remember me" cookies
setcookie('remember_email', '', time() - 3600, '/');
setcookie('remember_token', '', time() - 3600, '/');

// Set message before redirecting
session_start(); // Re-start to set a message
$_SESSION['logout_success'] = "✅ You have been logged out successfully.";

header("Location: login.php");
exit;
