<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Company List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Company List</h2>
            <a href="create_company.php" class="btn btn-success mb-3">+ Add Company</a>
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Company Name</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Country</th>
                        <th>ZIP</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Website</th>
                        <th>Industry</th>
                        <th>Employees</th>
                        <th>Notes</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = $conn->query("SELECT * FROM companies");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['address']}</td>
                        <td>{$row['city']}</td>
                        <td>{$row['state']}</td>
                        <td>{$row['country']}</td>
                        <td>{$row['zip']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['email']}</td>
                        <td><a href='{$row['website']}' target='_blank'>{$row['website']}</a></td>
                        <td>{$row['industry']}</td>
                        <td>{$row['employees']}</td>
                        <td>{$row['notes']}</td>
                        <td>";
                        if (!empty($row['image_path'])) {
                            echo "<img src='{$row['image_path']}' width='60' height='60' style='object-fit:cover; border-radius:5px;'>";
                        } else {
                            echo "N/A";
                        }
                        echo "</td>
                        <td>
                            <a href='update_company.php?id={$row['id']}' class='btn btn-sm btn-primary'>Edit</a>
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
