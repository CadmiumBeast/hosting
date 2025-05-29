<?php
header("Content-Type: application/json");
include("db.php"); 
include("mail.php"); // Include the email sending function
include("addToGoogleCalendar.php"); // Include the calendar integration


$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['booking_id']) || !isset($data['status'])) {
    echo json_encode(["success" => false, "error" => "Missing parameters"]);
    exit;
}

$booking_id = intval($data['booking_id']);
$status = in_array($data['status'], ["Approved", "Rejected"]) ? $data['status'] : "Pending";

    // Fetch the booking details
    $stmt = $conn->prepare("SELECT * FROM discroombooking WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();
    // Feth the user details
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $booking['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    $timeslot = $booking['timeslot'];
    $date = $booking['date'];

    list($start_time, $end_time) = explode(' - ', $timeslot);

    // Format the start_time and end_time with the date
    $start_time = "$date $start_time:00";
    $end_time = "$date $end_time:00";
    $eventsummary = "Discussion Room Booking $status";
    $eventdescription = "Discussion Room Booking for ".$user['name'];
    $discussion_room = $booking['discroom_id'];
    // Fetch the discussion room details
    $stmt = $conn->prepare("SELECT * FROM discussionroom WHERE discroom_id = ?");
    $stmt->bind_param("i", $discussion_room);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
    $stmt->close();
    $discussion_room = $room['discroom_name'];
    $student_mail = $user['email'];
    $student_name = $user['name'];
    
    // Send email to student
    sendMeetingEmail($student_mail,$student_name,"",$eventsummary  ,$eventdescription,$discussion_room,$start_time,$end_time);
        
    if  ($status == "Approved"){
    // Format start and end time to RFC3339 format (required by Google Calendar)
        $startDateTime = date('c', strtotime($start_time)); // e.g., 2025-05-15T10:00:00+05:30
        $endDateTime = date('c', strtotime($end_time));

        addEventToGoogleCalendar(
            $booking['user_id'],           // User ID to fetch tokens
            $eventsummary,                 // Event summary
            $eventdescription . " in " . $discussion_room, // Full description
            $startDateTime,
            $endDateTime
    );

}


$query = "UPDATE discroombooking SET status = ? WHERE booking_id = ?";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        echo json_encode(["success" => true]);
        //get back to approval page


    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["success" => false, "error" => "Failed to prepare statement"]);
}


?>
