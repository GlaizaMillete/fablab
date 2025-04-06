<?php
$conn = new mysqli('localhost', 'root', '', 'fablab_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php
// Handle File Upload
$pdfPath = '';
if (isset($_FILES['billing_pdf']) && $_FILES['billing_pdf']['error'] == UPLOAD_ERR_OK) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $originalFileName = basename($_FILES["billing_pdf"]["name"]);
    $hashedFileName = uniqid() . '_' . $originalFileName; // Add a unique hash prefix
    $targetFile = "{$targetDir}{$hashedFileName}";

    if (move_uploaded_file($_FILES["billing_pdf"]["tmp_name"], $targetFile)) {
        $pdfPath = $hashedFileName; // Store the hashed file name
    }
}

// Get form data
$clientProfile = $conn->real_escape_string($_POST['client_profile']);
$clientName = $conn->real_escape_string($_POST['client_name']);
$billingDate = $conn->real_escape_string($_POST['billing_date']);
$totalInvoice = floatval($_POST['total_invoice']);

// Handle equipment array
$equipment = isset($_POST['equipment']) ? implode(", ", $_POST['equipment']) : '';
$equipment = $conn->real_escape_string($equipment);

// Handle "OTHERS" profile
if ($clientProfile === 'OTHERS' && isset($_POST['client_profile_other'])) {
    $clientProfile = $conn->real_escape_string($_POST['client_profile_other']);
}

// Insert into database
$sql = "INSERT INTO `billing`(`client_profile`, `client_name`, `equipment`, `billing_date`, `total_invoice`, `billing_pdf`)
        VALUES ('$clientProfile', '$clientName', '$equipment', '$billingDate', $totalInvoice, " . ($pdfPath ? "'$pdfPath'" : "NULL") . ")";

if ($conn->query($sql)) {
    $response = [
        'success' => true,
        'client_profile' => $clientProfile,
        'client_name' => $clientName,
        'equipment' => $equipment,
        'billing_date' => $billingDate,
        'total_invoice' => number_format($totalInvoice, 2),
        'billing_pdf' => $pdfPath // Return the hashed file name
    ];
} else {
    $response = ['success' => false, 'message' => "Error: " . $conn->error];
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>