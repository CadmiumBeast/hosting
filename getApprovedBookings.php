<?php
header("Content-Type: application/json");
include("db.php"); 

$query = "SELECT 
            drb.booking_id AS id, 
            u.name AS bookedBy, 
            dr.discroom_name AS discRoom, 
            drb.date, 
            drb.timeslot, 
            drb.purpose, 
            drb.numStudents 
          FROM discroombooking drb
          JOIN discussionroom dr ON drb.discroom_id = dr.discroom_id
          JOIN users u ON drb.user_id = u.user_id
          WHERE drb.status = 'Approved'
          ORDER BY drb.booking_id DESC"; 

$result = mysqli_query($conn, $query);

$bookings = [];

while ($row = mysqli_fetch_assoc($result)) {
    $bookings[] = $row;
}

// Send JSON response
echo json_encode(["data" => $bookings]);
?>
