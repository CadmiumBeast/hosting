<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

$bookedSlots = [];

if (isset($_GET['date'])) {
    $date = $_GET['date'];


    $query = "SELECT timeslot FROM discroombooking WHERE date = ? AND status IN ('Pending', 'Approved')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $bookedSlots[] = trim($row['timeslot']);
    }

    $stmt->close();
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($bookedSlots);
?>