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

<body class="h-screen bg-cover bg-center flex flex-col items-center" style="background-color: rgba(0, 121, 107, 0.41);">

    <div class="w-full flex justify-between items-center p-4">
        <!-- Logo on the left -->
        <div>
            <a href="StudHomepage.php" class="flex items-center space-x-2">
               <img src="CCimages/Click2BookLogo.png" alt="App Logo" class="h-16 w-auto max-w-[150px] sm:max-w-[200px]">                
            </a>
        </div>
        <!-- Notification and Logout on the right -->
        <div class="flex space-x-4">
            <a href="StudHomepage.php" class="p-2 bg-white text-black rounded-full shadow">
                Back
            </a>
            <a href="logout.php" class="p-2 bg-white text-black rounded-full shadow">
                Logout
            </a>
        </div>
    </div>

    <div class="w-11/12 md:w-3/4 mt-6">
        <div class="w-full h-32 sm:h-40 rounded-xl bg-[#00796b] flex items-center justify-center">
                <span class="text-black text-2xl sm:text-3xl font-bold">Book a Lecturer Appointment</span>
        </div>
    </div>

    <div class="w-11/12 md:w-3/4 bg-white p-4 sm:p-6 mt-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Book an Appointment</h2>
        <form action="bookAppointment.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Lecturer -->
            <div class="col-span-1">
                <label for="lecturerSelect" class="block text-gray-700 font-semibold mb-2">Lecturer</label>
                <select name="lecturer_id" id="lecturerSelect" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="" disabled selected>Select a Lecturer</option>
                    <?php foreach ($lecturers as $lecturer): ?>
                        <option value="<?= $lecturer['user_id'] ?>"><?= htmlspecialchars($lecturer['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Date -->
            <div class="col-span-1">
                <label for="dateInput" class="block text-gray-700 font-semibold mb-2">Date</label>
                <input type="date" name="date" id="dateInput" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600">
            </div>

            <!-- Timeslot -->
            <div class="col-span-1">
                <label for="timeslotSelect" class="block text-gray-700 font-semibold mb-2">Timeslot</label>
                <select name="timeslot" id="timeslotSelect" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600">
                    <!-- Dynamically filled -->
                </select>
            </div>

            <!-- Purpose -->
            <div class="col-span-1">
                <label for="purpose" class="block text-gray-700 font-semibold mb-2">Purpose</label>
                <input type="text" name="purpose" id="purpose" required placeholder="Enter purpose"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600">
            </div>

            <!-- Submit Button: spans full width -->
            <div class="col-span-1 md:col-span-2">
                <button type="submit"
                    class="w-full bg-[#00796b] text-black p-3 rounded-xl font-semibold hover:bg-green-800 transition duration-200">
                    Book Appointment
                </button>
            </div>
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