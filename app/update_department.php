<?php
include('../components/navbar.php');
include('../components/sidebar.php');
include('../db/connection.php');

$id = $_GET['id'];

// Get existing data
$result = $conn->query("SELECT * FROM departments WHERE id = $id");
$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $company = $_POST['company'];
  $phone = $_POST['phone'];
  $fax = $_POST['fax'];
  $manager = $_POST['manager'];
  $location = $_POST['location'];

  $image = $data['image']; // keep old image by default

  // Check for new upload
  if (!empty($_FILES['image']['name'])) {
    $image = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $_FILES['image']['name']);
    $target = "../uploaded_file/" . $image;
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
  }

  $stmt = $conn->prepare("UPDATE departments SET name=?, company=?, phone=?, fax=?, manager=?, location=?, image=? WHERE id=?");
  $stmt->bind_param("sssssssi", $name, $company, $phone, $fax, $manager, $location, $image, $id);
  $stmt->execute();

  header("Location: department.php");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Department</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="content" style="margin-left: 200px; padding-top: 70px;">
  <div class="container mt-4">
    <h2>Edit Department</h2>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3"><label>Name</label><input type="text" name="name" value="<?= $data['name'] ?>" class="form-control" required></div>
      <div class="mb-3"><label>Company</label><input type="text" name="company" value="<?= $data['company'] ?>" class="form-control"></div>
      <div class="mb-3"><label>Phone</label><input type="text" name="phone" value="<?= $data['phone'] ?>" class="form-control"></div>
      <div class="mb-3"><label>Fax</label><input type="text" name="fax" value="<?= $data['fax'] ?>" class="form-control"></div>
      <div class="mb-3"><label>Manager</label><input type="text" name="manager" value="<?= $data['manager'] ?>" class="form-control"></div>
      <div class="mb-3"><label>Location</label><input type="text" name="location" value="<?= $data['location'] ?>" class="form-control"></div>
      <div class="mb-3">
        <label>Current Image</label><br>
        <img src="../uploaded_file/<?= $data['image'] ?>" height="60"><br><br>
        <label>Upload New Image</label>
        <input type="file" name="image" class="form-control">
      </div>
      <button type="submit" class="btn btn-primary">Update</button>
    </form>
  </div>
</div>
</body>
</html>
