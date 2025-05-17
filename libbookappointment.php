<?php

include "db.php";
include "addToGoogleCalendar.php"; // Make sure this is included only once
require_once 'vendor/autoload.php';
require_once 'mail.php';

function dd($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit();
}


// Get Form Data
$lecturer_id = $_POST['lecturer_id'];
$date = $_POST['date'];
$timeslot = $_POST['timeslot'];
$purpose = $_POST['purpose'];
$student_cid = $_POST['student_id'];
$discussion_room = $_POST['discussion_room'];


//check if student_id is valid
$student_mail = "$student_cid@students.apiit.lk";
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $student_mail);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "<script>alert('Invalid student ID.'); window.location.href='LibLecBook.php';</script>";
    exit();
}

$row = $result->fetch_assoc();
$student_id = $row['user_id'];
$student_name = $row['name'];
$stmt->close();

// Check if the appointment already exists
$stmt = $conn->prepare("SELECT * FROM appointBooking WHERE lecturer_id = ? AND date = ? AND timeslot_id = ?");
$stmt->bind_param("iss", $lecturer_id, $date, $timeslot);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "<script>alert('This appointment slot is already booked. Please choose another one.'); window.location.href='LibLecBook.php';</script>";
    exit();
}
$stmt->close();

// Insert into appointBooking table
$stmt = $conn->prepare("INSERT INTO appointBooking (student_id, lecturer_id, date, timeslot_id, purpose) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $student_id, $lecturer_id, $date, $timeslot, $purpose);
$stmt->execute();
$appointment_id = $stmt->insert_id;  // Get the last inserted appointment_id
$stmt->close();



// Mail the appointment details to the student

// Fetch lecturer details
$stmt = $conn->prepare("SELECT name, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$lecturer_mail = $row['email'];
$lecName = $row['name'];
$stmt->close();

$stmt = $conn->prepare("SELECT start_time,end_time FROM lecturer_timeslots WHERE id = ?");
$stmt->bind_param("i", $timeslot);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$start_time = $row['start_time'];
$end_time = $row['end_time'];
$stmt->close();


$startDateTime = date('c', strtotime($start_time));
$endDateTime = date('c', strtotime($end_time));


// Send email to lecturer
$eventsummary = "Appointment with $student_name";
$eventdescription = "Appointment with $student_name for $purpose";
sendMeetingEmail($lecturer_mail,$lecName,$student_name,$eventsummary  ,$eventdescription,$discussion_room,$start_time,$end_time);

// Use DateTime to safely parse start and end time
$tz = new DateTimeZone('Asia/Colombo'); // or your app's actual timezone

// ✅ Use $start_time and $end_time fetched from the DB
$startObj = DateTime::createFromFormat('Y-m-d H:i:s', "$date $start_time", $tz);
$endObj   = DateTime::createFromFormat('Y-m-d H:i:s', "$date $end_time", $tz);


// Validate parsed datetimes
if (!$startObj || !$endObj) {
    echo "<script>alert('Invalid time format.'); window.location.href='LibLecBook.php';</script>";
    exit();
}

if ($endObj <= $startObj) {
    echo "<script>alert('End time must be after start time.'); window.location.href='LibLecBook.php';</script>";
    exit();
}

// Format times in RFC3339 (required by Google Calendar)
$startDateTime = $startObj->format(DateTime::RFC3339);
$endDateTime   = $endObj->format(DateTime::RFC3339);

// Create event summary and description
$eventsummary = "Appointment with $student_name";
$eventdescription = "Appointment with $student_name for $purpose";

// Add event to Lecturer's Google Calendar
addEventToGoogleCalendar(
    $lecturer_id,
    $eventsummary,
    $eventdescription,
    $startDateTime,
    $endDateTime
);

// Create student-specific summary/description
$eventsummary_student = "Appointment with $lecName";
$eventdescription_student = "Appointment with $lecName regarding $purpose";

// Add event to Student's Google Calendar
addEventToGoogleCalendar(
    $student_id,
    $eventsummary_student,
    $eventdescription_student,
    $startDateTime,
    $endDateTime
);

 //return to the booking page
echo "<script>alert('Appointment booked successfully.'); window.location.href='LibLecBook.php';</script>";
$stmt->close();
$conn->close();
?>