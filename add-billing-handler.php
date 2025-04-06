<?php
session_start(); // Start the session
include 'config.php'; // Include the database connection

// Set the timezone to Philippines
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientName = $_POST['client_name'];
    $billingDate = $_POST['billing_date'];
    $clientProfile = $_POST['client_profile'];
    $totalInvoice = floatval($_POST['total_invoice']);
    $equipment = isset($_POST['equipment']) ? implode(", ", $_POST['equipment']) : '';
    $billingPdf = '';

    // Handle "OTHERS" profile
    if ($clientProfile === 'OTHERS' && isset($_POST['client_profile_other'])) {
        $clientProfile = $_POST['client_profile_other'];
    }

    // Correct the upload directory path
    $uploadDir = 'uploads/billing/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
    }

    // Handle file upload
    if (isset($_FILES['billing_pdf']) && $_FILES['billing_pdf']['error'] === UPLOAD_ERR_OK) {
        $fileType = mime_content_type($_FILES['billing_pdf']['tmp_name']);
        if ($fileType !== 'application/pdf') {
            die('Error: Only PDF files are allowed.');
        }

        $originalFileName = basename($_FILES['billing_pdf']['name']);
        $hashedFileName = uniqid() . '_' . $originalFileName; // Add a unique hash prefix
        $targetFile = $uploadDir . $hashedFileName;

        if (!move_uploaded_file($_FILES['billing_pdf']['tmp_name'], $targetFile)) {
            die('Error uploading file.');
        }

        $billingPdf = $hashedFileName; // Store the hashed file name
    } else {
        // If no file is uploaded, throw an error
        die('Error: A PDF file is required for billing.');
    }

    // Insert billing data into the database
    $stmt = $conn->prepare("INSERT INTO billing (client_name, billing_date, client_profile, equipment, total_invoice, billing_pdf) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssds', $clientName, $billingDate, $clientProfile, $equipment, $totalInvoice, $billingPdf);

    if ($stmt->execute()) {
        // Log the action
        if (isset($_SESSION['staff_name'])) {
            $staffName = $_SESSION['staff_name'];
            $action = "Added billing for client: $clientName";
            $logDate = date('Y-m-d H:i:s');

            $logStmt = $conn->prepare("INSERT INTO logs (staff_name, action, log_date) VALUES (?, ?, ?)");
            $logStmt->bind_param('sss', $staffName, $action, $logDate);
            $logStmt->execute();
            $logStmt->close();
        }

        // Redirect to staff-home.php with the billing tab active
        header('Location: staff-home.php?tab=billing&status=success');
        $stmt->close();
        $conn->close();
        exit();
    } else {
        die('Error: ' . $stmt->error);
    }
}
