<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header("Location: staff-login.php");
    exit();
}

// Fetch the next billing number (if requested)
if (isset($_GET['action']) && $_GET['action'] === 'get_next_no') {
    $result = $conn->query("SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'fablab_db' AND TABLE_NAME = 'billing'");
    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'next_no' => $row['AUTO_INCREMENT']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Unable to fetch next number.']);
    }
    $conn->close();
    exit();
}

// Fetch a single billing record by 'no'
if (isset($_GET['no'])) {
    $no = intval($_GET['no']); // Sanitize input
    $stmt = $conn->prepare("SELECT * FROM billing WHERE no = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement.']);
        $conn->close();
        exit();
    }

    $stmt->bind_param('i', $no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $billing = $result->fetch_assoc();

        // Fetch associated services
        $serviceStmt = $conn->prepare("SELECT service_name, unit, rate, total_cost FROM service_details WHERE billing_id = ?");
        if ($serviceStmt) {
            $serviceStmt->bind_param('i', $no);
            $serviceStmt->execute();
            $serviceResult = $serviceStmt->get_result();

            $services = [];
            while ($serviceRow = $serviceResult->fetch_assoc()) {
                $services[] = $serviceRow;
            }

            $billing['services'] = $services; // Add services to the billing data
            $serviceStmt->close();
        }

        echo json_encode(['success' => true, 'billing' => $billing]); // Include services in the response
    } else {
        echo json_encode(['success' => false, 'message' => 'Billing record not found.']);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// Fetch all billing records (default behavior)
$result = $conn->query("SELECT * FROM billing ORDER BY billing_date ASC"); // Changed to ASC for oldest to newest
if ($result) {
    $billingRows = [];
    while ($row = $result->fetch_assoc()) {
        $billingRows[] = $row;
    }

    echo json_encode(['success' => true, 'billing_records' => $billingRows]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch billing records.']);
}

$conn->close();
?>