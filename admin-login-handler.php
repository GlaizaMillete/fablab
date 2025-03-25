<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hard-coded credentials
    $adminUsername = 'admin';
    $adminPassword = 'password123';

    if ($username === $adminUsername && $password === $adminPassword) {
        // Set a session variable to indicate the admin is logged in
        $_SESSION['admin_logged_in'] = true;

        // Redirect to admin-home.php with a success message
        header('Location: admin-home.php?login=success');
        exit();
    } else {
        // Invalid credentials
        echo '<script>alert("Invalid username or password."); window.location.href = "admin-login.php";</script>';
    }
}
?>