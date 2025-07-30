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

function buildFullName($fname, $mname, $lname) {
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

// Set half-legal portrait
$pageWidth = 178;
$pageHeight = 216;

$pdf = new TCPDF('P', 'mm', [$pageWidth, $pageHeight]);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PRMS System');
$pdf->SetTitle($title);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 10);
$pdf->AddPage();

// Gradient background (yellow to white, LEFT to RIGHT, only inside black border)
$outerMargin = 2.5; // already defined above
$borderThickness = 5.5; // already defined above
$gradientX = $outerMargin + ($borderThickness / 2);
$gradientY = $outerMargin + ($borderThickness / 2);
$gradientWidth = $pageWidth - 2 * $gradientX;
$gradientHeight = $pageHeight - 2 * $gradientY;

$startColor = array(255, 255, 204); // light yellow
$endColor = array(255, 255, 255);   // white
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


// Draw black border (3/4 cm = 7.5mm thick, 2.5mm from edge)
$outerMargin = 2.5; // 1/4 cm from edge
$borderThickness = 5.5;
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth($borderThickness);
$pdf->Rect(
    $outerMargin + ($borderThickness / 2),
    $outerMargin + ($borderThickness / 2),
    $pageWidth - 2 * ($outerMargin + ($borderThickness / 2)),
    $pageHeight - 2 * ($outerMargin + ($borderThickness / 2))
);

// HTML content
$html = '
<style>
    p { font-size: 10pt; margin: 2px 0; }
    .center { text-align: center; }
</style>

<div class="center">
    <h2 style="margin-bottom: 4px;">Certificate of Confirmation</h2>
    <p><i>THIS IS TO CERTIFY that the Registry of Confirmation on file in this Rectory shows the following data:</i></p>
</div><br>

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
        <td><strong>Certified true copy from the Book of Confirmation:</strong></td>
        <td align="center"><strong>' . $registrar . '</strong><br>Parish Registrar</td>
    </tr>
    <tr>
        <td>Book Number: ' . $data['book_no'] . '</td>
        <td></td>
    </tr>
    <tr>
        <td>Page Number: ' . $data['page_no'] . '</td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td align="center"><br><strong>' . $priest . '</strong><br>Parish Priest</td>
    </tr>
</table>
';

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

$fullNameRaw = $firstName . $lastName;
$fullName = preg_replace('/[^A-Za-z0-9]/', '', $fullNameRaw);
$dateString = date('mdY');
$filename = "{$type}_{$fullName}_{$dateString}.pdf";

$pdf->Output($filename, 'I');
exit;
?>