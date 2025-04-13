<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'fablab_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle File Upload
$filePath = '';
if (isset($_FILES['reference_file']) && $_FILES['reference_file']['error'] == UPLOAD_ERR_OK) {
    $targetDir = "requests/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $originalFileName = basename($_FILES["reference_file"]["name"]);
    $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
    $hashedFileName = uniqid() . '.' . $fileExtension;
    $targetFile = $targetDir . $hashedFileName;

    // Validate file type and size (10MB max)
    $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'zip'];
    $maxSize = 10 * 1024 * 1024; // 10MB
    
    if (in_array(strtolower($fileExtension), $allowedTypes) && 
        $_FILES["reference_file"]["size"] <= $maxSize) {
        if (move_uploaded_file($_FILES["reference_file"]["tmp_name"], $targetFile)) {
            $filePath = $hashedFileName;
        }
    }
}

// Get form data and sanitize
$requestTitle = $conn->real_escape_string($_POST['request_title']);
$requestDate = $conn->real_escape_string($_POST['request_date']);
$clientName = $conn->real_escape_string($_POST['client_name']);
$contactNumber = $conn->real_escape_string($_POST['contact_number']);
$clientProfile = $conn->real_escape_string($_POST['client_profile']);
$description = $conn->real_escape_string($_POST['request_description']);
$priority = $conn->real_escape_string($_POST['priority']);
$completionDate = $conn->real_escape_string($_POST['completion_date']);

// Handle equipment array
$equipment = isset($_POST['equipment']) ? implode(", ", $_POST['equipment']) : '';
$equipment = $conn->real_escape_string($equipment);

// Handle "OTHERS" profile
if ($clientProfile === 'OTHERS' && isset($_POST['client_profile_other'])) {
    $clientProfile = $conn->real_escape_string($_POST['client_profile_other']);
}

// Insert into database
$sql = "INSERT INTO job_requests (
            request_title, 
            request_date, 
            client_name, 
            contact_number, 
            client_profile, 
            client_profile_other, 
            request_description, 
            equipment, 
            priority, 
            completion_date, 
            reference_file,
            status
        ) VALUES (
            '$requestTitle',
            '$requestDate',
            '$clientName',
            '$contactNumber',
            '$clientProfile',
            " . ($clientProfile === 'OTHERS' ? "'$clientProfile'" : "NULL") . ",
            '$description',
            '$equipment',
            '$priority',
            '$completionDate',
            " . ($filePath ? "'$filePath'" : "NULL") . ",
            'Pending'
        )";

if ($conn->query($sql)) {
    $response = [
        'success' => true,
        'request_title' => $requestTitle,
        'request_date' => $requestDate,
        'client_name' => $clientName,
        'contact_number' => $contactNumber,
        'client_profile' => $clientProfile,
        'request_description' => $description,
        'equipment' => $equipment,
        'priority' => $priority,
        'completion_date' => $completionDate,
        'reference_file' => $filePath,
        'status' => 'Pending'
    ];
} else {
    $response = [
        'success' => false, 
        'message' => "Error: " . $conn->error
    ];
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>