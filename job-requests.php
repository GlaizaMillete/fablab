<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}
include 'config.php'; // Include the database connection

// Check if the user is logged in as staff
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    // Redirect to the staff login page if not logged in
    header("Location: staff-login.php");
    exit();
}

$whereClauses = [];
if (!empty($_GET['from_month']) && !empty($_GET['to_month'])) {
    $from_month = intval($_GET['from_month']);
    $to_month = intval($_GET['to_month']);
    $whereClauses[] = "MONTH(request_date) BETWEEN $from_month AND $to_month";
}
if (!empty($_GET['year'])) {
    $year = intval($_GET['year']);
    $whereClauses[] = "YEAR(request_date) = $year";
}
if (!empty($_GET['designation'])) {
    $designation = $conn->real_escape_string($_GET['designation']);
    $whereClauses[] = "designation = '$designation'";
}
if (!empty($_GET['service_requested'])) {
    $service_requested = $conn->real_escape_string($_GET['service_requested']);
    $whereClauses[] = "service_requested LIKE '%$service_requested%'";
}
if (!empty($_GET['search_name'])) {
    $search_name = $conn->real_escape_string(trim($_GET['search_name']));
    $whereClauses[] = "LOWER(client_name) LIKE LOWER('%$search_name%')";
}

$whereClause = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";
$sql = "SELECT * FROM job_requests $whereClause ORDER BY request_date ASC"; // Changed to ASC for oldest to newest
$result = $conn->query($sql);

$jobRequests = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jobRequests[] = $row;
    }
}

// Calculate totals for job requests
$totalRequests = count($jobRequests);

// Initialize status counts
$statusCounts = [
    'Pending' => 0,
    'In Progress' => 0,
    'Completed' => 0,
    'Cancelled' => 0
];

foreach ($jobRequests as $request) {
    if (isset($request['status']) && isset($statusCounts[$request['status']])) {
        $statusCounts[$request['status']]++;
    }
}

// For the chart data (used in JavaScript later)
$chartData = [
    'labels' => array_keys($statusCounts),
    'data' => array_values($statusCounts),
    'colors' => [
        'rgba(245, 158, 11, 0.8)', // Pending (warning)
        'rgba(37, 99, 235, 0.8)',   // In Progress (primary)
        'rgba(16, 185, 129, 0.8)',  // Completed (success)
        'rgba(239, 68, 68, 0.8)'    // Cancelled (danger)
    ]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Profile and Service Request Records</title>
    <link rel="icon" href="fablab.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style3.css">
</head>

<body>
    <div class="back-button">
        <a href="staff-home.php">&larr; Back</a>
    </div>
    <div class="container">
        <h1>Client Profile and Service Request</h1>
        <!-- The Modal -->
        <div id="jobRequestModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Client Profile and Service Request Form</h2>
                <form id="jobRequestForm" enctype="multipart/form-data" action="add-job_request-handler.php" method="POST">
                    <input type="hidden" name="id"> <!-- Hidden input for ID -->
                    <h3>Personal Information</h3>
                    <div class="form-columns">
                        <div>
                            <label>Name:</label>
                            <input type="text" name="personal_name" required>
                        </div>
                        <div>
                            <label>Address:</label>
                            <input type="text" name="address" required>
                        </div>
                    </div>
                    <div class="form-columns">
                        <div>
                            <label>Contact No:</label>
                            <input type="number" name="contact_no" required>
                        </div>
                        <br>
                        <div>
                            <label>Gender:</label>
                            <input type="radio" name="gender" value="Male" required> Male<br>
                            <input type="radio" name="gender" value="Female" required> Female<br>
                            <input type="radio" name="gender" value="Prefer not to say"> Prefer not to say<br>
                            <input type="text" name="gender_optional" placeholder="Optional if 'Prefer not to say'" style="margin-top: 5px;">
                        </div>
                    </div>
                    <br>
                    <div class="form-columns">
                        <div>
                            <label>Age:</label>
                            <input type="number" name="age" required>
                        </div>
                        <br>
                        <div>
                            <label>Work/Position/Designation:</label>
                            <input type="radio" name="designation" value="Student" required> Student<br>
                            <input type="radio" name="designation" value="MSME/Entrepreneur" required> MSME/Entrepreneur<br>
                            <input type="radio" name="designation" value="Teacher" required> Teacher<br>
                            <input type="radio" name="designation" value="Hobbyist" required> Hobbyist<br>
                            <input type="radio" name="designation" value="Others"> Others (Please Specify):
                            <input type="text" name="designation_other" placeholder="Enter your designation" disabled>
                        </div>
                    </div>
                    <br>
                    <div>
                        <label>Company/Affiliated with:</label>
                        <input type="text" name="company" required>
                    </div>
                    <br>
                    <div>
                        <label>Service Requested:</label>
                        <input type="checkbox" name="service_requested[]" value="Training/Tour/Orientation"> Training/Tour/Orientation<br>
                        <input type="checkbox" name="service_requested[]" value="Product/Design/Consultation"> Product/Design/Consultation<br>
                        <input type="checkbox" name="service_requested[]" value="Equipment"> Equipment<br>
                        <div style="margin-left: 20px;">
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
                            <input type="checkbox" name="equipment[]" value="Hand Tools"> Hand Tools (Please specify):
                            <input type="text" name="hand_tools_other"><br>
                            <input type="checkbox" name="equipment[]" value="Other"> Other (Please specify):
                            <input type="text" name="equipment_other">
                        </div>
                    </div>
                    <br>
                    <div>
                        <label>Other Details:</label>
                        <label>If consultation, what mode of meeting do you prefer?</label>
                        <input type="radio" name="consultation_mode" value="Virtual"> Virtual<br>
                        <input type="radio" name="consultation_mode" value="Face to Face"> Face to Face<br>
                        <input type="text" name="consultation_schedule" placeholder="Specify your schedule (date and time)">
                    </div>
                    <br>
                    <div>
                        <label>If equipment utilization, please specify your schedule:</label>
                        <input class="datetime-local" type="datetime-local" name="equipment_schedule">
                    </div>
                    <br>
                    <div>
                        <label>Describe the work requested:</label>
                        <textarea class="work_description" name="work_description" required></textarea>
                    </div>
                    <br>
                    <div>
                        <label>Date:</label>
                        <input type="date" name="date" required>
                    </div>
                    <br>
                    <div>
                        <label>Client Name:</label>
                        <input type="text" name="client_name" required>
                    </div>
                    <br>
                    <div>
                        <label>Reference File:</label>
                        <input type="file" name="reference_file" accept=".pdf,.jpg,.jpeg,.png,.zip">
                    </div>
                    <br>
                    <h3>For FAB LAB Personnel Only</h3>
                    <div class="form-columns">
                        <div>
                            <label>Name of Personnel:</label>
                            <input type="text" name="personnel_name" value="<?= isset($_SESSION['staff_name']) ? htmlspecialchars($_SESSION['staff_name']) : '' ?>" readonly>
                        </div>
                        <div>
                            <label>Date:</label>
                            <input type="date" name="personnel_date">
                        </div>
                    </div>
                    <br>
                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
        <div class="dashboard">
            <div class="chart-container">
                <canvas id="requestChart"></canvas>
            </div>
            <div class="totals-card">
                <h3>Data Visualization</h3>
                <div style="margin-bottom: 12px;">
                    <label for="graphColumn">Column:</label>
                    <select id="graphColumn">
                        <option value="designation">Client Profile</option>
                        <option value="service_requested">Service Requested</option>
                    </select>
                </div>
                <label>Column Details:</label>
                <div id="columnDetails">
                    <!-- Column details will be displayed here -->
                </div>
            </div>
        </div>

        <h2>Filter Client Profile and Service Requests</h2>
        <div class="filter-section">
            <form method="GET" class="filter-grid">
                <div>
                    <label>From Month:</label>
                    <select name="from_month">
                        <option value="">All</option>
                        <?php for ($m = 1; $m <= 12; $m++) {
                            $monthName = date("F", mktime(0, 0, 0, $m, 1));
                            echo "<option value='$m'" . (isset($_GET['from_month']) && $_GET['from_month'] == $m ? ' selected' : '') . ">$monthName</option>";
                        } ?>
                    </select>
                </div>
                <div>
                    <label>To Month:</label>
                    <select name="to_month">
                        <option value="">All</option>
                        <?php for ($m = 1; $m <= 12; $m++) {
                            $monthName = date("F", mktime(0, 0, 0, $m, 1));
                            echo "<option value='$m'" . (isset($_GET['to_month']) && $_GET['to_month'] == $m ? ' selected' : '') . ">$monthName</option>";
                        } ?>
                    </select>
                </div>
                <div>
                    <label>Year:</label>
                    <select name="year">
                        <option value="">All</option>
                        <?php for ($y = date('Y'); $y >= 2016; $y--) {
                            echo "<option value='$y'" . (isset($_GET['year']) && $_GET['year'] == $y ? ' selected' : '') . ">$y</option>";
                        } ?>
                    </select>
                </div>
                <!-- <div>
                    <label>Designation:</label>
                    <select name="designation">
                        <option value="">All</option>
                        <option value="Student" <?= isset($_GET['designation']) && $_GET['designation'] == 'Student' ? 'selected' : '' ?>>Student</option>
                        <option value="MSME/Entrepreneur" <?= isset($_GET['designation']) && $_GET['designation'] == 'MSME/Entrepreneur' ? 'selected' : '' ?>>MSME/Entrepreneur</option>
                        <option value="Teacher" <?= isset($_GET['designation']) && $_GET['designation'] == 'Teacher' ? 'selected' : '' ?>>Teacher</option>
                        <option value="Hobbyist" <?= isset($_GET['designation']) && $_GET['designation'] == 'Hobbyist' ? 'selected' : '' ?>>Hobbyist</option>
                        <option value="Others" <?= isset($_GET['designation']) && $_GET['designation'] == 'Others' ? 'selected' : '' ?>>Others</option>
                    </select>
                </div>
                <div>
                    <label>Service Request:</label>
                    <select name="service_requested">
                        <option value="">All</option>
                        <?php
                        // Fetch distinct service_requested values from the database
                        $serviceRequests = $conn->query("SELECT DISTINCT service_requested FROM job_requests");
                        if ($serviceRequests->num_rows > 0) {
                            while ($row = $serviceRequests->fetch_assoc()) {
                                $selected = isset($_GET['service_requested']) && $_GET['service_requested'] == $row['service_requested'] ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($row['service_requested']) . "' $selected>" . htmlspecialchars($row['service_requested']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div> -->
                <div class="filter-div">
                    <button type="submit">Filter</button>
                </div>
            </form>
        </div>

        <h2>Search Client Name</h2>
        <div class="search-section">
            <form method="GET" class="search-form">
                <input type="text" name="search_name" placeholder="Enter client name"
                    value="<?= isset($_GET['search_name']) ? htmlspecialchars($_GET['search_name']) : '' ?>"
                    style="flex-grow: 1;">
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="request-table">
            <h2>Client Profile and Service Requests</h2>
            <button class="cpsc_button" id="openFormBtn">Add New Request</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Request Date</th>
                    <th>Client Name</th>
                    <th>Service Request</th>
                    <th>Client Profile</th>
                    <th>Reference</th>
                    <th>Action</th> <!-- Add Action Column -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobRequests as $request): ?>
                    <tr data-id="<?= $request['id'] ?>">
                        <td><?= date("F d, Y", strtotime($request['request_date'])) ?></td>
                        <td><?= htmlspecialchars($request['client_name']) ?></td>
                        <td>
                            <?php
                            // Start with the service requested
                            $serviceRequest = htmlspecialchars($request['service_requested']);

                            // Append equipment if available
                            if (!empty($request['equipment'])) {
                                $serviceRequest .= ": " . htmlspecialchars($request['equipment']);
                            }

                            // Append hand tools if available, ensuring no redundancy
                            if (!empty($request['hand_tools_other'])) {
                                $serviceRequest .= ": " . htmlspecialchars($request['hand_tools_other']);
                            }

                            // Append other equipment if available
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
                        <td>
                            <?php if (!empty($request['reference_file'])): ?>
                                <a href="uploads/job-requests/<?= htmlspecialchars($request['reference_file']) ?>" class="ref-link" target="_blank">View File</a>
                            <?php else: ?>
                                None
                            <?php endif; ?>
                        </td>
                        <td class="action-container">
                            <button class="view-btn" data-id="<?= $request['id'] ?>">View</button>
                            <button class="edit-btn" data-id="<?= $request['id'] ?>">Edit</button>
                            <button class="delete-btn" data-id="<?= $request['id'] ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add the View Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content" style="position: relative;">
            <span class="close-view">&times;</span>
            <h2 style="text-align: center;">View Job Request Details</h2>
            <div id="viewDetails">
                <!-- Data will be dynamically populated here -->
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        const modal = document.getElementById("jobRequestModal");
        const btn = document.getElementById("openFormBtn");
        const span = document.getElementsByClassName("close")[0];
        const form = document.getElementById("jobRequestForm");

        // Open modal for adding a new request
        btn.onclick = function() {
            // Reset the form fields
            form.reset();

            // Remove the hidden ID field if it exists
            const hiddenIdField = document.querySelector('[name="id"]');
            if (hiddenIdField) {
                hiddenIdField.remove();
            }

            // Show the modal
            modal.style.display = "block";
        };

        // Close the modal
        span.onclick = function() {
            modal.style.display = "none";
        };

        // Close the modal when clicking outside of it
        // window.onclick = function(event) {
        //     if (event.target == modal) {
        //         modal.style.display = "none";
        //     }
        // };

        // Chart.js configuration
        const ctx = document.getElementById('requestChart').getContext('2d');
        let requestChart;

        // Function to fetch and update chart data
        function updateChart(column) {
            // Get the current URL's query string
            const currentQueryString = window.location.search;

            // Construct the fetch URL, including the column and existing filters
            // Ensure we don't duplicate the 'column' parameter if it's already in the query string
            const fetchUrl = `fetch-graph-data.php?column=${column}${currentQueryString ? '&' + currentQueryString.substring(1) : ''}`;

            fetch(fetchUrl)
                .then(response => {
                    if (!response.ok) {
                        // Handle HTTP errors
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        console.error('Error fetching chart data:', data.error);
                        // Optionally display an error message to the user
                        return;
                    }

                    const labels = data.labels;
                    const values = data.values;
                    // Define a set of colors. You might need more colors if you have many categories.
                    // Consider generating colors dynamically or using a color palette library for more categories.
                    const colors = [
                        'rgba(245, 158, 11, 0.8)', // Pending/Category 1
                        'rgba(37, 99, 235, 0.8)', // In Progress/Category 2
                        'rgba(16, 185, 129, 0.8)', // Completed/Category 3
                        'rgba(239, 68, 68, 0.8)', // Cancelled/Category 4
                        'rgba(139, 92, 246, 0.8)', // Category 5
                        'rgba(249, 128, 255, 0.8)', // Category 6
                        'rgba(113, 113, 122, 0.8)', // Category 7
                        'rgba(250, 204, 21, 0.8)', // Category 8
                        'rgba(34, 197, 94, 0.8)', // Category 9
                        'rgba(59, 130, 246, 0.8)', // Category 10
                        'rgba(168, 85, 247, 0.8)', // Category 11
                        'rgba(244, 63, 94, 0.8)', // Category 12
                        'rgba(6, 182, 212, 0.8)', // Category 13
                        'rgba(132, 204, 22, 0.8)', // Category 14
                        'rgba(249, 168, 16, 0.8)', // Category 15
                        'rgba(236, 72, 153, 0.8)', // Category 16
                        'rgba(14, 165, 233, 0.8)', // Category 17
                        'rgba(163, 230, 53, 0.8)', // Category 18
                        'rgba(251, 146, 60, 0.8)', // Category 19
                        'rgba(232, 121, 220, 0.8)', // Category 20
                    ];

                    // Display column details
                    const columnDetails = document.getElementById('columnDetails');
                    columnDetails.innerHTML = ''; // Clear existing details

                    labels.forEach((label, index) => {
                        const value = parseFloat(values[index]);
                        const formattedValue = Number.isInteger(value) ?
                            value.toLocaleString('en-PH') // No decimals for whole numbers
                            :
                            value.toLocaleString('en-PH', {
                                minimumFractionDigits: 2
                            }); // Two decimals for non-whole numbers

                        const detailDiv = document.createElement('div');
                        detailDiv.className = 'profile-total';
                        // Use modulo to cycle through the defined colors if there are more labels than colors
                        detailDiv.style.borderLeftColor = colors[index % colors.length];
                        detailDiv.innerHTML = `<strong>${label}:</strong> ${formattedValue}`;
                        columnDetails.appendChild(detailDiv);
                    });

                    // If the chart already exists, destroy it before creating a new one
                    if (requestChart) {
                        requestChart.destroy();
                    }

                    // Create a new chart
                    requestChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: values,
                                // Use modulo to cycle through the defined colors
                                backgroundColor: labels.map((_, index) => colors[index % colors.length]),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        font: {
                                            family: 'Roboto Slab', // Custom font family
                                            size: 14, // Font size
                                            weight: 'bold', // Font weight
                                            style: 'italic' // Optional: Font style
                                        },
                                        color: '#333' // Font color
                                    }
                                },
                                tooltip: {
                                    bodyFont: {
                                        family: 'Roboto Slab', // Custom font for tooltip body
                                        size: 14, // Font size
                                        weight: 'normal' // Font weight
                                    },
                                    titleFont: {
                                        family: 'Roboto Slab', // Custom font for tooltip title
                                        size: 16, // Font size
                                        weight: 'bold' // Font weight
                                    }
                                },
                                title: {
                                    display: true,
                                    text: `Distribution by ${column === 'designation' ? 'Client Profile' : 'Service Requested'}`,
                                    font: {
                                        family: 'Roboto Slab', // Custom font for title
                                        size: 18, // Font size
                                        weight: 'bold' // Font weight
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching chart data:', error));
        }

        // Event listener for dropdown change
        document.getElementById('graphColumn').addEventListener('change', function() {
            const selectedColumn = this.value;
            updateChart(selectedColumn);
        });

        // Initialize the chart with the default column and current filters on page load
        // Get the initial selected column from the dropdown
        const initialColumn = document.getElementById('graphColumn').value;
        updateChart(initialColumn);

        // Form validation for service requested
        document.getElementById('jobRequestForm').addEventListener('submit', function(e) {
            const serviceRequested = document.querySelectorAll('input[name="service_requested[]"]:checked');
            if (serviceRequested.length === 0) {
                e.preventDefault(); // Prevent form submission
                alert('Please select at least one service requested.');
            }
        });

        // Edit Button Click
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                fetch(`fetch-job_requests-handler.php?id=${id}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest' // Add this header
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to fetch job request data.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }

                        // Populate the form with the fetched data
                        document.querySelector('[name="personal_name"]').value = data.personal_name || '';
                        document.querySelector('[name="client_name"]').value = data.client_name || '';
                        document.querySelector('[name="address"]').value = data.address || '';
                        document.querySelector('[name="contact_no"]').value = data.contact_number || '';
                        if (data.gender) {
                            document.querySelector(`[name="gender"][value="${data.gender}"]`).checked = true;
                        }
                        document.querySelector('[name="age"]').value = data.age || '';
                        if (data.designation) {
                            document.querySelector(`[name="designation"][value="${data.designation}"]`).checked = true;
                        }
                        document.querySelector('[name="work_description"]').value = data.work_description || '';
                        document.querySelector('[name="date"]').value = data.request_date || '';

                        // Add the ID to a hidden input field
                        const hiddenIdField = document.querySelector('[name="id"]');
                        if (!hiddenIdField) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'id';
                            input.value = data.id;
                            document.getElementById('jobRequestForm').appendChild(input);
                        } else {
                            hiddenIdField.value = data.id;
                        }

                        // Show the modal
                        const modal = document.getElementById("jobRequestModal");
                        modal.style.display = "block";
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while fetching the job request data.');
                    });
            });
        });

        // Delete Button Click
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this job request?')) {
                    fetch(`delete-job_request-handler.php`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams({
                                id
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Job request deleted successfully!');
                                document.querySelector(`tr[data-id="${id}"]`).remove();
                            } else {
                                alert('Error deleting job request: ' + data.message);
                            }
                        });
                }
            });
        });

        // Enable/disable the "designation_other" field based on the "Others" radio button
        document.querySelectorAll('input[name="designation"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const otherInput = document.querySelector('input[name="designation_other"]');
                if (this.value === "Others") {
                    otherInput.disabled = false;
                } else {
                    otherInput.disabled = true;
                    otherInput.value = ""; // Clear the field if "Others" is not selected
                }
            });
        });

        // Modal functionality for View
        const viewModal = document.getElementById("viewModal");
        const closeView = document.querySelector(".close-view");

        closeView.onclick = function() {
            viewModal.style.display = "none";
        };

        window.onclick = function(event) {
            if (event.target == viewModal) {
                viewModal.style.display = "none";
            }
        };

        // View Button Click
        document.querySelectorAll('.view-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                fetch(`fetch-job_requests-handler.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert('Error fetching job request data: ' + data.error);
                            return;
                        }

                        const details = data;

                        // Format the request date
                        const requestDate = new Date(details.request_date).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });

                        // Populate the modal with job request details
                        document.getElementById('viewDetails').innerHTML = `
                        <div style="display: flex; justify-content: space-between; width: 90%; margin-bottom: 20px;">
                            <div>
                                <p><strong>Client Name:</strong> ${details.client_name}</p>
                                <p><strong>Address:</strong> ${details.address}</p>
                                <p><strong>Contact No:</strong> ${details.contact_number}</p>
                                <p><strong>Gender:</strong> ${details.gender}</p>
                                <p><strong>Age:</strong> ${details.age}</p>
                            </div>
                            <div>
                                <p><strong>Request ID:</strong> ${details.id}</p>
                                <p><strong>Request Date:</strong> ${requestDate}</p>
                                <p><strong>Client Profile:</strong> ${details.designation}</p>
                                <p><strong>Company:</strong> ${details.company}</p>
                            </div>
                        </div>
                        <p><strong>Service Requested:</strong> ${details.service_requested}</p>
                        <p><strong>Equipment:</strong> ${details.equipment || 'N/A'}</p>
                        <p><strong>Hand Tools:</strong> ${details.hand_tools_other || 'N/A'}</p>
                        <p><strong>Other Equipment:</strong> ${details.equipment_other || 'N/A'}</p>
                        <p><strong>Work Description:</strong></p>
                        <p>${details.work_description}</p>
                    `;
                        viewModal.style.display = "block";
                    })
                    .catch(error => console.error('Error:', error));
            });
        });

        function enableModalCloseOnOutsideClick(modalSelector, contentSelector, closeCallback) {
            document.addEventListener('mousedown', function(event) {
                const modal = document.querySelector(modalSelector);
                const content = document.querySelector(contentSelector);
                if (modal && content && modal.style.display === "block") {
                    if (!content.contains(event.target) && modal.contains(event.target)) {
                        modal.style.display = "none";
                        if (typeof closeCallback === "function") closeCallback();
                    }
                }
            });
        }

        // Enable for both modals
        enableModalCloseOnOutsideClick('#jobRequestModal', '.modal-content', function() {
            document.getElementById('jobRequestForm').reset();
        });
        enableModalCloseOnOutsideClick('#viewModal', '.modal-content');
    </script>
</body>

</html>