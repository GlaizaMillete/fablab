<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}
include 'config.php'; // Include the database connection

// Check if the database connection is successful
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if the user is logged in as staff
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    // Redirect to the staff login page if not logged in
    header("Location: staff-login.php");
    exit();
}

// Fetch feedback data
$sql = "SELECT id, client_name, feedback_pdf, feedback_date FROM feedback";
$result = $conn->query($sql);

if (!$result) {
    die("Error executing query: " . $conn->error);
}

// Store the fetched rows in an array
$feedbackRows = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedbackRows[] = $row;
    }
}

// Close the database connection
$conn->close();
