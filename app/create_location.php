<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('../db/connection.php');

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

    $captcha = $_POST['g-recaptcha-response'];
    $secretKey = '6Lf_umwrAAAAAA-1uQOEo9AJ-JV-sDqAUuHLPBPS';
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha");
    $resp = json_decode($response);

    if ($resp->success) {
        $stmt = $conn->prepare("INSERT INTO locations (name, code, type, address, city, state, country, zip, contact_person, contact_email, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $name, $code, $type, $address, $city, $state, $country, $zip, $contact, $email, $phone);
        $stmt->execute();
        $stmt->close();
        header("Location: location.php?msg=added");
        exit;
    } else {
        $error = "reCAPTCHA failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Location</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Add New Location</h2>
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="POST" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Location Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Location Code</label>
                        <input type="text" class="form-control" name="code" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Type</label>
                        <select class="form-select" name="type" required>
                            <option value="">Select</option>
                            <option>Head Office</option>
                            <option>Branch</option>
                            <option>Warehouse</option>
                        </select>
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
                        <label>Contact Person</label>
                        <input type="text" class="form-control" name="contact" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Contact Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="g-recaptcha" data-sitekey="6Lf_umwrAAAAAIZxf97hPqgaCsRwm2iKtFZtv8s5"></div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Create Location</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
