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

include "fetch-job_requests-handler.php";

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
                    <div class="form-columns">
                        <div>
                            <label>Request Title:</label>
                            <input type="text" name="request_title" required>
                        </div>
                        <div>
                            <label>Request Date:</label>
                            <input type="date" name="request_date" required>
                        </div>
                    </div>
                    <div class="form-columns">
                        <div>
                            <label>Client Name:</label>
                            <input type="text" name="client_name" required>
                        </div>
                        <div>
                            <label>Contact Number:</label>
                            <input type="text" name="contact_number" required>
                        </div>
                    </div>
                    <div>
                        <label>Client Profile:</label><br>
                        <input type="radio" name="client_profile" value="STUDENT" required> STUDENT<br>
                        <input type="radio" name="client_profile" value="MSME" required> MSME<br>
                        <input type="radio" name="client_profile" value="OTHERS" required> OTHERS (Specify):
                        <input type="text" name="client_profile_other">
                    </div>
                    <div>
                        <label>Request Description:</label>
                        <textarea name="request_description" placeholder="Provide detailed information about the service request..." required></textarea>
                    </div>
                    <div>
                        <label>Equipment Needed:</label><br>
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
                    </div>
                    <div class="form-columns">
                        <div>
                            <label>Priority Level:</label><br>
                            <input type="radio" name="priority" value="Low" required> Low<br>
                            <input type="radio" name="priority" value="Medium" required> Medium<br>
                            <input type="radio" name="priority" value="High" required> High<br>
                        </div>
                        <div>
                            <label>Estimated Completion Date:</label>
                            <input type="date" name="completion_date" required>
                        </div>
                    </div>
                    <div>
                        <label>Attach Reference Files:</label>
                        <input type="file" name="reference_file" accept=".pdf,.jpg,.jpeg,.png,.zip">
                        <p class="text-sm text-primary">Accepted formats: PDF, JPG, PNG, ZIP (Max: 10MB)</p>
                    </div>
                    <button type="submit">Submit Service Request</button>
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
                    <label>Client Profile:</label>
                    <select name="client_profile">
                        <option value="">All</option>
                        <option value="STUDENT" <?= isset($_GET['client_profile']) && $_GET['client_profile'] == 'STUDENT' ? 'selected' : '' ?>>STUDENT</option>
                        <option value="MSME" <?= isset($_GET['client_profile']) && $_GET['client_profile'] == 'MSME' ? 'selected' : '' ?>>MSME</option>
                        <option value="OTHERS" <?= isset($_GET['client_profile']) && $_GET['client_profile'] == 'OTHERS' ? 'selected' : '' ?>>OTHERS</option>
                    </select>
                </div>
                <div>
                    <label>Status:</label>
                    <select name="status">
                        <option value="">All</option>
                        <option value="Pending" <?= isset($_GET['status']) && $_GET['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="In Progress" <?= isset($_GET['status']) && $_GET['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="Completed" <?= isset($_GET['status']) && $_GET['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="Cancelled" <?= isset($_GET['status']) && $_GET['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label>Priority:</label>
                    <select name="priority">
                        <option value="">All</option>
                        <option value="Low" <?= isset($_GET['priority']) && $_GET['priority'] == 'Low' ? 'selected' : '' ?>>Low</option>
                        <option value="Medium" <?= isset($_GET['priority']) && $_GET['priority'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="High" <?= isset($_GET['priority']) && $_GET['priority'] == 'High' ? 'selected' : '' ?>>High</option>
                    </select>
                </div>
                <div>
                    <button type="submit">Filter</button>
                </div>
            </form>
            <h2>Search Requests</h2>
            <form method="GET" class="search-form">
                <input type="text" name="search_term" placeholder="Search by title, client name, or description" value="<?= isset($_GET['search_term']) ? htmlspecialchars($_GET['search_term']) : '' ?>" style="flex-grow: 1;">
                <button type="submit">Search</button>
            </form>
        </div>

        <h2>Client Profile and Service Requests</h2>
        <button id="openFormBtn" style="margin-bottom: 15px; float: right;">Add New Request</button>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Client</th>
                    <th>Profile</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Files</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobRequests as $request): ?>
                    <tr>
                        <td><?= htmlspecialchars($request['request_title']) ?></td>
                        <td><?= htmlspecialchars($request['client_name']) ?></td>
                        <td><?= htmlspecialchars($request['client_profile']) ?></td>
                        <td><?= htmlspecialchars($request['priority']) ?></td>
                        <td><?= htmlspecialchars($request['status']) ?></td>
                        <td><?= date("F d, Y", strtotime($request['request_date'])) ?></td>
                        <td>
                            <?php if (!empty($request['reference_file'])): ?>
                                <a href="requests/<?= htmlspecialchars($request['reference_file']) ?>" target="_blank">View File</a>
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
    </script>
</body>

</html>