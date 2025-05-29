<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include 'db.php';

// Ensure that the user is a lecturer
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "Lecturer") {
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

// Get the lecturer's ID
$lecturerId = $_SESSION["user_id"];

// Fetch appointments for the lecturer (where the lecturer is assigned to the appointment)
$sql = "SELECT a.appointment_id, a.date, a.purpose, a.status, s.name AS student, 
       lt.start_time, lt.end_time
        FROM appointbooking a
        JOIN users s ON a.student_id = s.user_id
        JOIN lecturer_timeslots lt ON a.timeslot_id = lt.id
        WHERE a.lecturer_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lecturerId);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any appointments
$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

echo json_encode(['data' => $appointments]);

$stmt->close();
$conn->close();
?>
