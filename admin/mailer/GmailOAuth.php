<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use League\OAuth2\Client\Provider\Google;

require 'vendor/autoload.php';

// Load OAuth credentials
$client_id = '1016084889476-a5b5kgkvbcvql898aqqds3kj0v3ufrk4.apps.googleusercontent.com';
$client_secret = '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';
$redirect_uri = 'http://localhost/oauth2callback.php';
$refresh_token = 'YOUR_REFRESH_TOKEN';

$provider = new Google([
    'clientId'     => $client_id,
    'clientSecret' => $client_secret,
    'redirectUri'  => $redirect_uri
]);

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Set up SMTP with OAuth2
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Set OAuth2 credentials
    $mail->AuthType = 'XOAUTH2';
    $mail->setOAuth(
        new PHPMailer\PHPMailer\OAuth([
            'provider' => $provider,
            'clientId' => $client_id,
            'clientSecret' => $client_secret,
            'refreshToken' => $refresh_token,
            'userName' => 'your_email@gmail.com'
        ])
    );

    // Email details
    $mail->setFrom('your_email@gmail.com', 'Your Name');
    $mail->addAddress('recipient@example.com', 'Recipient Name');
    $mail->isHTML(true);
    $mail->Subject = 'OAuth2 Test Email';
    $mail->Body    = '<h1>Hello, this is a test email!</h1>';

    $mail->send();
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo "Email failed: {$mail->ErrorInfo}";
}
?>
