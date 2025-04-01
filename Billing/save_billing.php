<?php
$conn = new mysqli('localhost', 'root', '', 'fablab_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_profile = $_POST['client_profile'];
    $client_name = $_POST['client_name'];
    $billing_date = $_POST['billing_date'];
    $equipment = isset($_POST['equipment']) ? implode(",", $_POST['equipment']) : "";
    if ($client_profile == 'OTHERS' && !empty($_POST['client_profile_other'])) {
        $client_profile = $_POST['client_profile_other'];
    }
    $total_invoice = $_POST['total_invoice'];
    
    $pdfFileName = "";
    if (!empty($_FILES['billing_pdf']['name'])) {
        $pdfFileName = time() . "_" . basename($_FILES['billing_pdf']['name']);
        $targetPath = "/Applications/XAMPP/xamppfiles/htdocs/Billing/uploads/" . $pdfFileName;
        move_uploaded_file($_FILES['billing_pdf']['tmp_name'], $targetPath);
    }
    
    $stmt = $conn->prepare("INSERT INTO billing (client_profile, client_name, equipment, total_invoice, billing_date, billing_pdf) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $client_profile, $client_name, $equipment, $total_invoice, $billing_date, $pdfFileName);
    $stmt->execute();
    header("Location: billing.php");
    exit;
}
?>