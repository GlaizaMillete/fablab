<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}
include 'config.php'; // Include the database connection

// Check if the user is logged in as staff
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    // Redirect to the staff login page if not logged in
    header("Location: staff-login.php");
    exit();
}

// Set the timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Handle File Upload
$filePath = '';
if (isset($_FILES['reference_file']) && $_FILES['reference_file']['error'] == UPLOAD_ERR_OK) {
    $targetDir = "uploads/job-requests/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true); // Create the directory if it doesn't exist
    }

    $originalFileName = basename($_FILES["reference_file"]["name"]);
    $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
    $hashedFileName = uniqid() . '.' . $fileExtension;
    $targetFile = $targetDir . $hashedFileName;

    // Validate file type and size (10MB max)
    $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'zip'];
    $maxSize = 10 * 1024 * 1024; // 10MB

    if (
        in_array(strtolower($fileExtension), $allowedTypes) &&
        $_FILES["reference_file"]["size"] <= $maxSize
    ) {
        if (move_uploaded_file($_FILES["reference_file"]["tmp_name"], $targetFile)) {
            $filePath = $hashedFileName;
        } else {
            die('Error: Failed to upload the file.');
        }
    } else {
        die('Error: Invalid file type or size exceeds 10MB.');
    }
}

// Get form data and sanitize
$requestTitle = $conn->real_escape_string($_POST['request_title']);
$requestDate = $conn->real_escape_string($_POST['request_date']);
$clientName = $conn->real_escape_string($_POST['client_name']);
$contactNumber = $conn->real_escape_string($_POST['contact_number']);
$clientProfile = $conn->real_escape_string($_POST['client_profile']);
$description = $conn->real_escape_string($_POST['request_description']);
$priority = $conn->real_escape_string($_POST['priority']);
$completionDate = $conn->real_escape_string($_POST['completion_date']);

// Handle equipment array
$equipment = isset($_POST['equipment']) ? implode(", ", $_POST['equipment']) : '';
$equipment = $conn->real_escape_string($equipment);

// Handle "OTHERS" profile
if ($clientProfile === 'OTHERS' && isset($_POST['client_profile_other'])) {
    $clientProfile = $conn->real_escape_string($_POST['client_profile_other']);
}

// Insert into database
$sql = "INSERT INTO job_requests (
            request_title, 
            request_date, 
            client_name, 
            contact_number, 
            client_profile, 
            request_description, 
            equipment, 
            priority, 
            completion_date, 
            reference_file,
            status
        ) VALUES (
            '$requestTitle',
            '$requestDate',
            '$clientName',
            '$contactNumber',
            '$clientProfile',
            '$description',
            '$equipment',
            '$priority',
            '$completionDate',
            " . ($filePath ? "'$filePath'" : "NULL") . ",
            'Pending'
        )";

if ($conn->query($sql)) {
    // Log the action
    if (isset($_SESSION['staff_name'])) {
        $staffName = $_SESSION['staff_name'];
        $logDate = date('Y-m-d H:i:s');
        $action = "Added job request for client: $clientName";

        $logStmt = $conn->prepare("INSERT INTO logs (staff_name, action, log_date) VALUES (?, ?, ?)");
        $logStmt->bind_param('sss', $staffName, $action, $logDate);
        $logStmt->execute();
        $logStmt->close();
    }

    // Redirect back to the referring page
    $conn->close();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
} else {
    die('Error: ' . $conn->error);
}
?>