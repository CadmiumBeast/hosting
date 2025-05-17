<?php
session_start();
include 'db.php';




$timeslot_id = $_POST['id'];
$status = $_POST['status'];

if ($status == 1) {
    // If the status is 1, it means the timeslot is booked
    $stmt = $conn->prepare("UPDATE lecturer_timeslots SET active = 0 WHERE id = ?");
} else {
    // If the status is 0, it means the timeslot is not booked
    $stmt = $conn->prepare("UPDATE lecturer_timeslots SET active = 1 WHERE id = ?");
}

//delete the timeslot
$stmt->bind_param("i", $timeslot_id);
$stmt->execute();
$stmt->close();

$conn->close();

// Redirect to the lecturer's homepage
header("Location: LecManSlots.php");