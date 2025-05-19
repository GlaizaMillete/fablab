<?php
session_start(); // Start the session
include 'config.php'; // Include the database connection

// Check if the user is logged in as staff
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header("Location: staff-login.php");
    exit();
}

// Set the timezone to Philippines
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $repository_id = isset($_POST['repository_id']) ? intval($_POST['repository_id']) : null; // Check if editing
    $listing_name = trim($_POST['listing_name']);
    $listing_type = trim($_POST['listing_type']);
    $reference_file = trim($_POST['reference_file']);
    $note = trim($_POST['note']);

    // Validate the reference_file field
    if (filter_var($reference_file, FILTER_VALIDATE_URL) === false && !is_dir($reference_file)) {
        die('Error: The reference file must be a valid URL or an existing directory.');
    }

    if ($repository_id) {
        // Update existing repository
        $stmt = $conn->prepare("UPDATE repository SET listing_name = ?, listing_type = ?, reference_file = ?, note = ? WHERE id = ?");
        $stmt->bind_param('ssssi', $listing_name, $listing_type, $reference_file, $note, $repository_id);
    } else {
        // Insert new repository
        $stmt = $conn->prepare("INSERT INTO repository (listing_name, listing_type, reference_file, note) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $listing_name, $listing_type, $reference_file, $note);
    }

    if ($stmt->execute()) {
        // Log the action
        if (isset($_SESSION['staff_name'])) {
            $staff_name = $_SESSION['staff_name'];
            $log_date = date('Y-m-d H:i:s');

            // Field label mapping
            $fieldLabels = [
                'listing_name' => 'Listing Name',
                'listing_type' => 'Listing Type',
                'reference_file' => 'Reference File',
                'note' => 'Note'
            ];

            if ($repository_id) {
                // Fetch old data for comparison
                $oldStmt = $conn->prepare("SELECT * FROM repository WHERE id = ?");
                $oldStmt->bind_param('i', $repository_id);
                $oldStmt->execute();
                $oldData = $oldStmt->get_result()->fetch_assoc();
                $oldStmt->close();

                $changes = [];
                foreach ($oldData as $key => $value) {
                    if (array_key_exists($key, $fieldLabels) && $value != ${$key}) {
                        $label = $fieldLabels[$key];
                        $changes[] = "$label: '$value' -> '" . ${$key} . "'";
                    }
                }
                $action = "Edited repository listing: $listing_name\nChanges:\n" . implode("\n", $changes);
            } else {
                $action = "Added repository listing: $listing_name";
            }

            $log_stmt = $conn->prepare("INSERT INTO logs (staff_name, action, log_date) VALUES (?, ?, ?)");
            $log_stmt->bind_param('sss', $staff_name, $action, $log_date);
            $log_stmt->execute();
            $log_stmt->close();
        }

        // Redirect back to the referring page
        $stmt->close();
        $conn->close();
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        die('Error: ' . $stmt->error);
    }
}
