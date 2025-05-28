<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authorized Bookings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-cover bg-center flex flex-col items-center px-4" style="background-color:rgba(0, 121, 107, 0.41);">



    <!-- Header with Notification and Logout -->
    <div class="w-full flex justify-between items-center p-4">
        <!-- Logo -->
        <div>
            <a href="LibDiscBook.php" class="flex items-center space-x-2">
                <img src="CCimages/Click2BookLogo.png" alt="App Logo" class="h-16 w-auto max-w-[150px] sm:max-w-[200px]">
            </a>
        </div>
        <!-- Buttons -->
        <div class="flex space-x-4">
            <a href="LibDiscBook.php" class="p-2 bg-white text-black rounded-full shadow text-center">Back</a>
            <a href="logout.php" class="p-2 bg-white text-black rounded-full shadow text-center">Logout</a>
        </div>
    </div>

    <div class="w-full sm:w-11/12 md:w-3/4 mt-6">
        <div class="w-full h-28 sm:h-36 rounded-xl bg-[#00796b] flex items-center justify-center px-4">
            <span class="text-black text-xl sm:text-3xl font-bold text-center">Authorized Booking</span>
        </div>
    </div>

    <!-- Approved Booking Requests Table -->
    <div class="w-full sm:w-11/12 md:w-3/4 bg-white p-4 sm:p-6 mt-6 rounded-xl shadow-md">
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