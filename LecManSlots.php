<?php
    // Include database connection
    include 'db.php';
    session_start();
    // Get the lecturer's ID from the session
    $lecturer_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Time Slots</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-cover bg-center flex flex-col items-center px-4" style="background-color:rgba(0, 121, 107, 0.41);">

    <!-- Header with Notification and Logout -->
    <div class="w-full flex justify-between items-center p-4">
        <!-- Logo -->
        <div>
            <a href="LecHomepage.php" class="flex items-center space-x-2">
                <img src="CCimages/Click2BookLogo.png" alt="App Logo" class="h-16 w-auto max-w-[150px] sm:max-w-[200px]">
            </a>
        </div>
        <!-- Buttons -->
        <div class="flex space-x-4">
            <a href="LecHomepage.php" class="p-2 bg-white text-black rounded-full shadow text-center">Back</a>
            <a href="logout.php" class="p-2 bg-white text-black rounded-full shadow text-center">Logout</a>
        </div>
    </div>

    <!-- Page Heading -->
    <div class="w-full sm:w-11/12 md:w-3/4 mt-6">
        <div class="w-full h-28 sm:h-36 rounded-xl bg-[#00796b] flex items-center justify-center px-4">
            <span class="text-black text-xl sm:text-3xl font-bold text-center">Time Slots</span>
        </div>
    </div>
    
    <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg w-full sm:w-11/12 md:w-3/4 mt-6">
    <h2 class="text-2xl font-bold mb-4 text-center">Add Free Time Slots</h2>
    <form action="process_timeslot.php" method="POST" class="space-y-4">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Days of the week -->
            <div class="w-full md:flex-1">
                <label for="day" class="block text-gray-700 font-bold mb-2">Day</label>
                <select id="day" name="day" class="w-full p-3 border rounded" required>
                    <option value="" disabled selected>Select a day</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
            </div>
            <!-- Start Time Field -->
            <div class="w-full md:flex-1">
                <label for="start_time" class="block text-gray-700 font-bold mb-2">Start Time</label>
                <input type="time" id="start_time" name="start_time" class="w-full p-3 border rounded" required>
            </div>
            <!-- End Time Field -->
            <div class="w-full md:flex-1">
                <label for="end_time" class="block text-gray-700 font-bold mb-2">End Time</label>
                <input type="time" id="end_time" name="end_time" class="w-full p-3 border rounded" required>
            </div>
        </div>
        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit" class="w-full bg-[#00796b] text-black p-3 rounded-xl font-semibold hover:bg-green-800 transition duration-200 text-base sm:text-lg">Add Time Slot</button>
        </div>
    </form>

    <!-- Current Time Slots -->
    <h3 class="text-xl font-bold mt-8 mb-4 text-center">Current Time Slots</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse border border-gray-300 text-sm sm:text-base">
            <thead>
                <tr>
                    <th class="border border-gray-300 p-2">Day</th>
                    <th class="border border-gray-300 p-2">Start Time</th>
                    <th class="border border-gray-300 p-2">End Time</th>
                    <th class="border border-gray-300 p-2">Status</th>
                    <th class="border border-gray-300 p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch the lecturer's time slots
                $stmt = $conn->prepare("SELECT id, dayofweek, start_time, end_time, active FROM lecturer_timeslots WHERE lecturer_id = ? ORDER BY dayofweek, start_time");
                $stmt->bind_param("i", $lecturer_id);
                $stmt->execute();
                $result = $stmt->get_result();
                    

                // Check if there are any time slots
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class='border border-gray-300 p-2'>" . $row['dayofweek'] . "</td>";
                        echo "<td class='border border-gray-300 p-2'>" . $row['start_time'] . "</td>";
                        echo "<td class='border border-gray-300 p-2'>" . $row['end_time'] . "</td>";
                        echo "<td class='border border-gray-300 p-2'>" . ($row['active'] ? "Active" : "Inactive") . "</td>";
                        // Delete button
                        echo "<td class='border border-gray-300 p-2'>";
                        echo "<form action='delete_timeslot.php' method='POST' class='inline'>";
                        echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                        echo "<input type='hidden' name='status' value='" . $row['active'] . "'>";
                        echo "<button type='submit' class='bg-red-600 text-white py-1 px-3 rounded hover:bg-red-700'>". ($row['active'] ? "Inactivate" : "Activate") ."</button>";
                        echo "</form>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='border border-gray-300 p-2 text-center'>No time slots available</td></tr>";
                }

                $stmt->close();
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>