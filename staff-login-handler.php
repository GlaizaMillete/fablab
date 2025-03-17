<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT staffPassword FROM staffFablab WHERE staffUsername = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashedPassword)) {
        // Successful login
        header('Location: staff-home.php');
        exit();
    } else {
        // Invalid credentials
        echo '<script>alert("Invalid username or password."); window.location.href = "staff-login.php";</script>';
    }

    $stmt->close();
}

$conn->close();
