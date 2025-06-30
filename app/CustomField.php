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
            <a href="create_customfield.php" class="btn btn-success mb-3">+ Create Custom Field</a>

            <?php
            if (isset($_GET['msg'])) {
                $msg = $_GET['msg'];
                $alertType = 'info';
                if ($msg == 'added') $alertType = 'success';
                else if ($msg == 'updated') $alertType = 'primary';
                else if ($msg == 'deleted') $alertType = 'danger';

                echo "<div class='alert alert-$alertType'>Custom Field $msg successfully.</div>";
            }
            ?>

            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Field Name</th>
                        <th>Form Element</th>
                        <th>Format</th>
                        <th>Fieldsets</th>
                        <th>Flags</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = $conn->query("SELECT * FROM custom_fields");
                while ($row = $result->fetch_assoc()) {
                    // Combine flags into readable format
                    $flags = [];
                    if ($row['encrypt']) $flags[] = "Encrypt";
                    if ($row['auto_add']) $flags[] = "Auto Add";
                    if ($row['show_in_list']) $flags[] = "List";
                    if ($row['show_in_requestable']) $flags[] = "Request";
                    if ($row['include_in_email']) $flags[] = "Email";
                    if ($row['must_be_unique']) $flags[] = "Unique";
                    if ($row['allow_user_view']) $flags[] = "User View";

                    echo "<tr>
                        <td>{$row['field_name']}</td>
                        <td>{$row['form_element']}</td>
                        <td>{$row['format']}</td>
                        <td>{$row['fieldsets']}</td>
                        <td>" . implode(", ", $flags) . "</td>
                        <td>
                            <a href='update_customfield.php?id={$row['id']}' class='btn btn-sm btn-primary'>Edit</a>
                            <a href='delete_customfield.php?id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this field?')\" class='btn btn-sm btn-danger'>Delete</a>
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

            <?php if (isset($_GET['msg'])): ?>
                <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055">
                    <div class="toast align-items-center text-white <?= $_GET['msg'] === 'deleted' ? 'bg-success' : 'bg-danger' ?> border-0 show" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                <?= $_GET['msg'] === 'deleted' ? 'Field deleted successfully!' : 'Action failed or field not found.' ?>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <a href="create_customfield.php" class="btn btn-success">+ Create Custom Field</a>
                <button id="bulkDeleteBtn" class="btn btn-danger ms-2" disabled>Delete Selected</button>
            </div>

            <form id="bulkDeleteForm" method="POST" action="delete_customfield.php">
                <input type="hidden" name="bulk_ids" id="bulk_ids">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID</th>
                            <th>Field Name</th>
                            <th>Type</th>
                            <th>Options</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM custom_fields");
                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><input type="checkbox" class="selectRow" value="<?= $row['id'] ?>"></td>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['field_name']) ?></td>
                            <td><?= $row['form_element'] ?></td>
                            <td>
                                <?= $row['encrypt'] ? 'Encrypted<br>' : '' ?>
                                <?= $row['auto_add'] ? 'Auto Add<br>' : '' ?>
                                <?= $row['show_in_list'] ? 'List View<br>' : '' ?>
                            </td>
                            <td>
                                <a href="update_customfield.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Edit</a>

                                <!-- Delete Button (Triggers Modal) -->
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['id'] ?>">
                                    Delete
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="deleteModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $row['id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="deleteModalLabel<?= $row['id'] ?>">Confirm Delete</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete the field "<?= htmlspecialchars($row['field_name']) ?>"?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <a href="delete_customfield.php?id=<?= $row['id'] ?>" class="btn btn-danger">Yes, Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Select All Checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.selectRow');
        checkboxes.forEach(cb => cb.checked = this.checked);
        toggleBulkButton();
    });

    // Individual Checkbox Change
    document.querySelectorAll('.selectRow').forEach(cb => {
        cb.addEventListener('change', toggleBulkButton);
    });

    function toggleBulkButton() {
        const selected = Array.from(document.querySelectorAll('.selectRow')).filter(cb => cb.checked);
        document.getElementById('bulkDeleteBtn').disabled = selected.length === 0;
    }

    document.getElementById('bulkDeleteBtn').addEventListener('click', function () {
        const selectedIds = Array.from(document.querySelectorAll('.selectRow'))
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        if (selectedIds.length > 0 && confirm("Are you sure you want to delete selected fields?")) {
            document.getElementById('bulk_ids').value = selectedIds.join(',');
            document.getElementById('bulkDeleteForm').submit();
        }
    });
</script>
</body>
</html>