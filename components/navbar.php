<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../db/connection.php';

$userName = 'User';
$roleBadge = '';

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT name, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $userName = $user['name'];
        $role = $user['role'];
        $roleBadge = "<span class='badge bg-info ms-2'>" . ucfirst($role) . "</span>";
    }
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
  <div class="container-fluid px-3">
    <a class="navbar-brand d-flex align-items-center" href="/IT_Asset/app/index.php">
      <i class="bi bi-hdd-network fs-4 me-2"></i><strong>IT Asset</strong>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
      <form class="d-flex me-3" role="search">
        <input class="form-control form-control-sm me-2" type="search" placeholder="Search">
        <button class="btn btn-outline-light btn-sm" type="submit"><i class="bi bi-search"></i></button>
      </form>

      <ul class="navbar-nav align-items-center">
        <li class="nav-item me-3 position-relative">
          <a class="nav-link text-white" href="#"><i class="bi bi-bell-fill"></i><span class="badge bg-danger position-absolute top-0 start-100 translate-middle">3</span></a>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle fs-5"></i> <?= htmlspecialchars($userName) ?><?= $roleBadge ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
            <li><a class="dropdown-item" href="/IT_Asset/app/profile.php"><i class="bi bi-person"></i> Profile</a></li>
            <li><a class="dropdown-item" href="/IT_Asset/app/profile.php#password"><i class="bi bi-shield-lock"></i> Change Password</a></li>
            <?php if (!empty($role) && $role === 'super_admin'): ?>
            <li><a class="dropdown-item" href="/IT_Asset/app/users.php"><i class="bi bi-shield-shaded"></i> Admin Panel</a></li>
            <?php endif; ?>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="/IT_Asset/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
