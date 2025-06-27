<?php ob_start(); ?>


<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>

<?php
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM categories WHERE id = $id");


$data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $use_default_eula = isset($_POST['use_default_eula']) ? 1 : 0;
    $require_acceptance = isset($_POST['require_acceptance']) ? 1 : 0;
    $send_email = isset($_POST['send_email']) ? 1 : 0;

    // Image handling
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $imageTmp = $_FILES['image']['tmp_name'];
        $imagePath = "../uploaded_file/" . $imageName;
        move_uploaded_file($imageTmp, $imagePath);
    } else {
        $imagePath = $data['image_path']; // keep existing image
    }

    $stmt = $conn->prepare("UPDATE categories SET name=?, type=?, use_default_eula=?, require_acceptance=?, send_email=?, image_path=? WHERE id=?");
    $stmt->bind_param("siiissi", $name, $type, $use_default_eula, $require_acceptance, $send_email, $imagePath, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: category.php?msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Edit Category</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Category Name</label>
                        <input type="text" class="form-control" name="name" value="<?= $data['name'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Type</label>
                        <select class="form-select" name="type" required>
                            <option value="">Select Type</option>
                            <option <?= $data['type'] == 'Accessory' ? 'selected' : '' ?>>Accessory</option>
                            <option <?= $data['type'] == 'Asset' ? 'selected' : '' ?>>Asset</option>
                            <option <?= $data['type'] == 'Consumable' ? 'selected' : '' ?>>Consumable</option>
                            <option <?= $data['type'] == 'Component' ? 'selected' : '' ?>>Component</option>
                            <option <?= $data['type'] == 'License' ? 'selected' : '' ?>>License</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Category EULA Options</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="use_default_eula" id="eula1" <?= $data['use_default_eula'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="eula1">
                                Use the primary default EULA instead.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="require_acceptance" id="eula2" <?= $data['require_acceptance'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="eula2">
                                Require users to confirm acceptance of assets in this category.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="send_email" id="eula3" <?= $data['send_email'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="eula3">
                                Send email to user on checkin/checkout.
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Upload New Image (optional)</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <?php if (!empty($data['image_path'])): ?>
                            <img src="<?= $data['image_path'] ?>" width="100" class="mt-2" style="border-radius: 6px;">
                        <?php endif; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Category</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>


<?php ob_end_flush(); ?>
