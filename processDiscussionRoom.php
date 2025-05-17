<?php

include "db.php";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $discussion_name = $_POST["discussion_name"];
    $location = $_POST["location"];

    // Validate inputs
    if(empty($discussion_name) || empty($location) ) {
        echo "<script>alert('All fields are required.'); window.location.href='AddDiscusionRoom.php';</script>";
        exit();
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO discussionroom (discroom_name, location) VALUES ( ?, ?)");
    $stmt->bind_param("ss",$discussion_name, $location);
    

    if($stmt->execute()) {
        echo "<script>alert('Discussion room added successfully.'); window.location.href='LibDiscBook.php';</script>";
    } else {
        echo "<script>alert('Error adding discussion room.'); window.location.href='AddDiscusionRoom.php';</script>";
    }

    $stmt->close();
}
