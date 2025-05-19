<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('admin_session');
    session_start(); // Start the session only if it's not already started
}

include 'config.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch admin credentials from the database
    $sql = "SELECT adminID, adminPassword FROM adminfablab WHERE adminUsername = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($adminID, $hashedPassword);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_id'] = $adminID;

            // Set a session variable for the success message
            $_SESSION['login_success'] = "You have successfully logged in.";

            // Redirect to admin-home.php without the query parameter
            header("Location: admin-home.php");
            exit();
        } else {
            // Invalid password
            echo '<script>alert("Invalid username or password."); window.location.href = "admin-login.php";</script>';
        }
    } else {
        // Invalid username
        echo '<script>alert("Invalid username or password."); window.location.href = "admin-login.php";</script>';
    }

    $stmt->close();
}

$conn->close();
?>