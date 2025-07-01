<?php ob_start(); ?>

<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>

<?php
$id = $_GET['id'];

// Fetch current location data
$result = $conn->query("SELECT * FROM locations WHERE id = $id");
$data = $result->fetch_assoc();

// Fetch company list for dropdown
$companies = $conn->query("SELECT id, name FROM companies");

// Update location on form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $code = $_POST['code'];
    $type = $_POST['type'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $zip = $_POST['zip'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $company_id = $_POST['company_id'];

    $stmt = $conn->prepare("UPDATE locations SET name=?, code=?, type=?, address=?, city=?, state=?, country=?, zip=?, contact_person=?, contact_email=?, phone=?, company_id=? WHERE id=?");
    $stmt->bind_param("sssssssssssii", $name, $code, $type, $address, $city, $state, $country, $zip, $contact, $email, $phone, $company_id, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: location.php?msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Location</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2>Edit Location</h2>
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Location Name</label>
                        <input type="text" class="form-control" name="name" value="<?= $data['name'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Location Code</label>
                        <input type="text" class="form-control" name="code" value="<?= $data['code'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Type</label>
                        <select class="form-select" name="type" required>
                            <option <?= $data['type'] == 'Head Office' ? 'selected' : '' ?>>Head Office</option>
                            <option <?= $data['type'] == 'Branch' ? 'selected' : '' ?>>Branch</option>
                            <option <?= $data['type'] == 'Warehouse' ? 'selected' : '' ?>>Warehouse</option>
                        </select>
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
                        <label>Contact Person</label>
                        <input type="text" class="form-control" name="contact" value="<?= $data['contact_person'] ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" value="<?= $data['phone'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Contact Email</label>
                        <input type="email" class="form-control" name="email" value="<?= $data['contact_email'] ?>" required>
                    </div>

                    <!-- Company Dropdown -->
                    <div class="col-md-6 mb-3">
                        <label>Company</label>
                        <select class="form-select" name="company_id" required>
                            <option value="">Select Company</option>
                            <?php while ($row = $companies->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>" <?= $row['id'] == $data['company_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Location</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php ob_end_flush(); ?>
