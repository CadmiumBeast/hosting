<?php
require_once 'vendor/autoload.php';
session_start();
include "db.php"; // Connect to your database

$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
$client->setRedirectUri('https://cliq2book.me/googleCallback.php');
$client->addScope(Google_Service_Calendar::CALENDAR);
$client->setClientId('657887578144-18jlcl7uf4bsliqmu2m7aaltd6st5bmj.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setAccessType('offline');
$client->setPrompt('consent'); // to ensure refresh_token is returned

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        // Check for errors
        if (isset($token['error'])) {
            throw new Exception(join(", ", $token));
        }

        // Save token to session
        $_SESSION['google_access_token'] = $token;

        if (isset($_SESSION['user_id'])) {
            $access_token_json = json_encode($token);
            $refresh_token = isset($token['refresh_token']) ? $token['refresh_token'] : null;

            // Save tokens to DB
            $stmt = $conn->prepare("UPDATE users SET google_access_token = ?, google_refresh_token = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $access_token_json, $refresh_token, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();

            // Fetch user role
            $query = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
            $query->bind_param("i", $_SESSION['user_id']);
            $query->execute();
            $result = $query->get_result();
            $user = $result->fetch_assoc();
            $query->close();

            // Determine redirect based on role
            $role = strtolower($user['role']);
            if ($role === 'student') {
                $redirectPage = 'StudHomepage.php';
            } elseif ($role === 'lecturer') {
                $redirectPage = 'LecHomepage.php';
            } else {
                $redirectPage = 'index.php'; // fallback or for librarian/admin
            }

            echo "<script>alert('Google Calendar connected successfully!'); window.location.href = '$redirectPage';</script>";
            exit();
        }

    } catch (Exception $e) {
        echo "Error fetching token: " . $e->getMessage();
    }
} else {
    echo "Authorization failed. No code returned.";
}
?>
