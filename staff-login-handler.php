<?php
include 'config.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL query to fetch the user
    $sql = "SELECT staffPassword, status FROM stafffablab WHERE staffUsername = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword, $status);
        $stmt->fetch();

        // Check if the account is active
        if ($status !== 'Active') {
            echo '<script>alert("Your account is inactive. Please contact the administrator."); window.location.href = "staff-login.php";</script>';
            exit();
        }

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            // Start the session and set session variables
            session_start();
            $_SESSION['staff_logged_in'] = true;
            $_SESSION['staff_username'] = $username;

            // Redirect to the staff home page
            header("Location: staff-home.php");
            exit();
        } else {
            echo '<script>alert("Invalid username or password."); window.location.href = "staff-login.php";</script>';
        }
    } else {
        echo '<script>alert("Invalid username or password."); window.location.href = "staff-login.php";</script>';
    }

    $stmt->close();
}

$conn->close();
?>