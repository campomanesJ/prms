<?php
session_start();
include 'db_connect.php';

if (isset($_POST['btn-login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Save user details in session
            foreach ($user as $key => $value) {
                if ($key != 'password' && !is_numeric($key)) {
                    $_SESSION['login_' . $key] = $value;
                }
            }
            // Explicitly save the user ID for easy access
            $_SESSION['login_id'] = $user['id'];  // Make sure your table has an `id` column
            $_SESSION['login_username'] = $user['username'];
            $_SESSION['login_role'] = $user['role'];
            $_SESSION['login_full_name'] = $user['full_name'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header('Location: ../admin/home.php');
                exit;
            } elseif ($user['role'] == 'encoder') {
                header('Location: ../user/encoder_home.php');
                exit;
            } elseif ($user['role'] == 'front') {
                header('Location: ../frontencoder/home.php');
                exit;
            } else {
                header('Location: index.php?error=Invalid role');
                exit;
            }
        } else {
            header('Location: index.php?error=Incorrect password');
            exit;
        }
    } else {
        header('Location: index.php?error=User not found');
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>
