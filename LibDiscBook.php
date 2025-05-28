<?php
session_start();
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
$noofpending = $row['pending_count'];



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

<body class="h-screen bg-cover bg-center flex flex-col items-center" style="background-color:rgba(0, 121, 107, 0.41);">

   
    <div class="w-full flex justify-between items-center p-4">
        <!-- Logo on the left -->
        <div>
            <a href="LibDiscBook.php" class="flex items-center space-x-2">
               <img src="CCimages/Click2BookLogo.png" alt="App Logo" class="h-16 w-auto max-w-[150px] sm:max-w-[200px]">                
            </a>
        </div>
        <!-- Notification and Logout on the right -->
        <div class="flex space-x-4">
            <a href="logout.php" class="p-2 bg-white text-black rounded-full shadow">
                Logout
            </a>
        </div>
    </div>

    <div class="w-3/4 relative mt-6">
        <div class="relative w-full h-40 rounded-xl overflow-hidden">
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-[#00796b] text-5xl font-bold">Welcome, <?php echo($_SESSION["name"])  ?></span>
            </div>
        </div>
    </div>
    
    <div class="w-11/12 md:w-3/4 mt-8 flex flex-col md:flex-row justify-between gap-6">
        <!-- Column 1: Pending Booking Requests -->
        <a href="bookingRequests.php" class="w-full md:flex-1 block relative h-48 md:h-72 rounded-xl overflow-hidden shadow-lg <?= $hasPending ? 'bg-yellow-500' : 'bg-black' ?>">
            <!-- Bubble for pending count -->
            <span class="absolute top-2 right-2 bg-red-600 text-white text-xs sm:text-s font-bold px-3 py-1 rounded-full z-10">
                <?= $noofpending ?>
            </span>
            <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
                <span class="text-white text-lg md:text-2xl font-bold text-center">Pending Booking Requests</span>
            </div>
        </a>

        <!-- Column 2: Authorized Bookings -->
        <a href="authorizedBookings.php" class="w-full md:flex-1 block relative h-48 md:h-72 rounded-xl overflow-hidden shadow-md">
            <div class="absolute inset-0 bg-[#00796b] flex items-center justify-center">
                <span class="text-black text-lg md:text-xl font-bold text-center">Authorized Bookings</span>
            </div>
        </a>

        <!-- Column 3: Add Discussion Room -->
        <a href="AddDiscusionRoom.php" class="w-full md:flex-1 block relative h-48 md:h-72 rounded-xl overflow-hidden shadow-md">
            <div class="absolute inset-0 bg-[#00796b] flex items-center justify-center">
                <span class="text-black text-lg md:text-xl font-bold text-center">Add Discussion Room</span>
            </div>
        </a>

        <!-- Column 4: Lecturer Booking -->
        <a href="LibLecBook.php" class="w-full md:flex-1 block relative h-48 md:h-72 rounded-xl overflow-hidden shadow-md">
            <div class="absolute inset-0 bg-[#00796b] flex items-center justify-center">
                <span class="text-black text-lg md:text-xl font-bold text-center">Lecturer Booking</span>
            </div>
        </a>

        <!-- Column 5: Summary -->
        <a href="LibSummary.php" class="w-full md:flex-1 block relative h-48 md:h-72 rounded-xl overflow-hidden shadow-md">
            <div class="absolute inset-0 bg-[#00796b] flex items-center justify-center">
                <span class="text-black text-lg md:text-xl font-bold text-center">Summary</span>
            </div>
        </a>
    </div>



</body>

</html>