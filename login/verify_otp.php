<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    $user_otp = trim($_POST['otp']);

    if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_email']) || !isset($_SESSION['otp_time'])) {
        echo json_encode(["status" => "error", "message" => "No OTP found."]);
        exit();
    }

    $otp_lifetime = 300;
    if (time() - $_SESSION['otp_time'] > $otp_lifetime) {
        unset($_SESSION['otp'], $_SESSION['otp_time'], $_SESSION['otp_email']);
        echo json_encode(["status" => "expired", "message" => "OTP expired. Request a new one."]);
        exit();
    }

    if ($_SESSION['otp'] === intval($user_otp)) {
        $_SESSION['otp_verified'] = true;
        $_SESSION['reset_email'] = $_SESSION['otp_email'];
        echo json_encode(["status" => "success", "message" => "OTP verified successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Incorrect OTP. Try again."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>