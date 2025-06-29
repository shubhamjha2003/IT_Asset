<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>

<!DOCTYPE html>
<html>
<head>
  <title>Asset Models</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="content" style="margin-left: 200px; padding-top: 70px;">
  <div class="container mt-4">
    <h2>Asset Model List</h2>
    <a href="create_asset_model.php" class="btn btn-success mb-3">+ Add Asset Model</a>

    <table class="table table-bordered table-hover">
      <thead class="table-dark">
        <tr>
          <th>Model Name</th>
          <th>Model No</th>
          <th>Category</th>
          <th>Manufacturer</th>
          <th>Min Qty</th>
          <th>EOL</th>
          <th>Image</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $query = "SELECT am.*, c.name as category_name, m.name as manufacturer_name
                  FROM asset_models am
                  LEFT JOIN categories c ON am.category_id = c.id
                  LEFT JOIN manufacturers m ON am.manufacturer_id = m.id";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
          echo "<tr>
                  <td>{$row['model_name']}</td>
                  <td>{$row['model_no']}</td>
                  <td>{$row['category_name']}</td>
                  <td>{$row['manufacturer_name']}</td>
                  <td>{$row['min_qty']}</td>
                  <td>{$row['eol']} months</td>
                  <td>";
                  if (!empty($row['image_path'])) {
                    echo "<img src='{$row['image_path']}' height='50' style='object-fit:cover;border-radius:6px'>";
                  } else {
                    echo "N/A";
                  }
          echo    "</td>
                  <td>
                    <a href='update_asset_model.php?id={$row['id']}' class='btn btn-primary btn-sm'>Edit</a>
                    <a href='delete_asset_model.php?id={$row['id']}' onclick=\"return confirm('Are you sure?')\" class='btn btn-danger btn-sm'>Delete</a>
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
