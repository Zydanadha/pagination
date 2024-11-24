<?php
require 'fpdf/fpdf.php';
include 'koneksi.php';

// Ambil data pencarian yang diteruskan dari form
$search = isset($_POST['search']) ? $_POST['search'] : '';
$date = isset($_POST['date']) ? $_POST['date'] : '';
$jumlah = isset($_POST['jumlah']) ? $_POST['jumlah'] : '';

// Buat objek FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Judul PDF
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 10, 'Laporan Transaksi', 0, 1, 'C');
$pdf->Ln(10);


// Query untuk mengambil data berdasarkan pencarian
$query = "SELECT * FROM transaksi WHERE 1";

if ($search) {
    $query .= " AND nama_barang LIKE '$search%'";
}
if ($date) {
    $query .= " AND tanggal_transaksi = '$date'";
}
if ($jumlah) {
    $query .= " AND jumlah = '$jumlah'";
}

$result = $conn->query($query);

// Tampilkan tabel data transaksi
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 7, 'No', 1, 0, 'C');
$pdf->Cell(50, 7, 'Nama Barang', 1, 0, 'C');
$pdf->Cell(30, 7, 'Jumlah', 1, 0, 'C');
$pdf->Cell(50, 7, 'Tanggal Transaksi', 1, 0, 'C');
$pdf->Cell(50, 7, 'Total Transaksi', 1, 1, 'C');
$pdf->SetFont('Arial', '', 10);

$no = 1;
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(10, 7, $no++, 1, 0, 'C');
    $pdf->Cell(50, 7, $row['nama_barang'], 1, 0, 'C');
    $pdf->Cell(30, 7, $row['jumlah'], 1, 0, 'C');
    $pdf->Cell(50, 7, $row['tanggal_transaksi'], 1, 0, 'C');
    $pdf->Cell(50, 7, $row['total_transaksi'], 1, 1, 'C');
}

// Output PDF
$pdf->Output();
?>
