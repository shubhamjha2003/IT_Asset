<?php ob_start(); ?>
<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $model_name = $_POST['model_name'];
    $category_id = $_POST['category_id'];
    $manufacturer_id = $_POST['manufacturer_id'];
    $model_no = $_POST['model_no'];
    $depreciation = $_POST['depreciation'];
    $min_qty = $_POST['min_qty'];
    $eol = $_POST['eol'];
    $notes = $_POST['notes'];

    // Handle image upload
    $image_path = '';
    if ($_FILES['image']['error'] == 0) {
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

    $stmt = $conn->prepare("INSERT INTO asset_models (model_name, category_id, manufacturer_id, model_no, depreciation, min_qty, eol, notes, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiisiiis", $model_name, $category_id, $manufacturer_id, $model_no, $depreciation, $min_qty, $eol, $notes, $image_path);
    $stmt->execute();
    $stmt->close();

    header("Location: asset_model.php?msg=added");
    exit;
}

// Fetch dropdown values
$category_result = $conn->query("SELECT id, name FROM categories");
$manufacturer_result = $conn->query("SELECT id, name FROM manufacturers");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Asset Model</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Add New Asset Model</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Asset Model Name</label>
                        <input type="text" class="form-control" name="model_name" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Category</label>
                        <select class="form-select" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php while ($cat = $category_result->fetch_assoc()) { ?>
                                <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Manufacturer</label>
                        <select class="form-select" name="manufacturer_id" required>
                            <option value="">Select Manufacturer</option>
                            <?php while ($man = $manufacturer_result->fetch_assoc()) { ?>
                                <option value="<?= $man['id'] ?>"><?= $man['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Model Number</label>
                        <input type="text" class="form-control" name="model_no" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Depreciation</label>
                        <select class="form-select" name="depreciation">
                            <option value="0">Do Not Depreciate</option>
                            <option value="12">12 months</option>
                            <option value="24">24 months</option>
                            <option value="36">36 months</option>
                            <option value="60">60 months</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Minimum Quantity</label>
                        <input type="number" class="form-control" name="min_qty" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>End of Life (EOL)</label>
                        <input type="number" class="form-control" name="eol" placeholder="In months">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Upload Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Create Asset Model</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php ob_end_flush(); ?>
