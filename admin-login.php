<?php $pageTitle = "Admin Portal"; ?>
<?php include 'header.php'; ?>
<?php include 'admin-login-handler.php'; ?>

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