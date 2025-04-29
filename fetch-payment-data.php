<?php
// filepath: j:\PHP\fablab\fetch-payment-data.php
include 'config.php';

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