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
    $contact = $_POST['contact'];
    $phone = $_POST['phone'];
    $fax = $_POST['fax'];
    $email = $_POST['email'];
    $url = $_POST['url'];
    $notes = $_POST['notes'];

    // Handle image upload
    $image_path = "";

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {
        $upload_dir = "../uploads/suppliers/";

        // Create the folder if it does not exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Unique file name to prevent overwriting
        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Save relative path for DB
            $image_path = "uploads/suppliers/" . $file_name;
        } else {
            $error = "Image upload failed.";
        }
    }

    // Insert supplier only if upload succeeded (or no image given)
    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO suppliers (name, address, city, state, country, zip, contact_name, phone, fax, email, url, notes, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssss", $name, $address, $city, $state, $country, $zip, $contact, $phone, $fax, $email, $url, $notes, $image_path);
        $stmt->execute();
        $stmt->close();
        header("Location: supplier.php?msg=added");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Supplier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Add New Supplier</h2>
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="POST" enctype="multipart/form-data" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Supplier Name</label>
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
                        <input type="text" class="form-control" name="state" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Country</label>
                        <select class="form-select" name="country" required>
                            <option value="">Select Country</option>
                            <option>India</option>
                            <option>USA</option>
                            <option>UK</option>
                            <option>Germany</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>ZIP / Postal Code</label>
                        <input type="text" class="form-control" name="zip" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Contact Name</label>
                        <input type="text" class="form-control" name="contact" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Fax</label>
                        <input type="text" class="form-control" name="fax">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>URL</label>
                        <input type="url" class="form-control" name="url">
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
                <button type="submit" class="btn btn-primary">Create Supplier</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php ob_end_flush(); ?>
