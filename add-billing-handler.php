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
    }

    // Check if this is an update or a new record
    if (isset($_POST['billing_id']) && !empty($_POST['billing_id'])) {
        // Update existing billing record
        $billingId = intval($_POST['billing_id']);

        // Fetch the current record for comparison
        $stmt = $conn->prepare("SELECT * FROM billing WHERE id = ?");
        $stmt->bind_param('i', $billingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $oldData = $result->fetch_assoc();
        $stmt->close();

        // Prepare the update query
        if (!empty($billingPdf)) {
            $stmt = $conn->prepare("UPDATE billing SET client_name = ?, billing_date = ?, client_profile = ?, equipment = ?, total_invoice = ?, billing_pdf = ? WHERE id = ?");
            $stmt->bind_param('ssssdsi', $clientName, $billingDate, $clientProfile, $equipment, $totalInvoice, $billingPdf, $billingId);
        } else {
            $stmt = $conn->prepare("UPDATE billing SET client_name = ?, billing_date = ?, client_profile = ?, equipment = ?, total_invoice = ? WHERE id = ?");
            $stmt->bind_param('ssssdi', $clientName, $billingDate, $clientProfile, $equipment, $totalInvoice, $billingId);
        }

        // Execute the update query
        if ($stmt->execute()) {
            // Log the changes
            if (isset($_SESSION['staff_name'])) {
                $staffName = $_SESSION['staff_name'];
                $logDate = date('Y-m-d H:i:s');
                $changes = [];

                // Compare old and new values
                if ($oldData['client_name'] !== $clientName) {
                    $changes[] = "Client Name: '{$oldData['client_name']}' -> '{$clientName}'";
                }
                if ($oldData['billing_date'] !== $billingDate) {
                    $changes[] = "Billing Date: '{$oldData['billing_date']}' -> '{$billingDate}'";
                }
                if ($oldData['client_profile'] !== $clientProfile) {
                    $changes[] = "Client Profile: '{$oldData['client_profile']}' -> '{$clientProfile}'";
                }
                if ($oldData['equipment'] !== $equipment) {
                    $changes[] = "Equipment: '{$oldData['equipment']}' -> '{$equipment}'";
                }
                if ($oldData['total_invoice'] != $totalInvoice) { // Use != for numeric comparison
                    $changes[] = "Total Invoice: '{$oldData['total_invoice']}' -> '{$totalInvoice}'";
                }
                if (!empty($billingPdf) && $oldData['billing_pdf'] !== $billingPdf) {
                    $changes[] = "PDF was changed"; // Simplified log for PDF change
                }

                // Create the log entry
                if (!empty($changes)) {
                    $action = "Updated billing for client {$clientName}'s " . implode(", ", $changes);
                    $logStmt = $conn->prepare("INSERT INTO logs (staff_name, action, log_date) VALUES (?, ?, ?)");
                    $logStmt->bind_param('sss', $staffName, $action, $logDate);
                    $logStmt->execute();
                    $logStmt->close();
                }
            }
        } else {
            die('Error: ' . $stmt->error);
        }
    } else {
        // Insert new billing record
        if (empty($billingPdf)) {
            die('Error: A PDF file is required for billing.');
        }

        $stmt = $conn->prepare("INSERT INTO billing (client_name, billing_date, client_profile, equipment, total_invoice, billing_pdf) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssds', $clientName, $billingDate, $clientProfile, $equipment, $totalInvoice, $billingPdf);

        $action = "Added billing for client: $clientName";

        // Execute the insert query
        if ($stmt->execute()) {
            // Log the action
            if (isset($_SESSION['staff_name'])) {
                $staffName = $_SESSION['staff_name'];
                $logDate = date('Y-m-d H:i:s');

                $logStmt = $conn->prepare("INSERT INTO logs (staff_name, action, log_date) VALUES (?, ?, ?)");
                $logStmt->bind_param('sss', $staffName, $action, $logDate);
                $logStmt->execute();
                $logStmt->close();
            }
        } else {
            die('Error: ' . $stmt->error);
        }
    }

    // Redirect to staff-home.php with the billing tab active
    $stmt->close();
    $conn->close();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>