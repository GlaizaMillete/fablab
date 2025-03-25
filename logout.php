<?php
session_start(); // Start the session

// Destroy the session
session_unset();
session_destroy();

// Redirect to the login page with a success message
header("Location: admin-login.php?logout=success");
exit();
?>