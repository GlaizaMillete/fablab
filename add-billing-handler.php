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
    // Personal Information
    $billingDate = $_POST['date'];
    $clientName = $_POST['client_name'];
    $address = $_POST['address'];
    $contactNo = $_POST['contact_no'];
    $clientProfile = $_POST['client_profile'];
    $description = $_POST['description'];

    // Handle "OTHERS" profile
    if ($clientProfile === 'OTHERS' && isset($_POST['client_profile_other'])) {
        $clientProfile = $_POST['client_profile_other'];
    }

    // Completion Information
    $completionDate = $_POST['completion_date'];
    $preparedBy = $_SESSION['staff_name']; // Current staff name from session
    $approvedBy = $_POST['approved_by'];

    // Payment Information
    $orNo = intval($_POST['or_no']);
    $paymentDate = $_POST['payment_date'];
    $paymentReceivedBy = $_POST['payment_received_by'];

    // Receipt Information
    $receiptAcknowledgedBy = $_POST['receipt_acknowledged_by'];
    $receiptDate = $_POST['receipt_date'];

    // Handle file upload for the reference file (stored as billing_pdf)
    $billingPdf = '';
    $uploadDir = 'uploads/billing/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
    }

    if (isset($_FILES['billing_pdf']) && $_FILES['billing_pdf']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
        $fileType = mime_content_type($_FILES['billing_pdf']['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            die('Error: Invalid file type for reference file.');
        }

        $originalFileName = basename($_FILES['billing_pdf']['name']);
        $hashedFileName = uniqid() . '_' . $originalFileName; // Add a unique hash prefix
        $targetFile = $uploadDir . $hashedFileName;

        if (!move_uploaded_file($_FILES['billing_pdf']['tmp_name'], $targetFile)) {
            die('Error uploading reference file.');
        }

        $billingPdf = $hashedFileName; // Store the hashed file name
    }

    // Calculate Total Cost
    $totalCost = 0;
    foreach ($_POST['total_cost'] as $cost) {
        $totalCost += floatval($cost);
    }

    // Check if this is an update or a new record
    if (isset($_POST['billing_id']) && !empty($_POST['billing_id'])) {
        // Update existing billing record
        $billingId = intval($_POST['billing_id']);

        // Fetch the current record for comparison
        $stmt = $conn->prepare("SELECT * FROM billing WHERE no = ?"); // Changed 'id' to 'no'
        $stmt->bind_param('i', $billingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $oldData = $result->fetch_assoc();
        $stmt->close();

        // Prepare the update query
        if (!empty($billingPdf)) {
            $stmt = $conn->prepare("UPDATE billing SET no = ?, billing_date = ?, client_name = ?, address = ?, contact_no = ?, client_profile = ?, description = ?, completion_date = ?, prepared_by = ?, approved_by = ?, or_no = ?, payment_received_by = ?, receipt_acknowledged_by = ?, billing_pdf = ?, total_invoice = ? WHERE no = ?"); // Changed 'id' to 'no'
            $stmt->bind_param('isssssssssssssd', $no, $billingDate, $clientName, $address, $contactNo, $clientProfile, $description, $completionDate, $preparedBy, $approvedBy, $orNo, $paymentReceivedBy, $receiptAcknowledgedBy, $billingPdf, $totalCost, $billingId);
        } else {
            $stmt = $conn->prepare("UPDATE billing SET no = ?, billing_date = ?, client_name = ?, address = ?, contact_no = ?, client_profile = ?, description = ?, completion_date = ?, prepared_by = ?, approved_by = ?, or_no = ?, payment_received_by = ?, receipt_acknowledged_by = ?, total_invoice = ? WHERE no = ?"); // Changed 'id' to 'no'
            $stmt->bind_param('issssssssssssd', $no, $billingDate, $clientName, $address, $contactNo, $clientProfile, $description, $completionDate, $preparedBy, $approvedBy, $orNo, $paymentReceivedBy, $receiptAcknowledgedBy, $totalCost, $billingId);
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
                if ($oldData['total_invoice'] != $totalCost) { // Use != for numeric comparison
                    $changes[] = "Total Invoice: '{$oldData['total_invoice']}' -> '{$totalCost}'";
                }
                if (!empty($billingPdf) && $oldData['billing_pdf'] !== $billingPdf) {
                    $changes[] = "Reference File was updated";
                }

                // Create the log entry
                if (!empty($changes)) {
                    $action = "Updated billing for client {$clientName}: " . implode(", ", $changes);
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
        // Insert into billing table
        $stmt = $conn->prepare("INSERT INTO billing (no, billing_date, client_name, address, contact_no, client_profile, description, completion_date, prepared_by, approved_by, or_no, payment_received_by, receipt_acknowledged_by, billing_pdf, total_invoice) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"); // Changed 'id' to 'no'
        $stmt->bind_param('isssssssssssssd', $no, $billingDate, $clientName, $address, $contactNo, $clientProfile, $description, $completionDate, $preparedBy, $approvedBy, $orNo, $paymentReceivedBy, $receiptAcknowledgedBy, $billingPdf, $totalCost);
        $stmt->execute();
        $billingId = $stmt->insert_id; // Get the ID of the inserted billing record

        // Insert into service_details table
        foreach ($_POST['service_name'] as $index => $serviceName) {
            $unit = $_POST['unit'][$index];
            $rate = $_POST['rate'][$index];
            $totalCostRow = floatval($_POST['total_cost'][$index]);

            $serviceStmt = $conn->prepare("INSERT INTO service_details (billing_id, service_name, unit, rate, total_cost) VALUES (?, ?, ?, ?, ?)");
            $serviceStmt->bind_param('isssd', $billingId, $serviceName, $unit, $rate, $totalCostRow);
            $serviceStmt->execute();
        }

        // Log the action
        if (isset($_SESSION['staff_name'])) {
            $staffName = $_SESSION['staff_name'];
            $logDate = date('Y-m-d H:i:s');
            $action = "Added billing for client: $clientName";

            $logStmt = $conn->prepare("INSERT INTO logs (staff_name, action, log_date) VALUES (?, ?, ?)");
            $logStmt->bind_param('sss', $staffName, $action, $logDate);
            $logStmt->execute();
            $logStmt->close();
        }
    }

    // Close statements and connection
    $stmt->close();
    $conn->close();

    // Redirect to the previous page
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>