<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>

<!DOCTYPE html>
<html>
<head>
  <title>Departments</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Fixed Bottom-Right Button -->
<a href="d_department.php" 
   class="btn btn-primary position-fixed" 
   style="bottom: 20px; right: 20px; z-index: 999;">
   Download PDF
</a>




<div class="content" style="margin-left: 200px; padding-top: 70px;">
  <div class="container mt-4">
    <h2>Department List</h2>
    <a href="create_department.php" class="btn btn-success mb-3">+ Add Department</a>
    <table class="table table-bordered table-hover">
      <thead class="table-dark">
        <tr>
          <th>Name</th>
          <th>Company</th>
          <th>Phone</th>
          <th>Fax</th>
          <th>Manager</th>
          <th>Location</th>
          <th>Image</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $result = $conn->query("SELECT * FROM departments");
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['name']}</td>
                    <td>{$row['company']}</td>
                    <td>{$row['phone']}</td>
                    <td>{$row['fax']}</td>
                    <td>{$row['manager']}</td>
                    <td>{$row['location']}</td>
                    <td><img src='../uploaded_file/{$row['image']}' height='50'></td>
                    <td>
                      <a href='update_department.php?id={$row['id']}' class='btn btn-primary btn-sm'>Edit</a>
                      <a href='delete_department.php?id={$row['id']}' class='btn btn-danger btn-sm'>Delete</a>
                      <a href='generate_qr.php?id={$row['id']}' class='btn btn-warning btn-sm'>QR</a>
                      <a href='download_pdf.php?id={$row['id']}' class='btn btn-secondary btn-sm'>SAVE</a>
                    </td>
                  </tr>";
          }
        ?>
      </tbody>
    </table>
  </div>
</div>
<a href='delete_department.php?id=<?= $row['id'] ?>'
   onclick="return confirm('Are you sure you want to delete this department?')"
   class='btn btn-danger btn-sm'>Delete</a>
</body>
</html>
