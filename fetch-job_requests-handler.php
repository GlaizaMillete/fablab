<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}
include 'config.php'; // Include the database connection

// Fetch job requests from the database
$sql = "SELECT id, request_title, client_name, equipment, status, request_date, priority, reference_file FROM job_requests";
$result = $conn->query($sql);

$jobRequests = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jobRequests[] = $row;
    }
}
?>