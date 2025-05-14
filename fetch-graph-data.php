<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

// Check if the user is logged in as staff (optional, but good practice)
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    // Depending on how you want to handle unauthorized access to this data endpoint
    // you might return an error or redirect. For now, we'll just exit.
    // header("Location: staff-login.php"); // Example redirect
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}


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

// --- Start: Add filter logic ---
$whereClauses = [];
if (!empty($_GET['from_month']) && !empty($_GET['to_month'])) {
    $from_month = intval($_GET['from_month']);
    $to_month = intval($_GET['to_month']);
    $whereClauses[] = "MONTH(request_date) BETWEEN $from_month AND $to_month";
}
if (!empty($_GET['year'])) {
    $year = intval($_GET['year']);
    $whereClauses[] = "YEAR(request_date) = $year";
}
// Note: Filtering by designation and service_requested directly on the grouped column might not make sense for the chart itself,
// but applying them here will filter the *total* dataset from which the chart data is derived.
// If you want the chart to show the distribution *within* a specific designation or service, this is correct.
// If you want the chart to show the distribution *of* designations or services, you might need different logic.
// Assuming you want to filter the dataset first, then group:
if (!empty($_GET['designation'])) {
    $designation = $conn->real_escape_string($_GET['designation']);
    // Only add this filter if the chart column is NOT designation, otherwise it would filter the chart to only one slice.
    if ($column !== 'designation') {
        $whereClauses[] = "designation = '$designation'";
    }
}
if (!empty($_GET['service_requested'])) {
    $service_requested = $conn->real_escape_string($_GET['service_requested']);
    // Only add this filter if the chart column is NOT service_requested, otherwise it would filter the chart to only one slice.
    if ($column !== 'service_requested') {
        // Using LIKE for partial matches as in job-requests.php
        $whereClauses[] = "service_requested LIKE '%$service_requested%'";
    }
}
if (!empty($_GET['search_name'])) {
    $search_name = $conn->real_escape_string(trim($_GET['search_name']));
    $whereClauses[] = "LOWER(client_name) LIKE LOWER('%$search_name%')";
}

$whereClause = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";
// --- End: Add filter logic ---


// Fetch data for the selected column, applying the filters
$sql = "SELECT `$column`, COUNT(*) as count FROM job_requests $whereClause GROUP BY `$column`";
$result = $conn->query($sql);

$data = [
    'labels' => [],
    'values' => [],
    'total' => 0 // This total will now reflect the count *after* filtering
];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Handle empty or null labels gracefully if needed
        $label = $row[$column] === null || $row[$column] === '' ? 'Unknown/Not Specified' : $row[$column];
        $data['labels'][] = $label;
        $data['values'][] = $row['count'];
    }
}

// Fetch the total count of rows *after* filtering
$totalSql = "SELECT COUNT(*) as total FROM job_requests $whereClause";
$totalResult = $conn->query($totalSql);
if ($totalResult->num_rows > 0) {
    $data['total'] = $totalResult->fetch_assoc()['total'];
}

// If no data is found after filtering, return empty arrays
if (empty($data['labels'])) {
    $data['labels'] = ['No Data'];
    $data['values'] = [0];
    // You might want to adjust colors or add a specific message in the frontend
}


echo json_encode($data);
?>