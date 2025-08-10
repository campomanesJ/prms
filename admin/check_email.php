<?php
include 'db_connect.php';

if (isset($_GET['email'])) {
    $email = trim($_GET['email']);

    $stmt = $conn->prepare("SELECT COUNT(*) FROM parish_staff WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    echo $count > 0 ? 'exists' : 'available';
}
