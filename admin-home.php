<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to the login page if not logged in
    header("Location: admin-login.php");
    exit();
}

$pageTitle = "Admin Control Room";
include 'header.php';
include 'config.php';
?>

<div class="container">
    <div class="container-left">
        <div class="header">
            <img src="FABLAB_LOGO.png" alt="Description of image" class="admin-image">
        </div>
        <div class="user-content">
            <div class="button" onclick="showContent('users')">
                <p>Users</p>
            </div>
            <div class="button" onclick="showContent('logs')">
                <p>Logs</p>
            </div>
        </div>
        <div class="button" onclick="confirmLogout()">
            <p class="logout">Logout</p>
        </div>
    </div>
    <div class="container-right">
        <div class="full-width-container">
            <div class="title-container">
                <h1 id="content-title">Users</h1>
            </div>
            <div class="add-button-container" id="add-button-container">
                <p onclick="showForm()">Add</p>
            </div>
        </div>
        <div class="contents">
            <div class="contents-box" id="users-content">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT staffID, staffUsername FROM staffFablab";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["staffID"] . "</td>";
                                echo "<td>" . $row["staffUsername"] . "</td>";
                                echo "<td>
                    <button onclick=\"editUser('" . $row["staffID"] . "', '" . $row["staffUsername"] . "')\">Edit</button>
                    <button onclick=\"confirmDelete('" . $row["staffID"] . "')\">Delete</button>
                  </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No users found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="contents-box" id="logs-content" style="display: none;">
                <table>
                    <!-- <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Action</th>
                        </tr>
                    </thead> -->
                    <tbody>
                        <tr>
                            <td>2025-02-25 10:32:02</td>
                            <td>Jade Raposa</td>
                            <td>Change Billing details</td>
                        </tr>
                        <tr>
                            <td>2025-03-06 09:15:45</td>
                            <td>John Doe</td>
                            <td>Login</td>
                        </tr>
                        <tr>
                            <td>2025-03-06 17:45:30</td>
                            <td>Jane Smith</td>
                            <td>Logout</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Floating Form -->
<div id="floating-form" class="floating-form">
    <form id="add-user-form" action="add-user-handler.php" method="post">
        <h2 id="form-title">Add New User</h2>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <label for="confirm-password">Confirm Password:</label>
        <input type="password" id="confirm-password" name="confirm-password" required>
        <button type="submit">Submit</button>
        <button type="button" onclick="hideForm()">Cancel</button>
    </form>
</div>

<!-- Background Overlay -->
<div id="background-overlay" class="background-overlay"></div>

<script>
    function showForm() {
        document.getElementById('form-title').innerText = 'Edit User'; // Change title for editing
        document.getElementById('floating-form').style.display = 'block';
        document.getElementById('background-overlay').style.display = 'block';
    }

    function hideForm() {
        document.getElementById('form-title').innerText = 'Add New User'; // Reset title for adding
        document.getElementById('floating-form').style.display = 'none';
        document.getElementById('background-overlay').style.display = 'none';

        // Clear the form fields
        document.getElementById('username').value = '';
        document.getElementById('password').value = '';
        document.getElementById('confirm-password').value = '';

        // Remove the hidden user ID input if it exists
        const userIdInput = document.getElementById('user-id');
        if (userIdInput) {
            userIdInput.remove();
        }
    }

    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this user?")) {
            // Redirect to the delete handler with the user ID as a query parameter
            window.location.href = `delete-user-handler.php?id=${id}`;
        }
    }

    function showContent(contentId) {
        // Hide all content boxes
        document.getElementById('users-content').style.display = 'none';
        document.getElementById('logs-content').style.display = 'none';

        // Show the selected content box
        document.getElementById(contentId + '-content').style.display = 'block';

        // Update the title
        document.getElementById('content-title').innerText = contentId.charAt(0).toUpperCase() + contentId.slice(1);

        // Toggle the visibility of the add button
        if (contentId === 'logs') {
            document.getElementById('add-button-container').style.display = 'none';
        } else {
            document.getElementById('add-button-container').style.display = 'flex';
        }
    }

    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = 'logout.php';
        }
    }

    function editUser(id, username) {
        // Populate the form fields with the user's details
        document.getElementById('username').value = username;
        document.getElementById('password').value = ''; // Clear the password field
        document.getElementById('confirm-password').value = ''; // Clear the confirm password field

        // Add a hidden input to store the user's ID for editing
        let userIdInput = document.getElementById('user-id');
        if (!userIdInput) {
            userIdInput = document.createElement('input');
            userIdInput.type = 'hidden';
            userIdInput.id = 'user-id';
            userIdInput.name = 'user-id';
            document.getElementById('add-user-form').appendChild(userIdInput);
        }
        userIdInput.value = id;

        // Show the floating form
        showForm();
    }
</script>
</body>

</html>