<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Time Slots</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen bg-cover bg-center flex flex-col items-center" style="background-color: #00796b;">
    
    <!-- Header with Notification and Logout -->
    <div class="w-full flex justify-between items-center p-4">

        <div>
            <a href="LecHomepage.php" class="p-2 bg-white text-black rounded-full shadow" id="back">
                Back
            </a>
        </div>
    </div>

    <!-- Page Heading -->
    <div class="w-3/4 relative mt-6">
        <div class="relative w-full h-40 rounded-xl overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <span class="text-white text-3xl font-bold">Time Slots</span>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl m-10">
        <h2 class="text-2xl font-bold mb-4 text-center">Add Free Time Slots</h2>
        <form action="process_timeslot.php" method="POST" class="space-y-4">
            <div class="flex space-x-4">
                <!-- Days of the week -->
                <div class="flex-1">
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
                <div class="flex-1">
                    <label for="start_time" class="block text-gray-700 font-bold mb-2">Start Time</label>
                    <input type="time" id="start_time" name="start_time" class="w-full p-3 border rounded" required>
                </div>
                <!-- End Time Field -->
                <div class="flex-1">
                    <label for="end_time" class="block text-gray-700 font-bold mb-2">End Time</label>
                    <input type="time" id="end_time" name="end_time" class="w-full p-3 border rounded" required>
                </div>
            </div>
            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="w-full bg-green-600 text-white py-3 rounded hover:bg-green-700">Add Time Slot</button>
            </div>
        </form>

        <!-- Current Time Slots -->
        <h3 class="text-xl font-bold mt-8 mb-4 text-center">Current Time Slots</h3>
        <table class="w-full border-collapse border border-gray-300">
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
                // Include database connection
                include 'db.php';
                session_start();

                // Get the lecturer's ID from the session
                $lecturer_id = $_SESSION['user_id'];

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