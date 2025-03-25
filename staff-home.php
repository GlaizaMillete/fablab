<?php
session_start(); // Start the session

// Check if the user is logged in as staff
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    // Redirect to the staff login page if not logged in
    header("Location: staff-login.php");
    exit();
}

$pageTitle = "Job Requests";
include 'header.php';
?>

<div class="container">
    <div class="container-left">
        <div class="header">
            <img src="FABLAB_LOGO.png" alt="Description of image" class="admin-image">
        </div>
        <div class="user-content">
            <div class="button" onclick="showTab('job-description', 'Job Requests')">
                <p>Job Requests</p>
            </div>
            <div class="button" onclick="showTab('billing', 'Billings')">
                <p>Billings</p>
            </div>
            <div class="button" onclick="showTab('feedback', 'Feedbacks')">
                <p>Feedbacks</p>
            </div>
            <div class="button" onclick="showTab('summary', 'Summary')">
                <p>Summary</p>
            </div>
        </div>
        <div class="button" onclick="location.href='logout.php'">
            <p class="logout">Logout</p>
        </div>
    </div>
    <div class="container-right">
        <div class="full-width-container">
            <div class="title-container">
                <h1 id="job-request-title">Job Requests</h1>
                <!-- this area should be dynamic according to the active job request tab (job description, billing, feedback) -->
            </div>
            <div class="add-button-container">
                <p onclick="redirectToAddPage()">Add</p>
                <!-- will depend on the active tab, leave this area for now -->
            </div>
        </div>
        <div class="contents">
            <div class="contents-box">
                <div class="job-request-content active" id="job-description">
                    <table>
                        <thead>
                            <tr>
                                <th>Job ID</th>
                                <th>Job Service</th>
                                <th>Client</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>20250305</td>
                                <td>3D Printing</td>
                                <td>John Doe</td>
                                <td>Pending</td>
                            </tr>
                            <tr>
                                <td>20250307</td>
                                <td>Laser Cutting</td>
                                <td>Jane Smith</td>
                                <td>Completed</td>
                            </tr>
                            <tr>
                                <td>20250306</td>
                                <td>CNC Milling</td>
                                <td>Bob Johnson</td>
                                <td>In Progress</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="job-request-content" id="billing">
                    <table>
                        <thead>
                            <tr>
                                <th>Billing ID</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>$100</td>
                                <td>2025-03-01</td>
                                <td>Paid</td>
                                <td><button onclick="editBilling(1)">Edit</button></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>$200</td>
                                <td>2025-03-05</td>
                                <td>Unpaid</td>
                                <td><button onclick="editBilling(2)">Edit</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="job-request-content" id="feedback">
                    <table>
                        <thead>
                            <tr>
                                <th>Feedback ID</th>
                                <th>User</th>
                                <th>Comments</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>John Doe</td>
                                <td>Great job!</td>
                                <td>2025-03-02</td>
                                <td><button onclick="editFeedback(1)">Edit</button></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Jane Smith</td>
                                <td>Very satisfied with the service.</td>
                                <td>2025-03-06</td>
                                <td><button onclick="editFeedback(2)">Edit</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="job-request-content" id="summary">
                    <canvas id="summaryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Form -->
<!-- <div id="floating-form" class="floating-form">
    <form>
        <h2>Add New Job Request</h2>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        <button type="submit">Submit</button>
        <button type="button" onclick="hideForm()">Cancel</button>
    </form>
</div> -->

<!-- Background Overlay -->
<!-- <div id="background-overlay" class="background-overlay"></div> -->

<script>
    // function showForm() {
    //     document.getElementById('floating-form').style.display = 'block';
    //     document.getElementById('background-overlay').style.display = 'block';
    // }

    // function hideForm() {
    //     document.getElementById('floating-form').style.display = 'none';
    //     document.getElementById('background-overlay').style.display = 'none';
    // }

    function showTab(tabId, title) {
        // Hide all job request content
        document.querySelectorAll('.job-request-content').forEach(function(content) {
            content.style.display = 'none';
            content.classList.remove('active');
        });

        // Show the selected tab content
        document.getElementById(tabId).style.display = 'block';
        document.getElementById(tabId).classList.add('active');

        // Update the title
        document.getElementById('job-request-title').innerText = title;

        // Remove active class from all buttons
        document.querySelectorAll('.user-content .button').forEach(function(button) {
            button.classList.remove('active');
        });

        // Add active class to the clicked button
        document.querySelector('.user-content .button[onclick="showTab(\'' + tabId + '\', \'' + title + '\')"]').classList.add('active');

        // Render the chart if the summary tab is selected
        if (tabId === 'summary') {
            renderSummaryChart();
        }
    }

    // Show the default tab on page load
    document.addEventListener('DOMContentLoaded', function() {
        showTab('job-description', 'Job Requests');
    });

    function renderSummaryChart() {
        var ctx = document.getElementById('summaryChart').getContext('2d');
        var summaryChart = new Chart(ctx, {
            type: 'bar', // Change this to the type of chart you want
            data: {
                labels: ['Job Requests', 'Billings', 'Feedbacks'],
                datasets: [{
                    label: 'Summary Data',
                    data: [12, 19, 3], // Sample data
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function redirectToAddPage() {
        const activeTab = document.querySelector('.job-request-content.active').id;
        if (activeTab === 'job-description') {
            window.location.href = 'add_job_request.php';
        } else if (activeTab === 'billing') {
            window.location.href = 'add_billing.php';
        } else if (activeTab === 'feedback') {
            window.location.href = 'add_feedback.php';
        }
    }

    function editJobRequest(id) {
        window.location.href = 'edit_job_request.php?id=' + id;
    }

    function editBilling(id) {
        window.location.href = 'edit_billing.php?id=' + id;
    }

    function editFeedback(id) {
        window.location.href = 'edit_feedback.php?id=' + id;
    }
</script>
</body>

</html>