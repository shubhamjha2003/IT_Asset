<!-- components/navbar.php -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
  <div class="container-fluid px-4">

    <!-- Logo + Brand -->
    <a class="navbar-brand d-flex align-items-center" href="/app/index.php">
      <img src="/images/logo.png" height="30" class="me-2" alt="Logo">
      <strong>IT Asset</strong>
    </a>

    <!-- Toggler for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Content -->
    <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
      <form class="d-flex me-3" role="search">
        <input class="form-control form-control-sm me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-light btn-sm" type="submit"><i class="bi bi-search"></i></button>
      </form>

      <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">
        <li class="nav-item me-3">
          <a class="nav-link text-white" href="#"><i class="bi bi-bell-fill"></i></a>
        </li>
        <li class="nav-item me-3">
          <a class="nav-link text-white" href="#"><i class="bi bi-gear-fill"></i></a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li><a class="dropdown-item" href="#">Settings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>

  </div>
</nav>
