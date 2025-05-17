<?php
session_start();
include "db.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in
    if (!isset($_SESSION["user_id"])) {
        echo "<script>alert('Please log in first.'); window.location.href='index.php';</script>";
        exit();
    }
    function dd($data) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        exit();
    }


    // Validate and sanitize input data
    $user_id = $_SESSION["user_id"];
    $discroom_id = isset($_POST["discussionRoom"]) ? intval($_POST["discussionRoom"]) : null;
    $date = isset($_POST["date"]) ? $_POST["date"] : null;
    $timeslot = isset($_POST["timeslot"]) ? $_POST["timeslot"] : null;
    $purpose = isset($_POST["purpose"]) ? $_POST["purpose"] : null;
    $numStudents = isset($_POST["numStudents"]) ? intval($_POST["numStudents"]) : 1;

    // Ensure required fields are not empty
    if ($discroom_id === null || empty($date) || empty($timeslot)) {
        echo "<script>alert('Missing required fields. Please fill out the form correctly.'); window.location.href='StudBookDisc.php';</script>";
        exit();
    }

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("INSERT INTO discRoomBooking (user_id, discroom_id, date, timeslot, purpose, numStudents) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssi", $user_id, $discroom_id, $date, $timeslot, $purpose, $numStudents);

    if ($stmt->execute()) {
        if($_SESSION['role'] == 'Student') {
            echo "<script>alert('Booking Successful!'); window.location.href='StudBookDisc.php';</script>";
        }
        else if($_SESSION['role'] == 'Lecturer') {
            echo "<script>alert('Booking Successful!'); window.location.href='LecBookDisc.php';</script>";
        }

        

    } else {
        if($_SESSION['role'] == 'Student') {
            echo "<script>alert('Booking Failed!'); window.location.href='StudBookDisc.php';</script>";
        }
        else if($_SESSION['role'] == 'Lecturer') {
            echo "<script>alert('Booking Failed!'); window.location.href='LecBookDisc.php';</script>";
        }    }

    $stmt->close();
    $conn->close();


} else {
    
    echo "<script>alert('Invalid request.'); window.location.href='StudBookDisc.php';</script>";
}
?>