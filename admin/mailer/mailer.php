<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // Change this to your mail server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'jandy.arpon@evsu.edu.ph'; // Your email address
    $mail->Password   = 'Jandy@123'; // Your email password or App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = 587; 

    // Email details
    $mail->setFrom('jandy.arpon@evsu.edu.ph', 'Jandy Gwapo'); 
    $mail->addAddress('jansnooowarpon@gmail.com', 'Mwa'); 

    $mail->Subject = 'Nagkaon kana lab? Mwaaaaaaaaaaaaaaa';
    $mail->Body    = 'Hello, this is a test email sent using PHPMailer!';
    $mail->isHTML(true); 

    // Send email
    $mail->send();
    echo 'Email has been sent successfully!';
} catch (Exception $e) {
    echo "Error: {$mail->ErrorInfo}";
}
?>
