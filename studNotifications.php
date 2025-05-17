<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Notifications</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen bg-cover bg-center flex flex-col items-center" style="background-color: #00796b;">

    <div class="w-full flex justify-between items-center p-4">
        <a href="StudHomepage.php" class="p-2 bg-white text-black rounded-full shadow">Back</a>
    </div>

    <div class="w-3/4 relative mt-6">
        <div class="relative w-full h-40 rounded-xl overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <span class="text-white text-3xl font-bold">Your Notifications</span>
            </div>
        </div>
    </div>


    <div class="w-3/4 bg-white p-6 mt-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-4">Your Notifications</h2>
        <div id="notifications" class="overflow-x-auto">
            <p class="text-gray-500">Loading...</p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let div = document.getElementById("notifications");

            fetch("http://localhost/CC/getStudNotifications.php")
                .then(response => response.json())
                .then(data => {
                    console.log(data);

                    let notifications = data.notifications;

                    if (notifications.length > 0) {
                        div.innerHTML = notifications.map(notif => `
                            <div class="p-4 mb-4 rounded-lg shadow-md ${notif.includes('denied') ? 'bg-red-100 border-red-400' : 'bg-green-100 border-green-400'}">
                                <p class="text-gray-800">${notif}</p>
                            </div>
                        `).join('');
                    } else {
                        div.innerHTML = "<p class='text-gray-500'>No notifications available.</p>";
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    div.innerHTML = "<p class='text-red-500'>Failed to load notifications.</p>";
                });
        });
    </script>

</body>

</html>