<?php
require_once '../libs/fpdf/fpdf.php';
include('../db/connection.php');

$id = $_GET['id'];

// JOIN with companies and locations to get their names
$sql = "SELECT d.name, d.phone, d.fax, d.manager, 
               c.name AS company_name,
               l.name AS location_name
        FROM departments d
        LEFT JOIN companies c ON d.company_id = c.id
        LEFT JOIN locations l ON d.location_id = l.id
        WHERE d.id = $id";

$result = $conn->query($sql);
$data = $result->fetch_assoc();

// Generate PDF
$pdf = new FPDF();
$pdf->AddPage();

// Title
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Department Details',0,1,'C');
$pdf->Ln(10);

// Department Info
$pdf->SetFont('Arial','',12);
$pdf->Cell(50,10,'Name:',0);        $pdf->Cell(0,10,$data['name'],0,1);
$pdf->Cell(50,10,'Company:',0);     $pdf->Cell(0,10,$data['company_name'],0,1);
$pdf->Cell(50,10,'Phone:',0);       $pdf->Cell(0,10,$data['phone'],0,1);
$pdf->Cell(50,10,'Fax:',0);         $pdf->Cell(0,10,$data['fax'],0,1);
$pdf->Cell(50,10,'Manager:',0);     $pdf->Cell(0,10,$data['manager'],0,1);
$pdf->Cell(50,10,'Location:',0);    $pdf->Cell(0,10,$data['location_name'],0,1);

// Output
$pdf->Output();
?>
