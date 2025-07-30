<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['login_id'])) {
    header("Location: ../index.php");
    exit;
}

require_once('../../tcpdf/tcpdf.php');

if (!isset($_GET['id'])) {
    die("Missing ID parameter.");
}

$id = $conn->real_escape_string($_GET['id']);
$table = 'marriage_tbl';

$result = $conn->query("SELECT * FROM `$table` WHERE id = '$id'");

if (!$result || $result->num_rows === 0) {
    die("Record not found.");
}

$data = $result->fetch_assoc();

function formatColumnName($column)
{
    return ucwords(str_replace('_', ' ', $column));
}

$type = 'Marriage';
$title = $type . ' Certificate';

// Custom half-legal size landscape (7" x 8.5" in mm)
$pageWidth = 178;
$pageHeight = 216;

$pdf = new TCPDF('L', 'mm', [$pageWidth, $pageHeight]);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PRMS System');
$pdf->SetTitle($title);
$pdf->SetMargins(20, 20, 20);
$pdf->AddPage();

$html = '<h2 style="text-align: center;">' . $title . '</h2><br><br>';
foreach ($data as $key => $value) {
    $html .= '<strong>' . formatColumnName($key) . ':</strong> ' . htmlspecialchars($value) . '<br>';
}

$pdf->writeHTML($html, true, false, true, false, '');

$fullNameRaw = $data['husband_name'] ?? 'UnknownName';

$fullName = preg_replace('/[^A-Za-z0-9]/', '', $fullNameRaw);
$dateString = date('mdY');
$filename = "{$type}_{$fullName}_{$dateString}.pdf";

$pdf->Output($filename, 'I');
exit;
?>
