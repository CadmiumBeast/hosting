<?php
require_once 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
$client->setRedirectUri('http://localhost/your-folder/google_callback.php');
$client->addScope(Google_Service_Calendar::CALENDAR);
$client->setAccessType('offline'); // to get refresh token

$auth_url = $client->createAuthUrl();
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
exit();
