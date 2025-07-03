<!-- components/sidebar.php -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
  .sidebar {
    background-color: #f8f9fa;
    border-right: 1px solid #dee2e6;
    height: 100vh;
    width: 190px;
    position: fixed;
    top: 56px;
    left: 0;
    overflow-y: auto;
    padding: 20px 10px;
    transition: all 0.3s ease;
    z-index: 1030;
  }

  .sidebar h2 {
    font-size: 1rem;
    font-weight: bold;
    margin-bottom: 20px;
  }

  .menu {
    list-style: none;
    padding: 0;
  }

  .menu li {
    margin-bottom: 10px;
  }

  .menu li a {
    display: block;
    padding: 10px;
    color: #212529;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.2s ease;
  }

  .menu li a:hover {
    background-color: #e2e6ea;
  }

  .submenu {
    display: none;
    margin-top: 5px;
    margin-left: 15px;
    list-style: none;
    padding-left: 0;
  }

  .submenu.show {
    display: block;
  }

  .submenu li a {
    font-size: 0.95rem;
    padding: 8px 10px;
  }
</style>

<div class="sidebar">
  <h2><i class="bi bi-grid-3x3-gap-fill me-2"></i>Menu</h2>
  <ul class="menu">
    <li><a href="/IT_Asset/app/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
    <li><a href="/IT_Asset/app/asset.php"><i class="bi bi-hdd-stack me-2"></i>Assets</a></li>
    <li><a href="/IT_Asset/app/reports.php"><i class="bi bi-file-earmark-bar-graph me-2"></i>Reports</a></li>
    <li><a href="/IT_Asset/app/users.php"><i class="bi bi-people me-2"></i>Users</a></li>

    <li class="has-submenu">
      <a href="#" onclick="toggleSubmenu(this)">
        <i class="bi bi-box-arrow-down-right me-2"></i>More <i class="bi bi-caret-down-fill float-end"></i>
      </a>
      <ul class="submenu">
        <li><a href="/IT_Asset/app/company.php"><i class="bi bi-building me-2"></i>Company</a></li>
        <li><a href="/IT_Asset/app/location.php"><i class="bi bi-geo-alt me-2"></i>Location</a></li>
        <li><a href="/IT_Asset/app/department.php"><i class="bi bi-diagram-3 me-2"></i>Department</a></li>
        <li><a href="/IT_Asset/app/depreciation.php"><i class="bi bi-arrow-down-up me-2"></i>Depreciation</a></li>
        <li><a href="/IT_Asset/app/supplier.php"><i class="bi bi-truck me-2"></i>Supplier</a></li>
        <li><a href="/IT_Asset/app/manufacturer.php"><i class="bi bi-gear-fill me-2"></i>Manufacturer</a></li>
        <li><a href="/IT_Asset/app/category.php"><i class="bi bi-tags me-2"></i>Category</a></li>
        <li><a href="/IT_Asset/app/asset_model.php"><i class="bi bi-pc-display me-2"></i>Asset Model</a></li>
        <li><a href="/IT_Asset/app/custom_fields.php"><i class="bi bi-ui-checks-grid me-2"></i>Custom Fields</a></li>
        <li><a href="/IT_Asset/auth/logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
      </ul>
    </li>
  </ul>
</div>

<script>
  function toggleSubmenu(clickedElement) {
    event.preventDefault();
    // Collapse all submenus
    const allSubmenus = document.querySelectorAll(".submenu");
    allSubmenus.forEach(sub => {
      if (!sub.contains(clickedElement.nextElementSibling)) {
        sub.classList.remove("show");
      }
    });

    // Toggle the clicked one
    const submenu = clickedElement.nextElementSibling;
    submenu.classList.toggle("show");
  }
</script>
