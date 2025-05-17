<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authorized Bookings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen bg-cover bg-center flex flex-col items-center" style="background-color: #00796b;">



    <!-- Header Section -->
    <div class="w-3/4 relative mt-6">
        <div class="relative w-full h-40 rounded-xl overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <span class="text-white text-3xl font-bold">Authorized Bookings</span>
            </div>
        </div>
    </div>

    <!-- Approved Booking Requests Table -->
    <div class="w-3/4 bg-white p-6 mt-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-4">Approved Bookings</h2>
        <div id="approvedBookings" class="overflow-x-auto">
            <p class="text-gray-500">Loading...</p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let div = document.getElementById("approvedBookings");

            fetch("http://localhost/CC/getApprovedBookings.php") 
                .then(response => response.json())
                .then(data => {
                    console.log(data); 

                    let bookings = data.data;

                    if (bookings.length > 0) {
                        div.innerHTML = `<table class="min-w-full bg-white border border-gray-300 rounded-lg shadow">
                            <thead>
                                <tr class="bg-green-200">
                                    <th class="py-2 px-4 border">ID</th>
                                    <th class="py-2 px-4 border">Booked By</th>
                                    <th class="py-2 px-4 border">Room</th>
                                    <th class="py-2 px-4 border">Date</th>
                                    <th class="py-2 px-4 border">Timeslot</th>
                                    <th class="py-2 px-4 border">Purpose</th>
                                    <th class="py-2 px-4 border">Students</th>
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
                                </tr>`).join('')}
                            </tbody>
                        </table>`;
                    } else {
                        div.innerHTML = "<p class='text-gray-500'>No approved booking requests.</p>";
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    div.innerHTML = "<p class='text-red-500'>Failed to load approved bookings.</p>";
                });
        });
    </script>

</body>

</html>