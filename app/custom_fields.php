<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Custom Fields</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Custom Fields</h2>

            <a href="create_custom_fields.php" class="btn btn-success mb-3">+ Add Custom Field</a>

            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
                <div class="alert alert-success">Custom Field added successfully!</div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
                <div class="alert alert-info">Custom Field updated successfully!</div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-danger">Custom Field deleted successfully!</div>
            <?php endif; ?>

            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Field Name</th>
                        <th>Form Element</th>
                        <th>Format</th>
                        <th>Help Text</th>
                        <th>Encrypt</th>
                        <th>Unique</th>
                        <th>Show in List</th>
                        <th>Fieldsets</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = $conn->query("SELECT * FROM custom_fields");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['field_name']}</td>
                        <td>{$row['form_element']}</td>
                        <td>{$row['format']}</td>
                        <td>{$row['help_text']}</td>
                        <td>" . ($row['encrypt_value'] ? 'Yes' : 'No') . "</td>
                        <td>" . ($row['unique_value'] ? 'Yes' : 'No') . "</td>
                        <td>" . ($row['show_in_list'] ? 'Yes' : 'No') . "</td>
                        <td>";
                        $fieldsets = [];
                        if ($row['fieldset_select_all']) $fieldsets[] = 'All';
                        if ($row['fieldset_pc']) $fieldsets[] = 'PC';
                        if ($row['fieldset_printer']) $fieldsets[] = 'Printer';
                        echo implode(', ', $fieldsets);
                        echo "</td>
                        <td>
                            <a href='update_custom_fields.php?id={$row['id']}' class='btn btn-sm btn-primary'>Edit</a>
                            <a href='delete_custom_fields.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this custom field?\");'>Delete</a>
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
