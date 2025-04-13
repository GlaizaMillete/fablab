<?php
session_start(); // Start the session
include 'config.php'; // Include the database connection

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to the admin login page if not logged in
    header("Location: admin-login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    $userId = isset($_POST['user-id']) ? $_POST['user-id'] : null;

    if ($userId) {
        // Editing an existing user
        if (!empty($password)) {
            // If a new password is provided, validate and update it
            if ($password === $confirmPassword) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE staffFablab SET staffUsername = ?, staffPassword = ? WHERE staffID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $username, $hashedPassword, $userId);
            } else {
                echo '<script>alert("Passwords do not match."); window.location.href = "admin-home.php";</script>';
                exit();
            }
        } else {
            // If no password is provided, only update the username
            $sql = "UPDATE staffFablab SET staffUsername = ? WHERE staffID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $username, $userId);
        }
    } else {
        // Adding a new user
        if ($password === $confirmPassword) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO staffFablab (staffUsername, staffPassword) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $hashedPassword);
        } else {
            echo '<script>alert("Passwords do not match."); window.location.href = "admin-home.php";</script>';
            exit();
        }
    }

    if ($stmt->execute()) {
        echo '<script>alert("User saved successfully."); window.location.href = "admin-home.php";</script>';
    } else {
        echo '<script>alert("Error saving user."); window.location.href = "admin-home.php";</script>';
    }

    $stmt->close();
}

$conn->close();
