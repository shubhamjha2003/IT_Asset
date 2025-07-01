<?php ob_start(); ?>

<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>

<?php
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM companies WHERE id = $id");
$data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $zip = $_POST['zip'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $website = $_POST['website'];
    $industry = $_POST['industry'];
    $employees = $_POST['employees'];
    $notes = $_POST['notes'];

    // Handle image upload if a new one is selected
    if ($_FILES['image']['name'] != '') {
        $imageName = $_FILES['image']['name'];
        $imageTmp = $_FILES['image']['tmp_name'];
        $uploadDir = '../uploads/companies/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imagePath = $uploadDir . $imageName;
        move_uploaded_file($imageTmp, $imagePath);
    } else {
        $imagePath = $data['image_path']; // keep existing image if none selected
    }

    $stmt = $conn->prepare("UPDATE companies SET 
        name = ?, address = ?, city = ?, state = ?, country = ?, zip = ?, 
        phone = ?, email = ?, website = ?, industry = ?, employees = ?, 
        notes = ?, image_path = ?
        WHERE id = ?");
    $stmt->bind_param(
        "ssssssssssissi",
        $name, $address, $city, $state, $country, $zip,
        $phone, $email, $website, $industry, $employees,
        $notes, $imagePath, $id
    );
    $stmt->execute();
    $stmt->close();

    header("Location: company.php?msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Company</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2>Edit Company</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Company Name</label>
                        <input type="text" class="form-control" name="name" value="<?= $data['name'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Address</label>
                        <input type="text" class="form-control" name="address" value="<?= $data['address'] ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>City</label>
                        <input type="text" class="form-control" name="city" value="<?= $data['city'] ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>State</label>
                        <input type="text" class="form-control" name="state" value="<?= $data['state'] ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Country</label>
                        <input type="text" class="form-control" name="country" value="<?= $data['country'] ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>ZIP / Postal Code</label>
                        <input type="text" class="form-control" name="zip" value="<?= $data['zip'] ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" value="<?= $data['phone'] ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="<?= $data['email'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Website</label>
                        <input type="url" class="form-control" name="website" value="<?= $data['website'] ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Industry</label>
                        <input type="text" class="form-control" name="industry" value="<?= $data['industry'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Number of Employees</label>
                        <input type="number" class="form-control" name="employees" value="<?= $data['employees'] ?>" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="3"><?= $data['notes'] ?></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Upload New Image (optional)</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <?php if (!empty($data['image_path'])): ?>
                            <img src="<?= $data['image_path'] ?>" width="100" class="mt-2" style="border-radius: 6px;">
                        <?php endif; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Company</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php ob_end_flush(); ?>
