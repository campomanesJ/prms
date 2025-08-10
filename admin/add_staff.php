<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'db_connect.php';

$data = $_POST;

$role = $conn->real_escape_string($data['role'] ?? '');
$fname = $conn->real_escape_string($data['fname'] ?? '');
$mname = $conn->real_escape_string($data['mname'] ?? '');
$lname = $conn->real_escape_string($data['lname'] ?? '');
$username = $conn->real_escape_string($data['username'] ?? '');
$birthdate = $conn->real_escape_string($data['birthdate'] ?? '');
$address = $conn->real_escape_string($data['address'] ?? '');
$email = $conn->real_escape_string($data['email'] ?? '');

if (!$role || !$fname || !$lname || !$username || !$birthdate || !$address || !$email) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

function calculateAge($birthdate)
{
    $birthDate = new DateTime($birthdate);
    $today = new DateTime('today');
    return $birthDate->diff($today)->y;
}

$age = calculateAge($birthdate);

$sql = "INSERT INTO parish_staff (role, fname, mname, lname, username, age, birthdate, address, email)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssissss", $role, $fname, $mname, $lname, $username, $age, $birthdate, $address, $email);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
