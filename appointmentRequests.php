<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Appointments</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen bg-cover bg-center flex flex-col items-center" style="background-color: #00796b;">

    <div class="w-3/4 relative mt-6">
        <div class="relative w-full h-40 rounded-xl overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <span class="text-white text-3xl font-bold">My Appointments</span>
            </div>
        </div>
    </div>

    <div class="w-3/4 bg-white p-6 mt-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-4">Appointments</h2>
        <div id="appointmentRequests" class="overflow-x-auto">
            <p class="text-gray-500">Loading...</p>
        </div>
    </div>

    <!-- Form to add a note -->
    <div id="addNoteForm" class="hidden w-3/4 bg-white p-6 mt-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-4">Add Note</h2>
        <form id="noteForm">
            <input type="hidden" name="appointment_id" id="appointmentId">
            <div>
                <label for="note" class="block text-gray-700 font-bold">Note</label>
                <textarea name="note" id="note" rows="4" required class="w-full p-2 border border-gray-300 rounded"></textarea>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Submit</button>
        </form>
    </div>
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
                        container.innerHTML = `<table class="min-w-full bg-white border border-gray-300 rounded-lg shadow">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="py-2 px-4 border">ID</th>
                                    <th class="py-2 px-4 border">Student</th>
                                    <th class="py-2 px-4 border">Date</th>
                                    <th class="py-2 px-4 border">Timeslot</th>
                                    <th class="py-2 px-4 border">Purpose</th>
                                    <th class="py-2 px-4 border">Status</th>
                                    <th class="py-2 px-4 border">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${appointments.map(app => {
                            return ` 
                                    <tr>
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
                        </table>`;
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