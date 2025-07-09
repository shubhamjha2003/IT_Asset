<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>
<?php require_once '../vendor/autoload.php'; // For QR code (e.g., endroid/qr-code) ?>

<!DOCTYPE html>
<html>
<head>
    <title>All Assets</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>All Assets</h2>
    <a href="createassets.php" class="btn btn-primary mb-3">Create New</a>

    <table class="table table-bordered">
        <thead class="table-dark">
        <tr>
            <th>QR Code</th>
            <th>Asset Name</th>
            <th>Asset Tag</th>
            <th>Serial</th>
            <th>Model</th>
            <th>Company</th>
            <th>Location</th>
            <th>Supplier</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $query = "SELECT a.*, c.name AS company_name, l.name AS location_name, s.name AS supplier_name 
                  FROM assets a
                  LEFT JOIN companies c ON a.company_id = c.id
                  LEFT JOIN locations l ON a.location_id = l.id
                  LEFT JOIN suppliers s ON a.supplier_id = s.id";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            $qr_data = "C: " . $row['company_name'] . "\n"
                     . "T: " . $row['asset_tag'] . "\n"
                     . "S: " . $row['serial'] . "\n"
                     . "M: " . $row['model'];

            // Generate QR
            $qr_path = '../qrcodes/' . $row['id'] . '.png';
            if (!file_exists($qr_path)) {
                $qrCode = new \Endroid\QrCode\QrCode($qr_data);
                $qrCode->writeFile($qr_path);
            }

            echo "<tr>
                <td><img src='$qr_path' width='80'></td>
                <td>{$row['asset_name']}</td>
                <td>{$row['asset_tag']}</td>
                <td>{$row['serial']}</td>
                <td>{$row['model']}</td>
                <td>{$row['company_name']}</td>
                <td>{$row['location_name']}</td>
                <td>{$row['supplier_name']}</td>
                <td>{$row['status']}</td>
                <td>
                    <a href='updateassets.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                    <a href='deleteasset.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Delete this asset?')\">Delete</a>
                </td>
            </tr>";
        }
        ?>

        </tbody>
    </table>
</div>
</body>
</html>