<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the ID
    if (!isset($data['id']) || intval($data['id']) <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
        exit();
    }

    $id = intval($data['id']);

    // Fetch the client name before deleting the record
    $fetchSql = "SELECT client_name FROM billing WHERE no = ?";
    $fetchStmt = $conn->prepare($fetchSql);
    if (!$fetchStmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare fetch statement.']);
        exit();
    }
    $fetchStmt->bind_param("i", $id);
    $fetchStmt->execute();
    $fetchResult = $fetchStmt->get_result();

    if ($fetchResult->num_rows > 0) {
        $row = $fetchResult->fetch_assoc();
        $clientName = $row['client_name'];

        // Prepare the SQL query to delete the billing record
        $deleteSql = "DELETE FROM billing WHERE no = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        if (!$deleteStmt) {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare delete statement.']);
            exit();
        }
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Billing record deleted successfully.']);

            // Log the deletion action
            if (isset($_SESSION['staff_name'])) {
                $staffName = $_SESSION['staff_name'];
                $logDate = date('Y-m-d H:i:s');
                $action = "Deleted billing record for client: $clientName";

                $logStmt = $conn->prepare("INSERT INTO logs (staff_name, action, log_date) VALUES (?, ?, ?)");
                $logStmt->bind_param('sss', $staffName, $action, $logDate);
                $logStmt->execute();
                $logStmt->close();
            }
        } else {
            error_log('SQL Error: ' . $deleteStmt->error);
            echo json_encode(['success' => false, 'message' => 'Failed to delete billing record.']);
        }

        $deleteStmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Billing record not found.']);
    }

    $fetchStmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
$conn->close();
?>