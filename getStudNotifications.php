<?php
session_start();
include("db.php");

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$notifications = [];

/** 🔔 Discussion Room Notifications */
$query1 = "SELECT purpose, date, timeslot, status 
           FROM discroombooking 
           WHERE user_id = ? 
           ORDER BY booking_id DESC";

$stmt1 = mysqli_prepare($conn, $query1);
mysqli_stmt_bind_param($stmt1, "i", $user_id);
mysqli_stmt_execute($stmt1);
$result1 = mysqli_stmt_get_result($stmt1);

while ($row = mysqli_fetch_assoc($result1)) {
    if ($row['status'] === 'Approved') {
        $message = "✅ Your discussion room booking for '{$row['purpose']}' on {$row['date']} at {$row['timeslot']} has been approved!";
    } elseif ($row['status'] === 'Cancelled') {
        $message = "⚠️ Your discussion room booking for '{$row['purpose']}' on {$row['date']} at {$row['timeslot']} was cancelled. Please try booking another appointment for a different date or time.";
    } else {
        continue;
    }
    $notifications[] = $message;
}

/** 🔔 Lecturer Appointment Notifications */
$query2 = "SELECT ab.purpose, ab.date, ab.timeslot_id, ab.status, u.name AS lecturer_name, 
                  lt.start_time, lt.end_time
           FROM appointbooking ab
           JOIN users u ON ab.lecturer_id = u.user_id
           JOIN lecturer_timeslots lt ON ab.timeslot_id = lt.id
           WHERE ab.student_id = ? 
           ORDER BY ab.appointment_id DESC";

$stmt2 = mysqli_prepare($conn, $query2);
mysqli_stmt_bind_param($stmt2, "i", $user_id);
mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);

while ($row = mysqli_fetch_assoc($result2)) {
    if ($row['status'] === 'Approved') {
        $message = "📘 Your appointment with Lecturer {$row['lecturer_name']} for '{$row['purpose']}' on {$row['date']} at {$row['start_time']} - {$row['end_time']} has been approved!";
    } elseif ($row['status'] === 'Cancelled') {
        $message = "⚠️ Your appointment with Lecturer {$row['lecturer_name']} for '{$row['purpose']}' on {$row['date']} at {$row['start_time']} - {$row['end_time']} was cancelled. Please try booking another appointment for a different date or time.";
    } else {
        continue;
    }
    $notifications[] = $message;
}

// Respond with all notifications
echo json_encode(["notifications" => $notifications]);
?>
