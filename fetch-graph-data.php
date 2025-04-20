<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

if (!isset($_GET['column'])) {
    echo json_encode(['error' => 'No column specified']);
    exit();
}

$column = $_GET['column'];
$allowedColumns = ['status', 'designation', 'service_requested']; // Whitelist allowed columns

if (!in_array($column, $allowedColumns)) {
    echo json_encode(['error' => 'Invalid column']);
    exit();
}

// Fetch data for the selected column
$sql = "SELECT `$column`, COUNT(*) as count FROM job_requests GROUP BY `$column`";
$result = $conn->query($sql);

$data = [
    'labels' => [],
    'values' => [],
    'total' => 0
];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data['labels'][] = $row[$column];
        $data['values'][] = $row['count'];
    }
}

// Fetch the total count of all rows
$totalResult = $conn->query("SELECT COUNT(*) as total FROM job_requests");
if ($totalResult->num_rows > 0) {
    $data['total'] = $totalResult->fetch_assoc()['total'];
}

echo json_encode($data);
?>