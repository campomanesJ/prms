<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $user_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email address."]);
        exit;
    }

    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp; 
    $_SESSION['otp_email'] = $user_email; 
    $_SESSION['otp_time'] = time(); 

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'jandy.arpon@evsu.edu.ph';
        $mail->Password   = 'Jandy@123';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom('jandy.arpon@evsu.edu.ph', 'Jandy Gwapo');
        $mail->addAddress($user_email);

        $mail->Subject = 'Your OTP for Password Reset';
        $mail->Body    = "Your OTP is: <b>$otp</b>. It expires in 5 minutes.";
        $mail->isHTML(true);

        // Send email
        if ($mail->send()) {
            echo json_encode(["status" => "success", "message" => "OTP sent successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to send OTP."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Mail error: " . $mail->ErrorInfo]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
