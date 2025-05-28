<?php
$client = new Google_Client();

$client->setRedirectUri('https://cliq2book.me/googleCallback.php');
$client->setClientId('657887578144-18jlcl7uf4bsliqmu2m7aaltd6st5bmj.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->addScope(Google_Service_Calendar::CALENDAR);
$client->setAccessType('offline');
$client->setPrompt('consent');
?>
