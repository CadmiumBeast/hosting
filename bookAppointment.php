<?php
session_start();
require_once 'vendor/autoload.php';
include 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in first.'); window.location.href='index.php';</script>";
    exit();
}

function dd($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit();
}


// Get form data
$student_id = $_SESSION['user_id'];
$lecturer_id = $_POST['lecturer_id'];
$date = $_POST['date'];
$timeslot = $_POST['timeslot'];
$purpose = $_POST['purpose'];

// Insert into appointBooking table (without google_event_id initially)
$stmt = $conn->prepare("INSERT INTO appointbooking (student_id, lecturer_id, date, timeslot_id, purpose) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $student_id, $lecturer_id, $date, $timeslot, $purpose);
$stmt->execute();
$appointment_id = $stmt->insert_id;  // Get the last inserted appointment_id
$stmt->close();



// Fetch student & lecturer tokens
$stmt = $conn->prepare("SELECT user_id, name, google_access_token, google_refresh_token FROM users WHERE user_id IN (?, ?)");
$stmt->bind_param("ii", $student_id, $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[$row['user_id']] = $row;
}
$stmt->close();

// Setup Google Client
function getGoogleClient($accessTokenJson, $refreshToken)
{
    $client = new Google_Client();
    $client->setAuthConfig('client_secret.json');
    $client->addScope(Google_Service_Calendar::CALENDAR);
    $client->setAccessType('offline');

    $accessToken = json_decode($accessTokenJson, true);
    $client->setAccessToken($accessToken);

    if ($client->isAccessTokenExpired() && $refreshToken) {
        $client->fetchAccessTokenWithRefreshToken($refreshToken);
    }

    return $client;
}

// Function to add event
function addEventToCalendar($client, $title, $desc, $startDateTime, $endDateTime)
{
    $calendarService = new Google_Service_Calendar($client);

    $event = new Google_Service_Calendar_Event([
        'summary' => $title,
        'description' => $desc,
        'start' => ['dateTime' => $startDateTime, 'timeZone' => 'Asia/Kolkata'],
        'end' => ['dateTime' => $endDateTime, 'timeZone' => 'Asia/Kolkata']
    ]);

    try {
        $createdEvent = $calendarService->events->insert('primary', $event);
        return $createdEvent->getId();  // Get the event ID
    } catch (Exception $e) {
        error_log("Calendar Error: " . $e->getMessage());
        return null;
    }
}

// Helper to get timeslot start/end as ISO datetime
function parseTimeslot($date, $timeslot_id, $conn)
{
    $stmt = $conn->prepare("SELECT start_time, end_time FROM lecturer_timeslots WHERE id = ?");
    $stmt->bind_param("i", $timeslot_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {
        throw new Exception("Invalid timeslot ID");
    }

    $startDateTime = date('Y-m-d\TH:i:s', strtotime("$date {$row['start_time']}"));
    $endDateTime   = date('Y-m-d\TH:i:s', strtotime("$date {$row['end_time']}"));

    return [$startDateTime, $endDateTime];
}

// Add to student calendar and save event ID
if (!empty($users[$student_id]['google_access_token'])) {
    $client = getGoogleClient($users[$student_id]['google_access_token'], $users[$student_id]['google_refresh_token']);
    [$start, $end] = parseTimeslot($date, $timeslot, $conn);
    $lecName = $users[$lecturer_id]['name'] ?? "Lecturer";
    $studentEventId = addEventToCalendar($client, "Lecturer Appointment with $lecName", $purpose, $start, $end);

    // If the event was created, update the appointment with the event ID
    if ($studentEventId) {
        $stmt = $conn->prepare("UPDATE appointbooking SET google_event_id = ? WHERE appointment_id = ?");
        $stmt->bind_param("si", $studentEventId, $appointment_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Add to lecturer calendar and save event ID
if (!empty($users[$lecturer_id]['google_access_token'])) {
    $client = getGoogleClient($users[$lecturer_id]['google_access_token'], $users[$lecturer_id]['google_refresh_token']);
    [$start, $end] = parseTimeslot($date, $timeslot, $conn);
    $studName = $users[$student_id]['name'] ?? "Student";
    $lecturerEventId = addEventToCalendar($client, "Appointment with $studName", $purpose, $start, $end);

    // If the event was created, update the appointment with the event ID
    if ($lecturerEventId) {
        $stmt = $conn->prepare("UPDATE appointbooking SET google_event_id = ? WHERE appointment_id = ?");
        $stmt->bind_param("si", $lecturerEventId, $appointment_id);
        $stmt->execute();
        $stmt->close();
    }
}

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
$eventsummary = "Appointment with $users[$student_id]['name']";
$eventdescription = "Appointment with $users[$student_id]['name'] for $purpose";
sendMeetingEmail($lecturer_mail,$lecName,$users[$student_id]['name'],$eventsummary  ,$eventdescription,"",$start_time,$end_time);


echo "<script>alert('Appointment booked successfully!'); window.location.href = 'StudHomepage.php';</script>";
?>
