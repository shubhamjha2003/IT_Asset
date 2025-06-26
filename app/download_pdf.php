<?php
require_once '../libs/fpdf/fpdf.php';
include('../db/connection.php');

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM departments WHERE id = $id");
$data = $result->fetch_assoc();

$pdf = new FPDF();
$pdf->AddPage();

// Title
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Department Details',0,1,'C');
$pdf->Ln(10);

// Department Data
$pdf->SetFont('Arial','',12);
$pdf->Cell(50,10,'Name:',0);        $pdf->Cell(0,10,$data['name'],0,1);
$pdf->Cell(50,10,'Company:',0);     $pdf->Cell(0,10,$data['company'],0,1);
$pdf->Cell(50,10,'Phone:',0);       $pdf->Cell(0,10,$data['phone'],0,1);
$pdf->Cell(50,10,'Fax:',0);         $pdf->Cell(0,10,$data['fax'],0,1);
$pdf->Cell(50,10,'Manager:',0);     $pdf->Cell(0,10,$data['manager'],0,1);
$pdf->Cell(50,10,'Location:',0);    $pdf->Cell(0,10,$data['location'],0,1);

// Output
$pdf->Output();
?>
