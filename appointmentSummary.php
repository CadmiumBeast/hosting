<?php
    // Include database connection
    include 'db.php';
    session_start();
    // Get the lecturer's ID from the session
    $lecturer_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Summary</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-cover bg-center flex flex-col items-center px-4" style="background-color:rgba(0, 121, 107, 0.41);">

    <!-- Header with Notification and Logout -->
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

    <!-- Page Heading -->
    <div class="w-full sm:w-11/12 md:w-3/4 mt-6">
        <div class="w-full h-28 sm:h-36 rounded-xl bg-[#00796b] flex items-center justify-center px-4">
            <span class="text-black text-xl sm:text-3xl font-bold text-center">Appointment Summary</span>
        </div>
    </div>

    <div class="w-full sm:w-11/12 md:w-3/4 mt-6 bg-white p-6 rounded-xl shadow-md">
        <form method="POST" class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <label for="start_date" class="block text-gray-700 font-bold mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" required class="w-full border p-2 rounded">
            </div>
            <div class="flex-1">
                <label for="end_date" class="block text-gray-700 font-bold mb-1">End Date</label>
                <input type="date" name="end_date" id="end_date" required class="w-full border p-2 rounded">
            </div>
            <button type="submit" class="bg-[#00796b] text-white font-bold px-6 py-2 rounded">Filter</button>
        </form>
    </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_date'], $_POST['end_date'])) {
            $start = $_POST['start_date'];
            $end = $_POST['end_date'];

            // Sanitize and prepare
            $start = htmlspecialchars($start);
            $end = htmlspecialchars($end);

            // Assuming your appointments table is named `appointments`
            // and has `student_id`, `date`, and `lecturer_id` columns
            $query = "SELECT student_id, COUNT(*) AS appointment_count 
                    FROM appointbooking 
                    WHERE lecturer_id = ? AND date BETWEEN ? AND ?
                    GROUP BY student_id";

            $stmt = $conn->prepare($query);
            $stmt->bind_param('sss', $lecturer_id, $start, $end);
            $stmt->execute();
            $result = $stmt->get_result();

            echo '<div class="w-full sm:w-11/12 md:w-3/4 mt-4 bg-white p-6 rounded-xl shadow">';
            echo '<h2 class="text-xl font-bold mb-4">Appointments Summary</h2>';
            echo '<table class="w-full table-auto border border-gray-300 text-left">';
            echo '<thead><tr class="bg-gray-100"><th class="p-2 border">Student ID</th><th class="p-2 border">Appointments</th></tr></thead><tbody>';

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    //Display student name and email
                    $studentQuery = "SELECT name,email FROM users WHERE user_id = ?";
                    $studentStmt = $conn->prepare($studentQuery);
                    $studentStmt->bind_param('i', $row['student_id']);
                    $studentStmt->execute();
                    $studentResult = $studentStmt->get_result();
                    $studentRow = $studentResult->fetch_assoc();
                    $row['student_id'] = htmlspecialchars($studentRow['name']) . " - " . htmlspecialchars($studentRow['email']);
                    echo "<tr><td class='p-2 border'>{$row['student_id']}</td><td class='p-2 border'>{$row['appointment_count']}</td></tr>";
                }
            } else {
                echo "<tr><td colspan='2' class='p-2 border text-center'>No appointments found in this range.</td></tr>";
            }

            echo '</tbody></table>';
            echo '</div>';
        }
        ?>

    </body>
</html>