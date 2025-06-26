<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Manufacturer List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Manufacturer List</h2>
            <a href="create_manufacturer.php" class="btn btn-success mb-3">+ Add Manufacturer</a>

            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
                <div class="alert alert-success">Manufacturer added successfully!</div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-danger">Manufacturer deleted successfully!</div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
                <div class="alert alert-info">Manufacturer updated successfully!</div>
            <?php endif; ?>

            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>URL</th>
                        <th>Support URL</th>
                        <th>Warranty URL</th>
                        <th>Support Phone</th>
                        <th>Support Email</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = $conn->query("SELECT * FROM manufacturers");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td><a href='{$row['url']}' target='_blank'>{$row['url']}</a></td>
                        <td><a href='{$row['support_url']}' target='_blank'>{$row['support_url']}</a></td>
                        <td><a href='{$row['warranty_url']}' target='_blank'>{$row['warranty_url']}</a></td>
                        <td>{$row['support_phone']}</td>
                        <td>{$row['support_email']}</td>
                        <td>";
                        if (!empty($row['image_path'])) {
                            echo "<img src='{$row['image_path']}' width='60' height='60' style='object-fit:cover; border-radius:5px;'>";
                        } else {
                            echo "N/A";
                        }
                        echo "</td>
                        <td>
                            <a href='update_manufacturer.php?id={$row['id']}' class='btn btn-sm btn-primary'>Edit</a>
                            <a href='delete_manufacturer.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this manufacturer?\");'>Delete</a>
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
