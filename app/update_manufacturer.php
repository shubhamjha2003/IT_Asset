<?php ob_start(); ?>


<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php
include('../db/connection.php');

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM manufacturers WHERE id = $id");
$data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $url = $_POST['url'];
    $support_url = $_POST['support_url'];
    $warranty_url = $_POST['warranty_url'];
    $support_phone = $_POST['support_phone'];
    $support_email = $_POST['support_email'];

    // Handle image upload
    if ($_FILES['image']['name'] != '') {
        $imageName = $_FILES['image']['name'];
        $imageTmp = $_FILES['image']['tmp_name'];
        $imagePath = '../uploads/manufacturers/' . $imageName;
        move_uploaded_file($imageTmp, $imagePath);
    } else {
        $imagePath = $data['image_path']; // keep previous image
    }

    $conn->query("UPDATE manufacturers SET 
        name='$name',
        url='$url',
        support_url='$support_url',
        warranty_url='$warranty_url',
        support_phone='$support_phone',
        support_email='$support_email',
        image_path='$imagePath'
        WHERE id=$id");

    header("Location: manufacturer.php?msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Manufacturer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Edit Manufacturer</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" value="<?= $data['name'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Main Website URL</label>
                        <input type="url" class="form-control" name="url" value="<?= $data['url'] ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Support URL</label>
                        <input type="url" class="form-control" name="support_url" value="<?= $data['support_url'] ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Warranty Lookup URL</label>
                        <input type="url" class="form-control" name="warranty_url" value="<?= $data['warranty_url'] ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Support Phone</label>
                        <input type="text" class="form-control" name="support_phone" value="<?= $data['support_phone'] ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Support Email</label>
                        <input type="email" class="form-control" name="support_email" value="<?= $data['support_email'] ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Upload New Image (optional)</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <?php if (!empty($data['image_path'])): ?>
                            <img src="<?= $data['image_path'] ?>" width="100" class="mt-2" style="border-radius: 6px;">
                        <?php endif; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Manufacturer</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php ob_end_flush(); ?>
