<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Booking Requests</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen bg-cover bg-center flex flex-col items-center" style="background-color: #00796b;">



    <!-- Header Section -->
    <div class="w-3/4 relative mt-6">
        <div class="relative w-full h-40 rounded-xl overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <span class="text-white text-3xl font-bold">Pending Booking Requests</span>
            </div>
        </div>
    </div>

    <!-- Booking Requests Table -->
    <div class="w-3/4 bg-white p-6 mt-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-4">Requests</h2>
        <div id="bookingRequests" class="overflow-x-auto">
            <p class="text-gray-500">Loading...</p>
        </div>
    </div>

    <script>
        function updateStatus(bookingId, newStatus) {
            fetch("http://localhost/CC/updateBookingStatus.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ booking_id: bookingId, status: newStatus })
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Response:", data); 
                    if (data.success) {
                        alert(`Booking ${bookingId} ${newStatus.toLowerCase()} successfully!`);
                        location.reload(); 
                    } else {
                        alert(`Failed to update status: ${data.error}`);
                    }
                })
                .catch(error => console.error("Error:", error));

                window.location.href = 'bookingRequests.php';
        }

        document.addEventListener("DOMContentLoaded", function () {
            let div = document.getElementById("bookingRequests");

            fetch("http://localhost/CC/getBookingRequests.php") 
                .then(response => response.json())
                .then(data => {
                    console.log(data); 

                    let bookings = data.data;

                    if (bookings.length > 0) {
                        div.innerHTML = `<table class="min-w-full bg-white border border-gray-300 rounded-lg shadow">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="py-2 px-4 border">ID</th>
                                    <th class="py-2 px-4 border">Booked By</th>
                                    <th class="py-2 px-4 border">Room</th>
                                    <th class="py-2 px-4 border">Date</th>
                                    <th class="py-2 px-4 border">Timeslot</th>
                                    <th class="py-2 px-4 border">Purpose</th>
                                    <th class="py-2 px-4 border">Students</th>
                                    <th class="py-2 px-4 border">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${bookings.map(booking => `
                                <tr>
                                    <td class="py-2 px-4 border">${booking.id}</td>
                                    <td class="py-2 px-4 border">${booking.bookedBy}</td>
                                    <td class="py-2 px-4 border">${booking.discRoom}</td>
                                    <td class="py-2 px-4 border">${booking.date}</td>
                                    <td class="py-2 px-4 border">${booking.timeslot}</td>
                                    <td class="py-2 px-4 border">${booking.purpose}</td>
                                    <td class="py-2 px-4 border">${booking.numStudents}</td>
                                    <td class="py-2 px-4 border">
                                        <button onclick="updateStatus(${booking.id}, 'Approved')" class="bg-green-500 text-white px-2 py-1 rounded">Accept</button>
                                        <button onclick="updateStatus(${booking.id}, 'Rejected')" class="bg-red-500 text-white px-2 py-1 rounded ml-2">Deny</button>
                                    </td>
                                </tr>`).join('')}
                            </tbody>
                        </table>`;
                    } else {
                        div.innerHTML = "<p class='text-gray-500'>No pending booking requests.</p>";
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    div.innerHTML = "<p class='text-red-500'>Failed to load booking requests.</p>";
                });
        });
    </script>

</body>

</html>