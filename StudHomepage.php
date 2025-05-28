<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "CCassignment1", 3309);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if Google is connected
$googleConnected = false;
if (isset($_SESSION["user_id"])) {
    $stmt = $conn->prepare("SELECT google_access_token FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $stmt->bind_result($accessToken);
    $stmt->fetch();
    $stmt->close();
    $googleConnected = !empty($accessToken);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Homepage</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen bg-cover bg-center flex flex-col items-center" style="background-color:rgba(0, 121, 107, 0.41);">

    <div class="w-full flex justify-between items-center p-4">
        <!-- Logo on the left -->
        <div>
            <a href="StudHomepage.php" class="flex items-center space-x-2">
                <img src="CCimages/Click2BookLogo.png" alt="App Logo" class="h-25 w-1/4"> <!-- Change the src to your logo file -->
                
            </a>
        </div>
        <!-- Notification and Logout on the right -->
        <div class="flex space-x-4">
            <button onclick="window.location.href='studNotifications.php'" class="p-2 bg-white rounded-full shadow">
                <img src="CCimages/BellLogo.svg" alt="Notifications" class="h-[20px] w-[20px] min-w-[20px] min-h-[20px]">
            </button>
            <a href="logout.php" class="p-2 bg-white text-black rounded-full shadow">
                Logout
            </a>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="w-3/4 relative mt-6">
        <div class="relative w-full h-40 rounded-xl overflow-hidden">
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-[#00796b] text-5xl font-bold">Welcome, <?php echo($_SESSION["name"])  ?></span>
            </div>
        </div>
    </div>

    <!-- Student Options -->
    <div class="w-3/4 mt-8 flex flex-col items-center space-y-8">

        <!-- Options Row -->
        <div class="w-full flex flex-col md:flex-row justify-center items-center space-y-6 md:space-y-0 md:space-x-8">
            <!-- Book A Discussion Room -->
            <a href="StudBookDisc.php" class="block relative w-full md:w-1/4 h-72 rounded-xl overflow-hidden shadow-lg bg-[#00796b] hover:scale-105 transition">
                <!-- <img src="CCimages/discussion_room.jpg" alt="Discussion Room" class="absolute inset-0 w-full h-full object-cover opacity-70"> -->
                <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center">
                    <span class="text-white text-xl font-bold text-center">Book A<br>Discussion Room</span>
                </div>
            </a>

            <!-- Book A Lecturer Appointment -->
            <a href="lecturerBooking.php" class="block relative w-full md:w-1/4 h-72 rounded-xl overflow-hidden shadow-lg bg-[#00796b] hover:scale-105 transition">
                <!-- <img src="CCimages/lecturer_appointment.jpg" alt="Lecturer Appointment" class="absolute inset-0 w-full h-full object-cover opacity-70"> -->
                <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center">
                    <span class="text-white text-xl font-bold text-center">Book A<br>Lecturer Appointment</span>
                </div>
            </a>

            <!-- Appointment Notes -->
            <a href="studentappointmentnotes.php" class="block relative w-full md:w-1/4 h-72 rounded-xl overflow-hidden shadow-lg bg-[#00796b] hover:scale-105 transition">
                <!-- <img src="CCimages/appointment_notes.jpg" alt="Appointment" class="absolute inset-0 w-full h-full object-cover opacity-70"> -->
                <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center">
                    <span class="text-white text-xl font-bold text-center">Appointment</span>
                </div>
            </a>
        </div>

        <!-- Connect Google Calendar Button/Status -->
        <div class="w-full flex justify-center">
            <?php if (!$googleConnected): ?>
                <a href="https://accounts.google.com/o/oauth2/auth?client_id=657887578144-18jlcl7uf4bsliqmu2m7aaltd6st5bmj.apps.googleusercontent.com&redirect_uri=http://localhost/CC/googleCallback.php&scope=https://www.googleapis.com/auth/calendar&response_type=code&access_type=offline&prompt=consent"
                    class="bg-yellow-500 text-white font-bold px-6 py-3 rounded-lg shadow-lg hover:bg-yellow-600">
                    Connect Google Calendar
                </a>
            <?php else: ?>
                <div class="text-white bg-green-700 px-6 py-3 rounded-lg shadow-lg">
                    Google Calendar Connected ✔️
                </div>
            <?php endif; ?>
        </div>

    </div>


</body>

</html>