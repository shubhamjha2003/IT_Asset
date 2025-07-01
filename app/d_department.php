<?php
require_once '../libs/fpdf/fpdf.php';
include('../db/connection.php');

// Fetch departments with company and location names
$sql = "SELECT d.name, d.phone, d.fax, d.manager,
               c.name AS company_name,
               l.name AS location_name
        FROM departments d
        LEFT JOIN companies c ON d.company_id = c.id
        LEFT JOIN locations l ON d.location_id = l.id";

$result = $conn->query($sql);

// Setup FPDF
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
    $pdf->Cell(30, 10, $row['company_name'], 1);
    $pdf->Cell(25, 10, $row['phone'], 1);
    $pdf->Cell(25, 10, $row['fax'], 1);
    $pdf->Cell(35, 10, $row['manager'], 1);
    $pdf->Cell(45, 10, $row['location_name'], 1);
    $pdf->Ln();
}

// Output the PDF
$pdf->Output();
?>
