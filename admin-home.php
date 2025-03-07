<?php $pageTitle = "Admin Control Room"; ?>
<?php include 'header.php'; ?>

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
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>
                                <button>Edit</button>
                                <button>Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Jane Smith</td>
                            <td>
                                <button>Edit</button>
                                <button>Delete</button>
                            </td>
                        </tr>
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
    <form>
        <h2>Add New User</h2>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Submit</button>
        <button type="button" onclick="hideForm()">Cancel</button>
    </form>
</div>

<!-- Background Overlay -->
<div id="background-overlay" class="background-overlay"></div>

<script>
    function showForm() {
        document.getElementById('floating-form').style.display = 'block';
        document.getElementById('background-overlay').style.display = 'block';
    }

    function hideForm() {
        document.getElementById('floating-form').style.display = 'none';
        document.getElementById('background-overlay').style.display = 'none';
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
</script>
</body>

</html>