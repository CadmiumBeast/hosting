<?php
session_start();
header('Content-Type: application/json');
include 'db.php';
require_once 'vendor/autoload.php'; // Path to Google API autoload

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "Lecturer") {
        echo json_encode(["success" => false, "error" => "Unauthorized"]);
        exit();
    }

    $appointment_id = $data['appointment_id'] ?? null;
    $lecturer_id = $_SESSION["user_id"];

    if (!$appointment_id) {
        echo json_encode(["success" => false, "error" => "Appointment ID is required."]);
        exit();
    }

    // Fetch appointment info
    $stmt = $conn->prepare("SELECT date, google_event_id FROM appointbooking WHERE appointment_id = ? AND lecturer_id = ?");
    $stmt->bind_param("ii", $appointment_id, $lecturer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || $result->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "Appointment not found."]);
        exit();
    }

    $row = $result->fetch_assoc();
    $appointmentDate = new DateTime($row["date"]);
    $today = new DateTime();
    $diff = $today->diff($appointmentDate)->days;
    $eventId = $row["google_event_id"];

    // Check if the appointment can be canceled (should be in the future)
    if (!($appointmentDate > $today && $diff > 0)) {
        echo json_encode(["success" => false, "error" => "Appointment can't be cancelled. Try rescheduling via email."]);
        exit();
    }

    // Log the event ID and appointment date for debugging
    error_log("Attempting to cancel appointment. Event ID: $eventId, Appointment Date: " . $appointmentDate->format('Y-m-d'));

    // Fetch access token
    $tokenQuery = $conn->prepare("SELECT google_access_token, google_refresh_token FROM users WHERE user_id = ?");
    $tokenQuery->bind_param("i", $lecturer_id);
    $tokenQuery->execute();
    $tokenResult = $tokenQuery->get_result();
    $tokens = $tokenResult->fetch_assoc();

    if (!$tokens) {
        echo json_encode(["success" => false, "error" => "No tokens found for the lecturer."]);
        exit();
    }

    $accessToken = $tokens['google_access_token'];
    $refreshToken = $tokens['google_refresh_token'];

    // Check if access token is expired
    $client = new Google_Client();
    $client->setAccessToken($accessToken);

    if ($client->isAccessTokenExpired()) {
        if ($refreshToken) {
            // Attempt to refresh the access token
            $client->fetchAccessTokenWithRefreshToken($refreshToken);
            $newAccessToken = $client->getAccessToken();

            // Update the database with the new access token
            $updateStmt = $conn->prepare("UPDATE users SET access_token = ? WHERE user_id = ?");
            $updateStmt->bind_param("si", json_encode($newAccessToken), $lecturer_id);
            $updateStmt->execute();

            // Use the new access token
            $accessToken = json_encode($newAccessToken);
        } else {
            echo json_encode(["success" => false, "error" => "Google access token expired. Please reconnect your calendar."]);
            exit();
        }
    }

    // Create Google client and delete the event from Google Calendar
    $client->setAccessToken($accessToken);
    $service = new Google_Service_Calendar($client);

    try {
        // Try deleting the event from Google Calendar
        $service->events->delete('primary', $eventId);
        error_log("Successfully deleted event from Google Calendar: $eventId");
    } catch (Exception $e) {
        error_log("Google Calendar delete error: " . $e->getMessage());
        echo json_encode(["success" => false, "error" => "Google Calendar delete error: " . $e->getMessage()]);
        exit();
    }

    // Cancel the appointment in the database
    $cancel_stmt = $conn->prepare("UPDATE appointbooking SET status = 'Cancelled' WHERE appointment_id = ?");
    $cancel_stmt->bind_param("i", $appointment_id);
    $cancel_stmt->execute();

    // Check if the database update was successful
    if ($cancel_stmt->affected_rows > 0) {
        echo json_encode(["success" => true]);
    } else {
        error_log("Failed to update the appointment status in the database. Appointment ID: $appointment_id");
        echo json_encode(["success" => false, "error" => "Failed to cancel the appointment in the database."]);
    }
}
?>