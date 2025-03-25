<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}

// Redirect to admin-home.php if the user is already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin-home.php");
    exit();
}

// Display a logout success message if redirected from logout.php
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    echo '<script>alert("You have successfully logged out.");</script>';
}

$pageTitle = "Admin Portal"; 
include 'header.php'; 
include 'admin-login-handler.php'; 

?>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-user-circle"></i>
            <div class="login-header-text">
                <p>FABLAB</p>
                <p>Master Portal</p>
            </div>
        </div>
        <div class="login-inputs">
            <form action="" method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" name="login" class="login login-submit" value="Login">
            </form>
        </div>
    </div>
</div>

</body>

</html>