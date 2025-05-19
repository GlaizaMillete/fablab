<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('staff_session');
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
$personalName = isset($_POST['personal_name']) ? $conn->real_escape_string($_POST['personal_name']) : null;
$clientName = isset($_POST['client_name']) ? $conn->real_escape_string($_POST['client_name']) : null;
$address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : null;
$contactNumber = isset($_POST['contact_no']) ? $conn->real_escape_string($_POST['contact_no']) : null;
$gender = isset($_POST['gender']) ? $conn->real_escape_string($_POST['gender']) : null;
$genderOptional = isset($_POST['gender_optional']) ? $conn->real_escape_string($_POST['gender_optional']) : null;
$age = isset($_POST['age']) ? intval($_POST['age']) : null;
$designation = isset($_POST['designation']) ? $conn->real_escape_string($_POST['designation']) : null;
$designationOther = isset($_POST['designation_other']) ? $conn->real_escape_string($_POST['designation_other']) : null;
$company = isset($_POST['company']) ? $conn->real_escape_string($_POST['company']) : null;
$serviceRequested = isset($_POST['service_requested']) ? implode(", ", $_POST['service_requested']) : null;
$equipment = isset($_POST['equipment']) ? implode(", ", $_POST['equipment']) : null;
$handToolsOther = isset($_POST['hand_tools_other']) ? $conn->real_escape_string($_POST['hand_tools_other']) : null;
$equipmentOther = isset($_POST['equipment_other']) ? $conn->real_escape_string($_POST['equipment_other']) : null;
$consultationMode = isset($_POST['consultation_mode']) ? $conn->real_escape_string($_POST['consultation_mode']) : null;
$consultationSchedule = isset($_POST['consultation_schedule']) ? $conn->real_escape_string($_POST['consultation_schedule']) : null;
$equipmentSchedule = isset($_POST['equipment_schedule']) ? $conn->real_escape_string($_POST['equipment_schedule']) : null;
$workDescription = isset($_POST['work_description']) ? $conn->real_escape_string($_POST['work_description']) : null;
$requestDate = isset($_POST['date']) ? $conn->real_escape_string($_POST['date']) : null;
$personnelName = isset($_POST['personnel_name']) ? $conn->real_escape_string($_POST['personnel_name']) : null;
$personnelDate = isset($_POST['personnel_date']) ? $conn->real_escape_string($_POST['personnel_date']) : null;

// Check if this is an update or a new insert
if (isset($_POST['id']) && !empty($_POST['id'])) {
    // Update existing job request
    $id = intval($_POST['id']);

    // Fetch the current values from the database
    $fetchSql = "SELECT * FROM job_requests WHERE id = ?";
    $fetchStmt = $conn->prepare($fetchSql);
    $fetchStmt->bind_param("i", $id);
    $fetchStmt->execute();
    $currentData = $fetchStmt->get_result()->fetch_assoc();
    $fetchStmt->close();

    // Use the current values if the fields are empty
    $personalName = !empty($_POST['personal_name']) ? $conn->real_escape_string($_POST['personal_name']) : $currentData['personal_name'];
    $clientName = !empty($_POST['client_name']) ? $conn->real_escape_string($_POST['client_name']) : $currentData['client_name'];
    $address = !empty($_POST['address']) ? $conn->real_escape_string($_POST['address']) : $currentData['address'];
    $contactNumber = !empty($_POST['contact_no']) ? $conn->real_escape_string($_POST['contact_no']) : $currentData['contact_number'];
    $gender = !empty($_POST['gender']) ? $conn->real_escape_string($_POST['gender']) : $currentData['gender'];
    $genderOptional = !empty($_POST['gender_optional']) ? $conn->real_escape_string($_POST['gender_optional']) : $currentData['gender_optional'];
    $age = !empty($_POST['age']) ? intval($_POST['age']) : $currentData['age'];
    $designation = !empty($_POST['designation']) ? $conn->real_escape_string($_POST['designation']) : $currentData['designation'];
    $designationOther = !empty($_POST['designation_other']) ? $conn->real_escape_string($_POST['designation_other']) : $currentData['designation_other'];
    $company = !empty($_POST['company']) ? $conn->real_escape_string($_POST['company']) : $currentData['company'];
    $serviceRequested = !empty($_POST['service_requested']) ? implode(", ", $_POST['service_requested']) : $currentData['service_requested'];
    $equipment = !empty($_POST['equipment']) ? implode(", ", $_POST['equipment']) : $currentData['equipment'];
    $handToolsOther = !empty($_POST['hand_tools_other']) ? $conn->real_escape_string($_POST['hand_tools_other']) : $currentData['hand_tools_other'];
    $equipmentOther = !empty($_POST['equipment_other']) ? $conn->real_escape_string($_POST['equipment_other']) : $currentData['equipment_other'];
    $consultationMode = !empty($_POST['consultation_mode']) ? $conn->real_escape_string($_POST['consultation_mode']) : $currentData['consultation_mode'];
    $consultationSchedule = !empty($_POST['consultation_schedule']) ? $conn->real_escape_string($_POST['consultation_schedule']) : $currentData['consultation_schedule'];
    $equipmentSchedule = !empty($_POST['equipment_schedule']) ? $conn->real_escape_string($_POST['equipment_schedule']) : $currentData['equipment_schedule'];
    $workDescription = !empty($_POST['work_description']) ? $conn->real_escape_string($_POST['work_description']) : $currentData['work_description'];
    $requestDate = !empty($_POST['date']) ? $conn->real_escape_string($_POST['date']) : $currentData['request_date'];
    $personnelName = !empty($_POST['personnel_name']) ? $conn->real_escape_string($_POST['personnel_name']) : $currentData['personnel_name'];
    $personnelDate = !empty($_POST['personnel_date']) ? $conn->real_escape_string($_POST['personnel_date']) : $currentData['personnel_date'];

    // Update the database
    $sql = "UPDATE job_requests SET 
                personal_name = ?, 
                client_name = ?, 
                address = ?, 
                contact_number = ?, 
                gender = ?, 
                gender_optional = ?, 
                age = ?, 
                designation = ?, 
                designation_other = ?, 
                company = ?, 
                service_requested = ?, 
                equipment = ?, 
                hand_tools_other = ?, 
                equipment_other = ?, 
                consultation_mode = ?, 
                consultation_schedule = ?, 
                equipment_schedule = ?, 
                work_description = ?, 
                request_date = ?, 
                personnel_name = ?, 
                personnel_date = ?, 
                reference_file = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssisisssssssssssssi",
        $personalName,
        $clientName,
        $address,
        $contactNumber,
        $gender,
        $genderOptional,
        $age,
        $designation,
        $designationOther,
        $company,
        $serviceRequested,
        $equipment,
        $handToolsOther,
        $equipmentOther,
        $consultationMode,
        $consultationSchedule,
        $equipmentSchedule,
        $workDescription,
        $requestDate,
        $personnelName,
        $personnelDate,
        $filePath,
        $id
    );
} else {
    // Insert new job request
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
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssisisssssssssssss",
        $personalName,
        $clientName,
        $address,
        $contactNumber,
        $gender,
        $genderOptional,
        $age,
        $designation,
        $designationOther,
        $company,
        $serviceRequested,
        $equipment,
        $handToolsOther,
        $equipmentOther,
        $consultationMode,
        $consultationSchedule,
        $equipmentSchedule,
        $workDescription,
        $requestDate,
        $personnelName,
        $personnelDate,
        $filePath
    );
}

// Execute the query
if ($stmt->execute()) {
    // Log the action
    if (isset($_SESSION['staff_name'])) {
        $staffName = $_SESSION['staff_name'];
        $logDate = date('Y-m-d H:i:s');

        // Field label mapping
        $fieldLabels = [
            'personal_name' => 'Personal Name',
            'client_name' => 'Client Name',
            'address' => 'Address',
            'contact_number' => 'Contact Number',
            'gender' => 'Gender',
            'gender_optional' => 'Gender (Optional)',
            'age' => 'Age',
            'designation' => 'Designation',
            'designation_other' => 'Other Designation',
            'company' => 'Company',
            'service_requested' => 'Service Requested',
            'equipment' => 'Equipment',
            'hand_tools_other' => 'Hand Tools (Other)',
            'equipment_other' => 'Other Equipment',
            'consultation_mode' => 'Consultation Mode',
            'consultation_schedule' => 'Consultation Schedule',
            'equipment_schedule' => 'Equipment Schedule',
            'work_description' => 'Work Description',
            'request_date' => 'Request Date',
            'personnel_name' => 'Personnel Name',
            'personnel_date' => 'Personnel Date',
            'reference_file' => 'Reference File'
        ];

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Log the changes for editing
            $changes = [];
            foreach ($currentData as $key => $value) {
                if (array_key_exists($key, $fieldLabels) && $value != $$key) {
                    $label = $fieldLabels[$key];
                    $changes[] = "$label: '$value' -> '" . $$key . "'";
                }
            }
            $action = "Edited job request ID $id. Changes:\n" . implode("\n", $changes);
        } else {
            // Log the addition
            $action = "Added new job request for client: $clientName";
        }

        $logStmt = $conn->prepare("INSERT INTO logs (staff_name, action, log_date) VALUES (?, ?, ?)");
        $logStmt->bind_param('sss', $staffName, $action, $logDate);
        $logStmt->execute();
        $logStmt->close();
    }
} else {
    die('Error: ' . $stmt->error);
}

// Redirect back to the job requests page
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();

?>
