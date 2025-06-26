<?php
include('../db/connection.php');

// Check if ID is provided and is numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch image path to delete the file from server
    $result = $conn->query("SELECT image_path FROM suppliers WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath = $row['image_path'];
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath); // delete image from server
        }
    }

    // Delete supplier from the database
    $conn->query("DELETE FROM suppliers WHERE id = $id");
}

// Redirect back to supplier list
header("Location: supplier.php?msg=deleted");
exit;
?>
