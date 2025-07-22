<?php
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $required_fields = ['id', 'role', 'username', 'fname', 'lname', 'age', 'birthdate', 'address', 'email'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            echo json_encode(["status" => "error", "message" => "Missing field: $field"]);
            exit();
        }
    }

    $id = intval($_POST['id']);
    $role = strtolower(trim($_POST['role']));
    $username = trim($_POST['username']);
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $age = intval($_POST['age']);
    $birthdate = trim($_POST['birthdate']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);

    if (empty($id) || empty($role) || empty($username) || empty($fname) || empty($lname) || empty($email)) {
        echo json_encode(["status" => "error", "message" => "All fields are required!"]);
        exit();
    }

    $stmt = $conn->prepare("UPDATE user SET role = ?, username = ?, fname = ?, lname = ?, age = ?, birthdate = ?, address = ?, email = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("ssssisssi", $role, $username, $fname, $lname, $age, $birthdate, $address, $email, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Update failed: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
