<?php

require_once 'db.php';
require_once 'vendor/autoload.php'; // Path to Google API autoload

session_start();

function dd($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$appointment_id = isset($data['appointment_id']) ? intval($data['appointment_id']) : null;
$note = isset($data['note']) ? trim($data['note']) : null;



if (!$appointment_id || !$note) {
    echo json_encode(["success" => false, "error" => "Appointment ID and note are required."]);
    exit();
}

// add the note to the table
$stmt = $conn->prepare("INSERT INTO appointmentnote (appointment_id , note ) VALUES (?, ?)");
$stmt->bind_param("is", $appointment_id, $note);
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Note added successfully."]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to add note."]);
}
