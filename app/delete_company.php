<?php
include('../db/connection.php');

// Check if ID is provided and is numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch image path using prepared statement
    $stmt = $conn->prepare("SELECT image_path FROM companies WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath = $row['image_path'];

        // Delete the image from the server
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    $stmt->close();

    // Delete company using prepared statement
    $stmt = $conn->prepare("DELETE FROM companies WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Redirect to company list page
header("Location: company.php?msg=deleted");
exit;
?>
