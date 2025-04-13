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

if (isset($_GET['id'])) {
    // Fetch a specific billing record by ID
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM billing WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $billing = $result->fetch_assoc();
        echo json_encode(['success' => true, 'billing' => $billing]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Billing record not found.']);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// Fetch all billing records sorted by billing_date in ascending order
$result = $conn->query("SELECT * FROM billing ORDER BY billing_date ASC");
$billingRows = [];
while ($row = $result->fetch_assoc()) {
    $billingRows[] = $row;
}
?>