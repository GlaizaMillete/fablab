<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hard-coded credentials
    $adminUsername = 'admin';
    $adminPassword = 'password123';

    if ($username === $adminUsername && $password === $adminPassword) {
        // Redirect to admin-home.php
        header('Location: admin-home.php');
        exit();
    } else {
        echo '<script>alert("Invalid username or password.");</script>';
    }
}
?>