<?php
include('../db/connection.php');

// Check if ID is provided and is numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Optional: Fetch image to delete from disk if needed
    $result = $conn->query("SELECT image FROM departments WHERE id = $id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath = "../uploaded_file/" . $row['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // delete image file
        }
    }

    // Now delete the department
    $conn->query("DELETE FROM departments WHERE id = $id");
}

header("Location: department.php");
exit;
?>
