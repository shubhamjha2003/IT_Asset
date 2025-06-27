<?php
include('../db/connection.php');
include('../libs/phpqrcode/qrlib.php');

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM departments WHERE id = $id");
$data = $result->fetch_assoc();

$tempDir = "../uploaded_file/";
$fileName = 'dept_qr_' . $id . '.png';
$filePath = $tempDir . $fileName;

// QR Content: Department Info
$qrContent = "Department: {$data['name']}\nCompany: {$data['company']}\nPhone: {$data['phone']}";

QRcode::png($qrContent, $filePath, QR_ECLEVEL_H, 5);

echo "<h2>QR Code for Department</h2>";
echo "<img src='$filePath'>";
echo "<br><a href='department.php'>Back</a>";
?>
