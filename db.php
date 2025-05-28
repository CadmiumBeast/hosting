<?php
$servername = "cliq2book.c2f0gy0es42a.us-east-1.rds.amazonaws.com";
$username = "admin";  
$password = "pineapple";  
$dbname = "CCassignment1"; 
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
