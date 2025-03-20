<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    $userId = isset($_POST['user-id']) ? $_POST['user-id'] : null;

    if ($password === $confirmPassword) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($userId) {
            // Update existing user
            $sql = "UPDATE staffFablab SET staffUsername = ?, staffPassword = ? WHERE staffID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $username, $hashedPassword, $userId);
        } else {
            // Add new user
            $sql = "INSERT INTO staffFablab (staffUsername, staffPassword) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $hashedPassword);
        }

        if ($stmt->execute()) {
            echo '<script>alert("User saved successfully."); window.location.href = "admin-home.php";</script>';
        } else {
            echo '<script>alert("Error saving user."); window.location.href = "admin-home.php";</script>';
        }

        $stmt->close();
    } else {
        echo '<script>alert("Passwords do not match."); window.location.href = "admin-home.php";</script>';
    }
}

$conn->close();
?>