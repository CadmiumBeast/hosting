<?php
session_start();
include "db.php";

// Ensure the user is logged in
if (!isset($_SESSION["user_id"])) {
    echo "<script>alert('Please log in first.'); window.location.href='index.php';</script>";
    exit();
}

$lecturers = [];
$result = $conn->query("SELECT user_id, name FROM users WHERE role = 'Lecturer'");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lecturers[] = $row;
    }
}

// Get the Discussion Room from the database
$discussionRooms = [];
$result = $conn->query("SELECT discroom_name FROM discussionroom");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $discussionRooms[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Book Lecturer Appointment</title>
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
            <span class="text-black text-lg sm:text-3xl font-bold text-center">Book a Lecturer Appointment</span>
        </div>
    </div>

    <div class="w-full sm:w-11/12 md:w-3/4 bg-white p-4 sm:p-6 mt-6 rounded-xl shadow-md">
        <form action="libbookappointment.php" method="POST" class="space-y-4">
            <!-- Row 1: Lecturer & Student ID -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-1/2">
                    <label class="block text-gray-700 font-bold">Lecturer</label>
                    <select name="lecturer_id" id="lecturerSelect" required class="w-full p-2 border border-gray-300 rounded">
                        <option value="" disabled selected>Select a Lecturer</option>
                        <?php foreach ($lecturers as $lecturer): ?>
                            <option value="<?= $lecturer['user_id'] ?>"><?= htmlspecialchars($lecturer['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="w-full md:w-1/2">
                    <label class="block text-gray-700 font-bold">Student ID</label>
                    <input type="text" name="student_id" required class="w-full p-2 border border-gray-300 rounded"
                        placeholder="Enter your Student ID">
                </div>
            </div>
            <!-- Row 2: Date & Discussion Room -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-1/2">
                    <label class="block text-gray-700 font-bold">Date</label>
                    <input type="date" name="date" required class="w-full p-2 border border-gray-300 rounded" id="dateInput">
                </div>
                <div class="w-full md:w-1/2">
                    <label class="block text-gray-700 font-bold">Discussion Room</label>
                    <select name="discussion_room" id="discussionRoomSelect" required class="w-full p-2 border border-gray-300 rounded">
                        <option value="" disabled selected>Select a Discussion Room</option>
                        <?php foreach ($discussionRooms as $room): ?>
                            <option value="<?= $room['discroom_name'] ?>"><?= htmlspecialchars($room['discroom_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <!-- Row 3: Timeslot & Purpose -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-1/2">
                    <label class="block text-gray-700 font-bold">Timeslot</label>
                    <select name="timeslot" required class="w-full p-2 border border-gray-300 rounded" id="timeslotSelect"></select>
                </div>
                <div class="w-full md:w-1/2">
                    <label class="block text-gray-700 font-bold">Purpose</label>
                    <input type="text" name="purpose" required class="w-full p-2 border border-gray-300 rounded"
                        placeholder="Enter purpose">
                </div>
            </div>
            <!-- Submit -->
            <button type="submit" class="w-full bg-[#00796b] text-black p-3 rounded-lg font-bold hover:bg-green-800">
                Book Appointment
            </button>
        </form>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            new TomSelect("#lecturerSelect");

            const dateInput = document.querySelector('#dateInput');
            const lecturerSelect = document.querySelector('#lecturerSelect');
            const timeslotSelect = document.querySelector('#timeslotSelect');

            const today = new Date();
            today.setDate(today.getDate() + 2); // Minimum booking is 2 days ahead
            const minDate = today.toISOString().split('T')[0];
            dateInput.min = minDate;
            dateInput.value = minDate;

            // Load timeslots when both date and lecturer are selected
            function loadAvailableTimeslots() {
                const date = dateInput.value;
                const lecturerId = lecturerSelect.value;

                if (!date || !lecturerId) return;

                fetch(`getBookedSlotsLec.php?date=${date}&lecturer_id=${lecturerId}`)
                .then(response => response.json())
                .then(bookedSlots => {
                    timeslotSelect.innerHTML = "";  // Clear previous options

                    // If there are no booked slots, show a message
                    if (bookedSlots.length === 0) {
                        const option = document.createElement("option");
                        option.textContent = "No booked slots for this lecturer on this date.";
                        option.disabled = true;
                        timeslotSelect.appendChild(option);
                    } else {
                        // Display each booked slot as an option
                        bookedSlots.forEach(slot => {
                            const option = document.createElement("option");
                            option.value = slot.id;  // Use the 'slot' value
                            option.textContent = slot.slot;  // Display the time slot text
                            timeslotSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error("Error loading booked slots:", error);
                });
            }

            // Load timeslots initially and when inputs change
            lecturerSelect.addEventListener("change", loadAvailableTimeslots);
            dateInput.addEventListener("change", loadAvailableTimeslots);

            // Initial load if both values are pre-filled
            loadAvailableTimeslots();
        });
    </script>
</body>

</html>