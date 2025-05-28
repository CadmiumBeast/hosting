<?php
require 'vendor/autoload.php';

$client = new \GuzzleHttp\Client();
$response = $client->request('GET', 'https://www.googleapis.com/discovery/v1/apis');
echo $response->getBody();
