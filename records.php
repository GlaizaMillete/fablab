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

// Initialize variables
$whereClauses = [];
if (!empty($_GET['from_month']) && !empty($_GET['to_month'])) {
    $from_month = intval($_GET['from_month']);
    $to_month = intval($_GET['to_month']);
    $whereClauses[] = "MONTH(billing_date) BETWEEN $from_month AND $to_month";
}
if (!empty($_GET['year'])) {
    $year = intval($_GET['year']);
    $whereClauses[] = "YEAR(billing_date) = $year";
}
if (!empty($_GET['client_profile'])) {
    $client_profile = $conn->real_escape_string($_GET['client_profile']);
    if ($client_profile == 'OTHERS') {
        $whereClauses[] = "client_profile NOT IN ('STUDENT', 'MSME')";
    } else {
        $whereClauses[] = "client_profile = '$client_profile'";
    }
}
if (!empty($_GET['prepared_by'])) {
    $prepared_by = $conn->real_escape_string(trim($_GET['prepared_by']));
    $whereClauses[] = "LOWER(prepared_by) LIKE LOWER('%$prepared_by%')";
}
if (!empty($_GET['approved_by'])) {
    $approved_by = $conn->real_escape_string(trim($_GET['approved_by']));
    $whereClauses[] = "LOWER(approved_by) LIKE LOWER('%$approved_by%')";
}
if (!empty($_GET['payment_received_by'])) {
    $payment_received_by = $conn->real_escape_string(trim($_GET['payment_received_by']));
    $whereClauses[] = "LOWER(payment_received_by) LIKE LOWER('%$payment_received_by%')";
}
if (!empty($_GET['receipt_acknowledged_by'])) {
    $receipt_acknowledged_by = $conn->real_escape_string(trim($_GET['receipt_acknowledged_by']));
    $whereClauses[] = "LOWER(receipt_acknowledged_by) LIKE LOWER('%$receipt_acknowledged_by%')";
}
if (!empty($_GET['search_name'])) {
    $search_name = $conn->real_escape_string(trim($_GET['search_name']));
    $whereClauses[] = "LOWER(client_name) LIKE LOWER('%$search_name%')";
}

$whereClause = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";
$query = "SELECT * FROM billing $whereClause ORDER BY id DESC";
$result = $conn->query($query);

if (!$result) {
    die("Error executing query: " . $conn->error);
}

$ovaTotal = 0;
$totalsByProfile = ['STUDENT' => 0, 'MSME' => 0, 'OTHERS' => 0];
$rows = [];

while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
    $ovaTotal += $row['total_invoice'];
    if (isset($totalsByProfile[$row['client_profile']])) {
        $totalsByProfile[$row['client_profile']] += $row['total_invoice'];
    } else {
        $totalsByProfile['OTHERS'] += $row['total_invoice'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment and Release Records</title>
    <link rel="icon" href="fablab.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style2.css">
</head>

<body>
    <div class="container">
        <h1>Payment and Release</h1>
        <!-- The Modal -->
        <div id="billingModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Payment and Releasing Form</h2>
                <form id="billingForm" enctype="multipart/form-data" action="add-billing-handler.php" method="POST">
                    <!-- Section 1: Personal Information -->
                    <h3>1. Personal Information</h3>
                    <div>
                        <label>No:</label>
                        <input type="number" name="no" required>
                    </div>
                    <div>
                        <label>Date:</label>
                        <input type="date" name="date" required>
                    </div>
                    <div>
                        <label>Client Name:</label>
                        <input type="text" name="client_name" required>
                    </div>
                    <div>
                        <label>Address:</label>
                        <input type="text" name="address" required>
                    </div>
                    <div>
                        <label>Contact No:</label>
                        <input type="number" name="contact_no" required>
                    </div>
                    <div>
                        <label>Client Profile:</label><br>
                        <input type="radio" name="client_profile" value="STUDENT" required> STUDENT<br>
                        <input type="radio" name="client_profile" value="MSME" required> MSME<br>
                        <input type="radio" name="client_profile" value="OTHERS" required> OTHERS (Specify):
                        <input type="text" name="client_profile_other">
                    </div>
                    <div>
                        <label>Description of the Project:</label>
                        <textarea name="description" required></textarea>
                    </div>

                    <!-- Section 2: Details of the Service to be Rendered -->
                    <h3>2. Details of the Service to be Rendered</h3>
                    <table id="serviceTable">
                        <thead>
                            <tr>
                                <th>Service Name</th>
                                <th>Unit</th>
                                <th>Rate</th>
                                <th>Total Cost</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="service_name[]" required></td>
                                <td><input type="text" name="unit[]" required></td>
                                <td><input type="text" name="rate[]" required></td>
                                <td><input type="number" name="total_cost[]" step="0.01" required></td>
                                <td><button type="button" onclick="removeRow(this)">Remove</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" id="addRowBtn">Add Row</button>
                    <div>
                        <label>Total:</label>
                        <input type="number" id="totalCost" name="total" step="0.01" readonly>
                    </div>

                    <!-- Completion Information -->
                    <div>
                        <label>Completion Date:</label>
                        <input type="date" name="completion_date" required>
                    </div>
                    <div>
                        <label>Prepared By:</label>
                        <input type="text" name="prepared_by" value="<?= $_SESSION['staff_name'] ?>" readonly>
                    </div>
                    <div>
                        <label>Date:</label>
                        <input type="date" name="prepared_date" value="<?= date('Y-m-d') ?>" readonly>
                    </div>

                    <!-- Section 3: Order Payment -->
                    <h3>3. Order Payment</h3>
                    <div>
                        <label>Please issue an Official Receipt in favor of:</label>
                        <input type="text" name="or_favor" required>
                    </div>
                    <div>
                        <label>For the amount of:</label>
                        <input type="number" id="totalAmount" name="or_amount" step="0.01" readonly>
                    </div>
                    <div>
                        <label>Approved by:</label>
                        <input type="text" name="approved_by" required>
                    </div>

                    <!-- Section 4: Payment -->
                    <h3>4. Payment</h3>
                    <div>
                        <label>OR No:</label>
                        <input type="number" name="or_no" required>
                    </div>
                    <div>
                        <label>Date:</label>
                        <input type="date" name="payment_date" required>
                    </div>
                    <div>
                        <label>Payment Received by:</label>
                        <input type="text" name="payment_received_by" required>
                    </div>

                    <!-- Section 5: Receipt of Completed Work -->
                    <h3>5. Receipt of Completed Work</h3>
                    <div>
                        <label>I, the client, acknowledge that I have received the above product.</label>
                        <input type="text" name="receipt_acknowledged_by" required>
                    </div>
                    <div>
                        <label>Date:</label>
                        <input type="date" name="receipt_date" required>
                    </div>

                    <!-- Section 6: Reference File -->
                    <div>
                        <label>Upload Reference File (PDF, DOC, DOCX, JPG, PNG):</label>
                        <input type="file" name="billing_pdf" accept=".pdf,.doc,.docx,.jpg,.png" required>
                    </div>

                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>

        <div class="dashboard">
            <div class="chart-container">
                <canvas id="paymentChart"></canvas>
            </div>
            <div class="totals-card">
                <h3>Data Visualization</h3>
                <div style="margin-bottom: 12px;">
                    <label for="paymentColumn">Column:</label>
                    <select id="paymentColumn">
                        <option value="client_profile">Client Profile</option>
                        <option value="prepared_by">Prepared By</option>
                        <option value="approved_by">Approved By</option>
                        <option value="payment_received_by">Payment Received By</option>
                        <option value="receipt_acknowledged_by">Receipt Acknowledged By</option>
                    </select>
                </div>
                <label>Column Details:</label>
                <div id="paymentColumnDetails">
                    <!-- Column details will be displayed here -->
                </div>
            </div>
        </div>

        <h2>Filter Payment and Release Records</h2>
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
                    <label>Prepared By:</label>
                    <input type="text" name="prepared_by" value="<?= isset($_GET['prepared_by']) ? htmlspecialchars($_GET['prepared_by']) : '' ?>">
                </div>
                <div>
                    <label>Approved By:</label>
                    <input type="text" name="approved_by" value="<?= isset($_GET['approved_by']) ? htmlspecialchars($_GET['approved_by']) : '' ?>">
                </div>
                <div>
                    <label>Payment Received By:</label>
                    <input type="text" name="payment_received_by" value="<?= isset($_GET['payment_received_by']) ? htmlspecialchars($_GET['payment_received_by']) : '' ?>">
                </div>
                <div>
                    <label>Receipt Acknowledged By:</label>
                    <input type="text" name="receipt_acknowledged_by" value="<?= isset($_GET['receipt_acknowledged_by']) ? htmlspecialchars($_GET['receipt_acknowledged_by']) : '' ?>">
                </div>
                <div>
                    <button type="submit">Filter</button>
                </div>
            </form>
        </div>

        <div class="search-section">
            <h2>Search Client Name</h2>
            <form method="GET" class="search-form">
                <input type="text" name="search_name" placeholder="Enter client name"
                    value="<?= isset($_GET['search_name']) ? htmlspecialchars($_GET['search_name']) : '' ?>"
                    style="flex-grow: 1;">
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="billing-table">
            <h2>Payment and Release Records</h2>
            <button id="openFormBtn">Add New Payment and Release</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Service Description</th>
                    <th>Client</th>
                    <th>Profile</th>
                    <th>Total Amount</th>
                    <th>Date</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['no']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['client_name']); ?></td>
                        <td><?php echo htmlspecialchars(trim($row['client_profile'])); ?></td>
                        <td>&#8369;<?php echo number_format($row['total_invoice'], 2); ?></td>
                        <td><?php echo date("F d, Y", strtotime($row['billing_date'])); ?></td>
                        <td>
                            <?php if (!empty($row['billing_pdf'])): ?>
                                <button class="ref-link" onclick="window.open('uploads/billing/<?php echo htmlspecialchars($row['billing_pdf']); ?>', '_blank')">
                                    View PDF
                                </button>
                            <?php else: ?>
                                <span class="no-pdf">None</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Modal functionality
        const modal = document.getElementById("billingModal");
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
        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        let paymentChart;

        // Function to fetch and update chart data
        function updatePaymentChart(column) {
            fetch(`fetch-payment-data.php?column=${column}`)
                .then(response => response.json())
                .then(data => {
                    const labels = data.labels;
                    const values = data.values;
                    const colors = [
                        'rgba(245, 158, 11, 0.8)', // Example colors
                        'rgba(37, 99, 235, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ];

                    // Display column details
                    const columnDetails = document.getElementById('paymentColumnDetails');
                    columnDetails.innerHTML = ''; // Clear existing details

                    labels.forEach((label, index) => {
                        const detailDiv = document.createElement('div');
                        detailDiv.className = 'profile-total';
                        detailDiv.style.borderLeftColor = colors[index]; // Assign color dynamically
                        detailDiv.innerHTML = `<strong>${label}:</strong> &#8369;${parseFloat(values[index]).toLocaleString('en-PH', {
                    minimumFractionDigits: 2
                })}`;
                        columnDetails.appendChild(detailDiv);
                    });

                    // If the chart already exists, destroy it before creating a new one
                    if (paymentChart) {
                        paymentChart.destroy();
                    }

                    // Create a new chart
                    paymentChart = new Chart(paymentCtx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: values,
                                backgroundColor: colors, // Use the same colors for the chart
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching chart data:', error));
        }

        // Event listener for dropdown change
        document.getElementById('paymentColumn').addEventListener('change', function() {
            const selectedColumn = this.value;
            updatePaymentChart(selectedColumn);
        });

        // Initialize the chart with the default column
        updatePaymentChart('client_profile');

        // Add new row to the service table
        document.getElementById('addRowBtn').addEventListener('click', function() {
            const table = document.getElementById('serviceTable').getElementsByTagName('tbody')[0];
            const newRow = table.insertRow();
            newRow.innerHTML = `
            <td><input type="text" name="service_name[]" required></td>
            <td><input type="text" name="unit[]" required></td>
            <td><input type="text" name="rate[]" required></td>
            <td><input type="number" name="total_cost[]" step="0.01" required></td>
            <td><button type="button" onclick="removeRow(this)">Remove</button></td>
        `;
        });

        // Remove a row from the service table
        function removeRow(button) {
            const row = button.parentElement.parentElement;
            row.parentElement.removeChild(row);
        }
    </script>
</body>

</html>