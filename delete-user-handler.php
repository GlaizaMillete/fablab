<?php
include 'config.php';

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Prepare the SQL statement to delete the user
    $sql = "DELETE FROM staffFablab WHERE staffID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo '<script>alert("User deleted successfully."); window.location.href = "admin-home.php";</script>';
    } else {
        echo '<script>alert("Error deleting user."); window.location.href = "admin-home.php";</script>';
    }

    $stmt->close();
}

$conn->close();
?>