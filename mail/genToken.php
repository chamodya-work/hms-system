<?php
// return; //Avoid Accidental Requests -- CM
require __DIR__ . '/vendor/autoload.php';

use Google\Client;


use Google\Service\Drive;
use Google\Service\Gmail;
// Define the path to your client_secret.json file
// $clientSecretPath = '/var/www/client_secret.json';
$clientSecretPath = __DIR__ . '/client_secret.json';

// Define the scopes required by your application
$scopes = [
    // Google_Service_Calendar::CALENDAR,
    // Google_Service_Calendar::CALENDAR_EVENTS,
    // Google_Service_Drive::DRIVE_FILE,
    // Google_Service_Gmail::GMAIL_SEND



    Drive::DRIVE_FILE,
    Gmail::GMAIL_SEND,
];

// Create a new Google Client instance
$client = new Client();
$client->setAccessType('offline');
$client->setPrompt('consent');
$client->setApplicationName('Students Portal');
$client->setScopes($scopes);
$client->setAuthConfig($clientSecretPath);

// print_r($client);
// return;

// Generate the authorization URL
$authUrl = $client->createAuthUrl();

// Display the authorization URL to the user and instruct them to visit it
header("Location:".$authUrl);
echo "Authorization URL: " . $authUrl;
echo "\n\n";
echo "Please visit the above URL and grant access to your Google Calendar.\n";
echo "After granting access, you will be redirected to the callback URL.";

// After the user grants access and gets redirected to the callback URL, obtain the authorization code
// $authCode = $_GET['code'] ?? null;

// if ($authCode) {
//     // Exchange the authorization code for a token
//     $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

//     // Save the token to a file
//     $tokenPath = 'token.json';
//     file_put_contents($tokenPath, json_encode($accessToken));

//     echo "\n\n";
//     echo "Token saved to: " . $tokenPath;
// }
