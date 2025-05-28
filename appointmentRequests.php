<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-cover bg-center flex flex-col items-center px-4" style="background-color:rgba(0, 121, 107, 0.41);">

    <!-- Header -->
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

    <!-- Title Box -->
    <div class="w-full sm:w-11/12 md:w-3/4 mt-6">
        <div class="w-full h-28 sm:h-36 rounded-xl bg-[#00796b] flex items-center justify-center px-4">
            <span class="text-black text-xl sm:text-3xl font-bold text-center">My Appointments</span>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="w-full sm:w-11/12 md:w-3/4 bg-white p-4 sm:p-6 mt-6 rounded-xl shadow-md overflow-x-auto">
        <div id="appointmentRequests">
            <p class="text-gray-500">Loading...</p>
        </div>
    </div>

    <!-- Add Note Form -->
    <div id="addNoteForm" class="hidden w-full sm:w-11/12 md:w-3/4 bg-white p-4 sm:p-6 mt-6 rounded-xl shadow-md">
        <h2 class="text-xl sm:text-2xl font-bold mb-4">Add Note</h2>
        <form id="noteForm">
            <input type="hidden" name="appointment_id" id="appointmentId">
            <div class="mb-4">
                <label for="note" class="block text-gray-700 font-bold mb-1">Note</label>
                <textarea name="note" id="note" rows="4" required class="w-full p-2 border border-gray-300 rounded"></textarea>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Submit</button>
        </form>
    </div>

    <!-- JavaScript -->
    <script>
        function cancelAppointment(appointmentId) {
            fetch("cancelAppointment.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ appointment_id: appointmentId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Appointment cancelled successfully.");
                        location.reload();
                    } else {
                        alert(data.error);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred.");
                });
        }

        document.addEventListener("DOMContentLoaded", function () {
            let container = document.getElementById("appointmentRequests");

            fetch("getAppointmentRequests.php")
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        container.innerHTML = `<p class='text-red-500'>${data.error}</p>`;
                        return;
                    }

                    const appointments = data.data;

                    if (appointments.length > 0) {
                        container.innerHTML = `<div class="overflow-x-auto"><table class="min-w-full bg-white border border-gray-300 rounded-lg shadow text-sm sm:text-base">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="py-2 px-4 border">ID</th>
                                    <th class="py-2 px-4 border">Student</th>
                                    <th class="py-2 px-4 border">Date</th>
                                    <th class="py-2 px-4 border">Timeslot</th>
                                    <th class="py-2 px-4 border">Purpose</th>
                                    <th class="py-2 px-4 border">Status</th>
                                    <th class="py-2 px-4 border">Action</th>
                                    <th class="py-2 px-4 border">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${appointments.map(app => {
                                    return `<tr>
                                        <td class="py-2 px-4 border">${app.appointment_id}</td>
                                        <td class="py-2 px-4 border">${app.student}</td>
                                        <td class="py-2 px-4 border">${app.date}</td>
                                        <td class="py-2 px-4 border">${app.start_time} - ${app.end_time}</td>
                                        <td class="py-2 px-4 border">${app.purpose}</td>
                                        <td class="py-2 px-4 border">${app.status}</td>
                                        <td class="py-2 px-4 border">
                                            ${app.status === 'Approved' ? `<button onclick="cancelAppointment(${app.appointment_id})" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Cancel</button>` : ''}
                                        </td>
                                        <td class="py-2 px-4 border">
                                            <button onclick="addNote(${app.appointment_id})" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Add Note</button>
                                        </td>
                                    </tr>`;
                                }).join('')}
                            </tbody>
                        </table></div>`;
                    } else {
                        container.innerHTML = "<p class='text-gray-500'>No upcoming appointments.</p>";
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = "<p class='text-red-500'>Failed to load appointments.</p>";
                });
        });

        function addNote(appointmentId) {
            const form = document.getElementById("addNoteForm");
            const appointmentIdInput = document.getElementById("appointmentId");
            const noteInput = document.getElementById("note");

            appointmentIdInput.value = appointmentId;
            form.classList.remove("hidden");
            noteInput.focus();

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                const note = noteInput.value;

                fetch("addNote.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ appointment_id: appointmentId, note: note })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Note added successfully.");
                            location.reload();
                        } else {
                            alert(data.error);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("An error occurred.");
                    });
            });
        }
    </script>
</body>

</html>
