<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}
include 'config.php'; // Include the database connection

// Fetch all job requests from the database
$sql = "SELECT * FROM job_requests ORDER BY request_date DESC";
$result = $conn->query($sql);
$jobRequests = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jobRequests[] = $row;
    }
}
?>