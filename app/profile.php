<?php
session_start();
require '../db/connection.php';

// Restrict access to logged-in users
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../components/navbar.php';
include '../components/sidebar.php';

$email = $_SESSION['email'];
$query = $conn->prepare("SELECT * FROM users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .main-content {
            margin-left: 200px;
            padding: 20px;
            margin-top: 60px;
        }
        .profile-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ccc;
        }
        .form-section {
            max-width: 600px;
        }
        .toast {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
        }
    </style>
</head>
<body>
<div class="main-content">
    <h3><i class="bi bi-person-circle me-2"></i>My Profile</h3>

    <?php if ($success): ?>
        <div class="toast show bg-success text-white p-3">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <ul class="nav nav-tabs mt-4" id="profileTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="info-tab" data-bs-toggle="tab" href="#info" role="tab">Profile Info</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password" role="tab">Change Password</a>
        </li>
        <?php if ($_SESSION['role'] === 'super_admin'): ?>
        <li class="nav-item">
            <a class="nav-link" id="logs-tab" data-bs-toggle="tab" href="#logs" role="tab">Activity Logs</a>
        </li>
        <?php endif; ?>
    </ul>

    <div class="tab-content p-3 border bg-white" id="profileTabContent">
        <!-- Profile Info -->
        <div class="tab-pane fade show active" id="info" role="tabpanel">
            <form action="update_profile.php" method="POST" enctype="multipart/form-data" class="form-section">
                <div class="mb-3 text-center">
                    <img src="../uploaded_file/<?= $user['image'] ?? 'default.png' ?>" class="profile-img" id="previewImg">
                    <input type="file" name="image" class="form-control mt-2" onchange="previewImage(event)">
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email (Re-verification Required)</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" value="<?= ucfirst($user['role']) ?>" readonly>
                </div>

                <button type="submit" class="btn btn-primary">Update Info</button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="tab-pane fade" id="password" role="tabpanel">
            <form action="update_password.php" method="POST" class="form-section">
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" id="newPassword" required>
                    <div id="strengthMessage" class="mt-1 text-muted small"></div>
                </div>

                <button type="submit" class="btn btn-warning">Change Password</button>
            </form>
        </div>

        <!-- Activity Logs (Only for Super Admin) -->
        <?php if ($_SESSION['role'] === 'super_admin'): ?>
        <div class="tab-pane fade" id="logs" role="tabpanel">
            <p>🔐 <strong>Activity Logs (Coming Soon)</strong></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('previewImg').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}

document.getElementById('newPassword').addEventListener('input', function () {
    const val = this.value;
    const strength = document.getElementById('strengthMessage');
    if (val.length < 6) {
        strength.innerText = 'Weak password';
        strength.style.color = 'red';
    } else if (!/\d/.test(val) || !/[a-z]/i.test(val)) {
        strength.innerText = 'Medium (add letters & numbers)';
        strength.style.color = 'orange';
    } else {
        strength.innerText = 'Strong password';
        strength.style.color = 'green';
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
