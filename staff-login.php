<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('staff_session');
    session_start(); // Start the session only if it's not already started
}

// Redirect to staff-home.php if the user is already logged in
if (isset($_SESSION['staff_logged_in']) && $_SESSION['staff_logged_in'] === true) {
    header("Location: staff-home.php");
    exit();
}

// Display a logout success message if redirected from logout.php
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    echo '<script>
        alert("You have successfully logged out.");
        window.location.href = "staff-login.php"; // Redirect after alert is closed
    </script>';
}

$pageTitle = "FabLab Login";
include 'header.php';
?>

<div class="login-container-staff">
    <div class="login-left">
        <img src="FABLAB_BICOL_LOGO (04).png" alt="Logo" class="logo">
        <p class="logo-text">Welcome to Fablab Bicol!</p>
        <i>
            <p class="additional-text">"Where imagination meets machine – the future is made here."</p>
        </i>
    </div>
    <div class="login-right">
        <div class="login-card-staff">
            <div class="login-pictext">
                <img src="user.png" alt="user">
                <p class="login-text">Staff Login</p>
            </div>
            <div class="login-inputs-lower">
                <div class="login-inputs">
                    <form action="staff-login-handler.php" method="post">
                        <input type="text" name="username" placeholder="Username" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <input type="submit" class="login login-submit" name="login" value="LOGIN">
                    </form>
                </div>
                <p class="admin-login-inline-link">
                    Not a staff? <a href="admin-login.php">Go to Admin Login</a>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="credits-container">
    <div class="credits-toggle" tabindex="0">
        <span>👨‍💻</span>
    </div>
    <div class="credits-panel">
        <h3>Developers</h3>
        <ul class="credits-devs">
            <li>
                <div class="dev-card">
                    <img src="devs/jaded.jpg" alt="Jade Raposa">
                    <span>Jade Raposa</span>
                </div>
            </li>
            <li>
                <div class="dev-card">
                    <img src="devs/glaiza.jfif" alt="Glaiza Mea Millete">
                    <span>Glaiza Mea Millete</span>
                </div>
            </li>
            <li>
                <div class="dev-card">
                    <img src="devs/rey.jpg" alt="Rey Gabriel Literal">
                    <span>Rey Gabriel Literal</span>
                </div>
            </li>
        </ul>
    </div>
</div>

<script>
    // Slide in/out on hover or click
    const creditsToggle = document.querySelector('.credits-toggle');
    const creditsPanel = document.querySelector('.credits-panel');

    creditsToggle.addEventListener('mouseenter', () => {
        creditsPanel.classList.add('show');
    });
    creditsToggle.addEventListener('mouseleave', () => {
        creditsPanel.classList.remove('show');
    });
    creditsPanel.addEventListener('mouseenter', () => {
        creditsPanel.classList.add('show');
    });
    creditsPanel.addEventListener('mouseleave', () => {
        creditsPanel.classList.remove('show');
    });

    // Also allow click/tap to toggle for mobile
    creditsToggle.addEventListener('click', () => {
        creditsPanel.classList.toggle('show');
    });
</script>

</body>

</html>