<?php $pageTitle = "Job Requests"; ?>
<?php include 'header.php'; ?>

<div class="container">
    <div class="container-left">
        <div class="header">
            <img src="FABLAB_LOGO.png" alt="Description of image" class="admin-image">
        </div>
        <div class="user-content">
            <div class="button" onclick="location.href='#'">
                <p>Job Requests</p>
            </div>
        </div>
        <div class="button" onclick="location.href='logout.php'">
            <p>Logout</p>
        </div>
    </div>
    <div class="container-right">
        <div class="full-width-container">
            <div class="title-container">
                <h1 id="job-request-title">Job Requests</h1>
                <!-- this area should be dynamic according to the active job request tab (job description, billing, feedback) -->
            </div>
            <div class="add-button-container">
                <!-- <p>Add</p> -->
                <!-- will depend on the active tab, leave this area for now -->
            </div>
        </div>
        <div class="contents">
            <div class="contents-box">
                <div class="job-requests-tabs">
                    <button onclick="showTab('job-description', 'Job Description')">Job Description</button>
                    <button onclick="showTab('billing', 'Billing')">Billing</button>
                    <button onclick="showTab('feedback', 'Feedback')">Feedback</button>
                </div>
                <div class="job-request-content" id="job-description">
                    <h2>Job Description</h2>
                    <p>This is a sample job description content. Here you can describe the job details, requirements, and other relevant information.</p>
                </div>
                <div class="job-request-content" id="billing" style="display: none;">
                    <h2>Billing</h2>
                    <p>This is a sample billing content. Here you can provide billing details, payment information, and other relevant billing data.</p>
                </div>
                <div class="job-request-content" id="feedback" style="display: none;">
                    <h2>Feedback</h2>
                    <p>This is a sample feedback content. Here you can provide feedback, comments, and other relevant information regarding the job.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Form -->
<div id="floating-form" class="floating-form">
    <form>
        <h2>Add New Job Request</h2>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        <button type="submit">Submit</button>
        <button type="button" onclick="hideForm()">Cancel</button>
    </form>
</div>

<!-- Background Overlay -->
<div id="background-overlay" class="background-overlay"></div>

<script>
    function showTab(tabId, title) {
        // Hide all job request content
        document.getElementById('job-description').style.display = 'none';
        document.getElementById('billing').style.display = 'none';
        document.getElementById('feedback').style.display = 'none';

        // Show the selected tab content
        document.getElementById(tabId).style.display = 'block';

        // Update the title
        document.getElementById('job-request-title').innerText = title;
    }
</script>

</body>

</html>