<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Location List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Location List</h2>
            <a href="create_location.php" class="btn btn-success mb-3">+ Add Location</a>
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Country</th>
                        <th>Company</th>
                        <th>Contact</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = $conn->query("SELECT l.*, c.name AS company_name 
                                        FROM locations l 
                                        LEFT JOIN companies c ON l.company_id = c.id");

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['code']}</td>
                        <td>{$row['type']}</td>
                        <td>{$row['city']}</td>
                        <td>{$row['state']}</td>
                        <td>{$row['country']}</td>
                        <td>{$row['company_name']}</td>
                        <td>{$row['contact_person']}<br>{$row['contact_email']}<br>{$row['phone']}</td>
                        <td>
                            <a href='update_location.php?id={$row['id']}' class='btn btn-sm btn-primary'>Edit</a>
                            <a href='delete_company.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure you want to delete this company?');\">Delete</a>
                        </td>
                    </tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
