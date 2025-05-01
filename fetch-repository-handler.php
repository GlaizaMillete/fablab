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

// Fetch repository data
$sql = "SELECT id, listing_name, listing_type, reference_file, note, date FROM repository";
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

$conn->close();
