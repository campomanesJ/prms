<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $age = trim($_POST['age']);
    $birthdate = trim($_POST['birthdate']);
    $address = trim($_POST['address']);
    $role = strtolower(trim($_POST['role']));
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $username = trim($_POST['username']);

    if (empty($fname) || empty($lname) || empty($age) || empty($birthdate) || empty($address) || empty($role) || empty($email) || empty($password) || empty($username)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required!']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format!']);
        exit;
    }

    $checkStmt = $conn->prepare("SELECT username, email FROM user WHERE LOWER(username) = LOWER(?) OR LOWER(email) = LOWER(?)");
    $checkStmt->bind_param("ss", $username, $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (strcasecmp($row['username'], $username) === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Username already exists!']);
        } elseif (strcasecmp($row['email'], $email) === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Email already exists!']);
        }
        exit;
    }

    $checkStmt->close();

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO user (fname, lname, age, birthdate, address, role, email, password, username) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissssss", $fname, $lname, $age, $birthdate, $address, $role, $email, $hashed_password, $username);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
