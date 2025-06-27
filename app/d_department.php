<?php
require_once '../libs/fpdf/fpdf.php';
include('../db/connection.php');

// Fetch all departments
$result = $conn->query("SELECT * FROM departments");

$pdf = new FPDF();
$pdf->AddPage();

// Title
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'All Department Details', 0, 1, 'C');
$pdf->Ln(10);

// Table Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 10, 'Name', 1);
$pdf->Cell(30, 10, 'Company', 1);
$pdf->Cell(25, 10, 'Phone', 1);
$pdf->Cell(25, 10, 'Fax', 1);
$pdf->Cell(35, 10, 'Manager', 1);
$pdf->Cell(45, 10, 'Location', 1);
$pdf->Ln();

// Table Data
$pdf->SetFont('Arial', '', 11);

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(30, 10, $row['name'], 1);
    $pdf->Cell(30, 10, $row['company'], 1);
    $pdf->Cell(25, 10, $row['phone'], 1);
    $pdf->Cell(25, 10, $row['fax'], 1);
    $pdf->Cell(35, 10, $row['manager'], 1);
    $pdf->Cell(45, 10, $row['location'], 1);
    $pdf->Ln();
}

// Output
$pdf->Output();
?>
