<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/discovery/v1/apis");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
} else {
    echo $response;
}

curl_close($ch);
