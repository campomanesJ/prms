<?php
require __DIR__ . '/../../vendor/autoload.php';

use League\OAuth2\Client\Provider\Google;

// Replace with your actual Client ID and Secret from Google Cloud Console
$client_id = '1016084889476-a5b5kgkvbcvql898aqqds3kj0v3ufrk4.apps.googleusercontent.com';
$client_secret = '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';
$redirect_uri = 'http://127.0.0.1/admin/mailer/oauth2callback.php'; // Try 127.0.0.1 instead of localhost

// Create Google OAuth2 provider
$provider = new Google([
    'clientId'     => $client_id,
    'clientSecret' => $client_secret,
    'redirectUri'  => $redirect_uri,
    'accessType'   => 'offline',
    'scopes'       => ['openid', 'profile', 'email'],
]);

// Step 1: Redirect user to Google's OAuth Consent Screen
if (!isset($_GET['code'])) {
    // Ensure scopes and access_type are correctly set
    $authUrl = $provider->getAuthorizationUrl([
        'scope' => ['openid', 'https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'],
        'access_type' => 'offline', // Needed to get a refresh token
        'prompt' => 'consent' // Forces consent screen to show every time
    ]);

    // Store state token to prevent CSRF attacks
    $_SESSION['oauth2state'] = $provider->getState();

    header('Location: ' . $authUrl);
    exit;
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid OAuth state. Please try again.');
} else {
    try {
        // Step 2: Get access and refresh tokens
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // Save tokens securely (in a database or .env file)
        file_put_contents(__DIR__ . '/tokens.json', json_encode([
            'access_token' => $token->getToken(),
            'refresh_token' => $token->getRefreshToken(),
            'expires_in' => $token->getExpires(),
        ]));

        echo "Authentication successful! You can now use the access token.";
    } catch (Exception $e) {
        exit('Error fetching OAuth tokens: ' . $e->getMessage());
    }
}
?>
