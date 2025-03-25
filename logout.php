<?php
session_start(); // Start the session

// Check if the user is logged in and determine the user type
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $redirectPage = "admin-login.php";
} elseif (isset($_SESSION['staff_logged_in']) && $_SESSION['staff_logged_in'] === true) {
    $redirectPage = "staff-login.php";
} else {
    // Default redirection if no user type is identified
    $redirectPage = "index.php"; // Replace with your default landing page if needed
}

// Destroy the session
session_unset();
session_destroy();

// Redirect to the appropriate login page with a success message
header("Location: $redirectPage?logout=success");
exit();
?>