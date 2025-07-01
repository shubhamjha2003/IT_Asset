<?php ob_start(); ?>
<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>

<?php
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

    $stmt = $conn->prepare("INSERT INTO locations (name, code, type, address, city, state, country, zip, contact_person, contact_email, phone, company_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssi", $name, $code, $type, $address, $city, $state, $country, $zip, $contact, $email, $phone, $company_id);
    $stmt->execute();
    $stmt->close();

    header("Location: location.php?msg=added");
    exit;
}

// Fetch companies for dropdown
$companies = $conn->query("SELECT id, name FROM companies");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Location</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Add New Location</h2>
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3"><label>Location Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-md-6 mb-3"><label>Location Code</label><input type="text" name="code" class="form-control" required></div>
                    <div class="col-md-6 mb-3">
                        <label>Type</label>
                        <select name="type" class="form-select" required>
                            <option value="">Select</option>
                            <option>Head Office</option>
                            <option>Regional Office</option>
                            <option>Warehouse</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3"><label>Address</label><input type="text" name="address" class="form-control" required></div>
                    <div class="col-md-4 mb-3"><label>City</label><input type="text" name="city" class="form-control" required></div>
                    <div class="col-md-4 mb-3">
                        <label>State</label>
                        <select name="state" class="form-select" required>
                            <option value="">Select State</option>
                            <option>Jharkhand</option>
                            <option>Bihar</option>
                            <option>Odisha</option>
                            <!-- Add more as needed -->
                        </select>
                    </div>
                    <div class="col-md-4 mb-3"><label>Country</label><input type="text" name="country" class="form-control" value="India" readonly></div>
                    <div class="col-md-4 mb-3"><label>ZIP</label><input type="text" name="zip" class="form-control" required></div>
                    <div class="col-md-4 mb-3"><label>Contact Person</label><input type="text" name="contact" class="form-control" required></div>
                    <div class="col-md-4 mb-3"><label>Phone</label><input type="text" name="phone" class="form-control" required></div>
                    <div class="col-md-6 mb-3"><label>Contact Email</label><input type="email" name="email" class="form-control" required></div>

                    <!-- Company Dropdown -->
                    <div class="col-md-6 mb-3">
                        <label>Company</label>
                        <select name="company_id" class="form-select" required>
                            <option value="">Select Company</option>
                            <?php while ($row = $companies->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <button type="submit" class="btn btn-primary">Create Location</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php ob_end_flush(); ?>
