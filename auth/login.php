<?php
session_start();
require '../db/connection.php';
require '../functions/logActivity.php';
require '../vendor/autoload.php';

$error = '';
$showToast = false;
$toastMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_id = trim($_POST['emp_id']);
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $stmt = $conn->prepare("SELECT id, name, password, role, is_verified FROM users WHERE employee_id = ?");
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password, $role, $is_verified);
        $stmt->fetch();

        if ($role === 'do_not_login') {
            logActivity($conn, $id, 'unauthorized_login_attempt', 'User with restricted role tried to login');
            header("Location: unauthorized.php");
            exit;
        } elseif (password_verify($password, $hashed_password)) {
            if ($is_verified) {
                $_SESSION['user_id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['role'] = $role;

                logActivity($conn, $id, 'login', 'User logged in');
                header("Location: ../app/dashboard.php");
                exit;
            } else {
                $_SESSION['pending_user_id'] = $id;
                $_SESSION['pending_user_name'] = $name;
                logActivity($conn, $id, 'otp_redirect', 'OTP verification required');
                header("Location: verify_otp.php");
                exit;
            }
        } else {
            $toastMessage = "❌ Incorrect password.";
            $showToast = true;
        }
    } else {
        $toastMessage = "❌ Invalid employee ID.";
        $showToast = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - IT Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .status-valid { color: green; font-weight: bold; }
        .status-invalid { color: red; font-weight: bold; }
    </style>
</head>
<body class="bg-light">

<?php if ($showToast): ?>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
    <div class="toast text-bg-danger show" role="alert">
        <div class="d-flex">
            <div class="toast-body"><?= $toastMessage ?></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="container mt-5">
    <div class="card shadow mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h3 class="text-center mb-4">🔐 Login</h3>
            <form method="POST" id="loginForm">
                <div class="mb-3">
                    <label>Employee ID <span class="text-danger">*</span></label>
                    <input type="text" name="emp_id" id="emp_id" class="form-control" required>
                    <div id="empStatus" class="mt-1"></div>
                </div>

                <div class="mb-3">
                    <label>Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    <div class="form-check mt-1">
                        <input type="checkbox" class="form-check-input" id="showPassword">
                        <label class="form-check-label" for="showPassword">Show Password</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('emp_id').addEventListener('input', function () {
    const empId = this.value;
    const statusDiv = document.getElementById('empStatus');
    const passwordField = document.getElementById('password');

    if (empId.length >= 3) {
        fetch('check_emp_status.php?emp_id=' + encodeURIComponent(empId))
            .then(response => response.json())
            .then(data => {
                if (data.status === 'authorized') {
                    statusDiv.innerHTML = '<span class="status-valid">✅ Authorized to login</span>';
                    passwordField.disabled = false;
                } else if (data.status === 'unauthorized') {
                    statusDiv.innerHTML = '<span class="status-invalid">🚫 Not authorized to login</span>';
                    passwordField.disabled = true;
                    passwordField.value = '';
                } else {
                    statusDiv.innerHTML = '<span class="status-invalid">❌ Employee ID not found</span>';
                    passwordField.disabled = true;
                    passwordField.value = '';
                }
            });
    } else {
        statusDiv.innerHTML = '';
        passwordField.disabled = false;
    }
});

document.getElementById('showPassword').addEventListener('change', function () {
    const passwordField = document.getElementById('password');
    passwordField.type = this.checked ? 'text' : 'password';
});
</script>
</body>
</html>
