<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('admin_session');
    session_start(); // Start the session only if it's not already started
}

// Redirect to admin-home.php if the user is already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin-home.php");
    exit();
}

// Display a logout success message if redirected from logout.php
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    echo '<script>
        alert("You have successfully logged out.");
        window.location.href = "admin-login.php"; // Redirect after alert is closed
    </script>';
}

$pageTitle = "Admin Portal";
include 'header.php';
include 'admin-login-handler.php';

?>

<body class="admin-login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="FABLAB_LOGO.png" alt="fablab logo">
                <div class="login-header-text">
                    <p>FABLAB</p>
                    <p>Master Portal</p>
                </div>
            </div>
            <div class="login-inputs">
                <form action="" method="post">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="submit" name="login" class="login login-submit" value="LOGIN">
                </form>
                <p class="staff-login-inline-link">
                    <a href="staff-login.php">&#8592; Go Back</a>
                </p>
            </div>
        </div>
    </div>

</body>

</html>