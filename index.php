<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script> 
</head>

<body class="h-screen bg-cover bg-center" style="background-color: #009688;">
    <div class="flex items-center justify-center h-full flex-col">
        
        <div class="mb-4">
            <img src="CCimages/Click2BookLogo.png" alt="Logo" class="h-16"> 
        </div>

        
        <div class="bg-white p-8 rounded-lg w-96">
            <div class="mb-6 text-center">
                <input type="text" placeholder="Username" class="w-full p-2 border rounded mb-4">
                <input type="password" placeholder="Password" class="w-full p-2 border rounded">


            </div>
            <button class="w-full bg-green-600 text-white p-2 rounded hover:bg-green-700">Log in</button>
        </div>
    </div>
</body>

</html> -->

<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Click2Book</title>
    <script src="https://cdn.tailwindcss.com"></script> <!-- Tailwind CSS -->
    <style>
        body {
            background-color: #f3f4f6; /* Light gray background */
        }
    </style>
</head>

<body class="h-screen flex flex-col md:flex-row">

    <!-- Left Section (Image) -->
    <div class="w-full md:w-2/3 h-64 md:h-full">
        <img src="CCimages/loginpage.png" alt="Chic Kicks" class="w-full h-full object-cover">
    </div>

    <!-- Right Section (Form) -->
    <div class="w-full md:w-1/3 h-full flex items-center justify-center bg-white">
        <div class="w-3/4 max-w-md">
            <div class="mb-4 flex items-center justify-center">
                <img src="CCimages/Click2BookLogo.png" alt="Logo" class="h-16">
            </div>
            <?php if (isset($_SESSION['email'])): ?>
                <div class="text-center">
                    <p class="text-lg font-bold mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?>!</p>
                    <a href="logout.php" class="w-full bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 inline-block">Logout</a>
                </div>
            <?php else: ?>
                <form action="login.php" method="POST" class="space-y-4">
                    <input type="text" name="email" placeholder="Email" class="w-full p-3 border rounded" required>
                    <input type="password" name="password" placeholder="Password" class="w-full p-3 border rounded" required>
                    <button type="submit" class="w-full bg-[#00796b] text-white py-3 rounded hover:bg-green-700">Log in</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>