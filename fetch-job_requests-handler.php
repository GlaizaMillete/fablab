<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('staff_session');
    session_start(); // Start the session only if it's not already started
}
include 'config.php'; // Include the database connection

// Optional: Remove or comment out the AJAX request check if not needed
// if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
//     http_response_code(403); // Forbidden
//     exit('Access denied');
// }

if (isset($_GET['id'])) {
    // Fetch a single job request by ID
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM job_requests WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Job request not found']);
    }
} else {
    // Fetch all job requests
    $sql = "SELECT * FROM job_requests ORDER BY request_date ASC"; // Changed to ASC for oldest to newest
    $result = $conn->query($sql);
    $jobRequests = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $jobRequests[] = $row;
        }
    }

    echo json_encode($jobRequests);
}
?>