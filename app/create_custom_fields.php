<?php ob_start(); ?>

<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php
include('../db/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $field_name = $_POST['field_name'];
    $form_element = $_POST['form_element'];
    $format = $_POST['format'];
    $help_text = $_POST['help_text'];

    // Checkboxes
    $encrypt_value = isset($_POST['encrypt_value']) ? 1 : 0;
    $add_to_fieldset = isset($_POST['add_to_fieldset']) ? 1 : 0;
    $show_in_list = isset($_POST['show_in_list']) ? 1 : 0;
    $show_in_requestable = isset($_POST['show_in_requestable']) ? 1 : 0;
    $include_in_emails = isset($_POST['include_in_emails']) ? 1 : 0;
    $unique_value = isset($_POST['unique_value']) ? 1 : 0;
    $allow_checkedout_user = isset($_POST['allow_checkedout_user']) ? 1 : 0;

    // Fieldsets
    $fieldset_select_all = isset($_POST['fieldset_select_all']) ? 1 : 0;
    $fieldset_pc = isset($_POST['fieldset_pc']) ? 1 : 0;
    $fieldset_printer = isset($_POST['fieldset_printer']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO custom_fields (field_name, form_element, format, help_text, encrypt_value, add_to_fieldset, show_in_list, show_in_requestable, include_in_emails, unique_value, allow_checkedout_user, fieldset_select_all, fieldset_pc, fieldset_printer) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssiiiiiiiiii",
        $field_name,
        $form_element,
        $format,
        $help_text,
        $encrypt_value,
        $add_to_fieldset,
        $show_in_list,
        $show_in_requestable,
        $include_in_emails,
        $unique_value,
        $allow_checkedout_user,
        $fieldset_select_all,
        $fieldset_pc,
        $fieldset_printer
    );
    $stmt->execute();
    $stmt->close();

    header("Location: custom_fields.php?msg=added");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Custom Field</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Add New Custom Field</h2>
            <form method="POST">
                <div class="row">
                    <!-- Left side -->
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Field Name</label>
                            <input type="text" class="form-control" name="field_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Form Element</label>
                            <select class="form-select" name="form_element" required>
                                <option value="">-- Select --</option>
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="select">Dropdown</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="radio">Radio Button</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Format</label>
                            <select class="form-select" name="format" required>
                                <option value="">-- Select --</option>
                                <option value="string">String</option>
                                <option value="number">Number</option>
                                <option value="date">Date</option>
                                <option value="email">Email</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Help Text</label>
                            <textarea class="form-control" name="help_text" rows="2"></textarea>
                            <small class="form-text text-muted">This is optional text that will appear below the form elements while editing an asset to provide context on the field.</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="encrypt_value" id="encrypt_value">
                                <label class="form-check-label" for="encrypt_value">Encrypt value of this field in the database</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="add_to_fieldset" id="add_to_fieldset">
                                <label class="form-check-label" for="add_to_fieldset">Automatically add this to every new fieldset</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_in_list" id="show_in_list">
                                <label class="form-check-label" for="show_in_list">Show in list views by default. Authorized users will still be able to show/hide via the column selector.</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_in_requestable" id="show_in_requestable">
                                <label class="form-check-label" for="show_in_requestable">Show value in requestable assets list. Encrypted fields will not be shown.</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="include_in_emails" id="include_in_emails">
                                <label class="form-check-label" for="include_in_emails">Include the value of the field in checkbox emails sent to the user? Encrypted fields cannot be included in emails.</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="unique_value" id="unique_value">
                                <label class="form-check-label" for="unique_value">This value must be unique across all assets</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="allow_checkedout_user" id="allow_checkedout_user">
                                <label class="form-check-label" for="allow_checkedout_user">Allow the checked out user to view these values on their View Assignment Assets page.</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Create Custom Field</button>
                    </div>

                    <!-- Right side: Fieldsets -->
                    <div class="col-md-4">
                        <h5>Fieldsets</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fieldset_select_all" id="fieldset_select_all">
                            <label class="form-check-label" for="fieldset_select_all">Select All</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fieldset_pc" id="fieldset_pc">
                            <label class="form-check-label" for="fieldset_pc">PC</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fieldset_printer" id="fieldset_printer">
                            <label class="form-check-label" for="fieldset_printer">Printer</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php ob_end_flush(); ?>
