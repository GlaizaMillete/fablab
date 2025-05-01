<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}
include 'config.php'; // Include the database connection

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
    $sql = "SELECT * FROM job_requests ORDER BY request_date DESC";
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