<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}
include 'config.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']); // Get the ID of the job request to delete

    // Prepare the SQL query to delete the job request
    $sql = "DELETE FROM job_requests WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Job request deleted successfully.']);

        if (isset($_SESSION['staff_name'])) {
            $staffName = $_SESSION['staff_name'];
            $logDate = date('Y-m-d H:i:s');
            $action = "Deleted job request with ID: $id";

            $logStmt = $conn->prepare("INSERT INTO logs (staff_name, action, log_date) VALUES (?, ?, ?)");
            $logStmt->bind_param('sss', $staffName, $action, $logDate);
            $logStmt->execute();
            $logStmt->close();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete job request: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
$conn->close();
?>