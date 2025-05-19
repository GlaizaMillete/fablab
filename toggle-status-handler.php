<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('admin_session');
    session_start(); // Start the session only if it's not already started
}
include 'config.php'; // Include the database connection

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to the admin login page if not logged in
    header("Location: admin-login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $userId = $_GET['id'];
    $newStatus = $_GET['status'];

    // Update the user's status in the database
    $sql = "UPDATE staffFablab SET status = ? WHERE staffID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newStatus, $userId);

    if ($stmt->execute()) {
        echo '<script>alert("User status updated successfully."); window.location.href = "admin-home.php";</script>';
    } else {
        echo '<script>alert("Error updating user status."); window.location.href = "admin-home.php";</script>';
    }

    $stmt->close();
}

$conn->close();
?>