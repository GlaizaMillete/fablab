<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header("Location: staff-login.php");
    exit();
}

// Check if a specific repository ID is requested
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize the ID

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT id, listing_name, listing_type, reference_file, note, date FROM repository WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $repository = $result->fetch_assoc();
        echo json_encode(['success' => true, 'repository' => $repository]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Repository entry not found.']);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// Fetch all repository data for the table
$sql = "SELECT * FROM repository ORDER BY date ASC";
$result = $conn->query($sql);

if (!$result) {
    die("Error executing query: " . $conn->error);
}

$repositoryRows = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $repositoryRows[] = $row;
    }
}

// Return all rows as JSON (if needed for debugging or API use)
// echo json_encode(['success' => true, 'repositories' => $repositoryRows]);

$conn->close();
