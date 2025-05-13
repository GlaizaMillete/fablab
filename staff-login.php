<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}

// Redirect to staff-home.php if the user is already logged in
if (isset($_SESSION['staff_logged_in']) && $_SESSION['staff_logged_in'] === true) {
    header("Location: staff-home.php");
    exit();
}

// Display a logout success message if redirected from logout.php
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    echo '<script>alert("You have successfully logged out.");</script>';
}

$pageTitle = "FabLab Login";
include 'header.php';
?>

<div class="login-container-staff">
    <div class="login-left">
        <img src="FABLAB_BICOL_LOGO (04).png" alt="Logo" class="logo">
        <p class="logo-text">Welcome to Fablab Bicol!</p>
        <i>
            <p class="additional-text">Lorem ipsum dolor sit, amet consectetur adipisicing elit!</p>
        </i>
    </div>
    <div class="login-right">
        <div class="login-card-staff">
            <div>
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="login-inputs">
                <form action="staff-login-handler.php" method="post">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="submit" class="login login-submit" name="login" value="Login">
                </form>
            </div>
        </div>
    </div>
</div>
</body>

</html>