<?php
session_start(); // Start the session

$pageTitle = "Job Requests";

include 'header.php';
include 'config.php';

// Check if the user is logged in as staff
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    // Redirect to the staff login page if not logged in
    header("Location: staff-login.php");
    exit();
}

// Display a login success message if redirected from the login handler
if (isset($_GET['login']) && $_GET['login'] === 'success') {
    echo '<script>alert("You have successfully logged in.");</script>';
}

include 'fetch-billing-handler.php';
include 'fetch-feedback-handler.php';
?>

<div class="container">
    <div class="container-left">
        <div class="header">
            <img src="FABLAB_LOGO.png" alt="Description of image" class="admin-image">
            <div class="header-text">
                <p>Hello <b><?php echo htmlspecialchars($_SESSION['staff_name']); ?>!</b></p>
                <p>Staff</p>
            </div>
        </div>
        <div class="user-content">
            <div class="button" onclick="showTab('dashboard', 'Dashboard')">
                <p>Dashboard</p>
            </div>
            <div class="button" onclick="showTab('job-description', 'Job Requests')">
                <p>Job Requests</p>
            </div>
            <div class="button" onclick="showTab('billing', 'Billings')">
                <p>Billings</p>
            </div>
            <div class="button" onclick="showTab('feedback', 'Feedbacks')">
                <p>Feedbacks</p>
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
                <p onclick="handleAddButton()">Add</p>
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>20250305</td>
                                <td>3D Printing</td>
                                <td>John Doe</td>
                                <td>Pending</td>
                                <td><button onclick="">View</button></td>
                            </tr>
                            <tr>
                                <td>20250307</td>
                                <td>Laser Cutting</td>
                                <td>Jane Smith</td>
                                <td>Completed</td>
                                <td><button onclick="">View</button></td>
                            </tr>
                            <tr>
                                <td>20250306</td>
                                <td>CNC Milling</td>
                                <td>Bob Johnson</td>
                                <td>In Progress</td>
                                <td><button onclick="">View</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="job-request-content" id="billing">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Equipment</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>PDF</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($billingRows as $row): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['client_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['equipment']); ?></td>
                                    <td>&#8369;<?php echo number_format($row['total_invoice'], 2); ?></td>
                                    <td><?php echo date("F d, Y", strtotime($row['billing_date'])); ?></td>
                                    <td>
                                        <?php if (!empty($row['billing_pdf'])): ?>
                                            <a href="uploads/billing/<?php echo htmlspecialchars($row['billing_pdf']); ?>" target="_blank">View PDF</a>
                                        <?php else: ?>
                                            <span class="no-pdf">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><button onclick="editBilling(<?php echo $row['id']; ?>)">Edit</button></td>
                                </tr>
                            <?php endforeach; ?>
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
                            <?php
                            if ($result->num_rows > 0) {
                                // Output data for each row
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
                                    echo "<td>";
                                    if (!empty($row['feedback_pdf'])) {
                                        echo "<a href='uploads/feedback/" . htmlspecialchars($row['feedback_pdf']) . "' target='_blank'>View PDF</a>";
                                    } else {
                                        echo "<span class='no-pdf'>No Comments</span>";
                                    }
                                    echo "</td>";
                                    echo "<td>" . htmlspecialchars($row['feedback_date']) . "</td>";
                                    echo "<td><button onclick='editFeedback(" . htmlspecialchars($row['id']) . ")'>Edit</button></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No feedback available</td></tr>";
                            }

                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="job-request-content active" id="dashboard">
                    <canvas id="summaryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Billing Form Modal -->
<div id="billing-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeBillingForm()">&times;</span>
        <h2>Add Billing</h2>
        <form action="add-billing-handler.php" method="POST" enctype="multipart/form-data">
            <label for="client_name">Client Name:</label>
            <input type="text" id="client_name" name="client_name" required>

            <label for="billing_date">Billing Date:</label>
            <input type="date" id="billing_date" name="billing_date" required>

            <label for="client_profile">Client Profile:</label><br>
            <input type="radio" name="client_profile" value="STUDENT" required> STUDENT<br>
            <input type="radio" name="client_profile" value="MSME" required> MSME<br>
            <input type="radio" name="client_profile" value="OTHERS" required> OTHERS (Specify):
            <input type="text" name="client_profile_other">

            <label for="equipment">Equipment Used:</label><br>
            <input type="checkbox" name="equipment[]" value="3D Printer"> 3D Printer<br>
            <input type="checkbox" name="equipment[]" value="3D Scanner"> 3D Scanner<br>
            <input type="checkbox" name="equipment[]" value="Laser Cutting Machine"> Laser Cutting Machine<br>
            <input type="checkbox" name="equipment[]" value="Print and Cut Machine"> Print and Cut Machine<br>
            <input type="checkbox" name="equipment[]" value="CNC Machine(Big)"> CNC Machine(Big)<br>
            <input type="checkbox" name="equipment[]" value="CNC Machine(Small)"> CNC Machine(Small)<br>
            <input type="checkbox" name="equipment[]" value="Vinyl Cutter"> Vinyl Cutter<br>
            <input type="checkbox" name="equipment[]" value="Embroidery Machine(One Head)"> Embroidery Machine(One Head)<br>
            <input type="checkbox" name="equipment[]" value="Embroidery Machine(Four Heads)"> Embroidery Machine(Four Heads)<br>
            <input type="checkbox" name="equipment[]" value="Flatbed Cutter"> Flatbed Cutter<br>
            <input type="checkbox" name="equipment[]" value="Vacuum Forming"> Vacuum Forming<br>
            <input type="checkbox" name="equipment[]" value="Water Jet Machine"> Water Jet Machine<br>

            <label for="total_invoice">Total Invoice:</label>
            <input type="number" id="total_invoice" name="total_invoice" step="0.01" required>

            <label for="billing_pdf">Attach PDF:</label>
            <input type="file" id="billing_pdf" name="billing_pdf" accept="application/pdf" required>

            <button type="submit">Submit</button>
        </form>
    </div>
</div>

<!-- Feedback Form Modal -->
<div id="feedback-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeFeedbackForm()">&times;</span>
        <h2>Add Feedback</h2>
        <form action="add-feedback-handler.php" method="POST" enctype="multipart/form-data">
            <label for="client_name">Client Name:</label>
            <input type="text" id="client_name" name="client_name" required>

            <label for="feedback_pdf">Upload Feedback PDF:</label>
            <input type="file" id="feedback_pdf" name="feedback_pdf" accept=".pdf">

            <button type="submit">Submit</button>
        </form>
    </div>
</div>

<script>
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

        // Render the chart if the dashboard tab is selected
        if (tabId === 'dashboard') {
            renderSummaryChart();
        }
    }

    // Show the default or specified tab on page load
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'dashboard'; // Default to 'dashboard' if no tab is specified
        const tabTitleMap = {
            'dashboard': 'Dashboard',
            'job-description': 'Job Requests',
            'billing': 'Billings',
            'feedback': 'Feedbacks'
        };
        showTab(activeTab, tabTitleMap[activeTab]);
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

    function handleAddButton() {
        const activeTab = document.querySelector('.job-request-content.active').id;

        if (activeTab === 'job-description') {
            // Redirect to the Add Job Request page
            window.location.href = 'add_job_request.php';
        } else if (activeTab === 'billing') {
            // Show the Add Billing modal
            showBillingForm();
        } else if (activeTab === 'feedback') {
            // Show the Add Feedback modal
            showFeedbackForm();
        }
    }

    // Show the feedback form modal
    function showFeedbackForm() {
        document.getElementById('feedback-modal').style.display = 'block';
    }

    // Close the feedback form modal
    function closeFeedbackForm() {
        document.getElementById('feedback-modal').style.display = 'none';
    }

    // Close the modal if the user clicks outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('feedback-modal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };

    // Show the billing form modal
    function showBillingForm() {
        document.getElementById('billing-modal').style.display = 'block';
    }

    // Close the billing form modal
    function closeBillingForm() {
        document.getElementById('billing-modal').style.display = 'none';
    }

    // Close the modal if the user clicks outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('billing-modal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };

    // Handle form submission via AJAX
    document.getElementById('billing-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('save_billing.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Billing record added successfully!');
                    location.reload(); // Reload the page to update the billing table
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting the form.');
            });
    });

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