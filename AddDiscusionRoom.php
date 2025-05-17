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
<body class="h-screen bg-cover bg-center flex flex-col items-center" style="background-color: #00796b;">
    <!-- Header Section -->
    <div class="w-3/4 relative mt-6">
        <div class="relative w-full h-40 rounded-xl overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <span class="text-white text-3xl font-bold">Add Discussion Room</span>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="w-3/4 mt-8 bg-white p-6 rounded-lg shadow-lg">
        <form action="processDiscussionRoom.php" method="POST" class="space-y-4">
            
            <!-- Discussion Name -->
            <div>
                <label for="discussion_name" class="block text-gray-700 font-bold mb-2">Discussion Name</label>
                <input type="text" id="discussion_name" name="discussion_name" class="w-full p-3 border rounded" placeholder="Enter Discussion Name" required>
            </div>
    
            <!-- Location -->
            <div>
                <label for="location" class="block text-gray-700 font-bold mb-2">Location</label>
                <select id="location" name="location" class="w-full p-3 border rounded" required>
                    <option value="" disabled selected>Select Location</option>
                    <option value="City">City</option>
                    <option value="LawSchool">Law School</option>
                    <option value="Kandy">Kandy</option>
                </select>
            </div>
    
            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="w-full bg-green-600 text-white py-3 rounded hover:bg-green-700">Add Room</button>
            </div>
        </form>
    </div>

</body>
</html>