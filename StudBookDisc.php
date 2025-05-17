<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Discussion Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen bg-cover bg-center flex flex-col items-center" style="background-color: #00796b;">

    <!-- Header with Notification and Logout -->
    <div class="w-full flex justify-between items-center p-4">

        <div>
            <a href="StudHomepage.php" class="p-2 bg-white text-black rounded-full shadow">
                Back
            </a>
        </div>
    </div>

    <!-- Page Heading -->
    <div class="w-3/4 relative mt-6">
        <div class="relative w-full h-40 rounded-xl overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <span class="text-white text-3xl font-bold">Book A Discussion Room</span>
            </div>
        </div>
    </div>

    <!-- Booking Form -->
    <div class="w-3/4 bg-white p-6 mt-6 rounded-xl shadow-md">
        <form action="bookRoom.php" method="POST" class="space-y-4">
            <!-- Location Dropdown -->
            <div>
                <label class="block text-gray-700 font-bold">Location</label>
                <select name="location" id="locationSelect" required class="w-full p-2 border border-gray-300 rounded" onchange="fetchDiscussionRooms()">
                    <option value="" disabled selected>Select a Location</option>
                    <option value="City">City Campus</option>
                    <option value="LawSchool">Law School</option>
                    <option value="Kandy">Kandy</option>
                </select>
            </div>
            <!-- Discussion Room Dropdown -->
            <div>
                <label for="discussionRoomSelect" class="block text-gray-700 font-bold mb-2">Discussion Room</label>
                <select id="discussionRoomSelect" name="discussionRoom" class="w-full p-3 border rounded" required>
                    <option value="" disabled selected>Select a Location First</option>
                </select>
            </div>

            <!-- Date Selection -->
            <div>
                <label class="block text-gray-700 font-bold">Date</label>
                <input type="date" name="date" required class="w-full p-2 border border-gray-300 rounded">
            </div>

            <!-- Timeslot Selection -->
            <div id="timeslotContainer">
                <label class="block text-gray-700 font-bold">Timeslot</label>
                <select name="timeslot" required class="w-full p-2 border border-gray-300 rounded"></select>
            </div>

            <!-- Purpose -->
            <div>
                <label class="block text-gray-700 font-bold">Purpose Behind Booking</label>
                <input type="text" name="purpose" required class="w-full p-2 border border-gray-300 rounded"
                    placeholder="Enter purpose">
            </div>

            <!-- Number of Students -->
            <div>
                <label class="block text-gray-700 font-bold">Number of Students</label>
                <div class="flex items-center space-x-4">
                    <button type="button" onclick="updateStudentCount(-1)" class="p-2 bg-gray-300 rounded">-</button>
                    <input type="number" name="numStudents" id="studentCount" value="1" readonly
                        class="w-16 text-center p-2 border border-gray-300 rounded">
                    <button type="button" onclick="updateStudentCount(1)" class="p-2 bg-gray-300 rounded">+</button>
                    <p id="studentWarning" class="text-red-500 text-sm mt-1 hidden">
                        Not enough students to book a discussion room (Minimum: 3).
                    </p>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitBtn"
                class="w-full bg-green-700 text-white p-3 rounded-lg font-bold hover:bg-green-800 opacity-50 cursor-not-allowed"
                disabled>
                Book Room
            </button>
        </form>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const dateInput = document.querySelector('input[name="date"]');
            const timeslotSelect = document.querySelector('select[name="timeslot"]');
            const timeslotContainer = document.getElementById("timeslotContainer");

            if (!dateInput || !timeslotSelect || !timeslotContainer) {
                console.error("Missing one or more required DOM elements.");
                return;
            }

            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today;
            dateInput.min = today;

            loadAvailableTimeslots(today);

            dateInput.addEventListener("change", function () {
                loadAvailableTimeslots(this.value);
            });
        });

        function loadAvailableTimeslots(date) {
            console.log("Fetching timeslots for date:", date);

            const timeslotSelect = document.querySelector('select[name="timeslot"]');
            if (!timeslotSelect) {
                console.error("Timeslot select element not found.");
                return;
            }

            fetch(`getBookedSlots.php?date=${date}`)
                .then(response => response.json())
                .then(bookedSlots => {
                    console.log("Booked Slots:", bookedSlots);

                    const allTimeslots = [
                        "08:30 - 10:30",
                        "10:30 - 12:30",
                        "12:30 - 14:30",
                        "14:30 - 16:30"
                    ];

                    // Clear previous options
                    timeslotSelect.innerHTML = "";

                    const now = new Date();
                    const todayDate = now.toISOString().split('T')[0];

                    let availableSlots;

                    if (date === todayDate) {
                        const currentHour = now.getHours();
                        const currentMinutes = now.getMinutes();

                        const filteredTimeslots = allTimeslots.filter(slot => {
                            const [startTime] = slot.split(" - ");
                            const [startHour, startMinutes] = startTime.split(":").map(Number);
                            return (startHour > currentHour) || (startHour === currentHour && startMinutes > currentMinutes);
                        });

                        availableSlots = filteredTimeslots.filter(slot => !bookedSlots.includes(slot.trim()));

                        if (availableSlots.length === 0) {
                            const option = document.createElement("option");
                            option.disabled = true;
                            option.selected = true;
                            option.textContent = "No time slots available today";
                            timeslotSelect.appendChild(option);
                            return;
                        }
                    } else {
                        availableSlots = allTimeslots.filter(slot => !bookedSlots.includes(slot.trim()));
                    }

                    if (availableSlots.length === 0) {
                        const option = document.createElement("option");
                        option.disabled = true;
                        option.selected = true;
                        option.textContent = "No available timeslots";
                        timeslotSelect.appendChild(option);
                    } else {
                        availableSlots.forEach(slot => {
                            const option = document.createElement("option");
                            option.value = slot;
                            option.textContent = slot;
                            timeslotSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error("Error fetching booked slots:", error);
                });
        }

        function updateStudentCount(change) {
            const input = document.getElementById("studentCount");
            const submitBtn = document.getElementById("submitBtn");
            const warningMessage = document.getElementById("studentWarning");

            let currentValue = parseInt(input.value, 10);
            let newValue = currentValue + change;
            if (newValue < 1) newValue = 1;

            input.value = newValue;

            if (newValue < 3) {
                warningMessage.style.display = "block";
                submitBtn.disabled = true;
                submitBtn.classList.add("opacity-50", "cursor-not-allowed");
            } else {
                warningMessage.style.display = "none";
                submitBtn.disabled = false;
                submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
            }
        }
        function fetchDiscussionRooms() {
            const location = document.getElementById('locationSelect').value;
            const discussionRoomSelect = document.getElementById('discussionRoomSelect');

            // Clear existing options
            discussionRoomSelect.innerHTML = '<option value="" disabled selected>Loading...</option>';

            // Fetch data from the server
            fetch(`fetchDiscussionRooms.php?location=${location}`)
                .then(response => response.json())
                .then(data => {
                    discussionRoomSelect.innerHTML = '<option value="" disabled selected>Select a Discussion Room</option>';
                    data.forEach(room => {
                        const option = document.createElement('option');
                        option.value = room.discroom_id;
                        option.textContent = room.discroom_name;
                        discussionRoomSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching discussion rooms:', error);
                    discussionRoomSelect.innerHTML = '<option value="" disabled selected>Error loading rooms</option>';
                });
        }
    </script>

</body>

</html>