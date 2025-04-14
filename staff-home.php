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
include 'fetch-job_requests-handler.php';
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
        <div class="contents">
            <div class="contents-box">
                <div class="job-request-content active" id="job-description">
                    <h1 class="content-title">Job Request</h1>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Job Title</th>
                                <th>Client</th>
                                <th>Services/Equipment Availed</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Priority</th>
                                <th>PDF</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($jobRequests)): ?>
                                <?php foreach ($jobRequests as $job): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($job['id']); ?></td>
                                        <td><?php echo htmlspecialchars($job['request_title']); ?></td>
                                        <td><?php echo htmlspecialchars($job['client_name']); ?></td>
                                        <td><?php echo htmlspecialchars($job['equipment']); ?></td>
                                        <td><?php echo htmlspecialchars($job['status']); ?></td>
                                        <td><?php echo htmlspecialchars($job['request_date']); ?></td>
                                        <td><?php echo htmlspecialchars($job['priority']); ?></td>
                                        <td>
                                            <?php if (!empty($job['reference_file'])): ?>
                                                <a href="uploads/job_requests/<?php echo htmlspecialchars($job['reference_file']); ?>" target="_blank">View PDF</a>
                                            <?php else: ?>
                                                <span class="no-pdf">None</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button onclick="viewJobRequest(<?php echo $job['id']; ?>)">View</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9">No job requests found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="job-request-content" id="billing">
                    <h1 class="content-title">Billing</h1>
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
                                    <td>
                                        <button onclick="editBilling(<?php echo $row['id']; ?>)">Edit</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="job-request-content" id="feedback">
                    <h1 class="content-title">Feedback</h1>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Comments</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($feedbackRows)): ?>
                                <?php foreach ($feedbackRows as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['client_name']); ?></td>
                                        <td>
                                            <?php if (!empty($row['feedback_pdf'])): ?>
                                                <a href="uploads/feedback/<?php echo htmlspecialchars($row['feedback_pdf']); ?>" target="_blank">View PDF</a>
                                            <?php else: ?>
                                                <span class="no-pdf">No Comments</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['feedback_date']); ?></td>
                                        <td><button onclick="editFeedback(<?php echo htmlspecialchars($row['id']); ?>)">Edit</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No feedback available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="job-request-content active" id="dashboard">
                    <canvas id="summaryChart"></canvas>
                </div>
            </div>
            <div class="floating-add-button" id="add-button" onclick="handleAddButton()">Add</div>
        </div>
    </div>
</div>

<!-- Billing Form Modal -->
<div id="billing-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeBillingForm()">&times;</span>
        <h2 id="billing-modal-title">Add Billing</h2>
        <form id="billing-form" action="add-billing-handler.php" method="POST" enctype="multipart/form-data">
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
            <input type="checkbox" name="equipment[]" value="CNC Machine (Big)"> CNC Machine (Big)<br>
            <input type="checkbox" name="equipment[]" value="CNC Machine (Small)"> CNC Machine (Small)<br>
            <input type="checkbox" name="equipment[]" value="Vinyl Cutter"> Vinyl Cutter<br>
            <input type="checkbox" name="equipment[]" value="Embroidery Machine (One Head)"> Embroidery Machine (One Head)<br>
            <input type="checkbox" name="equipment[]" value="Embroidery Machine (Four Heads)"> Embroidery Machine (Four Heads)<br>
            <input type="checkbox" name="equipment[]" value="Flatbed Cutter"> Flatbed Cutter<br>
            <input type="checkbox" name="equipment[]" value="Vacuum Forming"> Vacuum Forming<br>
            <input type="checkbox" name="equipment[]" value="Water Jet Machine"> Water Jet Machine<br>

            <label for="total_invoice">Total Invoice:</label>
            <input type="number" id="total_invoice" name="total_invoice" step="0.01" required>

            <label for="billing_pdf">Attach PDF:</label>
            <input type="file" id="billing_pdf" name="billing_pdf" accept="application/pdf">

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

    // Ensure the floating button is visible only for the active tab
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.job-request-content');
        tabs.forEach(tab => {
            const addButton = tab.querySelector('.floating-add-button');
            if (addButton) {
                if (tab.classList.contains('active')) {
                    addButton.style.display = 'block';
                } else {
                    addButton.style.display = 'none';
                }
            }
        });
    });

    function showTab(tabId, title) {
        console.log(`Switching to tab: ${tabId}, Title: ${title}`); // Debugging

        // Hide all job request content
        document.querySelectorAll('.job-request-content').forEach(function(content) {
            content.style.display = 'none';
            content.classList.remove('active');
        });

        // Show the selected tab content
        const activeTab = document.getElementById(tabId);
        activeTab.style.display = 'block';
        activeTab.classList.add('active');

        // // Update the title inside the active tab
        // const contentTitle = activeTab.querySelector('.content-title');
        // if (contentTitle) {
        //     contentTitle.innerText = title;
        // } else {
        //     console.warn('Content title element not found in the active tab.');
        // }

        // Remove active class from all buttons
        document.querySelectorAll('.user-content .button').forEach(function(button) {
            button.classList.remove('active');
        });

        // Add active class to the clicked button
        document.querySelector(`.user-content .button[onclick="showTab('${tabId}', '${title}')"]`).classList.add('active');

        // Update the "Add" button text based on the active tab
        const addButton = document.getElementById('add-button');
        if (addButton) {
            if (tabId === 'job-description') {
                addButton.innerText = 'Add Job Request';
            } else if (tabId === 'billing') {
                addButton.innerText = 'Add Billing';
            } else if (tabId === 'feedback') {
                addButton.innerText = 'Add Feedback';
            } else {
                addButton.innerText = 'Add';
            }
        } else {
            console.error('Add button not found in the DOM'); // Debugging
        }

        // Render the chart if the dashboard tab is selected
        if (tabId === 'dashboard') {
            renderSummaryChart();
        }
    }

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

    function editBilling(id) {
        // Fetch the billing data using AJAX
        fetch(`fetch-billing-handler.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate the modal fields with the fetched data
                    document.getElementById('client_name').value = data.billing.client_name;
                    document.getElementById('billing_date').value = data.billing.billing_date;
                    document.querySelector(`input[name="client_profile"][value="${data.billing.client_profile}"]`).checked = true;

                    // Populate equipment checkboxes
                    const equipment = data.billing.equipment.split(', ');
                    document.querySelectorAll('input[name="equipment[]"]').forEach(checkbox => {
                        checkbox.checked = equipment.includes(checkbox.value);
                    });

                    document.getElementById('total_invoice').value = data.billing.total_invoice;

                    // Show the modal
                    document.getElementById('billing-modal').style.display = 'block';

                    // Add a hidden input for the billing ID
                    let billingIdInput = document.getElementById('billing_id');
                    if (!billingIdInput) {
                        billingIdInput = document.createElement('input');
                        billingIdInput.type = 'hidden';
                        billingIdInput.id = 'billing_id';
                        billingIdInput.name = 'billing_id';
                        document.getElementById('billing-form').appendChild(billingIdInput);
                    }
                    billingIdInput.value = data.billing.id;

                    // Update the modal title to indicate editing
                    document.getElementById('billing-modal-title').innerText = 'Edit Billing';
                } else {
                    alert('Error fetching billing data: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching the billing data.');
            });
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

    // Confirmation dialog for billing form submission
    document.getElementById('billing-form').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        // Show confirmation dialog
        const confirmation = confirm('Are you sure you want to submit these billing details?');
        if (confirmation) {
            // If the user clicks "Yes", submit the form
            this.submit();
            alert('Billing details submitted successfully!');
        } else {
            // If the user clicks "No", do nothing
            alert('Submission canceled.');
        }
    });

    // Confirmation dialog for feedback form submission
    document.getElementById('feedback-modal').querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        // Show confirmation dialog
        const confirmation = confirm('Are you sure you want to submit this feedback?');
        if (confirmation) {
            // If the user clicks "Yes", submit the form
            this.submit();
            alert('Feedback submitted successfully!');
        } else {
            // If the user clicks "No", do nothing
            alert('Submission canceled.');
        }
    });

    // Handle form submission via AJAX
    // document.getElementById('billing-form').addEventListener('submit', function(e) {
    //     e.preventDefault();

    //     const formData = new FormData(this);

    //     fetch('save_billing.php', {
    //             method: 'POST',
    //             body: formData
    //         })
    //         .then(response => response.json())
    //         .then(data => {
    //             if (data.success) {
    //                 alert('Billing record added successfully!');
    //                 location.reload(); // Reload the page to update the billing table
    //             } else {
    //                 alert('Error: ' + data.message);
    //             }
    //         })
    //         .catch(error => {
    //             console.error('Error:', error);
    //             alert('An error occurred while submitting the form.');
    //         });
    // });
</script>
</body>

</html>