<?php

session_start();
// Database connection
$conn = new mysqli("cliq2book.c2f0gy0es42a.us-east-1.rds.amazonaws.com", "admin", "pineapple", "CCassignment1", 3306);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//get the user id from the session
$userId = $_SESSION['user_id'] ?? null;

// Get the Completed Appointments
$completedAppointments = [];
if ($userId) {
    $stmt = $conn->prepare("SELECT * FROM appointbooking WHERE student_id = ? ORDER BY date DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result(); 
    
    while ($row = $result->fetch_assoc()) {
        $completedAppointments[] = $row;
    }
    
    $stmt->close();
}

function dd($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    exit;
}

//get the notes for the completed appointments
$notes = [];

foreach ($completedAppointments as $appointment) {
    $appointmentId = $appointment['appointment_id'];
    
    $stmt = $conn->prepare("SELECT * FROM appointmentnote WHERE appointment_id = ?");
    $stmt->bind_param("i", $appointmentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($note = $result->fetch_assoc()) {
        $notes[$appointmentId][] = $note;
    }
    
    $stmt->close();
}



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Homepage</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen bg-cover bg-center flex flex-col items-center" style="background-color:rgba(0, 121, 107, 0.41);">

    <div class="w-full flex justify-between items-center p-4">
        <!-- Logo on the left -->
        <div>
            <a href="StudHomepage.php" class="flex items-center space-x-2">
               <img src="CCimages/Click2BookLogo.png" alt="App Logo" class="h-16 w-auto max-w-[150px] sm:max-w-[200px]">                
            </a>
        </div>
        <!-- Notification and Logout on the right -->
        <div class="flex space-x-4">
            <a href="StudHomepage.php" class="p-2 bg-white text-black rounded-full shadow">
                Back
            </a>
            <a href="logout.php" class="p-2 bg-white text-black rounded-full shadow">
                Logout
            </a>
        </div>
    </div>

    <div class="w-11/12 md:w-3/4 mt-6">
        <div class="w-full h-32 sm:h-40 rounded-xl bg-[#00796b] flex items-center justify-center">
                <span class="text-black text-2xl sm:text-3xl font-bold">Lecturer Appointments</span>
        </div>
    </div>


    <!-- Display Completed Appointments -->
    <div class="w-3/4 mt-8">
        <?php if (empty($completedAppointments)): ?>
            <p class="text-white">No completed appointments found.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($completedAppointments as $appointment): ?>
                    <div class="bg-white p-4 rounded-lg shadow mb-4 flex flex-col">
                        <p>Lecturer: <?php 
                            $stmt = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
                            $stmt->bind_param("i", $appointment['lecturer_id']);
                            $stmt->execute();
                            $stmt->bind_result($lecturerName);
                            $stmt->fetch();
                            $stmt->close();
                            echo htmlspecialchars($lecturerName);
                        ?></p>
                        <p>Date: <?php echo $appointment['date']; ?></p>
                        <p>Time: <?php 
                            $stmt = $conn->prepare("SELECT start_time,end_time FROM lecturer_timeslots WHERE id = ?");
                            $stmt->bind_param("i", $appointment['timeslot_id']);
                            $stmt->execute();
                            $stmt->bind_result($startTime, $endTime);
                            $stmt->fetch();
                            $stmt->close();
                            echo htmlspecialchars($startTime) . ' - ' . htmlspecialchars($endTime);
                        ?></p>
                        <?php if (isset($notes[$appointment['appointment_id']])): ?>
                            <h4 class="mt-2 font-semibold">Notes:</h4>
                            <ul class="list-disc pl-5">
                                <?php foreach ($notes[$appointment['appointment_id']] as $note): ?>
                                    <li><?php echo htmlspecialchars($note['note']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No notes available for this appointment.</p>
                        <?php endif; ?>
                        <button onclick="showAddNoteForm(<?php echo $appointment['appointment_id']; ?>)" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-auto">
                            Add Note
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Add Note Form (outside the grid, appears as modal) -->
    <div id="addNoteForm" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-4">Add Note</h2>
            <form id="noteForm">
                <input type="hidden" name="appointment_id" id="appointmentId">
                <div>
                    <label for="note" class="block text-gray-700 font-bold">Note</label>
                    <textarea name="note" id="note" rows="4" required class="w-full p-2 border border-gray-300 rounded"></textarea>
                </div>
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" onclick="closeAddNoteForm()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function showAddNoteForm(appointmentId) {
            document.getElementById('appointmentId').value = appointmentId;
            document.getElementById('addNoteForm').classList.remove('hidden');
            document.getElementById('note').focus();
        }
        function closeAddNoteForm() {
            document.getElementById('addNoteForm').classList.add('hidden');
            document.getElementById('note').value = '';
        }
        document.getElementById('noteForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const appointmentId = document.getElementById('appointmentId').value;
            const note = document.getElementById('note').value;

            fetch('addNote.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ appointment_id: appointmentId, note: note })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while adding the note.');
                });
        });
    </script>
</body>
    
