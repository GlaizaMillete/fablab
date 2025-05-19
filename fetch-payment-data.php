<?php
// filepath: j:\PHP\fablab\fetch-payment-data.php
if (session_status() === PHP_SESSION_NONE) {
    session_name('staff_session');
    session_start(); // Start the session only if it's not already started
}
include 'config.php';

// Check if the user is logged in as staff
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    // Redirect to the staff login page if not logged in
    header("Location: staff-login.php");
    exit();
}

if (!isset($_GET['column'])) {
    echo json_encode(['success' => false, 'message' => 'No column specified']);
    exit();
}

$column = $conn->real_escape_string($_GET['column']);
$query = "SELECT $column AS label, SUM(total_invoice) AS value FROM billing GROUP BY $column";
$result = $conn->query($query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Error executing query: ' . $conn->error]);
    exit();
}

$data = ['labels' => [], 'values' => []];
while ($row = $result->fetch_assoc()) {
    $data['labels'][] = $row['label'] ?: 'Unknown';
    $data['values'][] = $row['value'];
}

echo json_encode($data);
?>