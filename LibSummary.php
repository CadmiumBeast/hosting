<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Discussion Room Summary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
</head>

<body class="min-h-screen bg-cover bg-center flex flex-col items-center px-2" style="background-color:rgba(0, 121, 107, 0.41);">

    <!-- Header with Notification and Logout -->
    <div class="w-full flex justify-between items-center p-4">
        <!-- Logo -->
        <div>
            <a href="LibDiscBook.php" class="flex items-center space-x-2">
                <img src="CCimages/Click2BookLogo.png" alt="App Logo" class="h-12 w-auto max-w-[120px] sm:h-16 sm:max-w-[200px]">
            </a>
        </div>
        <!-- Buttons -->
        <div class="flex space-x-2 sm:space-x-4">
            <a href="LibDiscBook.php" class="p-2 bg-white text-black rounded-full shadow text-center text-xs sm:text-base">Back</a>
            <a href="logout.php" class="p-2 bg-white text-black rounded-full shadow text-center text-xs sm:text-base">Logout</a>
        </div>
    </div>

    <div class="w-full sm:w-11/12 md:w-3/4 mt-6">
        <div class="w-full h-20 sm:h-28 rounded-xl bg-[#00796b] flex items-center justify-center px-2">
            <span class="text-black text-lg sm:text-3xl font-bold text-center">Discussion Room Summary</span>
        </div>
    </div>

    <div class="w-full sm:w-11/12 md:w-3/4 mt-6 bg-white p-6 rounded-xl shadow-md">
        <form method="POST" action="" class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <label for="start_date" class="block text-gray-700 font-bold mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" required class="w-full border p-2 rounded">
            </div>
            <div class="flex-1">
                <label for="end_date" class="block text-gray-700 font-bold mb-1">End Date</label>
                <input type="date" name="end_date" id="end_date" required class="w-full border p-2 rounded">
            </div>
            <button type="submit" class="bg-[#00796b] text-white font-bold px-6 py-2 rounded">Filter</button>
        </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_date'], $_POST['end_date'])) {
        $start = $_POST['start_date'];
        $end = $_POST['end_date'];

        // Sanitize input
        $start = htmlspecialchars($start);
        $end = htmlspecialchars($end);

        // Your DB connection
        include 'db.php'; // or your actual connection

        $query = "SELECT discroom_id, COUNT(*) AS booking_count 
                FROM discroombooking 
                WHERE date BETWEEN ? AND ?
                GROUP BY discroom_id";

        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $start, $end);
        $stmt->execute();
        $result = $stmt->get_result();

        echo '<div class="w-full sm:w-11/12 md:w-3/4 mt-4 bg-white p-6 rounded-xl shadow">';
        echo '<h2 class="text-xl font-bold mb-4">Booking Summary</h2>';
        echo '<table class="w-full table-auto border border-gray-300 text-left">';
        echo '<thead><tr class="bg-gray-100"><th class="p-2 border">Discussion Room</th><th class="p-2 border">Bookings</th></tr></thead><tbody>';

        while ($row = $result->fetch_assoc()) {

            // Fetch discussion room name
            $roomQuery = "SELECT discroom_name FROM discussionroom WHERE discroom_id = ?";
            $roomStmt = $conn->prepare($roomQuery);
            $roomStmt->bind_param('i', $row['discroom_id']);
            $roomStmt->execute();
            $roomResult = $roomStmt->get_result();
            $roomRow = $roomResult->fetch_assoc();
            $row['discussion_room'] = htmlspecialchars($roomRow['discroom_name']);
            $row['booking_count'] = htmlspecialchars($row['booking_count']);
            $roomStmt->close();
            echo "<tr><td class='p-2 border'>{$row['discussion_room']}</td><td class='p-2 border'>{$row['booking_count']}</td></tr>";
        }

        echo '</tbody></table>';
        echo '</div>';
    }
    ?>


</body>
</html>