<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    if ($password === $confirmPassword) {
        // Hash the password before storing it in the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO staffFablab (staffUsername, staffPassword) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $hashedPassword);

        if ($stmt->execute()) {
            echo '<script>alert("User added successfully."); window.location.href = "admin-home.php";</script>';
        } else {
            echo '<script>alert("Error adding user."); window.location.href = "admin-home.php";</script>';
        }

        $stmt->close();
    } else {
        echo '<script>alert("Passwords do not match."); window.location.href = "admin-home.php";</script>';
    }
}

$conn->close();
?>