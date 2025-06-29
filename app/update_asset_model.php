<?php ob_start(); ?>
<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>

<?php
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM asset_models WHERE id = $id");
$data = $result->fetch_assoc();

$categories = $conn->query("SELECT id, name FROM categories");
$manufacturers = $conn->query("SELECT id, name FROM manufacturers");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $model_name = $_POST['model_name'];
    $category_id = $_POST['category_id'];
    $manufacturer_id = $_POST['manufacturer_id'];
    $model_no = $_POST['model_no'];
    $depreciation = $_POST['depreciation'];
    $min_qty = $_POST['min_qty'];
    $eol = $_POST['eol'];
    $notes = $_POST['notes'];

    // Image upload
    if (!empty($_FILES['image']['name'])) {
        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $imagePath = "../uploaded_file/" . $fileName;
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    } else {
        $imagePath = $data['image_path'];
    }

    $stmt = $conn->prepare("UPDATE asset_models SET model_name=?, category_id=?, manufacturer_id=?, model_no=?, depreciation=?, min_qty=?, eol=?, notes=?, image_path=? WHERE id=?");
    $stmt->bind_param("siiisiiisi", $model_name, $category_id, $manufacturer_id, $model_no, $depreciation, $min_qty, $eol, $notes, $imagePath, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: asset_model.php?msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Asset Model</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2>Edit Asset Model</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Asset Model Name</label>
                        <input type="text" class="form-control" name="model_name" value="<?= $data['model_name'] ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Category</label>
                        <select class="form-select" name="category_id" required>
                            <?php while ($cat = $categories->fetch_assoc()) { ?>
                                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $data['category_id'] ? 'selected' : '' ?>>
                                    <?= $cat['name'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Manufacturer</label>
                        <select class="form-select" name="manufacturer_id" required>
                            <?php while ($man = $manufacturers->fetch_assoc()) { ?>
                                <option value="<?= $man['id'] ?>" <?= $man['id'] == $data['manufacturer_id'] ? 'selected' : '' ?>>
                                    <?= $man['name'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Model Number</label>
                        <input type="text" class="form-control" name="model_no" value="<?= $data['model_no'] ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Depreciation</label>
                        <select class="form-select" name="depreciation">
                            <option value="0" <?= $data['depreciation'] == 0 ? 'selected' : '' ?>>Do Not Depreciate</option>
                            <option value="12" <?= $data['depreciation'] == 12 ? 'selected' : '' ?>>12 months</option>
                            <option value="24" <?= $data['depreciation'] == 24 ? 'selected' : '' ?>>24 months</option>
                            <option value="36" <?= $data['depreciation'] == 36 ? 'selected' : '' ?>>36 months</option>
                            <option value="60" <?= $data['depreciation'] == 60 ? 'selected' : '' ?>>60 months</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Minimum Quantity</label>
                        <input type="number" class="form-control" name="min_qty" value="<?= $data['min_qty'] ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>EOL (months)</label>
                        <input type="number" class="form-control" name="eol" value="<?= $data['eol'] ?>">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="3"><?= $data['notes'] ?></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Upload New Image</label>
                        <input type="file" class="form-control" name="image">
                        <?php if (!empty($data['image_path'])): ?>
                            <img src="<?= $data['image_path'] ?>" width="100" class="mt-2" style="border-radius:6px;">
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Model</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php ob_end_flush(); ?>
