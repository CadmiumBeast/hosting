<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT user_id, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ($password === $user['password']) {
                $_SESSION["email"] = $user["email"];
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["role"] = $user["role"];

                // Redirect based on role
                if ($user["role"] === "Student") {
                    header("Location: StudHomepage.php");
                } elseif ($user["role"] === "Lecturer") {
                    header("Location: LecHomepage.php");
                } elseif ($user["role"] === "Librarian") {
                    header("Location: LibDiscBook.php");
                } else {
                    echo "<script>alert('Unknown role.'); window.location.href='index.php';</script>";
                }
                exit();
            } else {
                echo "<script>alert('Invalid email or password'); window.location.href='index.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('User not found'); window.location.href='index.php';</script>";
            exit();
        }

        $stmt->close();
    } else {
        echo "<script>alert('Please enter email and password'); window.location.href='index.php';</script>";
        exit();
    }
}
?>