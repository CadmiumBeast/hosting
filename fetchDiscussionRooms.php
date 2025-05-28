<?php
include 'db.php';

if (isset($_GET['location'])) {
    $location = $_GET['location'];

    // Fetch discussion rooms based on location
    $stmt = $conn->prepare("SELECT discroom_id, discroom_name FROM discussionroom WHERE Location = ?");
    $stmt->bind_param("s", $location);
    $stmt->execute();
    $result = $stmt->get_result();

    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }

    $stmt->close();
    $conn->close();

    // Return JSON response
    echo json_encode($rooms);
}