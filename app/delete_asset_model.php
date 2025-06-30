<?php
include('../db/connection.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Delete image if exists
    $stmt = $conn->prepare("SELECT image_path FROM asset_models WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $imagePath = $row['image_path'];
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    $stmt->close();

    // Delete record
    $stmt = $conn->prepare("DELETE FROM asset_models WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: asset_model.php?msg=deleted");
exit;
