<?php
session_name('staff_session');
session_start(); // Start the session

// Destroy staff session variables
unset($_SESSION['staff_logged_in']);
unset($_SESSION['staff_username']);
unset($_SESSION['staff_id']);
unset($_SESSION['staff_name']);

// Destroy the session completely
session_unset();
session_destroy();

// Redirect to the staff login page with a success message
header("Location: staff-login.php?logout=success");
exit();
?>