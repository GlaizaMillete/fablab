<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = $_POST['client_name'];
    $feedback_date = $_POST['feedback_date'];
    $feedback_pdf = '';

    // Correct the upload directory path
    $upload_dir = 'uploads/feedback/'; // Adjusted to match your folder structure
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
    }

    // Handle file upload
    if (isset($_FILES['feedback_pdf']) && $_FILES['feedback_pdf']['error'] === UPLOAD_ERR_OK) {
        // Validate file type
        $file_type = mime_content_type($_FILES['feedback_pdf']['tmp_name']);
        if ($file_type !== 'application/pdf') {
            die('Error: Only PDF files are allowed.');
        }

        $feedback_pdf = basename($_FILES['feedback_pdf']['name']);
        $target_file = $upload_dir . $feedback_pdf;

        if (!move_uploaded_file($_FILES['feedback_pdf']['tmp_name'], $target_file)) {
            die('Error uploading file.');
        }
    }

    // Insert feedback into the database
    $stmt = $conn->prepare("INSERT INTO feedback (client_name, feedback_pdf, feedback_date) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $client_name, $feedback_pdf, $feedback_date);

    if ($stmt->execute()) {
        header('Location: staff-home.php?tab=feedback&status=success');
    } else {
        die('Error: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
?>