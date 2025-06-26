<?php ob_start(); ?>


<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php
include('../db/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $url = $_POST['url'];
    $support_url = $_POST['support_url'];
    $warranty_url = $_POST['warranty_url'];
    $support_phone = $_POST['support_phone'];
    $support_email = $_POST['support_email'];

    // Handle image upload
    $image_path = "";
    if ($_FILES["image"]["error"] == 0) {
        $target_dir = "../uploaded_file/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    $stmt = $conn->prepare("INSERT INTO manufacturers (name, url, support_url, warranty_url, support_phone, support_email, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $url, $support_url, $warranty_url, $support_phone, $support_email, $image_path);
    $stmt->execute();
    $stmt->close();

    header("Location: manufacturer.php?msg=added");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Manufacturer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Add New Manufacturer</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Website URL</label>
                        <input type="url" class="form-control" name="url">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Support URL</label>
                        <input type="url" class="form-control" name="support_url">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Warranty Lookup URL</label>
                        <input type="url" class="form-control" name="warranty_url">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Support Phone</label>
                        <input type="text" class="form-control" name="support_phone">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Support Email</label>
                        <input type="email" class="form-control" name="support_email">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Upload Logo/Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Create Manufacturer</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>


<?php ob_end_flush(); ?>
