<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>

<!DOCTYPE html>
<html>
<head>
  <title>Category List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="content" style="margin-left: 200px; padding-top: 70px;">
  <div class="container mt-4">
    <h2>Category List</h2>
    <a href="create_category.php" class="btn btn-success mb-3">+ Add Category</a>
    <table class="table table-bordered table-hover">
      <thead class="table-dark">
        <tr>
          <th>Category Name</th>
          <th>Type</th>
          <th>EULA Settings</th>
          <th>Image</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $conn->query("SELECT * FROM categories");
        while ($row = $result->fetch_assoc()) {
          // Format EULA checkbox display
          $eula = [];
          if ($row['use_default_eula']) {
            $eula[] = "Use default EULA";
          }
          if ($row['require_acceptance']) {
            $eula[] = "Require user acceptance";
          }
          if ($row['send_email']) {
            $eula[] = "Send email on checkin/checkout";
          }
          $eulaText = implode('<br>', $eula);

          echo "<tr>
                  <td>{$row['name']}</td>
                  <td>{$row['type']}</td>
                  <td>{$eulaText}</td>
                  <td>";
                  if (!empty($row['image_path'])) {
                    echo "<img src='{$row['image_path']}' height='50' style='object-fit: cover; border-radius: 6px;'>";
                  } else {
                    echo "N/A";
                  }
          echo    "</td>
                  <td>
                    <a href='update_category.php?id={$row['id']}' class='btn btn-primary btn-sm'>Edit</a>
                    <a href='delete_category.php?id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this category?')\" class='btn btn-danger btn-sm'>Delete</a>
                  </td>
                </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
