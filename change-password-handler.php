<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('admin_session');
    session_start(); // Start the session only if it's not already started
}
include 'config.php'; // Include database connection

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to the admin login page if not logged in
    header("Location: admin-login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current-password'];
    $newPassword = $_POST['new-password'];
    $confirmPassword = $_POST['confirm-password'];

    // Check if the new password and confirm password match
    if ($newPassword !== $confirmPassword) {
        echo '<script>alert("New password and confirm password do not match."); window.location.href = "admin-home.php";</script>';
        exit();
    }

    // Fetch the current admin password from the database
    $adminID = $_SESSION['admin_id'];
    $sql = "SELECT adminPassword FROM adminfablab WHERE adminID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $adminID);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();

    // Verify the current password
    if (!password_verify($currentPassword, $hashedPassword)) {
        echo '<script>alert("Current password is incorrect."); window.location.href = "admin-home.php";</script>';
        exit();
    }

    // Hash the new password
    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the password in the database
    $sql = "UPDATE adminfablab SET adminPassword = ? WHERE adminID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newHashedPassword, $adminID);
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();

        // Destroy the session and redirect to the login page
        session_unset();
        session_destroy();
        echo '<script>alert("Password changed successfully. Please log in again."); window.location.href = "admin-login.php";</script>';
        exit();
    } else {
        echo '<script>alert("An error occurred while updating the password."); window.location.href = "admin-home.php";</script>';
    }
}
?>