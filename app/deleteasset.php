<?php
include('../db/connection.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Optional: delete the QR code image
    $qr_path = "../qrcodes/" . $id . ".png";
    if (file_exists($qr_path)) {
        unlink($qr_path);
    }

    // Optional: delete uploaded image
    $img_query = mysqli_query($conn, "SELECT image_path FROM assets WHERE id = $id");
    if ($img_row = mysqli_fetch_assoc($img_query)) {
        if (!empty($img_row['image_path']) && file_exists($img_row['image_path'])) {
            unlink($img_row['image_path']);
        }
    }

    $delete = "DELETE FROM assets WHERE id = $id";
    if (mysqli_query($conn, $delete)) {
        header("Location: assets.php?msg=deleted");
        exit;
    } else {
        echo "Error deleting asset: " . mysqli_error($conn);
    }
} else {
    echo "Invalid asset ID.";
}
?>
