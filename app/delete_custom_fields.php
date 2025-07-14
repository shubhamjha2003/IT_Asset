<?php
include('../db/connection.php');

// Check if ID is provided and valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the custom field
    $conn->query("DELETE FROM custom_fields WHERE id = $id");
}

// Redirect back to custom fields list
header("Location: custom_fields.php?msg=deleted");
exit;
?>
