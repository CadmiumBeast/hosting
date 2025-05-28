<?php
$client = new Google_Client();

$client->setRedirectUri('http://localhost/CC/googleCallback.php');
$client->addScope(Google_Service_Calendar::CALENDAR);
$client->setAccessType('offline');
$client->setPrompt('consent');
?>