<?php
session_start();
header('Content-Type: application/json');
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

if (!isset($_POST['password']) || empty($_POST['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Password cannot be empty']);
    exit;
}

$newPassword = trim($_POST['password']);
if (
    strlen($newPassword) < 8 ||
    !preg_match('/[A-Z]/', $newPassword) ||
    !preg_match('/[a-z]/', $newPassword) ||
    !preg_match('/[0-9]/', $newPassword) ||
    !preg_match('/[\W]/', $newPassword)
) {
    echo json_encode(['status' => 'error', 'message' => 'Password does not meet security requirements']);
    exit;
}
if (!isset($_SESSION['reset_email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session expired. Please request OTP again.']);
    exit;
}

$email = $_SESSION['reset_email'];
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
$stmt = $conn->prepare("UPDATE user SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashedPassword, $email);

if ($stmt->execute()) {
    unset($_SESSION['reset_email']);
    echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
}

$stmt->close();
$conn->close();
