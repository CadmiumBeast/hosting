<?php
session_start();
include 'db.php';

function dd($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit();
}


// Check if the user is logged in and is a lecturer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Lecturer') {
    echo "<script>alert('Access denied.'); window.location.href='index.php';</script>";
    exit();
}


$lecturer_id = $_SESSION['user_id']; // Get the logged-in lecturer's ID
$dayofweek = $_POST['day'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];

// Validate inputs
if (empty($dayofweek) || empty($start_time) || empty($end_time)) {
    echo "<script>alert('All fields are required.'); window.location.href='add_timeslot.php';</script>";
    exit();
}

$start_time = date('H:i:s', strtotime($start_time));  // example start time
$end_time = date('H:i:s', strtotime($end_time));    // example end time


// Function to generate time slots in 30-minute intervals
function generateTimeSlots($start_time, $end_time) {
    $time_slots = [];
    $current_time = strtotime($start_time);

    // Loop until the current time exceeds the end time
    while ($current_time < strtotime($end_time)) {
        $slot_start = date('H:i:s', $current_time);
        $current_time = strtotime("+30 minutes", $current_time);
        $slot_end = date('H:i:s', $current_time);

        // Add the time slot to the array
        $time_slots[] = [
            'start_time' => $slot_start,
            'end_time' => $slot_end
        ];
    }

    return $time_slots;
}

// Generate time slots for the lecturer
$lecturer_time_slots = generateTimeSlots($start_time, $end_time);

//Instert the time slots into the database
foreach ($lecturer_time_slots as $slot) {
    $stmt = $conn->prepare("INSERT INTO lecturer_timeslots (lecturer_id, dayofweek, start_time, end_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $lecturer_id, $dayofweek, $slot['start_time'], $slot['end_time']);

    if (!$stmt->execute()) {
        echo "<script>alert('Error adding time slot.'); window.location.href='add_timeslot.php';</script>";
        exit();
    }
}

$stmt->close();
$conn->close();

header("Location: LecManSlots.php");
?>