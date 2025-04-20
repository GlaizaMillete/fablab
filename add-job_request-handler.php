<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}
include 'config.php'; // Include the database connection

// Check if the user is logged in as staff
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    // Redirect to the staff login page if not logged in
    header("Location: staff-login.php");
    exit();
}

// Set the timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Handle File Upload
$filePath = '';
if (isset($_FILES['reference_file']) && $_FILES['reference_file']['error'] == UPLOAD_ERR_OK) {
    $targetDir = "uploads/job-requests/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true); // Create the directory if it doesn't exist
    }

    $originalFileName = basename($_FILES["reference_file"]["name"]);
    $hashedFileName = uniqid() . '-' . $originalFileName; // Use a unique name to avoid conflicts
    $targetFile = $targetDir . $hashedFileName;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES["reference_file"]["tmp_name"], $targetFile)) {
        $filePath = $hashedFileName;
    } else {
        die('Error: Failed to upload the file.');
    }
}

// Get form data and sanitize
$personalName = $conn->real_escape_string($_POST['personal_name']); // Personal Name
$clientName = $conn->real_escape_string($_POST['client_name']); // Client Name
$address = $conn->real_escape_string($_POST['address']);
$contactNumber = $conn->real_escape_string($_POST['contact_no']);
$gender = $conn->real_escape_string($_POST['gender']);
$genderOptional = isset($_POST['gender_optional']) ? $conn->real_escape_string($_POST['gender_optional']) : null;
$age = intval($_POST['age']);
$designation = $conn->real_escape_string($_POST['designation']);
$designationOther = isset($_POST['designation_other']) ? $conn->real_escape_string($_POST['designation_other']) : null;
$company = $conn->real_escape_string($_POST['company']);
$serviceRequested = implode(", ", $_POST['service_requested']);
$equipment = isset($_POST['equipment']) ? implode(", ", $_POST['equipment']) : null;
$handToolsOther = isset($_POST['hand_tools_other']) ? $conn->real_escape_string($_POST['hand_tools_other']) : null;
$equipmentOther = isset($_POST['equipment_other']) ? $conn->real_escape_string($_POST['equipment_other']) : null;
$consultationMode = isset($_POST['consultation_mode']) ? $conn->real_escape_string($_POST['consultation_mode']) : null;
$consultationSchedule = isset($_POST['consultation_schedule']) ? $conn->real_escape_string($_POST['consultation_schedule']) : null;
$equipmentSchedule = isset($_POST['equipment_schedule']) ? $conn->real_escape_string($_POST['equipment_schedule']) : null;
$workDescription = $conn->real_escape_string($_POST['work_description']);
$requestDate = $conn->real_escape_string($_POST['date']);
$personnelName = isset($_POST['personnel_name']) ? $conn->real_escape_string($_POST['personnel_name']) : null;
$personnelDate = isset($_POST['personnel_date']) ? $conn->real_escape_string($_POST['personnel_date']) : null;

// Insert into database
$sql = "INSERT INTO job_requests (
            personal_name, 
            client_name, 
            address, 
            contact_number, 
            gender, 
            gender_optional, 
            age, 
            designation, 
            designation_other, 
            company, 
            service_requested, 
            equipment, 
            hand_tools_other, 
            equipment_other, 
            consultation_mode, 
            consultation_schedule, 
            equipment_schedule, 
            work_description, 
            request_date, 
            personnel_name, 
            personnel_date, 
            reference_file
        ) VALUES (
            '$personalName',
            '$clientName',
            '$address',
            '$contactNumber',
            '$gender',
            '$genderOptional',
            $age,
            '$designation',
            '$designationOther',
            '$company',
            '$serviceRequested',
            '$equipment',
            '$handToolsOther',
            '$equipmentOther',
            '$consultationMode',
            '$consultationSchedule',
            '$equipmentSchedule',
            '$workDescription',
            '$requestDate',
            '$personnelName',
            '$personnelDate',
            " . ($filePath ? "'$filePath'" : "NULL") . "
        )";

if ($conn->query($sql)) {
    // Log the action
    if (isset($_SESSION['staff_name'])) {
        $staffName = $_SESSION['staff_name'];
        $logDate = date('Y-m-d H:i:s');
        $action = "Added client profile and service request for client: $clientName";

        $logStmt = $conn->prepare("INSERT INTO logs (staff_name, action, log_date) VALUES (?, ?, ?)");
        $logStmt->bind_param('sss', $staffName, $action, $logDate);
        $logStmt->execute();
        $logStmt->close();
    }

    // Redirect back to the referring page
    $conn->close();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
} else {
    die('Error: ' . $conn->error);
}
