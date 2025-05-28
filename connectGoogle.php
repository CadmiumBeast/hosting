<?php
require_once 'vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
$client->setRedirectUri('https://cliq2book/googleCallback.php');
$client->addScope(Google_Service_Calendar::CALENDAR);
$client->setAccessType('offline');
$client->setPrompt('consent');

$auth_url = $client->createAuthUrl();
header('Location: ' . $auth_url);
exit();
