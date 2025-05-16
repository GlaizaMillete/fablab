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
    // $orFavor = $_POST['or_favor'];
    $preparedDate = $_POST['prepared_date'];
    // $completionDate = $_POST['completion_date'];
    $preparedBy = $_SESSION['staff_name']; // Current staff name from session
    // $approvedBy = $_POST['approved_by'];

    // Payment Information
    // $orNo = intval($_POST['or_no']);
    // $paymentDate = $_POST['payment_date'];
    // $paymentReceivedBy = $_POST['payment_received_by'];

    // Receipt Information
    // $receiptAcknowledgedBy = $_POST['receipt_acknowledged_by'];
    // $receiptDate = $_POST['receipt_date'];

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
    // Ensure $_POST['total_cost'] is an array before iterating
    if (isset($_POST['total_cost']) && is_array($_POST['total_cost'])) {
        foreach ($_POST['total_cost'] as $cost) {
            $totalCost += floatval($cost);
        }
    }

    // Check if this is an update or a new record
    if (isset($_POST['billing_id']) && !empty($_POST['billing_id'])) {
        // Update existing billing record
        $billingId = intval($_POST['billing_id']);

        // Fetch the current record for comparison
        $stmt = $conn->prepare("SELECT * FROM billing WHERE no = ?");
        $stmt->bind_param('i', $billingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $oldData = $result->fetch_assoc();
        $stmt->close();

        // Prepare the update query
        if (!empty($billingPdf)) {
            $stmt = $conn->prepare("UPDATE billing SET
    billing_date = ?, 
    client_name = ?, 
    address = ?, 
    contact_no = ?, 
    client_profile = ?, 
    description = ?, 
    -- completion_date = ?, 
    prepared_by = ?, 
    prepared_date = ?, 
    -- approved_by = ?, 
    -- or_no = ?, 
    -- or_favor = ?, 
    -- payment_date = ?, 
    -- payment_received_by = ?, 
    -- receipt_acknowledged_by = ?, 
    -- receipt_date = ?, 
    billing_pdf = ?, 
    total_invoice = ?
    WHERE no = ?");
            // Corrected bind_param types: 15 s, 2 i, 1 d -> ssssssssssssssisdsi
            $stmt->bind_param(
                'sssssssssssssisdsi',
                $billingDate,
                $clientName,
                $address,
                $contactNo,
                $clientProfile,
                $description,
                // $completionDate,
                $preparedBy,
                $preparedDate,
                // $approvedBy,
                // $orNo,
                // $orFavor,
                // $paymentDate,
                // $paymentReceivedBy,
                // $receiptAcknowledgedBy,
                // $receiptDate,
                $billingPdf,
                $totalCost,
                $billingId
            );
        } else {
            $stmt = $conn->prepare("UPDATE billing SET
    billing_date = ?, 
    client_name = ?, 
    address = ?, 
    contact_no = ?, 
    client_profile = ?, 
    description = ?, 
    -- completion_date = ?, 
    prepared_by = ?, 
    prepared_date = ?, 
    -- approved_by = ?, 
    -- or_no = ?, 
    -- or_favor = ?, 
    -- payment_date = ?, 
    -- payment_received_by = ?, 
    -- receipt_acknowledged_by = ?, 
    -- receipt_date = ?, 
    total_invoice = ?
    WHERE no = ?");
            // Corrected bind_param types: 14 s, 2 i, 1 d -> sssssssssssssisdi
            $stmt->bind_param(
                'sssssssssssssisdi',
                $billingDate,
                $clientName,
                $address,
                $contactNo,
                $clientProfile,
                $description,
                // $completionDate,
                $preparedBy,
                $preparedDate,
                // $approvedBy,
                // $orNo,
                // $orFavor,
                // $paymentDate,
                // $paymentReceivedBy,
                // $receiptAcknowledgedBy,
                // $receiptDate,
                $totalCost,
                $billingId
            );
        }

        // Execute the update query
        if ($stmt->execute()) {
            // Delete existing service details for this billing record
            $deleteServiceStmt = $conn->prepare("DELETE FROM service_details WHERE billing_id = ?");
            $deleteServiceStmt->bind_param('i', $billingId);
            $deleteServiceStmt->execute();
            $deleteServiceStmt->close();

            // Insert the updated service details
            // Ensure $_POST['service_name'] is an array before iterating
            if (isset($_POST['service_name']) && is_array($_POST['service_name'])) {
                foreach ($_POST['service_name'] as $index => $serviceName) {
                    // Ensure corresponding keys exist in other arrays
                    if (isset($_POST['unit'][$index], $_POST['rate'][$index], $_POST['total_cost'][$index])) {
                        $unit = $_POST['unit'][$index];
                        $rate = $_POST['rate'][$index];
                        $totalCostRow = floatval($_POST['total_cost'][$index]);

                        $serviceStmt = $conn->prepare("INSERT INTO service_details (billing_id, service_name, unit, rate, total_cost) VALUES (?, ?, ?, ?, ?)");
                        $serviceStmt->bind_param('isssd', $billingId, $serviceName, $unit, $rate, $totalCostRow);
                        if (!$serviceStmt->execute()) {
                            // Log or handle error for service details insertion
                            error_log("Error inserting service detail for billing ID $billingId: " . $serviceStmt->error);
                            // Optionally, you might want to roll back the billing insertion here
                        }
                        $serviceStmt->close();
                    }
                }
            }


            // Log the changes
            if (isset($_SESSION['staff_name'])) {
                $staffName = $_SESSION['staff_name'];
                $logDate = date('Y-m-d H:i:s');

                $changes = [];
                foreach ($oldData as $key => $value) {
                    if ($value != $$key) {
                        $changes[] = "$key: '$value' -> '" . $$key . "'";
                    }
                }
                $action = "Edited billing record ID $billingId. Changes: " . implode(", ", $changes);

                $logStmt = $conn->prepare("INSERT INTO logs (staff_name, action, log_date) VALUES (?, ?, ?)");
                $logStmt->bind_param('sss', $staffName, $action, $logDate);
                $logStmt->execute();
                $logStmt->close();
            }
        } else {
            die('Error updating billing record: ' . $stmt->error);
        }
    } else {
        // Insert into billing table
        // Ensure the column names match the database schema
        $stmt = $conn->prepare("INSERT INTO billing (
            billing_date, 
            client_name, 
            address, 
            contact_no, 
            client_profile, 
            description, 
            -- completion_date, 
            prepared_by, 
            prepared_date, 
            -- approved_by, 
            -- or_no, 
            -- or_favor, 
            -- payment_date, 
            -- payment_received_by, 
            -- receipt_acknowledged_by, 
            -- receipt_date, 
            billing_pdf, 
            total_invoice
        ) VALUES (
        ?, 
        ?, 
        ?, 
        ?, 
        ?, 
        ?, 
        ?, 
        ?, 
        ?, 
        -- ?, 
        -- ?, 
        -- ?, 
        -- ?, 
        -- ?, 
        -- ?, 
        -- ?, 
        -- ?, 
        ?
        )");
        // Corrected bind_param types: 16 s, 1 i, 1 d -> ssssssssssssssisssd
        $stmt->bind_param(
            'sssssssssssssisssd',
            $billingDate,
            $clientName,
            $address,
            $contactNo,
            $clientProfile,
            $description,
            // $completionDate,
            $preparedBy,
            $preparedDate,
            // $approvedBy,
            // $orNo,
            // $orFavor,
            // $paymentDate,
            // $paymentReceivedBy,
            // $receiptAcknowledgedBy,
            // $receiptDate,
            $billingPdf,
            $totalCost
        );

        // Check if billing insertion was successful
        if ($stmt->execute()) {
            $billingId = $stmt->insert_id; // Get the ID of the inserted billing record
            $stmt->close(); // Close the billing statement

            // Insert into service_details table
            // Ensure $_POST['service_name'] is an array before iterating
            if (isset($_POST['service_name']) && is_array($_POST['service_name'])) {
                foreach ($_POST['service_name'] as $index => $serviceName) {
                    // Ensure corresponding keys exist in other arrays
                    if (isset($_POST['unit'][$index], $_POST['rate'][$index], $_POST['total_cost'][$index])) {
                        $unit = $_POST['unit'][$index];
                        $rate = $_POST['rate'][$index];
                        $totalCostRow = floatval($_POST['total_cost'][$index]);

                        $serviceStmt = $conn->prepare("INSERT INTO service_details (billing_id, service_name, unit, rate, total_cost) VALUES (?, ?, ?, ?, ?)");
                        // Use the successfully generated $billingId
                        $serviceStmt->bind_param('isssd', $billingId, $serviceName, $unit, $rate, $totalCostRow);
                        if (!$serviceStmt->execute()) {
                            // Log or handle error for service details insertion
                            error_log("Error inserting service detail for billing ID $billingId: " . $serviceStmt->error);
                            // Optionally, you might want to roll back the billing insertion here
                        }
                        $serviceStmt->close(); // Close statement after each execution
                    }
                }
            }


            // Log the action (only if billing and service details insertion were attempted)
            if (isset($_SESSION['staff_name'])) {
                $staffName = $_SESSION['staff_name'];
                $logDate = date('Y-m-d H:i:s');
                $action = "Added new billing record for client: $clientName";

                $logStmt = $conn->prepare("INSERT INTO logs (staff_name, action, log_date) VALUES (?, ?, ?)");
                $logStmt->bind_param('sss', $staffName, $action, $logDate);
                $logStmt->execute();
                $logStmt->close();
            }
        } else {
            // Handle the error if billing insertion failed
            die('Error inserting billing record: ' . $stmt->error);
        }
    }

    $conn->close();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>