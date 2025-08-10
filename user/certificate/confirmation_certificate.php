<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['login_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once('../../tcpdf/tcpdf.php');

if (!isset($_GET['id'])) {
    die("Missing ID parameter.");
}

$id = $conn->real_escape_string($_GET['id']);
$table = 'confirmation_tbl';

$result = $conn->query("SELECT * FROM $table WHERE id = '$id'");
if (!$result || $result->num_rows === 0) {
    die("Record not found.");
}

$data = $result->fetch_assoc();

function buildFullName($fname, $mname, $lname)
{
    $fullName = $fname;
    if (!empty($mname)) {
        $fullName .= ' ' . strtoupper(substr($mname, 0, 1)) . '.';
    }
    $fullName .= ' ' . $lname;
    return $fullName;
}

// Fetch priest and registrar
$priest = $registrar = '';
$staffQuery = $conn->query("SELECT role, fname, mname, lname FROM parish_staff WHERE LOWER(role) IN ('priest', 'registrar')");

if ($staffQuery && $staffQuery->num_rows > 0) {
    while ($row = $staffQuery->fetch_assoc()) {
        $role = strtolower(trim($row['role']));
        $fullName = strtoupper(buildFullName($row['fname'], $row['mname'], $row['lname']));
        if ($role === 'priest') {
            $priest = "REV. FR. " . $fullName;
        } elseif ($role === 'registrar') {
            $registrar = $fullName;
        }
    }
}

if (!$priest) $priest = 'REV. FR. PRIEST';
if (!$registrar) $registrar = 'REGISTRAR';

$firstName = strtoupper($data['firstname'] ?? '');
$lastName = strtoupper($data['lastname'] ?? '');
$suffix = strtoupper($data['suffix'] ?? '');
$confirmand = trim("$firstName $lastName $suffix");

$type = 'Confirmation';
$title = $type . ' Certificate';

// A5 portrait
$pageWidth = 148;
$pageHeight = 210;

$pdf = new TCPDF('P', 'mm', [$pageWidth, $pageHeight]);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PRMS System');
$pdf->SetTitle($title);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 10);
$pdf->AddPage();

// Gradient background
$outerMargin = 2.5;
$borderThickness = 5.5;
$gradientX = $outerMargin + ($borderThickness / 2);
$gradientY = $outerMargin + ($borderThickness / 2);
$gradientWidth = $pageWidth - 2 * $gradientX;
$gradientHeight = $pageHeight - 2 * $gradientY;

$startColor = array(255, 255, 204);
$endColor = array(255, 255, 255);
$steps = 100;
$stepWidth = $gradientWidth / $steps;

for ($i = 0; $i < $steps; $i++) {
    $ratio = $i / $steps;
    $r = $startColor[0] + ($endColor[0] - $startColor[0]) * $ratio;
    $g = $startColor[1] + ($endColor[1] - $startColor[1]) * $ratio;
    $b = $startColor[2] + ($endColor[2] - $startColor[2]) * $ratio;
    $x = $gradientX + ($i * $stepWidth);
    $pdf->SetFillColor($r, $g, $b);
    $pdf->Rect($x, $gradientY, $stepWidth, $gradientHeight, 'F');
}

// Border
$outerMargin = 1.5;
$borderThickness = 3.5;
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth($borderThickness);
$pdf->Rect(
    $outerMargin + ($borderThickness / 2),
    $outerMargin + ($borderThickness / 2),
    $pageWidth - 2 * ($outerMargin + ($borderThickness / 2)),
    $pageHeight - 2 * ($outerMargin + ($borderThickness / 2))
);

// Insert left and right logos (1 inch = 25.4mm)
$logoSize = 25.4;
$logoY = 12; // vertical position
$pdf->Image('../../assets/img/cert/left.png', 15, $logoY, $logoSize, $logoSize, '', '', '', false, 300);
$pdf->Image('../../assets/img/cert/right.png', $pageWidth - $logoSize - 15, $logoY, $logoSize, $logoSize, '', '', '', false, 300);

// Title inline with logos
$pdf->SetFont('times', 'B', 16);
$titleY = $logoY + ($logoSize / 2) - 3; // lowered slightly for better balance
$pdf->SetXY(15 + $logoSize, $titleY);
$pdf->Cell($pageWidth - (2 * (15 + $logoSize)), 8, 'Certificate of Confirmation', 0, 0, 'C');

// Subtitle right under the bottom of the logos
$pdf->SetFont('times', 'I', 11);
$subtitleY = $logoY + $logoSize + 2; // 2mm below the logos
$pdf->SetXY(20, $subtitleY);
$pdf->MultiCell($pageWidth - 40, 6, 'THIS IS TO CERTIFY that the Registry of Confirmation on file in this Rectory shows the following data:', 0, 'C');
$pdf->Ln(4);


// HTML content
$html = '
<style>
    p { font-size: 10pt; margin: 2px 0; }
</style>

<p><strong>CONFIRMAND:</strong> ' . $confirmand . '</p>
<p><strong>FATHER:</strong> ' . strtoupper($data['father_name'] ?? '') . ' ' . strtoupper($data['father_suffix'] ?? '') . '</p>
<p><strong>MOTHER:</strong> ' . strtoupper($data['mother_name'] ?? '') . '</p>
<p><strong>DATE OF BAPTISM:</strong> ' . $data['baptism_date'] . '</p>
<p><strong>PLACE OF BAPTISM:</strong> ' . strtoupper($data['baptism_place'] ?? 'ST. JOSEPH PARISH CHURCH') . '</p>
<br>
<p><strong>DATE CONFIRMED:</strong> ' . $data['confirmed_date'] . '</p>
<p><strong>PLACE OF CONFIRMATION:</strong> ' . strtoupper($data['confirmed_place'] ?? 'ST. JOSEPH PARISH CHURCH') . '</p>
<p><strong>MINISTER:</strong> ' . strtoupper($data['minister'] ?? '') . '</p>
<p><strong>SPONSORS:</strong> ' . strtoupper($data['sponsors'] ?? '') . '</p>
<br>
<p>Given at the Catholic Rectory this <strong>' . date('jS') . '</strong> day of <strong>' . date('F, Y') . '</strong>, at the St. Joseph Parish, Matalom, Leyte, Philippines. For whatever purpose it may serve well.</p>
<br><br>

<table width="100%" style="font-size: 10pt;">
    <tr>
        <td>
            <strong>Certified true copy from the Book of Confirmation:</strong>
        </td>
        <td></td>
    </tr>
    <tr>
        <td>Book Number: ' . $data['book_no'] . '</td>
        <td></td>
    </tr>
    <tr>
        <td>Page Number: ' . $data['page_no'] . '</td>
        <td></td>
    </tr>
</table>

<!-- Signature section aligned to the right -->

<table width="100%" style="font-size: 10pt;">
    <tr>
        <td></td>
        <td align="center">
            <strong>' . $registrar . '</strong><br>Parish Registrar
        </td>
    </tr>
    <tr><td colspan="2" style="height:35px;"></td></tr> <!-- space between signatures -->
    <tr>
        <td></td>
        <td align="center">
            <strong>' . $priest . '</strong><br>Parish Priest
        </td>
    </tr>
</table>

';

$pdf->writeHTML($html, true, false, true, false, '');

$fullNameRaw = $firstName . $lastName;
$fullName = preg_replace('/[^A-Za-z0-9]/', '', $fullNameRaw);
$dateString = date('mdY');
$filename = "{$type}_{$fullName}_{$dateString}.pdf";

$pdf->Output($filename, 'I');
exit;
