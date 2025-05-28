<?php 


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.tailwindcss.com"></script>
    <title>Click2Book | Add Discussion Rooms</title>
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
            <span class="text-black text-lg sm:text-3xl font-bold text-center">Add Discussion Room</span>
        </div>
    </div>

    <!-- Form Section -->
    <div class="w-full sm:w-11/12 md:w-3/4 mt-8 bg-white p-4 sm:p-6 rounded-lg shadow-lg">
        <form action="processDiscussionRoom.php" method="POST" class="space-y-4">
            <!-- Discussion Name -->
            <div>
                <label for="discussion_name" class="block text-gray-700 font-bold mb-2 text-base sm:text-lg">Discussion Name</label>
                <input type="text" id="discussion_name" name="discussion_name" class="w-full p-3 border rounded text-base" placeholder="Enter Discussion Name" required>
            </div>
            <!-- Location -->
            <div>
                <label for="location" class="block text-gray-700 font-bold mb-2 text-base sm:text-lg">Location</label>
                <select id="location" name="location" class="w-full p-3 border rounded text-base" required>
                    <option value="" disabled selected>Select Location</option>
                    <option value="City">City</option>
                    <option value="LawSchool">Law School</option>
                    <option value="Kandy">Kandy</option>
                </select>
            </div>
            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="w-full bg-[#00796b] text-black py-3 rounded hover:bg-green-700 text-base sm:text-lg">Add Room</button>
            </div>
        </form>
    </div>
</body>
</html>