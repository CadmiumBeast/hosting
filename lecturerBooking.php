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

<body class="h-screen bg-cover bg-center flex flex-col items-center" style="background-color: #00796b;">

    <div class="w-full flex justify-between items-center p-4">
        <div><a href="StudHomepage.php" class="p-2 bg-white text-black rounded-full shadow">Back</a></div>
    </div>

    <div class="w-3/4 relative mt-6">
        <div class="relative w-full h-40 rounded-xl overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <span class="text-white text-3xl font-bold">Book a Lecturer Appointment</span>
            </div>
        </div>
    </div>

    <div class="w-3/4 bg-white p-6 mt-6 rounded-xl shadow-md">
        <form action="bookAppointment.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 font-bold">Lecturer</label>
                <select name="lecturer_id" id="lecturerSelect" required
                    class="w-full p-2 border border-gray-300 rounded">
                    <option value="" disabled selected>Select a Lecturer</option>
                    <?php foreach ($lecturers as $lecturer): ?>
                        <option value="<?= $lecturer['user_id'] ?>"><?= htmlspecialchars($lecturer['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-bold">Date</label>
                <input type="date" name="date" required class="w-full p-2 border border-gray-300 rounded"
                    id="dateInput">
            </div>

            <div id="timeslotContainer">
                <label class="block text-gray-700 font-bold">Timeslot</label>
                <select name="timeslot" required class="w-full p-2 border border-gray-300 rounded"
                    id="timeslotSelect"></select>
            </div>

            <div>
                <label class="block text-gray-700 font-bold">Purpose</label>
                <input type="text" name="purpose" required class="w-full p-2 border border-gray-300 rounded"
                    placeholder="Enter purpose">
            </div>

            <button type="submit" class="w-full bg-green-700 text-white p-3 rounded-lg font-bold hover:bg-green-800">
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