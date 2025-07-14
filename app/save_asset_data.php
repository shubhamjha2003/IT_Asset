<?php
include('../db/connection.php');

$custom_field_id = $_POST['custom_field_id'];
$value = $_POST['value'];

// Get encrypt flag
$sql = "SELECT encrypt_value FROM custom_fields WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $custom_field_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row && $row['encrypt_value']) {
    $key = "secretkey";
    $value = openssl_encrypt($value, "AES-128-ECB", $key);
}

// Save value to asset_data
$stmt = $conn->prepare("INSERT INTO asset_data (custom_field_id, value) VALUES (?, ?)");
$stmt->bind_param("is", $custom_field_id, $value);
$stmt->execute();
$stmt->close();

echo "Data saved! <a href='list_asset_data.php'>View All Asset Data</a>";
