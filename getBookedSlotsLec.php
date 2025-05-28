<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

$bookedSlots = [];

function dd($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit();
}

// Get the date and lecturer_id from the GET request
$date = $_GET['date'];
$lecturer_id = $_GET['lecturer_id'];

// get the day of the week
$dayOfWeek = date('l', strtotime($date));

// Validate input (optional, for security)
if (empty($date) || empty($lecturer_id)) {
    echo json_encode([]);
    exit();
}

// Query to get the booked slots for the lecturer on the specified date
$query = "
    SELECT * FROM lecturer_timeslots
    WHERE lecturer_id = ? AND dayofweek = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("is", $lecturer_id, $dayOfWeek);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $start_time = $row['start_time'];
    $end_time = $row['end_time'];
    $id = $row['id'];

    // check if the slot is booked

        
         $slot = date('H:i', strtotime($start_time)) . ' - ' . date('H:i', strtotime($end_time));
        

        //add the slot id 
        $slot = [
            'slot' => $slot,
            'id' => $id
        ];
    
        $bookedSlots[] = $slot;
    


   


}

$stmt->close();
echo json_encode($bookedSlots);



// if (isset($_GET['date']) && isset($_GET['lecturer_id'])) {
//     $date = $_GET['date'];
//     $lecturerId = $_GET['lecturer_id'];

//     // Step 1: Get DB Booked Slots
//     $query = "SELECT timeslot FROM appointbooking WHERE date = ? AND status IN ('Pending', 'Approved') AND lecturer_id = ?";
//     $stmt = $conn->prepare($query);
//     $stmt->bind_param("si", $date, $lecturerId);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     while ($row = $result->fetch_assoc()) {
//         $bookedSlots[] = trim($row['timeslot']);
//     }
//     $stmt->close();

//     // Step 2: Get lecturer's Google Calendar token
//     $stmt = $conn->prepare("SELECT google_access_token FROM users WHERE user_id = ?");
//     $stmt->bind_param("i", $lecturerId);
//     $stmt->execute();
//     $stmt->bind_result($accessToken);
//     $stmt->fetch();
//     $stmt->close();

//     if ($accessToken) {
//         $client = new Google_Client();
//         $client->setAccessToken(json_decode($accessToken, true));

//         if ($client->isAccessTokenExpired()) {
//             // You can refresh the token here if refresh_token is stored
//             echo json_encode($bookedSlots);
//             exit();
//         }

//         $calendarService = new Google_Service_Calendar($client);

//         $timeMin = $date . 'T00:00:00+05:30'; // Adjust timezone as needed
//         $timeMax = $date . 'T23:59:59+05:30';

//         $events = $calendarService->events->listEvents('primary', [
//             'timeMin' => $timeMin,
//             'timeMax' => $timeMax,
//             'singleEvents' => true,
//             'orderBy' => 'startTime'
//         ]);

//         $calendarEvents = $events->getItems();

//         $slotRanges = [
//             "08:30 - 09:30" => ["08:30", "09:30"],
//             "09:30 - 10:30" => ["09:30", "10:30"],
//             "10:30 - 11:30" => ["10:30", "11:30"],
//             "11:30 - 12:30" => ["11:30", "12:30"],
//             "12:30 - 13:30" => ["12:30", "13:30"],
//             "13:30 - 14:30" => ["13:30", "14:30"],
//             "14:30 - 15:30" => ["14:30", "15:30"],
//             "15:30 - 16:30" => ["15:30", "16:30"],
//         ];

//         foreach ($calendarEvents as $event) {
//             $eventStart = strtotime($event->start->dateTime);
//             $eventEnd = strtotime($event->end->dateTime);

//             foreach ($slotRanges as $slot => [$start, $end]) {
//                 $slotStart = strtotime("$date $start");
//                 $slotEnd = strtotime("$date $end");

//                 if (
//                     ($slotStart >= $eventStart && $slotStart < $eventEnd) ||
//                     ($slotEnd > $eventStart && $slotEnd <= $eventEnd) ||
//                     ($slotStart <= $eventStart && $slotEnd >= $eventEnd)
//                 ) {
//                     $bookedSlots[] = $slot;
//                 }
//             }
//         }
//     }
// }

// $conn->close();

// header('Content-Type: application/json');
// echo json_encode(array_unique($bookedSlots));
