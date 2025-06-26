<?php ob_start(); ?>


<?php
include('../components/navbar.php');
include('../components/sidebar.php');
include('../db/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $company = $_POST['company'];
  $phone = $_POST['phone'];
  $fax = $_POST['fax'];
  $manager = $_POST['manager'];
  $location = $_POST['location'];

  $image = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $_FILES['image']['name']);
  $target = "../uploaded_file/" . $image;

  if (!empty($_FILES['image']['name'])) {
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
  }

  $stmt = $conn->prepare("INSERT INTO departments (name, company, phone, fax, manager, location, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sssssss", $name, $company, $phone, $fax, $manager, $location, $image);
  $stmt->execute();

  header("Location: department.php");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Create Department</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="content" style="margin-left: 200px; padding-top: 70px;">
  <div class="container mt-4">
    <h2>Add New Department</h2>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
      <div class="mb-3"><label>Company</label><input type="text" name="company" class="form-control"></div>
      <div class="mb-3"><label>Phone</label><input type="text" name="phone" class="form-control"></div>
      <div class="mb-3"><label>Fax</label><input type="text" name="fax" class="form-control"></div>
      <div class="mb-3"><label>Manager</label><input type="text" name="manager" class="form-control"></div>
      <div class="mb-3"><label>Location</label><input type="text" name="location" class="form-control"></div>
      <div class="mb-3"><label>Upload Image</label><input type="file" name="image" class="form-control"></div>
      <button type="submit" class="btn btn-success">Create</button>
    </form>
  </div>
</div>
</body>
</html>

<?php ob_end_flush(); ?>

