<?php include('../components/navbar.php'); ?>
<?php include('../components/sidebar.php'); ?>
<?php include('../db/connection.php'); ?>

<?php
// Get existing data
if (!isset($_GET['id'])) {
    header("Location: CustomField.php");
    exit;
}
$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM custom_fields WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$field = $result->fetch_assoc();
$stmt->close();

if (!$field) {
    echo "<div class='alert alert-danger'>Field not found.</div>";
    exit;
}

// Update logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $field_name = $_POST['field_name'];
    $form_element = $_POST['form_element'];
    $format = $_POST['format'];
    $help_text = $_POST['help_text'];
    $fieldsets = isset($_POST['fieldsets']) ? implode(',', $_POST['fieldsets']) : '';

    $encrypt = isset($_POST['encrypt']) ? 1 : 0;
    $auto_add = isset($_POST['auto_add']) ? 1 : 0;
    $show_in_list = isset($_POST['show_in_list']) ? 1 : 0;
    $show_in_requestable = isset($_POST['show_in_requestable']) ? 1 : 0;
    $include_in_email = isset($_POST['include_in_email']) ? 1 : 0;
    $must_be_unique = isset($_POST['must_be_unique']) ? 1 : 0;
    $allow_user_view = isset($_POST['allow_user_view']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE custom_fields SET 
        field_name=?, form_element=?, format=?, help_text=?, encrypt=?, auto_add=?, 
        show_in_list=?, show_in_requestable=?, include_in_email=?, must_be_unique=?, 
        allow_user_view=?, fieldsets=? WHERE id=?");

    $stmt->bind_param("ssssiiiiiiisi", $field_name, $form_element, $format, $help_text, $encrypt, $auto_add,
        $show_in_list, $show_in_requestable, $include_in_email, $must_be_unique, $allow_user_view, $fieldsets, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: CustomField.php?msg=updated");
    exit;
}

// Pre-select fieldsets
$selected_fieldsets = explode(',', $field['fieldsets']);
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
                    <div class="col-md-6 mb-3">
                        <label>Field Name</label>
                        <input type="text" name="field_name" class="form-control" required value="<?= $field['field_name'] ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Form Element</label>
                        <select name="form_element" class="form-select" required>
                            <option value="">Select</option>
                            <?php
                            $elements = ['Textbox', 'Textarea', 'Dropdown', 'Checkbox', 'Date'];
                            foreach ($elements as $e) {
                                $selected = ($field['form_element'] === $e) ? 'selected' : '';
                                echo "<option value='$e' $selected>$e</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Format</label>
                        <select name="format" class="form-select">
                            <option value="">Any</option>
                            <?php
                            $formats = ['Text', 'Number', 'Email'];
                            foreach ($formats as $f) {
                                $selected = ($field['format'] === $f) ? 'selected' : '';
                                echo "<option value='$f' $selected>$f</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Help Text</label>
                        <input type="text" name="help_text" class="form-control" value="<?= $field['help_text'] ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Fieldsets</label><br>
                        <?php
                        $options = ['PC', 'Printer'];
                        foreach ($options as $set) {
                            $checked = in_array($set, $selected_fieldsets) ? 'checked' : '';
                            echo "<div class='form-check'>
                                    <input class='form-check-input' type='checkbox' name='fieldsets[]' value='$set' id='$set' $checked>
                                    <label class='form-check-label' for='$set'>$set</label>
                                  </div>";
                        }
                        ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Options</label><br>
                        <?php
                        $flags = [
                            'encrypt' => 'Encrypt before storing',
                            'auto_add' => 'Auto add to all types',
                            'show_in_list' => 'Show in asset list',
                            'show_in_requestable' => 'Show in request modal',
                            'include_in_email' => 'Include in email',
                            'must_be_unique' => 'Must be unique',
                            'allow_user_view' => 'Allow user to view'
                        ];
                        foreach ($flags as $key => $label) {
                            $checked = $field[$key] ? 'checked' : '';
                            echo "<div class='form-check'>
                                    <input class='form-check-input' type='checkbox' name='$key' id='$key' $checked>
                                    <label class='form-check-label' for='$key'>$label</label>
                                  </div>";
                        }
                        ?>
                    </div>

                    <div class="col-md-12 mt-4 d-flex justify-content-between">
                        <a href="CustomField.php" class="btn btn-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Update Field</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
</body>
</html>
