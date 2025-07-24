<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['login_id'])) {
    header("Location: ../index.php");
    exit;
}

require_once('../tcpdf/tcpdf.php');

if (!isset($_GET['id'], $_GET['table'])) {
    die("Missing parameters.");
}

$id = $conn->real_escape_string($_GET['id']);
$table = $conn->real_escape_string($_GET['table']);

$result = $conn->query("SELECT * FROM `$table` WHERE id = '$id'");

if (!$result || $result->num_rows === 0) {
    die("Record not found.");
}

$data = $result->fetch_assoc();

function formatColumnName($column)
{
    return ucwords(str_replace('_', ' ', $column));
}

$type = ucfirst(str_replace('_tbl', '', $table));
$title = $type . ' Certificate';

// Custom half-legal size landscape (7" x 8.5" in mm)
$pageWidth = 178;  // 7 inches in mm
$pageHeight = 216; // 8.5 inches in mm

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

// Determine full name for filename
$lowerType = strtolower($type);

if (in_array($lowerType, ['baptism', 'death', 'confirmation'])) {
    $firstName = isset($data['first_name']) ? $data['first_name'] : (isset($data['firstname']) ? $data['firstname'] : '');
    $lastName = isset($data['last_name']) ? $data['last_name'] : (isset($data['lastname']) ? $data['lastname'] : '');
    $fullNameRaw = trim($firstName . $lastName) !== '' ? $firstName . $lastName : 'UnknownName';
} elseif ($lowerType === 'marriage') {
    $fullNameRaw = isset($data['husband_name']) ? $data['husband_name'] : 'UnknownName';
} else {
    $fullNameRaw = isset($data['full_name']) ? $data['full_name'] : 'UnknownName';
}

$fullName = preg_replace('/[^A-Za-z0-9]/', '', $fullNameRaw);
$dateString = date('mdY');
$filename = "{$type}_{$fullName}_{$dateString}.pdf";

$pdf->Output($filename, 'I');
exit;
?>
