<?php
session_name('admin_session');
session_start(); // Start the session

// Destroy admin session variables
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_id']);

// Destroy the session completely
session_unset();
session_destroy();

// Redirect to the admin login page with a success message
header("Location: admin-login.php?logout=success");
exit();
?>