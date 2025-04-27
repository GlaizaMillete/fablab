<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header("Location: staff-login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM billing WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $billing = $result->fetch_assoc();

        $serviceStmt = $conn->prepare("SELECT * FROM service_details WHERE billing_id = ?");
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

$result = $conn->query("SELECT * FROM billing ORDER BY billing_date ASC");
$billingRows = [];
while ($row = $result->fetch_assoc()) {
    $billingRows[] = $row;
}

echo json_encode(['success' => true, 'billing_records' => $billingRows]);

$conn->close();
?>