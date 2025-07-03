<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>

<?php
if (!isset($_GET['id'])) {
    header("Location: assets.php");
    exit;
}

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM assets WHERE id = $id");
$asset = mysqli_fetch_assoc($result);

if (!$asset) {
    echo "Asset not found!";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_id = $_POST['company_id'];
    $asset_tag = $_POST['asset_tag'];
    $serial = $_POST['serial'];
    $model = $_POST['model'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];
    $location_id = $_POST['location_id'];
    $requestable = isset($_POST['requestable']) ? 1 : 0;
    $asset_name = $_POST['asset_name'];
    $warranty = $_POST['warranty'];
    $next_audit = $_POST['next_audit'];
    $byod = isset($_POST['byod']) ? 1 : 0;
    $order_number = $_POST['order_number'];
    $purchase_date = $_POST['purchase_date'];
    $eol_date = $_POST['eol_date'];
    $supplier_id = $_POST['supplier_id'];
    $purchase_cost = $_POST['purchase_cost'];

    // Image update
    $image_path = $asset['image_path'];
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../uploads/assets/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_path = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    $update = "UPDATE assets SET
        company_id='$company_id',
        asset_tag='$asset_tag',
        serial='$serial',
        model='$model',
        status='$status',
        notes='$notes',
        location_id='$location_id',
        requestable='$requestable',
        image_path='$image_path',
        asset_name='$asset_name',
        warranty='$warranty',
        next_audit='$next_audit',
        byod='$byod',
        order_number='$order_number',
        purchase_date='$purchase_date',
        eol_date='$eol_date',
        supplier_id='$supplier_id',
        purchase_cost='$purchase_cost'
        WHERE id=$id";

    if (mysqli_query($conn, $update)) {
        header("Location: assets.php?msg=updated");
        exit;
    } else {
        echo "Error updating: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Asset</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Update Asset</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="row">

            <!-- Company -->
            <div class="col-md-6">
                <label>Company</label>
                <select name="company_id" class="form-select" required>
                    <option value="">Select Company</option>
                    <?php
                    $companies = mysqli_query($conn, "SELECT id, name FROM companies");
                    while ($c = mysqli_fetch_assoc($companies)) {
                        $selected = $c['id'] == $asset['company_id'] ? 'selected' : '';
                        echo "<option value='{$c['id']}' $selected>{$c['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Asset Tag -->
            <div class="col-md-6">
                <label>Asset Tag</label>
                <input type="text" name="asset_tag" value="<?= $asset['asset_tag'] ?>" class="form-control" required>
            </div>

            <!-- Serial -->
            <div class="col-md-6">
                <label>Serial</label>
                <input type="text" name="serial" value="<?= $asset['serial'] ?>" class="form-control" required>
            </div>

            <!-- Model -->
            <div class="col-md-6">
                <label>Model</label>
                <input type="text" name="model" value="<?= $asset['model'] ?>" class="form-control" required>
            </div>

            <!-- Status -->
            <div class="col-md-6">
                <label>Status</label>
                <select name="status" class="form-select">
                    <?php
                    $statuses = ["Ready to Deploy", "In Use", "In Repair", "Retired"];
                    foreach ($statuses as $status) {
                        $selected = $asset['status'] == $status ? 'selected' : '';
                        echo "<option value='$status' $selected>$status</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Notes -->
            <div class="col-md-6">
                <label>Notes</label>
                <textarea name="notes" class="form-control"><?= $asset['notes'] ?></textarea>
            </div>

            <!-- Location -->
            <div class="col-md-6">
                <label>Location</label>
                <select name="location_id" class="form-select" required>
                    <option value="">Select Location</option>
                    <?php
                    $locations = mysqli_query($conn, "SELECT id, name FROM locations");
                    while ($l = mysqli_fetch_assoc($locations)) {
                        $selected = $l['id'] == $asset['location_id'] ? 'selected' : '';
                        echo "<option value='{$l['id']}' $selected>{$l['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Requestable -->
            <div class="col-md-6 mt-4">
                <label><input type="checkbox" name="requestable" <?= $asset['requestable'] ? 'checked' : '' ?>> Requestable</label>
            </div>

            <!-- Upload Image -->
            <div class="col-md-6">
                <label>Upload Image</label>
                <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,.avif">
                <?php if ($asset['image_path']): ?>
                    <img src="<?= $asset['image_path'] ?>" width="80" class="mt-2">
                <?php endif; ?>
            </div>

            <!-- Asset Name -->
            <div class="col-md-6">
                <label>Asset Name</label>
                <input type="text" name="asset_name" value="<?= $asset['asset_name'] ?>" class="form-control" required>
            </div>

            <!-- Warranty -->
            <div class="col-md-6">
                <label>Warranty (months)</label>
                <input type="number" name="warranty" value="<?= $asset['warranty'] ?>" class="form-control">
            </div>

            <!-- Next Audit -->
            <div class="col-md-6">
                <label>Next Audit Date</label>
                <input type="date" name="next_audit" value="<?= $asset['next_audit'] ?>" class="form-control">
            </div>

            <!-- BYOD -->
            <div class="col-md-6 mt-4">
                <label><input type="checkbox" name="byod" <?= $asset['byod'] ? 'checked' : '' ?>> BYOD</label>
            </div>

            <!-- Order Number -->
            <div class="col-md-6">
                <label>Order Number</label>
                <input type="text" name="order_number" value="<?= $asset['order_number'] ?>" class="form-control">
            </div>

            <!-- Purchase Date -->
            <div class="col-md-6">
                <label>Purchase Date</label>
                <input type="date" name="purchase_date" value="<?= $asset['purchase_date'] ?>" class="form-control">
            </div>

            <!-- EOL Date -->
            <div class="col-md-6">
                <label>EOL Date</label>
                <input type="date" name="eol_date" value="<?= $asset['eol_date'] ?>" class="form-control">
            </div>

            <!-- Supplier -->
            <div class="col-md-6">
                <label>Supplier</label>
                <select name="supplier_id" class="form-select">
                    <option value="">Select Supplier</option>
                    <?php
                    $suppliers = mysqli_query($conn, "SELECT id, name FROM suppliers");
                    while ($s = mysqli_fetch_assoc($suppliers)) {
                        $selected = $s['id'] == $asset['supplier_id'] ? 'selected' : '';
                        echo "<option value='{$s['id']}' $selected>{$s['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Purchase Cost -->
            <div class="col-md-6">
                <label>Purchase Cost</label>
                <input type="text" name="purchase_cost" value="<?= $asset['purchase_cost'] ?>" class="form-control">
            </div>

        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-success">Update Asset</button>
            <a href="assets.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
