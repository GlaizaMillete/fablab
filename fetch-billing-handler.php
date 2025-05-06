<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header("Location: staff-login.php");
    exit();
}

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

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM billing WHERE no = ?"); // Changed 'id' to 'no'
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $billing = $result->fetch_assoc();

        $serviceStmt = $conn->prepare("SELECT * FROM service_details WHERE billing_id = ?"); // No changes needed here
        $serviceStmt->bind_param('i', $id);
        $serviceStmt->execute();
        $serviceResult = $serviceStmt->get_result();

        $services = [];
        while ($serviceRow = $serviceResult->fetch_assoc()) {
            $services[] = $serviceRow;
        }

        $billing['services'] = $services;

        echo json_encode(['success' => true, 'billing' => $billing]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Billing record not found.']);
    }

    $stmt->close();
    $serviceStmt->close();
    $conn->close();
    exit();
}

$result = $conn->query("SELECT * FROM billing ORDER BY no ASC"); // Changed 'id' to 'no'
$billingRows = [];
while ($row = $result->fetch_assoc()) {
    $billingRows[] = $row;
}

echo json_encode(['success' => true, 'billing_records' => $billingRows]);

$conn->close();
?>