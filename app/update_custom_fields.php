<?php ob_start(); ?>

<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php
include('../db/connection.php');

$id = $_GET['id'];

// Get the old encrypt flag first
$result = $conn->query("SELECT * FROM custom_fields WHERE id = $id");
$data = $result->fetch_assoc();
$old_encrypt_value = $data['encrypt_value'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $field_name = $_POST['field_name'];
    $form_element = $_POST['form_element'];
    $format = $_POST['format'];
    $help_text = $_POST['help_text'];

    $encrypt_value = isset($_POST['encrypt_value']) ? 1 : 0;
    $add_to_fieldset = isset($_POST['add_to_fieldset']) ? 1 : 0;
    $show_in_list = isset($_POST['show_in_list']) ? 1 : 0;
    $show_in_requestable = isset($_POST['show_in_requestable']) ? 1 : 0;
    $include_in_emails = isset($_POST['include_in_emails']) ? 1 : 0;
    $unique_value = isset($_POST['unique_value']) ? 1 : 0;
    $allow_checkedout_user = isset($_POST['allow_checkedout_user']) ? 1 : 0;

    $fieldset_select_all = isset($_POST['fieldset_select_all']) ? 1 : 0;
    $fieldset_pc = isset($_POST['fieldset_pc']) ? 1 : 0;
    $fieldset_printer = isset($_POST['fieldset_printer']) ? 1 : 0;

    // Update custom_fields table
    $conn->query("UPDATE custom_fields SET 
        field_name='$field_name',
        form_element='$form_element',
        format='$format',
        help_text='$help_text',
        encrypt_value='$encrypt_value',
        add_to_fieldset='$add_to_fieldset',
        show_in_list='$show_in_list',
        show_in_requestable='$show_in_requestable',
        include_in_emails='$include_in_emails',
        unique_value='$unique_value',
        allow_checkedout_user='$allow_checkedout_user',
        fieldset_select_all='$fieldset_select_all',
        fieldset_pc='$fieldset_pc',
        fieldset_printer='$fieldset_printer'
        WHERE id=$id");

    // If the encrypt_value changed, update asset_data too!
    if ($encrypt_value != $old_encrypt_value) {
        $key = "secretkey";

        // Get all related asset_data rows
        $result = $conn->query("SELECT id, value FROM asset_data WHERE custom_field_id = $id");
        while ($row = $result->fetch_assoc()) {
            $data_id = $row['id'];
            $value = $row['value'];

            if ($encrypt_value && !$old_encrypt_value) {
                // Was plain → now encrypt
                $new_value = openssl_encrypt($value, "AES-128-ECB", $key);
            } elseif (!$encrypt_value && $old_encrypt_value) {
                // Was encrypted → now decrypt
                $new_value = openssl_decrypt($value, "AES-128-ECB", $key);
            } else {
                continue; // Nothing to do
            }

            $stmt = $conn->prepare("UPDATE asset_data SET value = ? WHERE id = ?");
            $stmt->bind_param("si", $new_value, $data_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: custom_fields.php?msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Custom Field</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-2 p-4">
            <h2 class="mb-4">Edit Custom Field</h2>
            <form method="POST">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Field Name</label>
                            <input type="text" class="form-control" name="field_name" value="<?= htmlspecialchars($data['field_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Form Element</label>
                            <select class="form-select" name="form_element" required>
                                <option value="">-- Select --</option>
                                <option value="text" <?= $data['form_element'] == 'text' ? 'selected' : '' ?>>Text</option>
                                <option value="textarea" <?= $data['form_element'] == 'textarea' ? 'selected' : '' ?>>Textarea</option>
                                <option value="select" <?= $data['form_element'] == 'select' ? 'selected' : '' ?>>Dropdown</option>
                                <option value="checkbox" <?= $data['form_element'] == 'checkbox' ? 'selected' : '' ?>>Checkbox</option>
                                <option value="radio" <?= $data['form_element'] == 'radio' ? 'selected' : '' ?>>Radio Button</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Format</label>
                            <select class="form-select" name="format" required>
                                <option value="">-- Select --</option>
                                <option value="string" <?= $data['format'] == 'string' ? 'selected' : '' ?>>String</option>
                                <option value="number" <?= $data['format'] == 'number' ? 'selected' : '' ?>>Number</option>
                                <option value="date" <?= $data['format'] == 'date' ? 'selected' : '' ?>>Date</option>
                                <option value="email" <?= $data['format'] == 'email' ? 'selected' : '' ?>>Email</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Help Text</label>
                            <textarea class="form-control" name="help_text" rows="2"><?= htmlspecialchars($data['help_text']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="encrypt_value" id="encrypt_value" <?= $data['encrypt_value'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="encrypt_value">Encrypt value of this field in the database</label>
                            </div>
                            <!-- The rest of your checkboxes -->
                            <!-- Copy same structure for other checkboxes -->
                        </div>

                        <button type="submit" class="btn btn-primary">Update Custom Field</button>
                    </div>

                    <div class="col-md-4">
                        <h5>Fieldsets</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fieldset_select_all" id="fieldset_select_all" <?= $data['fieldset_select_all'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="fieldset_select_all">Select All</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fieldset_pc" id="fieldset_pc" <?= $data['fieldset_pc'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="fieldset_pc">PC</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fieldset_printer" id="fieldset_printer" <?= $data['fieldset_printer'] ? 'checked' : '' ?>>
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
