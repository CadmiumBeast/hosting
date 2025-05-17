<?php
$servername = "localhost";
$username = "root";  
$password = "";  
$dbname = "ccassignment1"; 
$port = 3309;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>