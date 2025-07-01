<?php ob_start(); ?>
<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('../db/connection.php');

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

    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $uploadDir = '../uploads/companies/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $imagePath = $uploadDir . $imageName;

    if (move_uploaded_file($imageTmp, $imagePath)) {
        $stmt = $conn->prepare("INSERT INTO companies (name, address, city, state, country, zip, phone, email, website, industry, employees, notes, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssiss", $name, $address, $city, $state, $country, $zip, $phone, $email, $website, $industry, $employees, $notes, $imagePath);
        $stmt->execute();
        $stmt->close();
        header("Location: company.php?msg=added");
        exit;
    } else {
        $error = "Image upload failed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Company</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Add New Company</h2>
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="POST" enctype="multipart/form-data" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Company Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Address</label>
                        <input type="text" class="form-control" name="address" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>City</label>
                        <input type="text" class="form-control" name="city" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>State</label>
                        <select class="form-select" name="state" required>
                            <option value="">Select State</option>
                            <option>Andhra Pradesh</option>
                            <option>Arunachal Pradesh</option>
                            <option>Assam</option>
                            <option>Bihar</option>
                            <option>Chhattisgarh</option>
                            <option>Goa</option>
                            <option>Gujarat</option>
                            <option>Haryana</option>
                            <option>Himachal Pradesh</option>
                            <option>Jharkhand</option>
                            <option>Karnataka</option>
                            <option>Kerala</option>
                            <option>Madhya Pradesh</option>
                            <option>Maharashtra</option>
                            <option>Manipur</option>
                            <option>Meghalaya</option>
                            <option>Mizoram</option>
                            <option>Nagaland</option>
                            <option>Odisha</option>
                            <option>Punjab</option>
                            <option>Rajasthan</option>
                            <option>Sikkim</option>
                            <option>Tamil Nadu</option>
                            <option>Telangana</option>
                            <option>Tripura</option>
                            <option>Uttar Pradesh</option>
                            <option>Uttarakhand</option>
                            <option>West Bengal</option>
                            <option>Andaman and Nicobar Islands</option>
                            <option>Chandigarh</option>
                            <option>Dadra and Nagar Haveli and Daman and Diu</option>
                            <option>Delhi</option>
                            <option>Lakshadweep</option>
                            <option>Puducherry</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Country</label>
                        <select class="form-select" name="country" required>
                            <option value="India" selected>India</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>ZIP / Postal Code</label>
                        <input type="text" class="form-control" name="zip" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Website</label>
                        <input type="url" class="form-control" name="website">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Industry</label>
                        <input type="text" class="form-control" name="industry" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Number of Employees</label>
                        <input type="number" class="form-control" name="employees" required>
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
                <button type="submit" class="btn btn-primary">Create Company</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php ob_end_flush(); ?>
