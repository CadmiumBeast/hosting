<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "CCassignment1", 3309);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user's token status
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

function dd($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Homepage</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen bg-cover bg-center flex flex-col items-center" style="background-color: #00796b;">

    <!-- Header with Notification and Logout -->
    <div class="w-full flex justify-between items-center p-4">
    

        <div>
            <a href="logout.php" class="p-2 bg-white text-black rounded-full shadow">
                Logout
            </a>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="w-3/4 relative mt-6">
        <div class="relative w-full h-40 rounded-xl overflow-hidden">
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-white text-5xl font-bold">Welcome, Lecturer!</span>
            </div>
        </div>
    </div>

    <!-- Lecturer Options -->
    <div class="w-3/4 mt-8 space-y-4 flex flex-col items-center">

        <a href="appointmentRequests.php" class="block relative w-full h-40 rounded-xl overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <span class="text-white text-3xl font-bold">View Appointments</span>
            </div>
        </a>

        <a href="LecBookDisc.php" class="block relative w-full h-40 rounded-xl overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <span class="text-white text-3xl font-bold">Book A Discussion Room</span>
            </div>
        </a>
        <a href="LecManSlots.php" class="block relative w-full h-40 rounded-xl overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <span class="text-white text-3xl font-bold">Manage Free Slots</span>
            </div>
        </a>

        <!-- Connect Google Calendar Button -->
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