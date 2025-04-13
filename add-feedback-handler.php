<?php
session_start(); // Start the session
include 'config.php'; // Include the database connection

// Check if the user is logged in as staff
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    // Redirect to the staff login page if not logged in
    header("Location: staff-login.php");
    exit();
}

// Set the timezone to Philippines
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = $_POST['client_name'];
    $feedback_pdf = '';
    $feedback_date = date('Y-m-d H:i:s'); // Automatically set the current date and time

    // Correct the upload directory path
    $upload_dir = 'uploads/feedback/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
    }

    // Handle file upload
    if (isset($_FILES['feedback_pdf']) && $_FILES['feedback_pdf']['error'] === UPLOAD_ERR_OK) {
        $file_type = mime_content_type($_FILES['feedback_pdf']['tmp_name']);
        if ($file_type !== 'application/pdf') {
            die('Error: Only PDF files are allowed.');
        }

        $feedback_pdf = basename($_FILES['feedback_pdf']['name']);
        $target_file = $upload_dir . $feedback_pdf;

        if (!move_uploaded_file($_FILES['feedback_pdf']['tmp_name'], $target_file)) {
            die('Error uploading file.');
        }
    }

    // Insert feedback into the database
    $stmt = $conn->prepare("INSERT INTO feedback (client_name, feedback_pdf, feedback_date) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $client_name, $feedback_pdf, $feedback_date);

    if ($stmt->execute()) {
        // Log the action
        if (isset($_SESSION['staff_name'])) {
            $staff_name = $_SESSION['staff_name']; // Get the staff's name from the session
        } else {
            die('Error: Staff name is not set in the session.');
        }
        $action = "Added feedback for client: $client_name";
        $log_date = date('Y-m-d H:i:s'); // Current date and time in Asia/Manila timezone

        $log_stmt = $conn->prepare("INSERT INTO logs (staff_name, action, log_date) VALUES (?, ?, ?)");
        $log_stmt->bind_param('sss', $staff_name, $action, $log_date);
        $log_stmt->execute();
        $log_stmt->close();

        // Redirect to staff-home.php with the feedback tab active
        header('Location: staff-home.php?tab=feedback&status=success');
        $stmt->close();
        $conn->close();
        exit();
    } else {
        die('Error: ' . $stmt->error);
    }
}
?>