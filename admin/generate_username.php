<?php
include 'db_connect.php';

if (isset($_GET['fname']) && isset($_GET['role'])) {
    $fname = strtolower(trim($_GET['fname']));
    $role = strtolower(trim($_GET['role']));
    $baseUsername = $fname . '@' . $role;
    $username = $baseUsername;
    $suffix = 1;

    $stmt = $conn->prepare("SELECT COUNT(*) FROM parish_staff WHERE username = ?");
    do {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        if ($count > 0) {
            $username = $fname . $suffix . '@' . $role;
            $suffix++;
        } else {
            break;
        }
    } while (true);
    $stmt->close();

    echo $username;
}
