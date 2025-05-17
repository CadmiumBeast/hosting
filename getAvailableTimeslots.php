<?php
include 'db.php';
require_once 'vendor/autoload.php';  // Assuming you are using the Google Client Library

$lecturer_id = isset($_GET['lecturer_id']) ? $_GET['lecturer_id'] : null;
$date = isset($_GET['date']) ? $_GET['date'] : null;

if ($lecturer_id && $date) {
    // Get the lecturer's Google access token from the database
    $stmt = $conn->prepare("SELECT google_access_token FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $lecturer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $google_access_token = null;
    if ($row = $result->fetch_assoc()) {
        $google_access_token = $row['google_access_token'];
    }
    $stmt->close();

    if (!$google_access_token) {
        echo json_encode(['error' => 'Google access token not found.']);
        exit();
    }

    // Set up the Google Client
    $client = new Google_Client();
    $client->setAccessToken($google_access_token);

    // If the token is expired, you would need to refresh it here (handle refresh token logic)
    if ($client->isAccessTokenExpired()) {
        // Handle token refresh logic here
        echo json_encode(['error' => 'Google access token expired.']);
        exit();
    }

    // Get the Google Calendar API client
    $service = new Google_Service_Calendar($client);

    // Convert date to start and end time
    $start_date = new DateTime($date . ' 00:00:00');
    $end_date = new DateTime($date . ' 23:59:59');

    // Prepare the time range for the query
    $optParams = array(
        'timeMin' => $start_date->format(DateTime::RFC3339),
        'timeMax' => $end_date->format(DateTime::RFC3339),
        'singleEvents' => true,
        'orderBy' => 'startTime',
    );

    // Fetch events from Google Calendar
    $events = $service->events->listEvents('primary', $optParams);

    // Get all booked timeslots for the selected lecturer on the given date (database check)
    $bookedSlots = [];
    $query = "SELECT timeslot FROM appointBooking WHERE lecturer_id = ? AND date = ? AND status IN ('Approved')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $lecturer_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $bookedSlots[] = $row['timeslot'];
    }
    $stmt->close();

    // Define the available timeslots (this could be dynamic, but here's a fixed example)
    $availableSlots = [
        '09:00 AM - 10:00 AM',
        '10:00 AM - 11:00 AM',
        '11:00 AM - 12:00 PM',
        '12:00 PM - 01:00 PM',
        '01:00 PM - 02:00 PM',
        '02:00 PM - 03:00 PM',
        '03:00 PM - 04:00 PM',
        '04:00 PM - 05:00 PM'
    ];

    // Convert event times to slots (For simplicity, just compare with start time)
    $googleBookedSlots = [];
    foreach ($events->getItems() as $event) {
        $eventStart = new DateTime($event->start->dateTime);
        $eventEnd = new DateTime($event->end->dateTime);

        // Check if the event's start time falls within any available slot
        foreach ($availableSlots as $slot) {
            $slotStart = new DateTime($date . ' ' . explode(' - ', $slot)[0]);
            $slotEnd = new DateTime($date . ' ' . explode(' - ', $slot)[1]);

            if ($eventStart >= $slotStart && $eventEnd <= $slotEnd) {
                $googleBookedSlots[] = $slot;
            }
        }
    }

    // Merge database and Google Calendar booked slots
    $allBookedSlots = array_merge($bookedSlots, $googleBookedSlots);

    // Remove booked slots from available slots
    $availableSlots = array_diff($availableSlots, $allBookedSlots);

    echo json_encode(['availableSlots' => array_values($availableSlots)]);
}
?>