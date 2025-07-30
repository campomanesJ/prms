<?php
include 'db_connect.php';

$data = $_POST;

$id = intval($data['id'] ?? 0);
$role = $conn->real_escape_string($data['role'] ?? '');
$fname = $conn->real_escape_string($data['fname'] ?? '');
$mname = $conn->real_escape_string($data['mname'] ?? '');
$lname = $conn->real_escape_string($data['lname'] ?? '');
$username = $conn->real_escape_string($data['username'] ?? '');
// Remove age input reading
$birthdate = $conn->real_escape_string($data['birthdate'] ?? '');
$address = $conn->real_escape_string($data['address'] ?? '');
$email = $conn->real_escape_string($data['email'] ?? '');

if (!$id || !$role || !$fname || !$lname || !$username || !$birthdate || !$address || !$email) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Calculate age based on birthdate
function calculateAge($birthdate) {
    $birthDate = new DateTime($birthdate);
    $today = new DateTime('today');
    return $birthDate->diff($today)->y;
}

$age = calculateAge($birthdate);

$sql = "UPDATE parish_staff SET role=?, fname=?, mname=?, lname=?, username=?, age=?, birthdate=?, address=?, email=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssisssi", $role, $fname, $mname, $lname, $username, $age, $birthdate, $address, $email, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
