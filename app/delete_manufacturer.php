<?php
include('../db/connection.php');

// Check if ID is provided and valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch image path to delete the file from the server
    $result = $conn->query("SELECT image_path FROM manufacturers WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath = $row['image_path'];
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath); // delete image from disk
        }
    }

    // Delete manufacturer from the database
    $conn->query("DELETE FROM manufacturers WHERE id = $id");
}

// Redirect back to manufacturer list
header("Location: manufacturer.php?msg=deleted");
exit;
?>
