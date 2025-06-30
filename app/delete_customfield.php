<?php
include('../db/connection.php');

// Bulk delete via POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['bulk_ids'])) {
    $ids = explode(",", $_POST['bulk_ids']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("DELETE FROM custom_fields WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat("i", count($ids)), ...$ids);
    $stmt->execute();
    $stmt->close();
    header("Location: CustomField.php?msg=deleted");
    exit;
}

// Single delete via GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM custom_fields WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: CustomField.php?msg=deleted");
    exit;
}

// If invalid request
header("Location: CustomField.php?msg=notfound");
exit;
