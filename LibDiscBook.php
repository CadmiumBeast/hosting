<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "CCassignment1", 3309);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if there are any pending bookings
$sql = "SELECT COUNT(*) as pending_count FROM discRoomBooking WHERE status = 'Pending'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$hasPending = $row['pending_count'] > 0;


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Interface</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen bg-cover bg-center flex flex-col items-center" style="background-color: #00796b;">

   
    <div class="w-full flex justify-between items-center p-4">
        <div class="flex space-x-4">
            
        </div>

        
        <div>
            <a href="logout.php" class="p-2 bg-white text-black rounded-full shadow">
                Logout
            </a>
        </div>
    </div>

    <div class="w-3/4 relative mt-6">
        <div class="relative w-full h-40 rounded-xl overflow-hidden">
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-white text-3xl font-bold">Discussion Room System</span>
            </div>
        </div>
    </div>
    
    <!-- Discussion Rooms -->
    <div class="w-3/4 mt-8 space-y-4 flex flex-col items-center">
        <a href="bookingRequests.php" class="block relative w-full h-40 rounded-xl overflow-hidden scale-105 shadow-lg 
            <?= $hasPending ? 'bg-yellow-500' : 'bg-black' ?>">
            <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
                <span class="text-white text-3xl font-bold">Pending Booking Requests</span>
            </div>
        </a>

        <a href="authorizedBookings.php" class="block relative w-2/3 h-28 rounded-xl overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <span class="text-white text-2xl font-bold">Authorized Bookings</span>
            </div>
        </a>

        <a href="AddDiscusionRoom.php" class="block relative w-2/3 h-28 rounded-xl overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <span class="text-white text-2xl font-bold">Add Discussion Room</span>
            </div>
        </a>

        <a href="LibLecBook.php" class="block relative w-2/3 h-28 rounded-xl overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <span class="text-white text-2xl font-bold">Lecturer Booking</span>
            </div>
        </a>
    </div>


</body>

</html>