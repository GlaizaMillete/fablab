<?php
session_name('staff_session'); 
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

// Display the success message if it exists
if (isset($_SESSION['login_success'])) {
    echo '<script>alert("' . $_SESSION['login_success'] . '");</script>';
    unset($_SESSION['login_success']); // Clear the message after displaying it
}

// Fetch billing records directly in staff-home.php
$result = $conn->query("SELECT * FROM billing ORDER BY billing_date ASC");
$billingRows = [];
while ($row = $result->fetch_assoc()) {
    $billingRows[] = $row;
}

// Fetch Job Requests
$jobRequests = [];
$sql = "SELECT * FROM job_requests ORDER BY request_date ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jobRequests[] = $row;
    }
}

// include 'fetch-billing-handler.php';
include 'fetch-repository-handler.php';
// include 'fetch-job_requests-handler.php';
?>

<div class="container">
    <div class="container-left">
        <div class="header">
            <img src="FABLAB_LOGO.png" alt="Description of image" class="admin-image">
            <div class="header-text">
                <p>Hello <b style="color: #F37123;"><?php echo htmlspecialchars($_SESSION['staff_name']); ?>!</b><br>
                <p style="color: white;">Staff</p>
            </div>
        </div>
        <hr style="color: white; width: 80%;">
        <div class="user-content">
            <!-- <div class="button" onclick="showTab('dashboard', 'Dashboard')">
                <p>Dashboard</p>
            </div> -->
            <div class="button" onclick="showTab('job-description', 'Client Profile and Service Requests')">
                <p>Client Profile &<br>Service Requests</p>
            </div>
            <div class="button" onclick="showTab('billing', 'Payment and Release')">
                <p>Payment and Release</p>
            </div>
            <div class="button" onclick="showTab('repository', 'Repository')">
                <p>Repository</p>
            </div>
        </div>
        <div class="button" onclick="confirmLogout()">
            <p class="logout">Logout</p>
        </div>
    </div>
    <div class="container-right">
        <div class="contents">
            <div class="contents-box">
                <div class="job-request-content active" id="job-description">
                    <div class="section-title">
                        <h1>Client Profile & Service Requests</h1>
                        <div class="search-full">
                            <input type="text" class="search-input" id="jobRequestsSearch" placeholder="Search..." onkeyup="filterJobRequestsTable()">
                            <button class="btn-blue" onclick="redirectToPage()">Go to Full Page</button>
                        </div>
                    </div>
                    <table id="jobRequestsTable">
                        <thead>
                            <tr>
                                <th onclick="sortJobRequestsTable(0)">Date Requested</th>
                                <th onclick="sortJobRequestsTable(1)">Client Name</th>
                                <th onclick="sortJobRequestsTable(2)">Service Request</th>
                                <th onclick="sortJobRequestsTable(3)">Client Profile</th>
                                <th onclick="sortJobRequestsTable(4)">Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($jobRequests)): ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; color:#888;">No client profile & service request data is available. Go to Full Page to be able to add, edit, or delete client profile & service request data.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($jobRequests as $request): ?>
                                    <tr>
                                        <td><?= date("F d, Y", strtotime($request['request_date'])) ?></td>
                                        <td><?= htmlspecialchars($request['client_name']) ?></td>
                                        <td>
                                            <?php
                                            $serviceRequest = htmlspecialchars($request['service_requested']);
                                            if (!empty($request['equipment'])) {
                                                $serviceRequest .= ": " . htmlspecialchars($request['equipment']);
                                            }
                                            if (!empty($request['hand_tools_other'])) {
                                                $serviceRequest .= ": " . htmlspecialchars($request['hand_tools_other']);
                                            }
                                            if (!empty($request['equipment_other'])) {
                                                $serviceRequest .= ": " . htmlspecialchars($request['equipment_other']);
                                            }
                                            echo $serviceRequest;
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            // Check if the designation is "Others" and display only the "designation_other" value
                                            if ($request['designation'] === "Others" && !empty($request['designation_other'])) {
                                                echo htmlspecialchars($request['designation_other']);
                                            } else {
                                                echo htmlspecialchars($request['designation']);
                                            }
                                            ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php if (!empty($request['reference_file'])): ?>
                                                <button onclick="window.open('uploads/job-requests/<?= htmlspecialchars($request['reference_file']) ?>', '_blank')">View</button>
                                            <?php else: ?>
                                                None
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="job-request-content" id="billing">
                    <div class="section-title">
                        <h1>Payment and Release</h1>
                        <div class="search-full">
                            <input type="text" class="search-input" id="billingSearch" placeholder="Search..." onkeyup="filterBillingTable()">
                            <button class="btn-blue" onclick="redirectToPage()">Go to Full Page</button>
                        </div>
                    </div>
                    <table id="billingTable">
                        <thead>
                            <tr>
                                <th onclick="sortBillingTable(0)">Date</th>
                                <th onclick="sortBillingTable(1)">No.</th>
                                <th onclick="sortBillingTable(2)">Client</th>
                                <th onclick="sortBillingTable(3)">Service Description</th>
                                <th onclick="sortBillingTable(4)">Profile</th>
                                <th onclick="sortBillingTable(5)">Total Amount</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($billingRows)): ?>
                                <tr>
                                    <td colspan="7" style="text-align:center; color:#888;">No payment and release data is available. Go to Full Page to be able to add, edit, or delete payment and release data.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($billingRows as $row): ?>
                                    <tr>
                                        <td><?php echo date("F d, Y", strtotime($row['billing_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['no']); ?></td>
                                        <td><?php echo htmlspecialchars($row['client_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                                        <td><?php echo htmlspecialchars(trim($row['client_profile'])); ?></td>
                                        <td>&#8369;<?php echo number_format($row['total_invoice'], 2); ?></td>
                                        <td style="text-align: center;">
                                            <?php if (!empty($row['billing_pdf'])): ?>
                                                <button onclick="window.open('uploads/billing/<?php echo htmlspecialchars($row['billing_pdf']); ?>', '_blank')">View</button>
                                            <?php else: ?>
                                                <span class="no-pdf">None</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="job-request-content" id="repository">
                    <div class="section-title">
                        <h1>Repository</h1>
                        <div class="search-full">
                            <input type="text" class="search-input" id="repositorySearch" placeholder="Search..." onkeyup="filterTable()">
                            <button class="btn-green" onclick="showRepositoryForm()">Add</button>
                        </div>
                    </div>
                    <!-- <div>
                        <input type="text" id="repositorySearch" placeholder="Search..." onkeyup="filterTable()" style="margin-bottom: 10px; width: 100%; padding: 8px 0; text-indent: 16px;">
                    </div> -->
                    <table id="repositoryTable">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0)">Date</th>
                                <th onclick="sortTable(1)">Name</th>
                                <th onclick="sortTable(2)">Type</th>
                                <th onclick="sortTable(3)">URL/Directory</th>
                                <th onclick="sortTable(4)">Note</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($repositoryRows)): ?>
                                <tr>
                                    <td colspan="6" style="text-align:center; color:#888;">No repository listings available.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($repositoryRows as $row): ?>
                                    <tr>
                                        <td><?php echo date("F d, Y", strtotime($row['date'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['listing_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['listing_type']); ?></td>
                                        <td style="text-align: center;">
                                            <?php if (filter_var($row['reference_file'], FILTER_VALIDATE_URL)): ?>
                                                <button onclick="window.open('<?php echo htmlspecialchars($row['reference_file']); ?>', '_blank')">Open</button>
                                            <?php else: ?>
                                                <button onclick="openDirectory('<?php echo addslashes($row['reference_file']); ?>')">Open</button>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['note']); ?></td>
                                        <td>
                                            <button class="btn-yellow" onclick="editRepository(<?php echo $row['id']; ?>)">Edit</button>
                                            <button class="btn-red" onclick="deleteRepository(<?php echo $row['id']; ?>)">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
<!-- <div id="billing-modal" class="modal">
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
</div> -->

<!-- Feedback Form Modal -->
<!-- <div id="feedback-modal" class="modal">
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
</div> -->

<!-- Repository Form Modal -->
<div id="repository-modal" class="modal">
    <div class="modal-content repository-form">
        <span class="close" onclick="closeRepositoryForm()">&times;</span>
        <h2>Add/Edit Repository</h2>
        <form action="add-repository-handler.php" method="POST">
            <input type="hidden" id="repository_id" name="repository_id"> <!-- Hidden input for ID -->
            <label for="listing_name">Name:</label>
            <input type="text" id="listing_name" name="listing_name" required>

            <label for="listing_type">Type:</label>
            <input type="text" id="listing_type" name="listing_type" required>

            <label for="reference_file">File Directory/URL:</label>
            <input type="text" id="reference_file" name="reference_file" placeholder="e.g., https://example.com/file.pdf or C:\files\document.pdf" required>

            <label for="note">Note:</label>
            <textarea id="note" name="note" rows="4"></textarea>

            <button type="submit" class="btn-green">Submit</button>
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
        } else if (activeTab === 'repository') {
            // Show the Add Repository modal
            showRepositoryForm();
        }
    }

    function redirectToPage() {
        const activeTab = document.querySelector('.job-request-content.active').id;

        if (activeTab === 'job-description') {
            // Redirect to the Job Requests page
            window.location.href = 'job-requests.php';
        } else if (activeTab === 'billing') {
            // Redirect to the Billing Records page
            window.location.href = 'records.php';
        } else {
            alert('No specific page associated with this tab.');
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
            } else if (tabId === 'repository') {
                addButton.innerText = 'Add Repository';
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
        const activeTab = urlParams.get('tab') || 'job-description'; // Default to 'job-description' (Job Requests tab)
        const tabTitleMap = {
            'job-description': 'Client Profile and Service Requests',
            'billing': 'Payment and Release',
            'repository': 'Repository'
        };
        showTab(activeTab, tabTitleMap[activeTab] || 'Client Profile and Service Requests');
    });

    function showRepositoryForm() {
        const modal = document.getElementById('repository-modal');
        modal.style.display = 'block';

        // Reset the form fields
        const form = modal.querySelector('form');
        form.reset();

        // Clear the hidden repository ID field
        document.getElementById('repository_id').value = '';

        // Set the modal title to "Add Repository Listing"
        document.querySelector('#repository-modal h2').innerText = 'Add Repository Listing';
    }

    function closeRepositoryForm() {
        const modal = document.getElementById('repository-modal');
        modal.style.display = 'none';

        // Reset the form fields
        const form = modal.querySelector('form');
        form.reset();

        // Clear the hidden repository ID field
        document.getElementById('repository_id').value = '';

        // Reset the modal title
        document.querySelector('#repository-modal h2').innerText = 'Edit Repository Listing';
    }

    function openDirectory(directoryPath) {
        fetch('open-directory-handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    path: directoryPath
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Directory opened successfully');
                } else {
                    alert('Failed to open directory: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while trying to open the directory.');
            });
    }

    function editRepository(id) {
        // Fetch the repository data using AJAX
        fetch(`fetch-repository-handler.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate the modal fields with the fetched data
                    document.getElementById('listing_name').value = data.repository.listing_name;
                    document.getElementById('listing_type').value = data.repository.listing_type;
                    document.getElementById('reference_file').value = data.repository.reference_file;
                    document.getElementById('note').value = data.repository.note;

                    // Add a hidden input for the repository ID
                    let repositoryIdInput = document.getElementById('repository_id');
                    if (!repositoryIdInput) {
                        repositoryIdInput = document.createElement('input');
                        repositoryIdInput.type = 'hidden';
                        repositoryIdInput.id = 'repository_id';
                        repositoryIdInput.name = 'repository_id';
                        document.querySelector('#repository-modal form').appendChild(repositoryIdInput);
                    }
                    repositoryIdInput.value = id;

                    // Show the modal
                    document.getElementById('repository-modal').style.display = 'block';
                } else {
                    alert('Error fetching repository data: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching the repository data.');
            });
    }

    function deleteRepository(id) {
        if (confirm('Are you sure you want to delete this repository entry?')) {
            // Send a request to delete the repository entry
            fetch(`delete-repository-handler.php?id=${id}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Repository entry deleted successfully!');
                        location.reload(); // Reload the page to update the table
                    } else {
                        alert('Error deleting repository entry: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the repository entry.');
                });
        }
    }

    // Function to sort the table by column
    function sortTable(columnIndex) {
        const table = document.getElementById("repositoryTable");
        const rows = Array.from(table.rows).slice(1); // Exclude the header row
        const isAscending = table.getAttribute("data-sort-order") === "asc";
        const direction = isAscending ? 1 : -1;

        rows.sort((a, b) => {
            const aText = a.cells[columnIndex].innerText.trim();
            const bText = b.cells[columnIndex].innerText.trim();

            // Handle date sorting
            if (columnIndex === 0) {
                return direction * (new Date(aText) - new Date(bText));
            }

            // Handle text sorting
            return direction * aText.localeCompare(bText);
        });

        // Append sorted rows back to the table
        const tbody = table.querySelector("tbody");
        rows.forEach(row => tbody.appendChild(row));

        // Toggle sort order
        table.setAttribute("data-sort-order", isAscending ? "desc" : "asc");
    }

    // Function to filter the table based on search input
    function filterTable() {
        const input = document.getElementById("repositorySearch");
        const filter = input.value.toLowerCase();
        const table = document.getElementById("repositoryTable");
        const rows = table.getElementsByTagName("tr");

        for (let i = 1; i < rows.length; i++) { // Skip the header row
            const cells = rows[i].getElementsByTagName("td");
            let isVisible = false;

            for (let j = 0; j < cells.length - 1; j++) { // Exclude the Actions column
                if (cells[j] && cells[j].innerText.toLowerCase().includes(filter)) {
                    isVisible = true;
                    break;
                }
            }

            rows[i].style.display = isVisible ? "" : "none";
        }
    }

    // Function to filter the Job Requests table
    function filterJobRequestsTable() {
        const input = document.getElementById("jobRequestsSearch");
        const filter = input.value.toLowerCase();
        const table = document.getElementById("jobRequestsTable");
        const rows = table.getElementsByTagName("tr");

        for (let i = 1; i < rows.length; i++) { // Skip the header row
            const cells = rows[i].getElementsByTagName("td");
            let isVisible = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j] && cells[j].innerText.toLowerCase().includes(filter)) {
                    isVisible = true;
                    break;
                }
            }

            rows[i].style.display = isVisible ? "" : "none";
        }
    }

    // Function to filter the Billing table
    function filterBillingTable() {
        const input = document.getElementById("billingSearch");
        const filter = input.value.toLowerCase();
        const table = document.getElementById("billingTable");
        const rows = table.getElementsByTagName("tr");

        for (let i = 1; i < rows.length; i++) { // Skip the header row
            const cells = rows[i].getElementsByTagName("td");
            let isVisible = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j] && cells[j].innerText.toLowerCase().includes(filter)) {
                    isVisible = true;
                    break;
                }
            }

            rows[i].style.display = isVisible ? "" : "none";
        }
    }

    // Function to sort the Job Requests table
    function sortJobRequestsTable(columnIndex) {
        const table = document.getElementById("jobRequestsTable");
        const rows = Array.from(table.rows).slice(1); // Exclude the header row
        const isAscending = table.getAttribute("data-sort-order") === "asc";
        const direction = isAscending ? 1 : -1;

        rows.sort((a, b) => {
            const aText = a.cells[columnIndex].innerText.trim();
            const bText = b.cells[columnIndex].innerText.trim();

            // Handle date sorting
            if (columnIndex === 0) {
                return direction * (new Date(aText) - new Date(bText));
            }

            // Handle text sorting
            return direction * aText.localeCompare(bText);
        });

        // Append sorted rows back to the table
        const tbody = table.querySelector("tbody");
        rows.forEach(row => tbody.appendChild(row));

        // Toggle sort order
        table.setAttribute("data-sort-order", isAscending ? "desc" : "asc");
    }

    // Function to sort the Billing table
    function sortBillingTable(columnIndex) {
        const table = document.getElementById("billingTable");
        const rows = Array.from(table.rows).slice(1); // Exclude the header row
        const isAscending = table.getAttribute("data-sort-order") === "asc";
        const direction = isAscending ? 1 : -1;

        rows.sort((a, b) => {
            const aText = a.cells[columnIndex].innerText.trim();
            const bText = b.cells[columnIndex].innerText.trim();

            // Handle date sorting
            if (columnIndex === 0) {
                return direction * (new Date(aText) - new Date(bText));
            }

            // Handle numeric sorting for Total Amount
            if (columnIndex === 5) {
                return direction * (parseFloat(aText.replace(/[^0-9.-]+/g, "")) - parseFloat(bText.replace(/[^0-9.-]+/g, "")));
            }

            // Handle text sorting
            return direction * aText.localeCompare(bText);
        });

        // Append sorted rows back to the table
        const tbody = table.querySelector("tbody");
        rows.forEach(row => tbody.appendChild(row));

        // Toggle sort order
        table.setAttribute("data-sort-order", isAscending ? "desc" : "asc");
    }

    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = 'staff-logout.php';
        }
    }
</script>
</body>

</html>