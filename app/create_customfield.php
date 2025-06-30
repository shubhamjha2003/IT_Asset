<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field_name = $_POST['field_name'];
    $form_element = $_POST['form_element'];
    $format = $_POST['format'];
    $help_text = $_POST['help_text'];
    $encrypt = isset($_POST['encrypt']) ? 1 : 0;
    $auto_add = isset($_POST['auto_add']) ? 1 : 0;
    $show_in_list = isset($_POST['show_in_list']) ? 1 : 0;
    $show_in_requestable = isset($_POST['show_in_requestable']) ? 1 : 0;
    $include_in_email = isset($_POST['include_in_email']) ? 1 : 0;
    $must_be_unique = isset($_POST['must_be_unique']) ? 1 : 0;
    $allow_user_view = isset($_POST['allow_user_view']) ? 1 : 0;
    $fieldsets = $_POST['fieldsets'];

    $stmt = $conn->prepare("INSERT INTO custom_fields (field_name, form_element, format, help_text, encrypt, auto_add, show_in_list, show_in_requestable, include_in_email, must_be_unique, allow_user_view, fieldsets) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiiiiiiis", $field_name, $form_element, $format, $help_text, $encrypt, $auto_add, $show_in_list, $show_in_requestable, $include_in_email, $must_be_unique, $allow_user_view, $fieldsets);
    $stmt->execute();
    $stmt->close();

    header("Location: CustomField.php?msg=added");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Custom Field</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Create New Custom Field</h2>
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Field Name</label>
                        <input type="text" name="field_name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Form Element</label>
                        <select name="form_element" class="form-select" required>
                            <option value="text">Text</option>
                            <option value="textarea">Textarea</option>
                            <option value="select">Select</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="radio">Radio</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Format</label>
                        <input type="text" name="format" class="form-control" placeholder="Optional">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Help Text</label>
                        <textarea name="help_text" class="form-control"></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label><input type="checkbox" name="encrypt"> Encrypt</label>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label><input type="checkbox" name="auto_add"> Auto Add</label>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label><input type="checkbox" name="show_in_list"> Show in List</label>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label><input type="checkbox" name="show_in_requestable"> Show in Requestable</label>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label><input type="checkbox" name="include_in_email"> Include in Email</label>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label><input type="checkbox" name="must_be_unique"> Must be Unique</label>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label><input type="checkbox" name="allow_user_view"> Allow User View</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Fieldsets (comma-separated)</label>
                    <input type="text" name="fieldsets" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Create Field</button>
                <a href="CustomField.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>