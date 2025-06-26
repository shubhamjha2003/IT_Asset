<?php ob_start(); ?>
<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $use_default_eula = isset($_POST['use_default_eula']) ? 1 : 0;
    $require_acceptance = isset($_POST['require_acceptance']) ? 1 : 0;
    $send_email = isset($_POST['send_email']) ? 1 : 0;

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

    $stmt = $conn->prepare("INSERT INTO categories (name, type, use_default_eula, require_acceptance, send_email, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiss", $name, $type, $use_default_eula, $require_acceptance, $send_email, $image_path);
    $stmt->execute();
    $stmt->close();

    header("Location: category.php?msg=added");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Add New Category</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Category Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Type</label>
                        <select class="form-select" name="type" required>
                            <option value="">Select Type</option>
                            <option>Accessory</option>
                            <option>Asset</option>
                            <option>Consumable</option>
                            <option>Component</option>
                            <option>License</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Category EULA Options</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="use_default_eula" id="eula1">
                            <label class="form-check-label" for="eula1">
                                Use the primary default EULA instead.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="require_acceptance" id="eula2">
                            <label class="form-check-label" for="eula2">
                                Require users to confirm acceptance of assets in this category.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="send_email" id="eula3">
                            <label class="form-check-label" for="eula3">
                                Send email to user on checkin/checkout.
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Upload Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Create Category</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php ob_end_flush(); ?>
