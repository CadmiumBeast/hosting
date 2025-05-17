<?php
require_once 'vendor/autoload.php';
include "db.php";
session_start();

function addEventToGoogleCalendar($user_id, $summary, $description, $startDateTime, $endDateTime) {
    global $conn;

    // Get user's stored tokens from DB
    $stmt = $conn->prepare("SELECT google_access_token, google_refresh_token FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($access_token_json, $refresh_token);
    $stmt->fetch();
    $stmt->close();

    $client = new Google_Client();
    $client->setAuthConfig('client_secret.json');
    $client->addScope(Google_Service_Calendar::CALENDAR);
    $client->setAccessType('offline');

    $token = json_decode($access_token_json, true);

    // Set current token
    $client->setAccessToken($token);

    // Refresh if expired
    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($refresh_token);
        $newToken = $client->getAccessToken();

        // Save refreshed token
        $stmt = $conn->prepare("UPDATE users SET google_access_token = ? WHERE user_id = ?");
        $stmt->bind_param("si", json_encode($newToken), $user_id);
        $stmt->execute();
        $stmt->close();
    }

    $service = new Google_Service_Calendar($client);

    $event = new Google_Service_Calendar_Event([
        'summary' => $summary,
        'description' => $description,
        'start' => [
            'dateTime' => $startDateTime,
            'timeZone' => 'Asia/Kolkata',
        ],
        'end' => [
            'dateTime' => $endDateTime,
            'timeZone' => 'Asia/Kolkata',
        ],
    ]);

    // Insert event to primary calendar
    $service->events->insert('primary', $event);
}
