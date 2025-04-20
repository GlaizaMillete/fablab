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

$whereClause = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";
$sql = "SELECT * FROM job_requests $whereClause ORDER BY request_date DESC";
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style3.css">
</head>

<body>
    <div class="container">
        <h1>Client Profile and Service Request Records</h1>
        <!-- The Modal -->
        <div id="jobRequestModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Client Profile and Service Request Form</h2>
                <form id="jobRequestForm" enctype="multipart/form-data" action="add-job_request-handler.php" method="POST">
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
                            <input type="text" name="designation_other">
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
                            <input type="text" name="personnel_name">
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
                <h3>Total Client Profile & Service Requests</h3>
                <div class="total-amount"><?= $totalRequests ?></div>
                <h4>By Status</h4>
                <?php foreach ($statusCounts as $status => $count): ?>
                    <div class="profile-total">
                        <strong><?= $status ?>:</strong> <?= $count ?>
                    </div>
                <?php endforeach; ?>
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
                <div>
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
                </div>
                <div>
                    <button type="submit">Filter</button>
                </div>
            </form>
        </div>

        <h2>Client Profile and Service Requests</h2>
        <button class="cpsc_button" id="openFormBtn">Add New Request</button>
        <table>
            <thead>
                <tr>
                    <th>Request Date</th>
                    <th>Client Name</th>
                    <th>Service Request</th>
                    <th>Client Profile</th>
                    <th>Reference File</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobRequests as $request): ?>
                    <tr>
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

                            // Append hand tools if available
                            if (!empty($request['hand_tools_other'])) {
                                $serviceRequest .= ": Hand Tools: " . htmlspecialchars($request['hand_tools_other']);
                            }

                            echo $serviceRequest;
                            ?>
                        </td>
                        <td>
                            <?php
                            // Start with the designation
                            $designation = htmlspecialchars($request['designation']);

                            // Append designation details if "Others" is selected
                            if ($designation === "Others" && !empty($request['designation_other'])) {
                                $designation .= ": " . htmlspecialchars($request['designation_other']);
                            }

                            echo $designation;
                            ?>
                        </td>
                        <td>
                            <?php if (!empty($request['reference_file'])): ?>
                                <a href="uploads/job-requests/<?= htmlspecialchars($request['reference_file']) ?>" target="_blank">View File</a>
                            <?php else: ?>
                                None
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        // Modal functionality
        const modal = document.getElementById("jobRequestModal");
        const btn = document.getElementById("openFormBtn");
        const span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Chart.js configuration
        const ctx = document.getElementById('requestChart').getContext('2d');
        const requestChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($chartData['labels']) ?>,
                datasets: [{
                    data: <?= json_encode($chartData['data']) ?>,
                    backgroundColor: <?= json_encode($chartData['colors']) ?>,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Allow the chart to resize dynamically
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Form validation for service requested
        document.getElementById('jobRequestForm').addEventListener('submit', function(e) {
            const serviceRequested = document.querySelectorAll('input[name="service_requested[]"]:checked');
            if (serviceRequested.length === 0) {
                e.preventDefault(); // Prevent form submission
                alert('Please select at least one service requested.');
            }
        });
    </script>
</body>

</html>