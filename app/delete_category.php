<?php
include('../db/connection.php');

// Check if ID is provided and is numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch image path to delete the file from server
    $result = $conn->query("SELECT image_path FROM categories WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath = $row['image_path'];
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath); // delete image from server
        }
    }

    // Delete the category from the database
    $conn->query("DELETE FROM categories WHERE id = $id");
}

// Redirect to category list page
header("Location: category.php?msg=deleted");
exit;
?>
